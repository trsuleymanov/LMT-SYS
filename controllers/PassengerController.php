<?php

namespace app\controllers;

use app\models\Passenger;
use Yii;
use app\models\Client;
use app\models\ClientSearch;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\base\ErrorException;
use yii\filters\VerbFilter;
use app\models\OrderSearch;


class PassengerController extends Controller
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


    public function actionAjaxGetPassenger()
    {
        Yii::$app->response->format = 'json';


        $passport_series = Yii::$app->request->post('passport_series');
        $passport_number = Yii::$app->request->post('passport_number');

        $passenger = Passenger::find()
            ->where([
                'series' => $passport_series,
                'number' => $passport_number,
            ])->one();

        if($passenger != null) {
            $passenger->date_of_birth = date("d.m.Y", $passenger->date_of_birth);
        }

        return [
            'success' => true,
            'passenger' => $passenger,
        ];
    }

}
