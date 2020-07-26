<?php

namespace app\modules\admin\controllers;

use app\models\Direction;
use Yii;
use app\models\Schedule;
use app\models\ScheduleSearch;
use app\models\ScheduleTrip;
use app\models\ScheduleTripSearch;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Order;

/**
 * ScheduleController implements the CRUD actions for Schedule model.
 */
class ScheduleController extends Controller
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



    public function actionCreate($direction_id)
    {
        $direction = Direction::findOne($direction_id);
        if($direction == null) {
            throw new ForbiddenHttpException('Направление не найдено');
        }

        $model = new Schedule();
        $model->direction_id = $direction->id;

        // выбор заказа последнего с наибольшей date с сортировкой по date сильно много жрет ресурсов
        //$last_order = Order::find()->where(['direction_id' => $model->direction_id])->orderBy(['date' => SORT_DESC])->one();

        // поэтому выберем заказ с наибольшей date из последних созданных 200 заказов
        $last_order = Order::getOrderWithLastDate($model->id);

        $model->start_date = ($last_order != null && $last_order->date > time() ? date('d.m.Y', $last_order->date + 86400) : date('d.m.Y', time() + 86400));

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['/admin/direction/update', 'id' => $model->direction_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
                'searchTripModel' => null,
                'dataTripProvider' => null,
            ]);
        }
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $searchTripModel = new ScheduleTripSearch();
        $dataTripProvider = $searchTripModel->search(Yii::$app->request->queryParams, $model->id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['/admin/direction/update', 'id' => $model->direction_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'searchTripModel' => $searchTripModel,
                'dataTripProvider' => $dataTripProvider,
            ]);
        }
    }


    public function actionView($id)
    {
        $model = $this->findModel($id);

        $searchTripModel = new ScheduleTripSearch();
        $dataTripProvider = $searchTripModel->search(Yii::$app->request->queryParams, $model->id);

        return $this->render('view', [
            'model' => $model,
            'searchTripModel' => $searchTripModel,
            'dataTripProvider' => $dataTripProvider,
        ]);
    }


    public function actionDelete($id)
    {
        $schedule = $this->findModel($id);

        ScheduleTrip::deleteAll(['schedule_id' => $schedule->id]);

        $direction_id = $schedule->direction_id;
        $schedule->delete();

        return $this->redirect(['/admin/direction/update', 'id' => $direction_id]);
    }


    protected function findModel($id)
    {
        if (($model = Schedule::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
