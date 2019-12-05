<?php

namespace app\modules\admin\controllers;

use app\models\Setting;
use ErrorException;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\YandexPoint;
use app\models\YandexPointSearch;


class YandexPointController extends Controller
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


    /*
     * Ajax-создание яндекс-метки остановки (в модальном окне)
     */
    public function actionAjaxCreate($city_id)
    {
        $model = new YandexPoint();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->response->format = 'json';
            return [
                'form_saved' => true,
                'city_id' => $model->city_id
            ];

        }else {

            $model->city_id = $city_id;

            return $this->renderAjax('ajax_form.php', [
                'model' => $model,
            ]);
        }
    }

    /*
     * Ajax-редактирование точки остановки (в модальном окне)
     */
    public function actionAjaxUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->response->format = 'json';
            return [
                'form_saved' => true,
                'city_id' => $model->city_id
            ];
        }else {
            return $this->renderAjax('ajax_form.php', [
                'model' => $model,
            ]);
        }
    }



    protected function findModel($id)
    {
        if (($model = YandexPoint::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }



    public function actionAjaxDelete($id)
    {
        Yii::$app->response->format = 'json';

        return $this->findModel($id)->delete();
    }


    public function actionAjaxRecountTimeTogether() {

        Yii::$app->response->format = 'json';

//        $p_AK = 6;
//        $p_KA = 2;
//        $max_time_short_trip_AK = 40*60;
//        $max_time_short_trip_KA = 40*60;

        $setting = Setting::find()->where(['id' => 1])->one();
        if($setting == null) {
            throw new ErrorException('Настройки не найдены');
        }

        YandexPoint::recountTimeToGetTogether(
            $setting->ya_point_p_AK,
            $setting->ya_point_p_KA,
            $setting->max_time_short_trip_AK,
            $setting->max_time_short_trip_KA
        );


        return [
            'success' => true
        ];
    }
}
