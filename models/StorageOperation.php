<?php

namespace app\models;

use Yii;
use yii\base\ErrorException;

/**
 * This is the model class for table "storage_operation".
 *
 * @property int $id
 * @property int $storage_detail_id Запчасть на складе
 * @property int $count Количество
 * @property int $transport_id C какой машиной связана операция
 * @property int $driver_id С каким водителем связана операция
 * @property int $created_at Дата операция
 * @property int $operation_type_id Тип операции
 * @property string $comment Комментарий
 */
class StorageOperation extends \yii\db\ActiveRecord
{
    public $storage_id;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'storage_operation';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        // Значение «Количество» должно быть целым числом. Значение «Дата операция» должно быть целым числом.
        return [
            [['created_at', 'operation_type_id', 'storage_detail_id', 'count', 'date'], 'required'],
            [['storage_detail_id', 'transport_id', 'driver_id', 'operation_type_id', 'creator_id', 'created_at'], 'integer'],
            [['comment'], 'string'],
            [['count'], 'checkCountCreateOperation', 'skipOnEmpty' => false, 'on' => 'create_operation_income'],
            [['count'], 'checkCountCreateOperation', 'skipOnEmpty' => false, 'on' => 'create_operation_expenditure'],
            [['count'], 'checkCountUpdateOperation', 'skipOnEmpty' => false, 'on' => 'update_operation_income'],
            [['count'], 'checkCountUpdateOperation', 'skipOnEmpty' => false, 'on' => 'update_operation_expenditure'],

            ['transport_id', 'checkTransport', 'skipOnEmpty' => false,],
            [['without_transport'], 'boolean'],
            [['date', 'count'], 'safe']
        ];
    }

    public function checkCountCreateOperation($attribute, $params)
    {
        if($this->count <= 0) {
            $this->addError($attribute, 'Установите количество деталей');
        }

        if($this->storage_detail_id > 0) {
            $storage_detail = $this->storageDetail;
            if($storage_detail == null) {
                throw new ErrorException('Деталь с id='.$this->storage_detail_id.' не найдена');
            }

            if($this->count > $storage_detail->remainder) {
                $this->addError($attribute, 'Всего достуно на складе '.$storage_detail->remainder.' * деталей, выберите допустимое количество.');
            }
        }
    }

    public function checkCountUpdateOperation($attribute, $params)
    {
        //echo "this:<pre>"; print_r($this); echo "</pre>";

        if($this->count <= 0) {
            $this->addError($attribute, 'Установите количество деталей');
        }

//        if($this->storage_detail_id > 0) {
//            $storage_detail = $this->storageDetail;
//            if($storage_detail == null) {
//                throw new ErrorException('Деталь с id='.$this->storage_detail_id.' не найдена');
//            }
//
//            if($this->count > $storage_detail->remainder) {
//                $this->addError($attribute, 'Всего достуно на складе '.$storage_detail->remainder.' * деталей, выберите допустимое количество.');
//            }
//        }
    }

    public function checkTransport($attribute, $params)
    {
        if($this->without_transport == false && empty($this->transport_id)) {
            $this->addError($attribute, 'Необходимо заполнить Т/с.');
        }
        if($this->without_transport == false && empty($this->driver_id)) {
            $this->addError($attribute, 'Необходимо заполнить Водитель.');
        }
    }


    public function scenarios()
    {
        $scenarios = parent::scenarios();

        // создание операции прихода
        $scenarios['create_operation_income'] = [
            'date',
            'operation_type_id',
            //'storage_detail_id',
            'without_transport',
            'transport_id',
            'driver_id',
            'count',
            'comment'
        ]; // 'storage_detail_id' записывается после создания операции прихода

        // создание операции расхода
        $scenarios['create_operation_expenditure'] = [
            'storage_detail_id',
            'count',
            'without_transport',
            'transport_id',
            'driver_id',
            'date',
            'operation_type_id',
            'comment'
        ];


        // редактирование операции прихода
        $scenarios['update_operation_income'] = [
            'date',
            'operation_type_id',
            //'storage_detail_id',
            'without_transport',
            'transport_id',
            'driver_id',
            'count',
            'comment'
        ]; // 'storage_detail_id' записывается после создания операции прихода

        // редактирование операции расхода
        $scenarios['update_operation_expenditure'] = [
            'storage_detail_id',
            'count',
            'without_transport',
            'transport_id',
            'driver_id',
            'date',
            'operation_type_id',
            'comment'
        ];

        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'date' => 'Дата операции',
            'storage_detail_id' => 'Запчасть', // +
            'count' => 'Количество',
            'without_transport' => 'Без участия т/с',
            'transport_id' => 'Т/с', // +
            'driver_id' => 'Водитель', // +
            'creator_id' => 'Создатель операции',
            'created_at' => 'Время провередения операции',
            'operation_type_id' => 'Тип операции', // +
            'comment' => 'Комментарий',
        ];
    }


    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->created_at = time();
            $this->creator_id = Yii::$app->user->id;
        }

        if(isset($this->date) && preg_match('/^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$/i', $this->date)) {
            $this->date = strtotime($this->date);   // convent '07.11.2016' to unixtime
        }

        return parent::beforeSave($insert);
    }


    public function getStorageDetail()
    {
        return $this->hasOne(StorageDetail::className(), ['id' => 'storage_detail_id']);
    }


    public function getTransport()
    {
        return $this->hasOne(Transport::className(), ['id' => 'transport_id']);
    }


    public function getDriver()
    {
        return $this->hasOne(Driver::className(), ['id' => 'driver_id']);
    }


    public function getCreator()
    {
        return $this->hasOne(User::className(), ['id' => 'creator_id']);
    }


    public function getStorageOperationType()
    {
        return $this->hasOne(StorageOperationType::className(), ['id' => 'operation_type_id']);
    }


    // откат по операции (без удаления самой операции)
    public function rejectOperation($oldAttributes) {

//        Array
//        (
//            [id] => 144
//            [date] => 1532725200
//            [storage_detail_id] => 104
//            [count] => 1
//            [without_transport] => 0
//            [transport_id] => 2
//            [driver_id] => 1
//            [creator_id] => 38
//            [created_at] => 1532799316
//            [operation_type_id] => 3
//            [comment] => fff111
//        )

        // при создании операции:
        // 1. если тип операции income
        //   - создается новая ед.изм-я DetailMeasurementValue() или используется старая
        //   - создается новая NomenclatureDetail или используется старая
        //   - создается новая StorageDetail или используется старая
        //   - создается новая StorageOperation
        // 2. если тип операции expenditure
        //   - создается новая StorageOperation
        //   - используется старая StorageDetail для изменения remainder

        // ! при откате операции ничего не удаляю, только перезаписываю.

        $operation_type = $this->storageOperationType;
        if($operation_type->operation_type == 1) { // income

            $old_storage_detail = StorageDetail::find()->where(['id' => $oldAttributes['storage_detail_id']])->one();
            if($old_storage_detail == null) {
                throw new ErrorException('Деталь склада ранее привязанныя к операции не найдена');
            }

            $old_storage_detail->remainder = $old_storage_detail->remainder - $oldAttributes['count'];
            if(!$old_storage_detail->save()) {
                throw new ErrorException('Не удалось сохранить изменения в детале на складе');
            }


        }else {     // expenditure

            $old_storage_detail = StorageDetail::find()->where(['id' => $oldAttributes['storage_detail_id']])->one();
            if($old_storage_detail == null) {
                throw new ErrorException('Прежняя деталь на складе не найдена');
            }

            $old_storage_detail->remainder = $old_storage_detail->remainder + $oldAttributes['count'];
            if(!$old_storage_detail->save()) {
                throw new ErrorException('Не удалось сохранить изменения в детале на складе');
            }
        }
    }

    /*
     * Вспомогательная для контроллеров функция
     */
    public static function _getDetailMeasurementValueFromPost($post) {

        $detail_measurement_value_name = mb_strtolower(trim($post['DetailMeasurementValue']['name']), 'UTF-8');
        $detail_measurement_value = DetailMeasurementValue::find()
            ->where(['name' => $detail_measurement_value_name])
            ->one();

        if($detail_measurement_value == null) {
            $detail_measurement_value = new DetailMeasurementValue();
        }

        return $detail_measurement_value;
    }

    /*
     * Вспомогательная для контроллеров функция
     */
    public static function _getNomenclatureDetailFromPost($post, $detail_measurement_value) {

        $nomenclature_detail = null;
        if($detail_measurement_value != null) {
            //$nomenclature_detail_name = mb_strtolower(trim($post['NomenclatureDetail']['name']), 'UTF-8');
            $detail_name_value = mb_strtolower(trim($post['NomenclatureDetail']['temp_name']), 'UTF-8');
            $detail_name_value = mb_strtoupper(mb_substr($detail_name_value, 0, 1, 'UTF-8'), 'UTF-8') . mb_substr($detail_name_value, 1, NULL, 'UTF-8');
            $nomenclature_detail_installation_place = intval($post['NomenclatureDetail']['installation_place']);
            $nomenclature_detail_installation_side = intval($post['NomenclatureDetail']['installation_side']);
            $nomenclature_detail_measurement_value_id = $detail_measurement_value->id;
            $nomenclature_detail_model_id = intval($post['NomenclatureDetail']['model_id']);


            $detail_name = DetailName::find()->where(['name' => $detail_name_value])->one();

            if($detail_name != null) {

                $nomenclature_detail = NomenclatureDetail::find()
                    ->where(['detail_name_id' => $detail_name->id])
                    ->andWhere(['installation_place' => $nomenclature_detail_installation_place])
                    ->andWhere(['installation_side' => $nomenclature_detail_installation_side])
                    ->andWhere(['measurement_value_id' => $nomenclature_detail_measurement_value_id])
                    ->andWhere(['model_id' => $nomenclature_detail_model_id])
                    ->one();
            }
        }

        if($nomenclature_detail == null) {
            $nomenclature_detail = new NomenclatureDetail();
        }

        return $nomenclature_detail;
    }

    /*
     * Вспомогательная для контроллеров функция
     */
    public static function _getStorageDetailFromPost($post, $nomenclature_detail) {

        $storage_detail = StorageDetail::find()->where([
            'nomenclature_detail_id' => $nomenclature_detail->id,
            'storage_id' => $post['StorageDetail']['storage_id'],
            'detail_state_id' => $post['StorageDetail']['detail_state_id'],
            'detail_origin_id' => $post['StorageDetail']['detail_origin_id'],
        ])->one();
        if($storage_detail == null) {
            $storage_detail = new StorageDetail();
        }

        return $storage_detail;
    }
}
