<?php

namespace app\modules\admin\controllers;

use app\models\City;
use app\models\OrderSearch;
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

//        $setting = Setting::find()->where(['id' => 1])->one();
//        if($setting == null) {
//            throw new ErrorException('Настройки не найдены');
//        }

        YandexPoint::recountTimeToGetTogether(
            Yii::$app->setting->ya_point_p_AK,
            Yii::$app->setting->ya_point_p_KA,
            Yii::$app->setting->max_time_short_trip_AK,
            Yii::$app->setting->max_time_short_trip_KA
        );


        return [
            'success' => true
        ];
    }

    public function actionStatistics()
    {
        $params = Yii::$app->request->queryParams;

        $cities = City::find()->all();
        $aCities = [];
        if(count($cities) > 0) {
            foreach ($cities as $city) {
                $aCities[$city->id] = $city;
            }
        }

        $searchModel = new YandexPointSearch();
        $dataProvider = $searchModel->searchStatistic($params);


        $search_points = $dataProvider->getModels();
        $aPointsIds = [];
        if(count($search_points) > 0) {
            foreach ($search_points as $search_point) {
                $aPointsIds[$search_point->id] = $search_point->id;
            }
        }


        $trip_date_start = 0;
        $trip_date_end = 0;
        if(isset($params['YandexPointSearch']['trip_date']) && !empty($params['YandexPointSearch']['trip_date'])) {
            list($trip_date_start, $trip_date_end) = explode('-', $params['YandexPointSearch']['trip_date']);
            $trip_date_start = strtotime($trip_date_start);
            $trip_date_end = strtotime($trip_date_end) + 3600 * 24 - 1;
        }


        // ищем всего сколько заказов для каждой яндекс-точки
//        $aTotalCounts = [];
//        $aTotalPlacesCount = [];
//        $sql = 'SELECT ya.id, SUM(`order`.`places_count`) as places_count, count(*) as counter
//                FROM `order`
//                LEFT JOIN yandex_point ya ON ya.id = `order`.yandex_point_from_id
//                LEFT JOIN trip ON trip.id = `order`.trip_id
//                WHERE `order`.yandex_point_from_id > 0
//                '.( $trip_date_start > 0 && $trip_date_end > 0 ? ' AND (trip.date > '.$trip_date_start.' AND trip.date <= '.$trip_date_end.')' : '' ).'
//                GROUP BY ya.id'; // ORDER BY counter DESC
//        $total_counts = Yii::$app->db->createCommand($sql)->queryAll();
//        if(count($total_counts) > 0) {
//            foreach ($total_counts as $total_count) {
//                $aTotalCounts[$total_count['id']] = $total_count['counter'];
//                $aTotalPlacesCount[$total_count['id']] = $total_count['places_count'];
//            }
//        }

        // отправленных заказов количество для каждой точки
        $aSendedCounts = [];
        $aSendedPlacesCount = [];
        $sql = 'SELECT ya.id, SUM(`order`.`places_count`) as places_count, count(*) as counter 
                FROM `order`
                LEFT JOIN yandex_point ya ON ya.id = `order`.yandex_point_from_id
                LEFT JOIN trip ON trip.id = `order`.trip_id
                WHERE `order`.yandex_point_from_id > 0
                    AND `order`.status_id = 3 
                    AND `order`.`places_count` > 0
                '.( $trip_date_start > 0 && $trip_date_end > 0 ? ' AND (trip.date >= '.$trip_date_start.' AND trip.date <= '.$trip_date_end.')' : '' ).'
                GROUP BY ya.id';
        $sended_counts = Yii::$app->db->createCommand($sql)->queryAll();
        if(count($sended_counts) > 0) {
            foreach ($sended_counts as $sended_count) {
                $aSendedCounts[$sended_count['id']] = $sended_count['counter'];
                $aSendedPlacesCount[$sended_count['id']] = $sended_count['places_count'];
            }
        }


        // заказов отменено
        $aCanceledCounts = [];
        $aCanceledPlacesCount = [];
        $sql = 'SELECT ya.id, SUM(`order`.`places_count`) as places_count, count(*) as counter 
                FROM `order`
                LEFT JOIN yandex_point ya ON ya.id = `order`.yandex_point_from_id
                LEFT JOIN trip ON trip.id = `order`.trip_id
                WHERE `order`.yandex_point_from_id > 0
                    AND `order`.status_id = 2 
                    AND `order`.`places_count` > 0
                '.( $trip_date_start > 0 && $trip_date_end > 0 ? ' AND (trip.date >= '.$trip_date_start.' AND trip.date <= '.$trip_date_end.')' : '' ).'
                GROUP BY ya.id';
        $canceled_counts = Yii::$app->db->createCommand($sql)->queryAll();
        if(count($canceled_counts) > 0) {
            foreach ($canceled_counts as $canceled_count) {
                $aCanceledCounts[$canceled_count['id']] = $canceled_count['counter'];
                $aCanceledPlacesCount[$canceled_count['id']] = $canceled_count['places_count'];
            }
        }


        // отправленных заказов с детьми количество для каждой точки
        $aChildrenSendedCounts = [];
        $aChildrenSendedPlacesCount = [];
        $sql = 'SELECT ya.id, SUM(`order`.`places_count`) as places_count, count(*) as counter 
                FROM `order`
                LEFT JOIN yandex_point ya ON ya.id = `order`.yandex_point_from_id
                LEFT JOIN trip ON trip.id = `order`.trip_id
                WHERE `order`.yandex_point_from_id > 0
                    AND `order`.status_id = 3 
                    AND `order`.`child_count` > 0
                '.( $trip_date_start > 0 && $trip_date_end > 0 ? ' AND (trip.date >= '.$trip_date_start.' AND trip.date <= '.$trip_date_end.')' : '' ).'
                GROUP BY ya.id';
        $sended_counts = Yii::$app->db->createCommand($sql)->queryAll();
        if(count($sended_counts) > 0) {
            foreach ($sended_counts as $sended_count) {
                $aChildrenSendedCounts[$sended_count['id']] = $sended_count['counter'];
                $aChildrenSendedPlacesCount[$sended_count['id']] = $sended_count['places_count'];
            }
        }


        // заказов с детьми отменено
        $aChildrenCanceledCounts = [];
        $aChildrenCanceledPlacesCount = [];
        $sql = 'SELECT ya.id, SUM(`order`.`places_count`) as places_count, count(*) as counter 
                FROM `order`
                LEFT JOIN yandex_point ya ON ya.id = `order`.yandex_point_from_id
                LEFT JOIN trip ON trip.id = `order`.trip_id
                WHERE `order`.yandex_point_from_id > 0
                    AND `order`.status_id = 2 
                    AND `order`.`child_count` > 0
                '.( $trip_date_start > 0 && $trip_date_end > 0 ? ' AND (trip.date >= '.$trip_date_start.' AND trip.date <= '.$trip_date_end.')' : '' ).'
                GROUP BY ya.id';
        $canceled_counts = Yii::$app->db->createCommand($sql)->queryAll();
        if(count($canceled_counts) > 0) {
            foreach ($canceled_counts as $canceled_count) {
                $aChildrenCanceledCounts[$canceled_count['id']] = $canceled_count['counter'];
                $aChildrenCanceledPlacesCount[$canceled_count['id']] = $canceled_count['places_count'];
            }
        }


        // отправленных заказов со студентами количество для каждой точки
        $aStudentsSendedCounts = [];
        $aStudentsSendedPlacesCount = [];
        $sql = 'SELECT ya.id, SUM(`order`.`places_count`) as places_count, count(*) as counter 
                FROM `order`
                LEFT JOIN yandex_point ya ON ya.id = `order`.yandex_point_from_id
                LEFT JOIN trip ON trip.id = `order`.trip_id
                WHERE `order`.yandex_point_from_id > 0
                    AND `order`.status_id = 3 
                    AND `order`.`student_count` > 0
                '.( $trip_date_start > 0 && $trip_date_end > 0 ? ' AND (trip.date >= '.$trip_date_start.' AND trip.date <= '.$trip_date_end.')' : '' ).'
                GROUP BY ya.id';
        $sended_counts = Yii::$app->db->createCommand($sql)->queryAll();
        if(count($sended_counts) > 0) {
            foreach ($sended_counts as $sended_count) {
                $aStudentsSendedCounts[$sended_count['id']] = $sended_count['counter'];
                $aStudentsSendedPlacesCount[$sended_count['id']] = $sended_count['places_count'];
            }
        }


        // заказов со студентами отменено
        $aStudentsCanceledCounts = [];
        $aStudentsCanceledPlacesCount = [];
        $sql = 'SELECT ya.id, SUM(`order`.`places_count`) as places_count, count(*) as counter 
                FROM `order`
                LEFT JOIN yandex_point ya ON ya.id = `order`.yandex_point_from_id
                LEFT JOIN trip ON trip.id = `order`.trip_id
                WHERE `order`.yandex_point_from_id > 0
                    AND `order`.status_id = 2 
                    AND `order`.`student_count` > 0
                '.( $trip_date_start > 0 && $trip_date_end > 0 ? ' AND (trip.date >= '.$trip_date_start.' AND trip.date <= '.$trip_date_end.')' : '' ).'
                GROUP BY ya.id';
        $canceled_counts = Yii::$app->db->createCommand($sql)->queryAll();
        if(count($canceled_counts) > 0) {
            foreach ($canceled_counts as $canceled_count) {
                $aStudentsCanceledCounts[$canceled_count['id']] = $canceled_count['counter'];
                $aStudentsCanceledPlacesCount[$canceled_count['id']] = $canceled_count['places_count'];
            }
        }




        return $this->render('statistics', [

            'params' => $params,

            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,

            'cities' => $cities,
            'aCities' => $aCities,

//            'aTotalCounts' => $aTotalCounts,
//            'aTotalPlacesCount' => $aTotalPlacesCount,

            'aSendedCounts' => $aSendedCounts,
            'aSendedPlacesCount' => $aSendedPlacesCount,
            'aCanceledCounts' => $aCanceledCounts,
            'aCanceledPlacesCount' => $aCanceledPlacesCount,

            'aChildrenSendedCounts' => $aChildrenSendedCounts,
            'aChildrenSendedPlacesCount' => $aChildrenSendedPlacesCount,
            'aChildrenCanceledCounts' => $aChildrenCanceledCounts,
            'aChildrenCanceledPlacesCount' => $aChildrenCanceledPlacesCount,

            'aStudentsSendedCounts' => $aStudentsSendedCounts,
            'aStudentsSendedPlacesCount' => $aStudentsSendedPlacesCount,
            'aStudentsCanceledCounts' => $aStudentsCanceledCounts,
            'aStudentsCanceledPlacesCount' => $aStudentsCanceledPlacesCount,
        ]);
    }
}
