<?php
namespace app\modules\serverapi\controllers;

use app\modules\serverapi\models\HttpSecretKeyAuth;


class CityController extends \yii\rest\ActiveController
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

        $actions['get-not-sync-cities']['class'] = 'app\modules\serverapi\actions\city\GetNotSyncCitiesAction';
        $actions['set-sync-to-cities']['class'] = 'app\modules\serverapi\actions\city\SetSyncToCitiesAction';

        return $actions;
    }
}
