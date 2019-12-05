<?php

namespace app\modules\admin\controllers;

use Yii;
use app\models\InformerOffice;
use app\models\InformerOfficeSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\DayReportTripTransport;
use app\models\DayReportTripTransportSearch;
use app\models\OrderReport;
use app\models\OrderReportSearch;


/**
 * InformerOfficeController implements the CRUD actions for InformerOffice model.
 */
class InformerOfficeController extends Controller
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
     * Lists all InformerOffice models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new InformerOfficeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    public function actionReport()
    {
        $orderReportSearchModel = new OrderReportSearch();
        $orderReportDataProvider = $orderReportSearchModel->searchInformerOffice(Yii::$app->request->queryParams);

        return $this->render('report', [
            'searchModel' => $orderReportSearchModel,
            'dataProvider' => $orderReportDataProvider,
        ]);
    }

    /**
     * Creates a new InformerOffice model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new InformerOffice();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing InformerOffice model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Finds the InformerOffice model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return InformerOffice the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = InformerOffice::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Deletes an existing InformerOffice model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }
}
