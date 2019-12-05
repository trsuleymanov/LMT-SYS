<?php
namespace app\modules\api\controllers;

use yii\filters\auth\HttpBearerAuth;


/**
 * Рейсо-машины
 */
class TripTransportController extends \yii\rest\ActiveController
{
    public $modelClass = '';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::className(),
        ];

        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['view'], $actions['delete'], $actions['index'], $actions['create'], $actions['update']);

        $actions['orderlist']['class'] = 'app\modules\api\actions\triptransport\OrderListAction';
        $actions['messagelist']['class'] = 'app\modules\api\actions\triptransport\MessageListAction';

        return $actions;
    }

    protected function verbs(){
        return [
            'orderlist' => ['GET', 'POST'],
            'messagelist' => ['GET', 'POST'],
        ];
    }

}
