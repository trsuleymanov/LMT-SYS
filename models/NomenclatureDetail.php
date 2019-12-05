<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "nomenclature_detail".
 *
 * @property int $id
 * @property string $name Наименование
 * @property string $comment Комментарий
 */
class NomenclatureDetail extends \yii\db\ActiveRecord
{
    public $temp_name;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'nomenclature_detail';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[/*'temp_name',*/ 'model_id', 'installation_place',
                'installation_side', /*'detail_name_id'*/], 'required'],
            [['comment'], 'string'],
            [['temp_name', 'detail_code'], 'string', 'max' => 50],
            [['measurement_value_id', 'model_id', 'installation_place', 'installation_side', 'detail_name_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'temp_name' => 'Наименование',
            'detail_name_id' => 'Наименование',
            'comment' => 'Комментарий',
            'measurement_value_id' => 'Единица измерения',
            'detail_code' => 'Код запчасти',
            'model_id' => 'Модель т/с',
            'installation_place' => 'Место установки',
            'installation_side' => ' Сторона установки',
        ];
    }


    public function getTransportModel()
    {
        return $this->hasOne(TransportModel::className(), ['id' => 'model_id']);
    }

    public function getDetailName()
    {
        return $this->hasOne(DetailName::className(), ['id' => 'detail_name_id']);
    }

    public function getDetailMeasurementValue()
    {
        return $this->hasOne(DetailMeasurementValue::className(), ['id' => 'measurement_value_id']);
    }

    public static function getInstallationPlaces() {
        return [
            0 => 'Без признака',
            1 => 'Сзади',
            2 => 'Спереди'
        ];
    }
    public function getInstallationPlace() {
        return self::getInstallationPlaces()[$this->installation_place];
    }


    public static function getInstallationSides() {
        return [
            0 => 'Без признака',
            1 => 'Слева',
            2 => 'Справа'
        ];
    }
    public function getInstallationSide() {
        return self::getInstallationSides()[$this->installation_side];
    }

}
