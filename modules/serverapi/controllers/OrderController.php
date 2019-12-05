<?php
namespace app\modules\serverapi\controllers;

use Yii;
//use yii\filters\auth\HttpBearerAuth;
use app\modules\serverapi\models\HttpSecretKeyAuth;


class OrderController extends \yii\rest\ActiveController
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

        $actions['get-not-sync-orders']['class'] = 'app\modules\serverapi\actions\order\GetNotSyncOrdersAction';
        $actions['set-sync-to-orders']['class'] = 'app\modules\serverapi\actions\order\SetSyncToOrdersAction';
        $actions['set-push-accept']['class'] = 'app\modules\serverapi\actions\order\SetPushAcceptAction';
        $actions['set-push-reject']['class'] = 'app\modules\serverapi\actions\order\SetPushRejectAction';

        return $actions;
    }
}