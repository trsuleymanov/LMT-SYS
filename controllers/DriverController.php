<?php

namespace app\controllers;

use app\models\DriverOperatorChat;
use app\models\SocketDemon;
use app\models\TripTransport;
use app\models\User;
use app\models\UserRole;
use ErrorException;
use Yii;
use app\models\Driver;
use app\models\DriverSearch;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * DriverController implements the CRUD actions for Driver model.
 */
class DriverController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionAjaxGetActiveDrivers() {

        Yii::$app->response->format = 'json';

        $search = Yii::$app->getRequest()->post('search');

        $selected_transport_id = Yii::$app->getRequest()->post('transport_id');

        $drivers = Driver::find()
            ->where(['active' => 1])
            ->andWhere(['like', 'fio', $search])
            ->all();

        if($selected_transport_id){
            $first_second_drivers_query = Driver::find()->where(['active' => 1])
                ->andWhere([
                    'OR',
                    ['primary_transport_id' => $selected_transport_id],
                    ['secondary_transport_id' => $selected_transport_id],
                ]);
            $first_second_drivers = $first_second_drivers_query->orderBy(['fio'=>'ASC'])->all();

            foreach($first_second_drivers as $first_second_driver) {
                foreach($drivers as $k => $item){
                    if($item->id == $first_second_driver->id){
                        unset($drivers[$k]);
                        break;
                    }
                }
            }

            $total_result = [];
            $total_result = array_merge($total_result, $first_second_drivers);
            $total_result = array_merge($total_result, $drivers);

        }else {
            $total_result = $drivers;
        }

        $out['results'] = [];
        foreach($total_result as $driver) {
            $out['results'][] = [
                'id' => $driver->id,
                'text' => $driver->fio,
            ];
        }

        return $out;
    }

    public function actionAjaxSendMsgToDriver($chat_id, $text) {

        Yii::$app->response->format = 'json';

        $chat = DriverOperatorChat::find()->where(['id' => $chat_id])->one();
        if($chat == null) {
            throw new ForbiddenHttpException('Переписка не найдена');
        }

//        $trip_transport = TripTransport::find()->where(['id' => $trip_transport_id])->one();
//        if($trip_transport == null) {
//            throw new ForbiddenHttpException('Транспорто-машина не найдена');
//        }

        $driver = $chat->driver;
        if($driver == null) {
            throw new ForbiddenHttpException('Водитель не найден');
        }


        // теперь нужно послать с сервера в демона, а демон должен послать в телефон
        $current_user = Yii::$app->user->identity;
        $user_fio = $current_user->firstname.' '.$current_user->lastname;

        // отправляем сообщение на все возможные устройства пользователя
        $magic_code = '';
        $aMesData = [
            'message_type' => 'message',
            'message' => $text,
            'user_fio' => $user_fio,
            'message_id' => $chat_id,
            'message_from_driver' => $chat->message_from_driver
        ];

        if($driver->magicDevice != null) {
            $magic_code = $driver->magicDevice->code;
            //SocketDemon::sendOutDeviceMessageInstant($magic_code, 'message', $text, $user_fio, $chat_id);
            SocketDemon::sendOutDeviceMessageInstant($magic_code, $aMesData);
        }
        if(!empty($driver->device_code) && $driver->device_code != $magic_code) {
            //SocketDemon::sendOutDeviceMessageInstant($driver->device_code, 'message', $text, $user_fio, $chat_id);
            SocketDemon::sendOutDeviceMessageInstant($driver->device_code, $aMesData);
        }


        $chat->operator_id = $current_user->id;
        $chat->answer_from_operator = $text;
        $chat->answer_from_operator_at = time();
        if(!$chat->save(false)) {
            throw new ErrorException('Не удалось сохранить сообщение');
        }


        $data = [
            'chat_id' => $chat->id,
            'answer' => $chat->answer_from_operator
        ];
        $aUsersIds = [];
        SocketDemon::sendOutBrowserMessageInstant('all_site_pages', [''], 'setAnswerForDriverMessage', $data, $aUsersIds);


        return [
            'success' => true,
        ];
    }


    protected function findModel($id)
    {
        if (($model = Driver::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
