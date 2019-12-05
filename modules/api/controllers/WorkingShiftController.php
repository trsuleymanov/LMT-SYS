<?php
namespace app\modules\api\controllers;

use yii\filters\auth\HttpBearerAuth;


/**
 * Получение сигналов из приложения о разблокировка/блокировке экрана и т.п.
 */
class WorkingShiftController extends \yii\rest\ActiveController
{
    public $modelClass = '';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::className(),
        ];

        $behaviors['authenticator']['except'] = [
            'setstatus',
            'getdata'
        ];

        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['view'], $actions['delete'], $actions['index'], $actions['create'], $actions['update']);

        $actions['setstatus']['class'] = 'app\modules\api\actions\workingshift\SetStatusAction';
        $actions['getdata']['class'] = 'app\modules\api\actions\workingshift\GetDataAction';

        return $actions;
    }

    protected function verbs(){
        return [
            'setstatus' => ['GET', 'POST'],
            'getdata' => ['GET', 'POST'],
        ];
    }

}
