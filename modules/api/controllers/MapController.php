<?php
namespace app\modules\api\controllers;

use yii\filters\auth\HttpBearerAuth;


/**
 * Контроллер для рабты яндекс-карты в мобильном
 */
class MapController extends \yii\rest\ActiveController
{
    public $modelClass = '';
    //public $modelClass = 'app\models\User';

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

        $actions['getmapdata']['class'] = 'app\modules\api\actions\map\GetMapDataAction';

        return $actions;
    }

    protected function verbs(){
        return [
            'getmapdata' => ['GET', 'POST'],
        ];
    }

}
