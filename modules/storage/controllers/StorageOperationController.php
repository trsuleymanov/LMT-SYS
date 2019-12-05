<?php

namespace app\modules\storage\controllers;


use app\models\DetailMeasurementValue;
use app\models\DetailName;
use app\models\NomenclatureDetail;
use app\models\StorageDetail;
use app\models\StorageOperation;
use app\models\TransportDetailOrigin;
use app\models\TransportDetailState;
use app\models\TransportModel;
use Yii;
use yii\base\ErrorException;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;


class StorageOperationController extends Controller
{

    public function actionAjaxGetOperationForm()
    {
        Yii::$app->response->format = 'json';

        $type = Yii::$app->request->post('type');
        $operation_id = intval(Yii::$app->request->post('operation_id'));

        if($operation_id > 0) {

            $storage_operation = StorageOperation::find()->where(['id' => $operation_id])->one();
            if($storage_operation == null) {
                throw new ForbiddenHttpException('Операция не найдена');
            }
            $storage_operation_type = $storage_operation->storageOperationType;
            if($storage_operation_type == null) {
                throw new ForbiddenHttpException('Тип операции не найден');
            }
            if($storage_operation_type->operation_type == 1) { // income

                $storage_detail = $storage_operation->storageDetail;
                $nomenclature_detail = $storage_detail->nomenclatureDetail;
                $nomenclature_detail->temp_name = $nomenclature_detail->detailName != null ? $nomenclature_detail->detailName->name : '';
                $detail_measurement_value = $nomenclature_detail->detailMeasurementValue;

                return [
                    'success' => true,
                    'html' => $this->renderAjax('create-income-form.php', [
                        'storage_detail' => $storage_detail,
                        'nomenclature_detail' => $nomenclature_detail,
                        'storage_operation' => $storage_operation,
                        'detail_measurement_value' => $detail_measurement_value
                    ]),
                    'operation_type' => $storage_operation_type->operation_type
                ];

            }else {

                $storage_operation->scenario = 'create_operation_expenditure';

                $storage_detail = $storage_operation->storageDetail;
                if($storage_detail == null) {
                    throw new ErrorException('Деталь на складе не найдена');
                }
                return [
                    'success' => true,
                    'html' => $this->renderAjax('create-expenditure-form.php', [
                        'storage_operation' => $storage_operation,
                        'selected_storage_id' => $storage_detail->storage_id
                    ]),
                    'operation_type' => $storage_operation_type->operation_type
                ];
            }

        }elseif($type == 'income') {

            $nomenclature_detail = new NomenclatureDetail();
            $storage_detail = new StorageDetail();
            $storage_operation = new StorageOperation();
            $detail_measurement_value = new DetailMeasurementValue();

            $storage_operation->date = strtotime(date('d.m.Y'));

            return [
                'success' => true,
                'html' => $this->renderAjax('create-income-form.php', [
                    'storage_detail' => $storage_detail,
                    'nomenclature_detail' => $nomenclature_detail,
                    'storage_operation' => $storage_operation,
                    'detail_measurement_value' => $detail_measurement_value
                ]),
                'operation_type' => 1
            ];


        }elseif($type == 'expenditure') {

            $storage_operation = new StorageOperation();
            $storage_operation->scenario = 'create_operation_expenditure';

            $storage_operation->date = strtotime(date('d.m.Y'));

            return [
                'success' => true,
                'html' => $this->renderAjax('create-expenditure-form.php', [
                    'storage_operation' => $storage_operation,
                    'selected_storage_id' => 0
                ]),
                'operation_type' => 0
            ];
        }
    }


