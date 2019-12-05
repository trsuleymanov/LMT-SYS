<?php

namespace app\controllers;

use app\models\ClientExt;
use app\models\UserRole;
use Yii;
use yii\web\Controller;


class ClientExtController extends Controller
{
    public function actionAjaxGetClientextBlock()
    {
        Yii::$app->response->format = 'json';

        return [
            'success' => true,
            'html' => \app\widgets\ClientextWidget::widget()
        ];
    }


    public function actionAjaxGetClientExtList()
    {
        Yii::$app->response->format = 'json';

        $clientexts = ClientExt::find()->where(['status' => 'created'])->all();

        return [
            'success' => true,
            'html' => $this->renderPartial('list.php', [
                'clientexts' => $clientexts,
            ])
        ];
    }
}
