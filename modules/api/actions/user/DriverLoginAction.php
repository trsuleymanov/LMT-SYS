<?php

namespace app\modules\api\actions\user;

use Yii;
use app\models\DriverLoginForm;



class DriverLoginAction extends \yii\rest\Action
{
    public $modelClass = '';

    /**
     * Аутентификация водителя по идентификатору машины
     */
    public function run()
    {
        $driverLoginForm = new DriverLoginForm();
        $driverLoginForm->load(Yii::$app->getRequest()->getBodyParams(), '');
        if (!$driverLoginForm->validate()) {
            return $driverLoginForm;
        }

        $driverLoginForm->login(false);

        $user = $driverLoginForm->getUser();
        $user_role = $user->userRole;

        $trip_transport = $driverLoginForm->getTripTransport();

        return [
            'id' => $user->id,
            'token' => $user->token,
            'user_role_alias' => $user_role->alias,
            'transport_access_key' => $driverLoginForm->transport_access_key, // возвращаем обратно, потому в приложение нужно сохранять правильный (правильно заполненный) идентификатор машины
            'trip_transport_id' => ($trip_transport != null ? $trip_transport->id : '')
        ];

    }
}
