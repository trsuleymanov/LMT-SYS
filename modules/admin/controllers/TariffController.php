<?php

namespace app\modules\admin\controllers;

use app\models\Order;
use Yii;
use app\models\Tariff;
use app\models\TariffSearch;
use yii\base\ErrorException;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TariffController implements the CRUD actions for Tariff model.
 */
class TariffController extends Controller
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
     * Lists all Tariff models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TariffSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * Creates a new Tariff model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Tariff();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {

            $model->start_date = time();
            $last_tariff = Tariff::find()->orderBy(['id' => SORT_DESC])->one();
            if($last_tariff != null) {
//                $model->common_price = $last_tariff->common_price;
//                $model->student_price = $last_tariff->student_price;
//                $model->baby_price = $last_tariff->baby_price;
//                $model->aero_price = $last_tariff->aero_price;
//                $model->parcel_price = $last_tariff->parcel_price;
//                $model->loyal_price = $last_tariff->loyal_price;

                $model->unprepayment_common_price = $last_tariff->unprepayment_common_price;
                $model->unprepayment_student_price = $last_tariff->unprepayment_student_price;
                $model->unprepayment_baby_price = $last_tariff->unprepayment_baby_price;
                $model->unprepayment_aero_price = $last_tariff->unprepayment_aero_price;
                $model->unprepayment_parcel_price = $last_tariff->unprepayment_parcel_price;
                $model->unprepayment_loyal_price = $last_tariff->unprepayment_loyal_price;
                $model->unprepayment_reservation_cost = $last_tariff->unprepayment_reservation_cost;

                $model->prepayment_common_price = $last_tariff->prepayment_common_price;
                $model->prepayment_student_price = $last_tariff->prepayment_student_price;
                $model->prepayment_baby_price = $last_tariff->prepayment_baby_price;
                $model->prepayment_aero_price = $last_tariff->prepayment_aero_price;
                $model->prepayment_parcel_price = $last_tariff->prepayment_parcel_price;
                $model->prepayment_loyal_price = $last_tariff->prepayment_loyal_price;
                $model->prepayment_reservation_cost = $last_tariff->prepayment_reservation_cost;

                $model->superprepayment_common_price = $last_tariff->superprepayment_common_price;
                $model->superprepayment_student_price = $last_tariff->superprepayment_student_price;
                $model->superprepayment_baby_price = $last_tariff->superprepayment_baby_price;
                $model->superprepayment_aero_price = $last_tariff->superprepayment_aero_price;
                $model->superprepayment_parcel_price = $last_tariff->superprepayment_parcel_price;
                $model->superprepayment_loyal_price = $last_tariff->superprepayment_loyal_price;
                $model->superprepayment_reservation_cost = $last_tariff->superprepayment_reservation_cost;
            }
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Tariff model.
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
     * Finds the Tariff model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Tariff the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Tariff::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /*
     * Пересчет цен будущих заказов связанных с тарифом
     */

    /**
     * Deletes an existing Tariff model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionAjaxUpdateOrdersPrice($tariff_id)
    {
        Yii::$app->response->format = 'json';

        $tariff = Tariff::findOne($tariff_id);
        if($tariff == null) {
            throw new ForbiddenHttpException('Не найден тариф');
        }

        $orders = $tariff->futureOrders;
        if(count($orders) > 0) {
            foreach($orders as $order) {
                $price = $order->getCalculatePrice();
                $used_cash_back = $order->getCalculateUsedCashBack();
                $prize_trip_count = $order->prizeTripCount;

                $command = Yii::$app->db->createCommand('UPDATE `'.Order::tableName().'` SET price="'.$price.'", used_cash_back="'.$used_cash_back.'", prize_trip_count="'.$prize_trip_count.'", sync_date=NULL WHERE id='.$order->id);
	            $command->execute();
            }
        }else {
            throw new ForbiddenHttpException('Не найдены заказы для пересчета цены');
        }

        return [
            'success' => true,
            'orders' => $orders
        ];
    }
}
