<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "storage_detail".
 *
 * @property int $id
 * @property int $storage_id Склад
 * @property int $nomenclature_detail_id Запчасть из номенклатуры
 * @property int $detail_state_id Состояние запчасти
 * @property int $detail_origin_id Происхождение детали
 * @property int $storage_place_count Мест на складе
 * @property int $remainder Остаток
 * @property string $comment Комментарий
 * @property int $updated_at Время изменения
 */
class StorageDetail extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'storage_detail';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['storage_id', 'detail_state_id', 'detail_origin_id',], 'required'],
            [['storage_id', 'nomenclature_detail_id', 'detail_state_id', 'detail_origin_id',
                'storage_place_count', 'created_at', 'updated_at', ], 'integer'],
            [['comment'], 'string'],
            [['remainder'], 'safe']
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();

        // создание операции прихода
        $scenarios['create_operation_income'] = [
            'storage_id',               // нужно это поле удалить из StorageOperation или продублировать
            //'nomenclature_detail_id', // +
            'detail_state_id', // +
            'detail_origin_id', // +
            //'storage_place_count', // count
            //'measurement value',
            //'remainder',
            //'comment',
            //'updated_at',
        ];

        // создание операции расхода
//        $scenarios['create_operation_expenditure'] = [
//            'created_at',
//            'storage_id',
//            'operation_type_id',
//            'storage_detail_id',
//            'transport_id',
//            'driver_id',
//            'count'
//        ];

        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'storage_id' => 'Склад', // +
            'nomenclature_detail_id' => 'Запчасть из номенклатуры',  // +
            'detail_state_id' => 'Состояние запчасти', // +
            'detail_origin_id' => 'Происхождение детали', // +
            'storage_place_count' => 'Место на складе',
            'remainder' => 'Остаток',
            'comment' => 'Комментарий',
            'created_at' => 'Время создания',
            'updated_at' => 'Время изменения',
        ];
    }


    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->created_at = time();
        }else {
            $this->updated_at = time();
        }

        return parent::beforeSave($insert);
    }

    public function getStorage()
    {
        return $this->hasOne(Storage::className(), ['id' => 'storage_id']);
    }

    public function getNomenclatureDetail()
    {
        return $this->hasOne(NomenclatureDetail::className(), ['id' => 'nomenclature_detail_id']);
    }


    public function getTransportDetailState()
    {
        return $this->hasOne(TransportDetailState::className(), ['id' => 'detail_state_id']);
    }

    public function getTransportDetailOrigin()
    {
        return $this->hasOne(TransportDetailOrigin::className(), ['id' => 'detail_origin_id']);
    }

    public function getDetailText($nomenclature_detail, $transport_model, $detail_state_name, $detail_origin_name) {

        $installation_place = mb_strtolower(NomenclatureDetail::getInstallationPlaces()[$nomenclature_detail->installation_place], 'UTF-8');
        if($installation_place == 'без признака') {
            $installation_place = ' - ';
        }

        $installation_side = mb_strtolower(NomenclatureDetail::getInstallationSides()[$nomenclature_detail->installation_side], 'UTF-8');
        if($installation_side == 'без признака') {
            $installation_side = ' - ';
        }

        $detail_name = $nomenclature_detail->detailName;

        $text =
            '('.$transport_model->sh_name.')'
            .' - ' . ($detail_name != null ? $detail_name->name : '').': '
            . $installation_place
            .'/'.$installation_side
            .', '.$detail_state_name
            .', '.$detail_origin_name;

        return $text;
    }
}
