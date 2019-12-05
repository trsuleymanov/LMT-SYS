<?php

namespace app\controllers;

use app\models\DispatcherAccounting;
use Yii;
use yii\base\ErrorException;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Point;
use app\models\PointSearch;
use app\models\Street;
use app\models\Direction;


class DispatcherAccountingController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }


    /*
     * Функция сохранение новой точки
     */
    public function actionAjaxCreateLogDblClick()
    {
        Yii::$app->response->format = 'json';

        $value = Yii::$app->getRequest()->post('value');
        DispatcherAccounting::createLog('double_click', 0, 0, 0, $value);

        return [
            'success' => true,
        ];
    }

    /*
     * Открытие модального окна "Печать рейса"
     */
    public function actionAjaxCreateLogOpenPrintModal()
    {
        Yii::$app->response->format = 'json';

        DispatcherAccounting::createLog('open_print_modal', 0, 0, 0);

        return [
            'success' => true,
        ];
    }

    public function actionAjaxCreateLogHandlingRequest($order_id, $phone)
    {
        Yii::$app->response->format = 'json';

        //$order_id = Yii::$app->getRequest()->post('order_id');
        DispatcherAccounting::createLog('handling_client_server_request', $order_id, 0, 0, $phone);

        return [
            'success' => true,
        ];
    }

}