    public function actionAjaxCreateOperation()
    {
        Yii::$app->response->format = 'json';

        $type = Yii::$app->request->post('type');
        $post = Yii::$app->request->post();


        if($type == 'income') {

            $detail_measurement_value = StorageOperation::_getDetailMeasurementValueFromPost($post);

            $detail_name = DetailName::find()->where(['name' => $post['NomenclatureDetail']['temp_name']])->one();
            if($detail_name == null) {
                $detail_name = new DetailName();
                $detail_name->name = $post['NomenclatureDetail']['temp_name'];

                //$detail_name->name = mb_strtolower(trim($detail_name->name), 'UTF-8');
                $detail_name->name = trim($detail_name->name);
                $detail_name->name = mb_strtoupper(mb_substr($detail_name->name, 0, 1, 'UTF-8'), 'UTF-8') . mb_substr($detail_name->name, 1, NULL, 'UTF-8');
            }
            $nomenclature_detail = StorageOperation::_getNomenclatureDetailFromPost($post, $detail_measurement_value);

            // этот объект $storage_detail - только для проверки валидности
            $storage_detail = new StorageDetail();
            $storage_detail->scenario = 'create_operation_income';
            $storage_operation = new StorageOperation();
            $storage_operation->scenario = 'create_operation_income';
            if(
                $detail_measurement_value->load($post)
                && $nomenclature_detail->load($post)
                && $storage_detail->load($post)
                && $storage_operation->load($post)

                && $detail_measurement_value->validate()
                && $detail_name->validate()
                && $nomenclature_detail->validate()
                && $storage_detail->validate()
                && $storage_operation->validate()

                && $detail_name->save()
            ) {


                // сохранение новых единицы измерения и номенклатуры
                if(empty($detail_measurement_value->id)) {
                    if(!$detail_measurement_value->save()) {
                        throw new ErrorException('Не удалось создать новую единицу измерения');
                    }
                }
                $nomenclature_detail->detail_name_id = $detail_name->id;
                $nomenclature_detail->measurement_value_id = $detail_measurement_value->id;
                if(empty($nomenclature_detail->id)) {
                    $nomenclature_detail->name = mb_strtolower(trim($nomenclature_detail->name), 'UTF-8');
                    $nomenclature_detail->name = mb_strtoupper(mb_substr($nomenclature_detail->name, 0, 1, 'UTF-8'), 'UTF-8').mb_substr($nomenclature_detail->name, 1, NULL, 'UTF-8');
                    if(!$nomenclature_detail->save()) {
                        throw new ErrorException('Не удалось создать запись в номенклатуре');
                    }
                }



                // поиск старой или создание новой детали на складе и сохранение
                $storage_detail = StorageOperation::_getStorageDetailFromPost($post, $nomenclature_detail);
                $storage_detail->load(Yii::$app->request->post());
                if(empty($storage_detail->id)) { // заполняем новую деталь
                    $storage_detail->nomenclature_detail_id = $nomenclature_detail->id;
                    $storage_detail->remainder = $storage_operation->count;
                }else { // изменяем старую деталь
                    $storage_detail->remainder = $storage_detail->remainder + $storage_operation->count;
                }
                if(!$storage_detail->save()) {
                    throw new ErrorException('Не удалось сохранить деталь на складе');
                }

                // сохранение операции
                $storage_operation->storage_detail_id = $storage_detail->id;
                if(empty($storage_operation->date)) {
                    $storage_operation->date = time();
                }
                if(!$storage_operation->save()) {
                    throw new ErrorException('Не удалось сохранить операцию прихода');
                }

                return [
                    'success' => true,
                ];

            }else {
                return [
                    'success' => false,
                    'nomenclature_detail_errors' => $nomenclature_detail->validate() ? '' : $nomenclature_detail->getErrors(),
                    'storage_detail_errors' => $storage_detail->validate() ? '' : $storage_detail->getErrors(),
                    'storage_operation_errors' => $storage_operation->validate() ? '' : $storage_operation->getErrors(),
                ];
            }


        } elseif($type == 'expenditure') {

            $storage_operation = new StorageOperation();
            $storage_operation->scenario = 'create_operation_expenditure';

            if ($storage_operation->load(Yii::$app->request->post()) && $storage_operation->save()) {

                $storage_detail = $storage_operation->storageDetail;
                $storage_detail->remainder = $storage_detail->remainder - $storage_operation->count;
                if(!$storage_detail->save()) {
                    throw new ForbiddenHttpException('Не удалось сохранить изменения на складе');
                }

                return [
                    'success' => true
                ];
            }else {
                return [
                    'success' => false,
                    'storage_operation_errors' => $storage_operation->validate() ? '' : $storage_operation->getErrors(),
                ];
            }
        }
    }


