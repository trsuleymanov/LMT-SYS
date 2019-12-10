<?php

namespace app\modules\serverapi\actions\city;

use app\models\City;


class GetNotSyncCitiesAction extends \yii\rest\Action
{
    public $modelClass = '';

    /**
     * Возвращаются города, если они не синхронизированны
     *
     */
    public function run()
    {
        \Yii::$app->response->format = 'json';

        $cities = City::find()
            ->where(['sync_date' => NULL])
            ->limit(2)
            ->all();

        $aCities = [];
        if(count($cities) > 0) {
            foreach($cities as $city) {
                $aCities[] = [
                    'id' => $city->id,
                    'name' => $city->name,
                    'extended_external_use' => $city->extended_external_use,
                    'center_lat' => $city->center_lat,
                    'center_long' => $city->center_long,
                    'map_scale' => $city->map_scale,
                    'search_scale' => $city->search_scale,
                    'point_focusing_scale' => $city->point_focusing_scale,
                    'all_points_show_scale' => $city->all_points_show_scale,
                ];
            }
        }

        return $aCities;
    }
}
