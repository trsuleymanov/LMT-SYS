<?php

namespace app\modules\admin\controllers;

use Yii;
use app\models\CashbackSetting;
use app\models\CashbackSettingSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CashbackSettingController implements the CRUD actions for CashbackSetting model.
 */
class CashbackSettingController extends Controller
{
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


    public function actionIndex()
    {
        $searchModel = new CashbackSettingSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    public function actionCreate()
    {
        $model = new CashbackSetting();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }else {
            $model->start_date = time();


            $last_cashback_setting = CashbackSetting::find()->orderBy(['id' => SORT_DESC])->one();

            $model->order_accrual_percent = 10;
            $model->red_penalty_max_time = 900;
            $model->yellow_penalty_max_time = 2400;
            $model->order_red_penalty_percent = 10;
            $model->order_yellow_penalty_percent = 5;
            $model->max_time_confirm_delta = 1800;
            $model->max_time_confirm_diff = 3600;


            if($last_cashback_setting != null) {
                $model->order_accrual_percent = $last_cashback_setting->order_accrual_percent;
                $model->red_penalty_max_time = $last_cashback_setting->red_penalty_max_time;
                $model->order_red_penalty_percent = $last_cashback_setting->order_red_penalty_percent;
                $model->yellow_penalty_max_time = $last_cashback_setting->yellow_penalty_max_time;
                $model->order_yellow_penalty_percent = $last_cashback_setting->order_yellow_penalty_percent;
                $model->max_time_confirm_diff = $last_cashback_setting->max_time_confirm_diff;
                $model->max_time_confirm_delta = $last_cashback_setting->max_time_confirm_delta;
                $model->with_commercial_trips = $last_cashback_setting->with_commercial_trips;
            }

            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }


    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }


    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }


    protected function findModel($id)
    {
        if (($model = CashbackSetting::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