    public function actionAjaxUpdateOperation($id) {

        Yii::$app->response->format = 'json';

        $post = Yii::$app->request->post();
        // echo "<pre>"; print_r($post); echo "</pre>";

        $storage_operation = StorageOperation::find()->where(['id' => $id])->one();
        if($storage_operation == null) {
            throw new ForbiddenHttpException('Операция не найдена');
        }
        $storage_operation_type = $storage_operation->storageOperationType;
        if($storage_operation_type == null) {
            throw new ForbiddenHttpException('Тип операции не найден');
        }
        if($storage_operation_type->operation_type == 1) { // income

            $storage_operation->scenario = 'update_operation_income';

//            $storage_detail = $storage_operation->storageDetail;
//            $nomenclature_detail = $storage_detail->nomenclatureDetail;
//            $detail_measurement_value = $nomenclature_detail->detailMeasurementValue;


            // есть операция, она может изменить:
            //   - количество деталей -> пересчет внутри операции и пересчет остатков старой детали на складе
            //   - водителя, машину, наличие водителя-машины -> пересчет внутри операции
            //   - id детали на складе:
            //      - ищеться деталь по множеству параметров, либо создается новая

            // находим старые модели или создаем новые (пока без сохранения)
            $detail_measurement_value = StorageOperation::_getDetailMeasurementValueFromPost($post);
            $nomenclature_detail = StorageOperation::_getNomenclatureDetailFromPost($post, $detail_measurement_value);

            // этот объект $storage_detail - только для проверки валидности
            $storage_detail = new StorageDetail();
            $storage_detail->scenario = 'create_operation_income';
//            $storage_operation = new StorageOperation();
//            $storage_operation->scenario = 'create_operation_income';
            $oldAttributes = $storage_operation->oldAttributes;
            if(
                $detail_measurement_value->load($post)
                && $nomenclature_detail->load($post)
                && $storage_detail->load($post)
                && $storage_operation->load($post)

                && $detail_measurement_value->validate()
                && $nomenclature_detail->validate()
                && $storage_detail->validate()
                && $storage_operation->validate()
            ) {
                // откат операции по старым данным
                $storage_operation->rejectOperation($oldAttributes);

                // сохранение новых единицы измерения и номенклатуры
                if(empty($detail_measurement_value->id)) {
                    if(!$detail_measurement_value->save()) {
                        throw new ErrorException('Не удалось создать новую единицу измерения');
                    }
                }
                $nomenclature_detail->measurement_value_id = $detail_measurement_value->id;
                if(empty($nomenclature_detail->id)) {
                    $nomenclature_detail->name = mb_strtolower(trim($nomenclature_detail->name), 'UTF-8');
                    $nomenclature_detail->name = mb_strtoupper(mb_substr($nomenclature_detail->name, 0, 1, 'UTF-8'), 'UTF-8').mb_substr($nomenclature_detail->name, 1, NULL, 'UTF-8');
                    if(!$nomenclature_detail->save()) {
                        throw new ErrorException('Не удалось создать запись в номенклатуре');
                    }
                }


                // поиск старой или создание новой детали на складе и сохранение
                $storage_detail = StorageOperation::_getStorageDetailFromPost($post, $nomenclature_detail);
                $storage_detail->load(Yii::$app->request->post());
                if(empty($storage_detail->id)) { // заполняем новую деталь
                    $storage_detail->nomenclature_detail_id = $nomenclature_detail->id;
                    $storage_detail->remainder = $storage_operation->count;
                }else { // изменяем старую деталь
                    $storage_detail->remainder = $storage_detail->remainder + $storage_operation->count;
                }
                if(!$storage_detail->save()) {
                    throw new ErrorException('Не удалось сохранить деталь на складе');
                }


                // сохранение операции
                $storage_operation->storage_detail_id = $storage_detail->id;
                if(empty($storage_operation->date)) {
                    $storage_operation->date = time();
                }
                if(!$storage_operation->save()) {
                    throw new ErrorException('Не удалось сохранить операцию прихода');
                }

                return [
                    'success' => true,
                ];


            }else {
                return [
                    'success' => false,
                    'nomenclature_detail_errors' => $nomenclature_detail->validate() ? '' : $nomenclature_detail->getErrors(),
                    'storage_detail_errors' => $storage_detail->validate() ? '' : $storage_detail->getErrors(),
                    'storage_operation_errors' => $storage_operation->validate() ? '' : $storage_operation->getErrors(),
                ];
            }


        }else {  // expenditure

            $storage_operation->scenario = 'update_operation_expenditure';
            $oldAttributes = $storage_operation->oldAttributes;

            if($storage_operation->load($post) && $storage_operation->save()) {

                $storage_operation->rejectOperation($oldAttributes);

                // здесь нужно старую операцию убрать и новую посчитать!!!
                //$storage_detail = $storage_operation->storageDetail; // кешированные данные, а не новые!!!
                $storage_detail = StorageDetail::find()->where(['id' => $storage_operation->storage_detail_id])->one();


                //$storage_detail->remainder = $storage_detail->remainder + $storage_operation_old_count - $storage_operation->count;
//                echo "кол.детелей на складе после отката: ".$storage_detail->remainder."\n";
//                echo "вычитаем: ".$storage_operation->count;
                $storage_detail->remainder = $storage_detail->remainder - $storage_operation->count;
                if(!$storage_detail->save()) {
                    throw new ForbiddenHttpException('Не удалось сохранить изменения на складе');
                }

                return [
                    'success' => true,
                ];

            }else {

                return [
                    'success' => false,
                    'storage_operation_errors' => $storage_operation->validate() ? '' : $storage_operation->getErrors(),
                ];
            }
        }
    }


