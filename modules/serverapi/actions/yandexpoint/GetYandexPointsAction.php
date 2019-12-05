<?php

namespace app\modules\serverapi\actions\yandexpoint;

use app\models\City;
use app\models\YandexPoint;
use Yii;
use yii\helpers\ArrayHelper;


class GetYandexPointsAction extends \yii\rest\Action
{
    public $modelClass = '';

    /**
     * Возвращается список не синхронизированных (новых или измененных) яндекс-точек
     *
     * запрос с кодом доступа:
     * curl -i -H "Authorization: SecretKey lsaeu5jERTffd_7" "Accept:application/json" -H "Content-Type:application/json" -XPOST "http://tobus-yii2.ru/serverapi/yandex-point/get-yandex-points"
     */
    public function run()
    {
        // нужны поля клиента: id, name, mobile_phone, логин - нет такого, пароль - нет такого
        \Yii::$app->response->format = 'json';
        
        $cities = City::find()->all();
        $aCities = ArrayHelper::map($cities, 'id', 'name');

        // нужно возвращать все несинхронизированные точки, а не только у которых стоит галка external_use, потому что
        // точка была предназначена для внешнего использования, а потом галку сняли и нужно получить эти изменения
        $yandex_points = YandexPoint::find()->where(['sync_date' => NULL])->all();

        $aYandexPoints = [];
        foreach($yandex_points as $yandex_point) {
            $aYandexPoints[] = [
                'id' => $yandex_point->id,
                'external_use' => $yandex_point->external_use,
                'name' => $yandex_point->name,
                'city_name' => isset($aCities[$yandex_point->city_id]) ? $aCities[$yandex_point->city_id] : '',
                'lat' => $yandex_point->lat,
                'long' => $yandex_point->long,
                'point_of_arrival' => $yandex_point->point_of_arrival,
                'critical_point' => $yandex_point->critical_point,
                'super_tariff_used' => $yandex_point->super_tariff_used,
                'alias' => $yandex_point->alias,
                'time_to_get_together_short' => $yandex_point->time_to_get_together_short,
                'time_to_get_together_long' => $yandex_point->time_to_get_together_long,
            ];
        }

        return [
            'yandex_points' => $aYandexPoints
        ];
    }
}
