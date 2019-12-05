<?php
namespace app\modules\serverapi\controllers;

use app\modules\serverapi\models\HttpSecretKeyAuth;


class SettingController extends \yii\rest\ActiveController
{
    public $modelClass = '';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpSecretKeyAuth::className(),
        ];

        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();

        $actions['get-not-sync-setting']['class'] = 'app\modules\serverapi\actions\setting\GetNotSyncSettingAction';
        $actions['set-sync-to-setting']['class'] = 'app\modules\serverapi\actions\setting\SetSyncToSettingAction';

        return $actions;
    }

//    protected function verbs(){
//        return [
//            'test' => ['GET', 'POST'],
//        ];
//    }

}
