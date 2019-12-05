<?php
namespace app\modules\serverapi\controllers;

use Yii;
use app\modules\serverapi\models\HttpSecretKeyAuth;


class TariffController extends \yii\rest\ActiveController
{
    public $modelClass = '';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpSecretKeyAuth::className(),
        ];

//        $behaviors['authenticator']['except'] = [
//            'get-yandex-points',
//        ];

        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();

        $actions['get-tariffs']['class'] = 'app\modules\serverapi\actions\tariff\GetTariffsAction';
        $actions['set-sync-to-tariffs']['class'] = 'app\modules\serverapi\actions\tariff\SetSyncToTariffsAction';

        return $actions;
    }

    protected function verbs(){
        return [
            'get-tariffs' => ['GET', 'POST'],
            'set-sync-to-tariffs' => ['GET', 'POST'],
        ];
    }
}
