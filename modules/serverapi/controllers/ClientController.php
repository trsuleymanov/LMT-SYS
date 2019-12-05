<?php
namespace app\modules\serverapi\controllers;

use app\modules\serverapi\models\HttpSecretKeyAuth;


class ClientController extends \yii\rest\ActiveController
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

        $actions['get-not-sync-clients']['class'] = 'app\modules\serverapi\actions\client\GetNotSyncClientsAction';
        $actions['set-sync-to-clients']['class'] = 'app\modules\serverapi\actions\client\SetSyncToClientsAction';

        return $actions;
    }

    protected function verbs(){
        return [
            'get-not-sync-clients' => ['GET', 'POST'],
            'set-sync-to-clients' => ['GET', 'POST'],
        ];
    }

}
