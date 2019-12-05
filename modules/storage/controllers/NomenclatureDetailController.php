<?php

namespace app\modules\storage\controllers;

use app\models\DetailName;
use app\models\NomenclatureDetail;
use app\models\StorageDetail;
use Yii;
use yii\base\ErrorException;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use app\models\StorageOperationSearch;
use yii\web\ForbiddenHttpException;


class NomenclatureDetailController extends Controller
{
    public function actionAjaxGetNames()
    {
        Yii::$app->response->format = 'json';

        $search = Yii::$app->getRequest()->post('search');
        //$nomenclature_details_query = NomenclatureDetail::find();
        $detail_name_query = DetailName::find();
        if($search != '') {
            //$nomenclature_details_query->andWhere(['LIKE', 'name', $search]);
            $detail_name_query->andWhere(['LIKE', 'name', $search]);
        }
        $detail_names = $detail_name_query
            ->orderBy(['name' => SORT_ASC])
            ->all();

        $aDetailNames = ArrayHelper::index($detail_names, 'name');

        $out['results'] = [];
        foreach($aDetailNames as $detail_name) {
            $out['results'][] = [
                'id' => $detail_name->name,
                'text' => $detail_name->name,
            ];
        }

        return $out;
    }


    public function actionAjaxCreate()
    {
        Yii::$app->response->format = 'json';

        $name = Yii::$app->getRequest()->post('name');
        $nomenclature_detail = new NomenclatureDetail();
        $nomenclature_detail->name = $name;
        if(!$nomenclature_detail->save()) {
            throw new ForbiddenHttpException('Деталь в номенклатуре не удалось создать');
        }

        return [
            'success' => true,
            'nomenclature_detail' => $nomenclature_detail
        ];
    }

    public function actionAjaxGetDetailMeasurementValue() {

        Yii::$app->response->format = 'json';

        $detail_name_value = Yii::$app->request->post('detail_name');
        if(!empty($detail_name_value)) {
            //$nomenclature_detail = NomenclatureDetail::find()->where(['name' => $nomenclature_detail_name])->one();
            $detail_name = DetailName::find()->where(['name' => $detail_name_value])->one();

            if($detail_name == null) {
                return null;
            }else {
                $nomenclature_detail = NomenclatureDetail::find()->where(['detail_name_id' => $detail_name->id])->one();
                return $nomenclature_detail == null ? '' : $nomenclature_detail->detailMeasurementValue;
            }

        }else {
            $storage_detail_id = intval(Yii::$app->request->post('storage_detail_id'));
            $storage_detail = StorageDetail::find()->where(['id' => $storage_detail_id])->one();
            if($storage_detail == null) {
                throw new ForbiddenHttpException('Деталь не найдена');
            }

            $nomenclature_detail = $storage_detail->nomenclatureDetail;
            if($nomenclature_detail == null) {
                throw new ErrorException('Номенклатура не найдена');
            }

            return $nomenclature_detail->detailMeasurementValue;
        }

    }

}