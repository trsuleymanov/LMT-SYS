<?php

namespace app\modules\admin\controllers;

use Yii;
use app\models\Direction;
use app\models\DirectionSearch;
use app\models\Schedule;
use app\models\ScheduleSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Order;
use app\models\ScheduleTrip;
use yii\helpers\ArrayHelper;

/**
 * DirectionController implements the CRUD actions for Direction model.
 */
class DirectionController extends Controller
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
     * Lists all Direction models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DirectionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Direction model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Direction();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['update', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
                'searchScheduleModel' => null,
                'dataScheduleProvider' => null
            ]);
        }
    }

    /**
     * Updates an existing Direction model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $searchScheduleModel = new ScheduleSearch();
        $dataScheduleProvider = $searchScheduleModel->search(Yii::$app->request->queryParams, $model->id);

        // выбор заказа последнего с наибольшей date с сортировкой по date сильно много жрет ресурсов
        // $last_order = Order::find()->where(['direction_id' => $model->id])->orderBy(['date' => SORT_DESC])->one();

        // поэтому выберу заказ с наибольшей date из последних созданных 200 заказов
        $last_orders = Order::find()
            ->where(['direction_id' => $model->id])
            ->orderBy(['id' => SORT_DESC])
            ->limit(1000)
            ->all();
        $last_order = null;
        $last_date = 0;
        if(count($last_orders) > 0) {
            foreach ($last_orders as $order) {
                if($order->date > $last_date) {
                    $last_date = $order->date;
                    $last_order = $order;
                }
            }
        }


        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
                'searchScheduleModel' => $searchScheduleModel,
                'dataScheduleProvider' => $dataScheduleProvider,
                'last_order' => $last_order
            ]);
        }
    }

    /**
     * Deletes an existing Direction model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Direction model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Direction the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Direction::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
