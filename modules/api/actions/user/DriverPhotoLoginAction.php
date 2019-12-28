<?php

namespace app\modules\api\actions\user;

use app\models\Driver;
use app\models\Setting;
use app\models\Transport;
use app\models\TransportExpenses;
use app\models\TripTransport;
use Yii;
//use app\models\LoginForm;
use app\models\DriverPhotoLoginForm;
use yii\base\ErrorException;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;


class DriverPhotoLoginAction extends \yii\rest\Action
{
    public $modelClass = '';

    /**
     * Аутентификация водителя по идентификатору машины
     */
    public function run()
    {
//        $loginForm = new LoginForm();
//
//        if ($loginForm->load(Yii::$app->getRequest()->getBodyParams(), '')) {
//            $user = $loginForm->getUser();
//
//            if($user->userRole->alias != 'driver') {
//                throw new ForbiddenHttpException('У пользователя должна быть роль Водителя');
//            }
//
//            if(!$loginForm->login(false)) {
//                $loginForm->validate();
//
//                return $loginForm;
//            }
//
//
//            return [
//                'token' => $user->token,
//                'photo_access_code' => $user->photoAccessCode
//            ];
//        }else {
//            $loginForm->validate();
//
//            return $loginForm;
//        }


        $driverLoginForm = new DriverPhotoLoginForm();
        $driverLoginForm->load(Yii::$app->getRequest()->getBodyParams(), '');
        if (!$driverLoginForm->validate()) {
            return $driverLoginForm;
        }

        $driverLoginForm->login(false);

        $user = $driverLoginForm->getUser();

        $transports = Transport::find()
            ->where(['active' => true])
            ->andWhere(['accountability' => true])
            ->all();
        $aTransports = [];
        foreach($transports as $transport) {
            //$aTransports[$transport->id] = $transport->name2;
            $aTransports[] = [
                'id' => $transport->id,
                //'name' => $transport->car_reg.' '.$transport->sh_model
                'car_reg' => $transport->car_reg,
                'sh_model' => $transport->sh_model,
                'model' => $transport->model
            ];
        }

        $aDrivers = [];
        $drivers = Driver::find()
            ->where(['active' => true])
            ->andWhere(['accountability' => true])
            ->all();
        foreach($drivers as $driver) {
            $aDrivers[] = [
                'id' => $driver->id,
                'name' => $driver->fio
            ];
        }

        //$aDrivers = ArrayHelper::map($drivers, 'id', 'fio');

        //$settings = Setting::find()->where(['id' => 1])->one();


        // ищем машину соответствующую водителю - это последняя отправленная машина с этим водителем
        $trip_transport = TripTransport::find()
            ->where(['driver_id' => $driverLoginForm->driver->id])
            ->andWhere(['>', 'date_sended', 0])
            ->orderBy(['date_sended' => SORT_DESC])
            ->one();
        if($trip_transport == null) {
            throw new ErrorException('Последний отправленный рейс водителя не найден');
        }

        return [
            'id' => $user->id,
            'token' => $user->token,
            'photo_access_code' => $user->photoAccessCode,
            'transports' => $aTransports,
            'drivers' => $aDrivers,
            'driver_id' => $driverLoginForm->driver->id,
            'transport_id' => $trip_transport->transport_id,
            'photo_server_url' => Yii::$app->setting->photo_server_url
        ];

    }
}
