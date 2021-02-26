<?php

namespace app\controllers;

use Yii;
use yii\base\ErrorException;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Point;
use app\models\PointSearch;
use app\models\Direction;
use app\models\YandexPoint;
use app\models\City;
use app\models\Street;


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
     * Функция возвращает результат поиска яндекс точек для SelectWidget`а или скажем для kartik-элемента формы
     */
    public function actionAjaxYandexPoints($is_from)
    {
        Yii::$app->response->format = 'json';

        $out['results'] = [];

        $search = Yii::$app->getRequest()->post('search');
        $direction_id = intval(Yii::$app->getRequest()->post('direction_id'));

        $direction = Direction::findOne($direction_id);
        if($direction == null) {
            throw new ForbiddenHttpException('Необходимо выбрать направление');
        }

        if($is_from == 1) {
            $yandex_points_query = YandexPoint::find()->where(['city_id' => $direction->city_from]);
        }else {
            $yandex_points_query = YandexPoint::find()->where(['city_id' => $direction->city_to])->andWhere(['point_of_arrival' => true]);
        }

        $yandex_points_query->andWhere(['active' => 1]);

        if($search != '') {
            $yandex_points_query->andWhere(['LIKE', 'name', $search]);
        }

        $yandex_points = $yandex_points_query->orderBy(['name' => SORT_ASC])->all();

        $out['results'] = [];
        foreach($yandex_points as $yandex_point) {
            $out['results'][] = [
                'id' => $yandex_point->id.'_'.$yandex_point->lat.'_'.$yandex_point->long.'_'.$yandex_point->name,
                'text' => $yandex_point->name,
            ];
        }

        return $out;
    }


    /*
     * Функция возвращает данные точки
     */
    public function actionAjaxGetYandexPoint($id)
    {
        Yii::$app->response->format = 'json';

        $yandex_point = YandexPoint::find()->where(['id' => $id])->one();
        if($yandex_point == null) {
            throw new ForbiddenHttpException('Точка не найдена');
        }

        return $yandex_point;
    }


    /*
     * Создание яндекс-точки
     */
    public function actionAjaxCreateYandexPoint($city_id, $name, $lat, $long) {

        Yii::$app->response->format = 'json';

        $city = City::find()->where(['id' => $city_id])->one();
        if($city == null) {
            throw new ForbiddenHttpException('Город не найден');
        }

        $yandex_point = new YandexPoint();
        $yandex_point->city_id = $city->id;
        $yandex_point->name = $name;
        $yandex_point->lat = $lat;
        $yandex_point->long = $long;
        $yandex_point->external_use = Yii::$app->getRequest()->post('external_use', 1);
        $yandex_point->critical_point = Yii::$app->getRequest()->post('critical_point', 0);
        $yandex_point->super_tariff_used = Yii::$app->getRequest()->post('super_tariff_used', 0);
        $yandex_point->point_of_arrival = Yii::$app->getRequest()->post('point_of_arrival', 0);
        $yandex_point->alias = Yii::$app->getRequest()->post('alias', '');
        $yandex_point->point_from_standart_price_diff = Yii::$app->getRequest()->post('point_from_standart_price_diff', 0);
        $yandex_point->point_from_commercial_price_diff = Yii::$app->getRequest()->post('point_from_commercial_price_diff', 0);
        $yandex_point->point_to_standart_price_diff = Yii::$app->getRequest()->post('point_to_standart_price_diff', 0);
        $yandex_point->point_to_commercial_price_diff = Yii::$app->getRequest()->post('point_to_commercial_price_diff', 0);

        if(!$yandex_point->save()) {
            return [
                'success' => false,
                'errors' => $yandex_point->getErrors()
            ];
        }else {
            return [
                'success' => true,
                'yandex_point' => $yandex_point
            ];
        }
    }

    /*
     * Обновление яндекс-точки
     */
    public function actionAjaxUpdateYandexPoint($id) {

        Yii::$app->response->format = 'json';

        $yandex_point = YandexPoint::find()->where(['id' => $id])->one();
        if($yandex_point == null) {
            throw new ForbiddenHttpException('Не найдена точка для сохранения');
        }

        $yandex_point->name = Yii::$app->getRequest()->post('name', $yandex_point->name);
        $yandex_point->description = Yii::$app->getRequest()->post('description', $yandex_point->description);
        $yandex_point->lat = Yii::$app->getRequest()->post('lat', $yandex_point->lat);
        $yandex_point->long = Yii::$app->getRequest()->post('long', $yandex_point->long);
        $yandex_point->external_use = Yii::$app->getRequest()->post('external_use', $yandex_point->external_use);
        $yandex_point->point_of_arrival = Yii::$app->getRequest()->post('point_of_arrival', $yandex_point->point_of_arrival);
        $yandex_point->super_tariff_used = Yii::$app->getRequest()->post('super_tariff_used', 0);
        $yandex_point->critical_point = Yii::$app->getRequest()->post('critical_point', $yandex_point->critical_point);
        $yandex_point->popular_departure_point = Yii::$app->getRequest()->post('popular_departure_point', $yandex_point->popular_departure_point);
        $yandex_point->popular_arrival_point = Yii::$app->getRequest()->post('popular_arrival_point', $yandex_point->popular_arrival_point);
        $yandex_point->alias = Yii::$app->getRequest()->post('alias', $yandex_point->alias);
        $yandex_point->point_from_standart_price_diff = Yii::$app->getRequest()->post('point_from_standart_price_diff', $yandex_point->point_from_standart_price_diff);
        $yandex_point->point_from_commercial_price_diff = Yii::$app->getRequest()->post('point_from_commercial_price_diff', $yandex_point->point_from_commercial_price_diff);
        $yandex_point->point_to_standart_price_diff = Yii::$app->getRequest()->post('point_to_standart_price_diff', $yandex_point->point_to_standart_price_diff);
        $yandex_point->point_to_commercial_price_diff = Yii::$app->getRequest()->post('point_to_commercial_price_diff', $yandex_point->point_to_commercial_price_diff);

        if(!$yandex_point->save()) {
            return [
                'success' => false,
                'errors' => $yandex_point->getErrors()
            ];
        }else {
            return [
                'success' => true,
                'yandex_point' => $yandex_point
            ];
        }

    }


    public function actionAjaxGetYandexPointDefaults($direction_id) {

        Yii::$app->response->format = 'json';

        $yandex_point_from = null;
        $yandex_point_to = null;
        if($direction_id == 2) { // КА
            $yandex_point_to = YandexPoint::find()->where(['id' => 13])->one();
            if($yandex_point_to == null) {
                throw new ForbiddenHttpException('Яндекс-точка по умолчанию не найдена ("АВ - Автовокзал Альм.")');
            }
        }

        return [
            'yandex_point_to' => $yandex_point_to,
            'yandex_point_from' => $yandex_point_from,
        ];
    }

    public function actionTest() {

        //$point1 = YandexPoint::find()->where(['id' => 152])->one();// Р-239
        //$point1 = YandexPoint::find()->where(['id' => 164])->one();// Тестовая точка 2
        //$point2 = YandexPoint::find()->where(['id' => 163])->one();// Тестовая точка

//        $point1 = YandexPoint::find()->where(['id' => 166])->one();// микрорайон Привокзальный
//        $point2 = YandexPoint::find()->where(['id' => 167])->one();// Улица Шибанкова - внизу

        //$point1 = YandexPoint::find()->where(['id' => 168])->one();//улица Шибанкова, 9-я школа
        //$point2 = YandexPoint::find()->where(['id' => 169])->one(); // центр Дивизии
        $point2 = YandexPoint::find()->where(['id' => 167])->one(); // Улица Шибанкова - внизу
        $point1 = YandexPoint::find()->where(['id' => 170])->one();

        //$point1 = YandexPoint::find()->where(['id' => 116])->one();// улица Найдова-Железова - дорога
        //$point2 = YandexPoint::find()->where(['id' => 165])->one();// улица Найдова-Железова, 1

        echo "lat1=".$point1->lat.' long1='.$point1->long."<br />";
        echo "lat2=".$point2->lat.' long2='.$point2->long."<br />";

        $distance = YandexPoint::getDistance($point1->lat, $point1->long, $point2->lat, $point2->long);

        echo "distance=$distance <br />";
    }
}
