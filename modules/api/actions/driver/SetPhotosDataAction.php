<?php

namespace app\modules\api\actions\driver;

use app\models\Driver;
use app\models\DriverPhoto;
use app\models\Transport;
use app\models\TripTransport;
use Yii;
use app\models\User;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;

/*
 * Получение от водителей фотографий с дополнительными данными
 */
class SetPhotosDataAction extends \yii\rest\Action
{
    public $modelClass = '';

    public function run()
    {
        //echo "post:<pre>"; print_r(Yii::$app->request->post()); echo "</pre>";
        //echo "get:<pre>"; print_r(Yii::$app->request->get()); echo "</pre>";
        //echo "files:<pre>"; print_r(Yii::$app->request->files); echo "</pre>";


        $user = User::find()->where(['id' => Yii::$app->user->id])->one();
        if($user == null) {
            throw new ForbiddenHttpException('Пользователь не найден');
        }

        $foto1_link = Yii::$app->request->post('foto1_link');
        $foto2_link = Yii::$app->request->post('foto2_link');
        $photo1_date = Yii::$app->request->post('photo1_date');
        $photo2_date = Yii::$app->request->post('photo2_date');
        $transport_id = Yii::$app->request->post('transport_id');
        $driver_id = Yii::$app->request->post('driver_id');
        //echo "post:<pre>"; print_r(Yii::$app->request->post()); echo "</pre>";

        $transport = Transport::find()->where(['id' => $transport_id])->one();
        if($transport == null) {
            throw new ForbiddenHttpException('Машина не найдена');
        }

        $driver_photo1 = new DriverPhoto();
        $driver_photo1->user_id = $user->id;
        $driver_photo1->driver_id = $driver_id;
        $driver_photo1->time_loading_finish = time();
        $driver_photo1->transport_id = $transport_id;
        $driver_photo1->transport_car_reg = $transport->car_reg;
        $driver_photo1->photo_created_on_mobile = $photo1_date;
        $driver_photo1->photo_link = 'http://417417.ru/RCP'.$foto1_link;
        if(!$driver_photo1->save()) {
            throw new ForbiddenHttpException('Не удалось сохранить данные первого скриншота');
        }

        if(!empty($foto2_link)) {
            $driver_photo2 = new DriverPhoto();
            $driver_photo2->user_id = $user->id;
            $driver_photo2->driver_id = $driver_id;
            $driver_photo2->time_loading_finish = time();
            $driver_photo2->transport_id = $transport_id;
            $driver_photo2->transport_car_reg = $transport->car_reg;
            $driver_photo2->photo_created_on_mobile = $photo2_date;
            $driver_photo2->photo_link = 'http://417417.ru/RCP'.$foto2_link;
            if (!$driver_photo2->save()) {
                throw new ForbiddenHttpException('Не удалось сохранить данные второго скриншота');
            }
        }

        return; // 200
    }
}
