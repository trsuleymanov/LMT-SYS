<?php

namespace app\modules\storage\controllers;

use app\models\DetailMeasurementValue;
use app\models\NomenclatureDetail;
use Yii;
use yii\web\Controller;
use app\models\StorageOperationSearch;
use yii\web\ForbiddenHttpException;


class DetailMeasurementValueController extends Controller
{

    public function actionAjaxGetNames() {

        Yii::$app->response->format = 'json';

        $search = Yii::$app->getRequest()->post('search');
        $measurement_values_query = DetailMeasurementValue::find();
        if($search != '') {
            $measurement_values_query->andWhere(['LIKE', 'name', $search]);
        }
        $measurement_values = $measurement_values_query->orderBy(['name' => SORT_ASC])->all();

        $out['results'] = [];
        foreach($measurement_values as $measurement_value) {
            $out['results'][] = [
                'id' => $measurement_value->name,
                'text' => $measurement_value->name,
                'count_is_double' => $measurement_value->count_is_double
            ];
        }

        return $out;
    }

}