    public function actionAjaxGetStorageDetailList()
    {
        Yii::$app->response->format = 'json';

        $search = trim(Yii::$app->getRequest()->post('search'));
        $storage_id = intval(Yii::$app->request->post('storage_id'));
        if($storage_id <= 0) {
            throw new ForbiddenHttpException('Выберите склад');
        }


        $storage_details = StorageDetail::find()
            ->where(['storage_id' => $storage_id])
            //->andWhere(['>', 'remainder', 0])
            ->all();

        $aNomenclatureDetails = [];
        $aTransportModels = [];
        $aDetailStates = [];
        $aDetailOrigins = [];
        if(count($storage_details) > 0) {
            $aNomenclatureDetails = ArrayHelper::index(NomenclatureDetail::find()->where(['id' => ArrayHelper::map($storage_details, 'nomenclature_detail_id', 'nomenclature_detail_id')])->all(), 'id');
            $aTransportModels = ArrayHelper::index(TransportModel::find()
                ->all(), 'id');
            $aDetailStates = ArrayHelper::map(TransportDetailState::find()->where(['id' => ArrayHelper::map($storage_details, 'detail_state_id', 'detail_state_id')])->all(), 'id', 'name');
            $aDetailOrigins = ArrayHelper::map(TransportDetailOrigin::find()->where(['id' => ArrayHelper::map($storage_details, 'detail_origin_id', 'detail_origin_id')])->all(), 'id', 'name');
        }

        $search = mb_strtolower($search, 'UTF-8');

        $out['results'] = [];
        foreach($storage_details as $storage_detail) {

            if(!isset($aNomenclatureDetails[$storage_detail->nomenclature_detail_id])) {
                throw new ErrorException('Не найдено nomenclature_detail_id='.$storage_detail->nomenclature_detail_id.' в массиве номенклатур');
            }
            $nomenclature_detail = $aNomenclatureDetails[$storage_detail->nomenclature_detail_id];
            $transport_model = $aTransportModels[$nomenclature_detail->model_id];
            $text = $storage_detail->getDetailText($nomenclature_detail, $transport_model, $aDetailStates[$storage_detail->detail_state_id], $aDetailOrigins[$storage_detail->detail_origin_id]);

            if(empty($search) || mb_strpos(mb_strtolower($text, 'UTF-8'), $search, 0, 'UTF-8') !== false) {
                $out['results'][] = [
                    'id' => $storage_detail->id,
                    'text' => $text,
                    'remainder' => $storage_detail->remainder,
                    'measurement_value' => $nomenclature_detail->measurement_value_id > 0 ? $nomenclature_detail->detailMeasurementValue->name : '',
                ];
            }
        }


        return $out;
    }

}