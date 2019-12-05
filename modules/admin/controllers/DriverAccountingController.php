<?php

namespace app\modules\admin\controllers;

use Yii;
use app\models\Driver;
use app\models\DriverSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\TripTransport;
use app\models\TripTransportSearch;
use app\models\DayReportTripTransportSearch;

/**
 * DriverAccountingController implements the CRUD actions for Driver model.
 */
class DriverAccountingController extends Controller
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

    /**
     * Lists all Driver models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DayReportTripTransportSearch();
        $dataProvider = $searchModel->searchDriverAccounting(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}
