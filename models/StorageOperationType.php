<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "storage_operation_type".
 *
 * @property int $id
 * @property string $name Наименование операции
 * @property int $operation_type Тип операции: 0 - расход, 1 - приход
 */
class StorageOperationType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'storage_operation_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 50],
            [['operation_type'], 'string', 'max' => 1],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Наименование операции',
            'operation_type' => 'Тип операции',
        ];
    }

    public static function getOperationTypes() {
        return [
            0 => 'Расход',
            1 => 'Приход'
        ];
    }
    public function getOperationType() {
        return self::getOperationTypes()[$this->operation_type];
    }
}
