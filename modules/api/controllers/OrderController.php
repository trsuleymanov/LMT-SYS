<?php
namespace app\modules\api\controllers;

use yii\filters\auth\HttpBearerAuth;


/**
 * Контроллер для работы с заказами в мобильном
 */
class OrderController extends \yii\rest\ActiveController
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

        $actions['setconfirmedtimesat']['class'] = 'app\modules\api\actions\order\SetConfirmedTimeSatAction';
        $actions['setcashreceived']['class'] = 'app\modules\api\actions\order\SetCashReceivedAction';
        $actions['setcashreceivedconfirm']['class'] = 'app\modules\api\actions\order\SetCashReceivedConfirmAction';

        return $actions;
    }

    protected function verbs(){
        return [
            'setconfirmedtimesat' => ['GET', 'POST'],
            'setcashreceived' => ['GET', 'POST'],
            'setcashreceivedconfirm' => ['GET', 'POST'],
        ];
    }

}
