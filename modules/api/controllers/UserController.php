<?php
namespace app\modules\api\controllers;

use yii\filters\auth\HttpBearerAuth;


/**
 * Default controller for the `m-api` module
 */
class UserController extends \yii\rest\ActiveController
{
    public $modelClass = '';
    //public $modelClass = 'app\models\User';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::className(),
        ];
        $behaviors['authenticator']['except'] = [
            'driverlogin', 'driverloginbydevicecode', 'driverphotologin'
        ];

        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['view'], $actions['delete'], $actions['index'], $actions['create'], $actions['update']);
        //unset($actions['view'], $actions['delete'], $actions['index']);

        $actions['view']['class'] = 'app\modules\api\actions\user\ViewAction';
        $actions['setlocation']['class'] = 'app\modules\api\actions\user\SetLocationAction';
        $actions['driverlogin']['class'] = 'app\modules\api\actions\user\DriverLoginAction';
        $actions['driverloginbydevicecode']['class'] = 'app\modules\api\actions\user\DriverLoginByDeviceCodeAction';
        $actions['driverphotologin']['class'] = 'app\modules\api\actions\user\DriverPhotoLoginAction';


        return $actions;
    }

    protected function verbs(){
        return [
            'view' => ['GET', 'POST'],
            'setlocation' => ['GET', 'POST'],
            'driverlogin' => ['GET', 'POST'],
            'driverloginbydevicecode' => ['GET', 'POST'],
            'driverphotologin' => ['GET', 'POST'],
        ];
    }

}
