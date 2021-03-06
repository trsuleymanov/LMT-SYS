<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "transport_expenses_paymenter".
 *
 * @property int $id
 * @property string $name Наименование
 */
class TransportExpensesPaymenter extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'transport_expenses_paymenter';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Наименование',
        ];
    }
}
