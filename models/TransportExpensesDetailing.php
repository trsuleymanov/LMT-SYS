<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "transport_expenses_detailing".
 *
 * @property int $id
 * @property int $expense_id Расход
 * @property string $name Наименование
 * @property string $price Сумма, руб
 * @property string $type
 */
class TransportExpensesDetailing extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'transport_expenses_detailing';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'price'], 'required'],
            [['expense_id'], 'integer'],
            //[['price'], 'number'],
            [['type'], 'string'],
            [['name'], 'string', 'max' => 100],
            [['price'], 'safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'expense_id' => 'Расход',
            //'doc_name' => 'Наименование документа',
            //'doc_date' => 'Дата документа',
            'name' => 'Наименование',
            'price' => 'Сумма, руб',
            'type' => 'Тип',
        ];
    }

    public static function getTypes() {

        return [
            'work_services' => 'Работы/Услуги',
            'details_goods' => 'Запчасти/Товары',
        ];
    }

    public function beforeValidate()
    {
        $this->price = str_replace(',', '.', $this->price);
        $this->price = str_replace(' ', '', $this->price);

        return parent::beforeValidate(); // TODO: Change the autogenerated stub
    }



    public function getExpenses()
    {
        return $this->hasOne(TransportExpenses::className(), ['id' => 'expense_id']);
    }

    public function getSeller() {
        return $this->hasOne(TransportExpensesSeller::className(), ['id' => 'expenses_seller_id']);
    }

    public function getTransport() {
        return $this->hasOne(Transport::className(), ['id' => 'waybill_transport_id']);
    }

    public function getDriver() {
        return $this->hasOne(Driver::className(), ['id' => 'waybill_driver_id']);
    }


//    public static function getWorkTypes() {
//
//        return [
//            'work' => 'Работа',
//        ];
//    }

//    public static function getGoodTypes() {
//
//        return [
//            'spare_part' => 'Запчасть',
//            'detail' => 'Деталь'
//        ];
//    }

}