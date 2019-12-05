<?php

namespace app\modules\admin\controllers;

use Yii;
use app\models\DayReportTransportCircle;
use app\models\DayReportTransportCircleSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;


class DayReportTransportCircleController extends Controller
{

    public function actionIndex()
    {
        $query_params = Yii::$app->request->queryParams;
        if(!isset($query_params['DayReportTransportCircleSearch']['date']) || empty($query_params['DayReportTransportCircleSearch']['date'])) {
            $query_params['DayReportTransportCircleSearch']['date'] = date('d.m.Y').'-'.date('d.m.Y');
        }

        $searchModel = new DayReportTransportCircleSearch();
        $dataProvider = $searchModel->search($query_params, $query_params['DayReportTransportCircleSearch']['date']);

        $dataProviderWithoutPagination = $searchModel->search($query_params, $query_params['DayReportTransportCircleSearch']['date'], false);

        return $this->render('index', [
            'date' => $query_params['DayReportTransportCircleSearch']['date'],
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'dataProviderWithoutPagination' => $dataProviderWithoutPagination,
        ]);
    }


    protected function findModel($id)
    {
        if (($model = DayReportTransportCircle::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
