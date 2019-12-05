<?php

namespace app\modules\admin\controllers;

use Yii;
use app\models\ScheduleTrip;
use app\models\ScheduleTripSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Schedule;

/**
 * ScheduleTripController implements the CRUD actions for ScheduleTrip model.
 */
class ScheduleTripController extends Controller
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
     * Lists all ScheduleTrip models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ScheduleTripSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new ScheduleTrip model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($schedule_id)
    {
        $schedule = Schedule::findOne($schedule_id);
        if($schedule == null) {
            throw new ForbiddenHttpException('Расписание не найдено');
        }

        $model = new ScheduleTrip();
        $model->schedule_id = $schedule->id;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['/admin/schedule/update', 'id' => $model->schedule_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing ScheduleTrip model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['/admin/schedule/update', 'id' => $model->schedule_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Finds the ScheduleTrip model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ScheduleTrip the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ScheduleTrip::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Deletes an existing ScheduleTrip model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $schedule_trip = $this->findModel($id);
        $schedule_id = $schedule_trip->schedule_id;
        $schedule_trip->delete();

        return $this->redirect(['/admin/schedule/update', 'id' => $schedule_id]);
    }
}
