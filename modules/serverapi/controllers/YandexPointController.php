<?php
namespace app\modules\serverapi\controllers;

use Yii;
use app\modules\serverapi\models\HttpSecretKeyAuth;


class YandexPointController extends \yii\rest\ActiveController
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

        $actions['get-yandex-points']['class'] = 'app\modules\serverapi\actions\yandexpoint\GetYandexPointsAction';
        $actions['set-sync-to-yandex-points']['class'] = 'app\modules\serverapi\actions\yandexpoint\SetSyncToYandexPointsAction';

        return $actions;
    }

    protected function verbs(){
        return [
            'get-yandex-points' => ['GET', 'POST'],
            'set-sync-to-yandex-points' => ['GET', 'POST'],
        ];
    }
}
