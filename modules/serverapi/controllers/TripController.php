<?php
namespace app\modules\serverapi\controllers;

use Yii;
use app\modules\serverapi\models\HttpSecretKeyAuth;


class TripController extends \yii\rest\ActiveController
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

        $actions['get-trips']['class'] = 'app\modules\serverapi\actions\trip\GetTripsAction';

        return $actions;
    }

    protected function verbs(){
        return [
            'get-trips' => ['GET', 'POST'],
        ];
    }
}
