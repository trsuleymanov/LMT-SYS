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
            if($last_cashback_setting != null) {
                $model->order_accrual_percent = $last_cashback_setting->order_accrual_percent;
                $model->order_penalty_percent = $last_cashback_setting->order_penalty_percent;
                $model->hours_before_start_trip_for_penalty = $last_cashback_setting->hours_before_start_trip_for_penalty;
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
