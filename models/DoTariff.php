<?php

namespace app\models;

use ParseError;
use Yii;

/**
 * This is the model class for table "do_tariff".
 *
 * @property int $id
 * @property string $description Команда
 * @property string $tariff_type
 * @property int $created_at Создан
 * @property int $updated_at Изменен
 */
class DoTariff extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'do_tariff';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tariff_type', 'description', /*'code'*/], 'required'],
            [['tariff_type', /*'code'*/], 'string'],
            [['created_at', 'updated_at', 'use_fix_price', 'use_client_do_tariff'], 'integer'],
            [['description', 'place_price_formula', 'order_comment', 'order_price_formula'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'description' => 'Описание команды',
            //'code' => 'Код команды',
            'place_price_formula' => 'Формула расчета цены за место',
            'use_fix_price' => 'Устанавить заказу фикс.цену',
            'order_price_formula' => 'Формула расчета итоговой цены заказа',
            'order_comment' => 'Примечание к заказу',
            'tariff_type' => 'Тип тарифа',
            'use_client_do_tariff' => 'Использовать признак клиента вместо текущего признака',
            'created_at' => 'Создан',
            'updated_at' => 'Изменен',
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

//    public static function changePlacePrice($place_price, $do_tariff) {
//
//        if($do_tariff == null) {
//            return $place_price;
//        }else {
//            switch($do_tariff->code) {
//                case 'source_price_plus_50_rub':// Примени тариф по умолчанию, сделай надбавку в 50 р - на каждое место
//                    return $place_price + 50;
//                    break;
//
//                case 'source_price_plus_200_rub': // Назначь наценку к стандартному тарифу в 200 руб
//                    return $place_price + 200;
//                    break;
//
//                case 'double_source_price': // Удвой стандартный тариф
//                    return 2*$place_price;
//                    break;
//            }
//        }
//    }


    public function calculatePlacePrice($price = 0) {

        $result = 0;

        try {
            eval($this->place_price_formula);   // например $result = $price + 50;
        }catch(ParseError $e) {
            throw new $e('Произошла ошибка при использовании формулы расчета цена за место. Признак цены id='.$this->id);
        }

        return $result;
    }

    public function calculateTotalPrice($price = 0, $source_price = 0) {

        $result = 0;

        try {
            eval($this->order_price_formula);   // например $result = $price; или $result = $source_price;
        }catch(ParseError $e) {
            throw new $e('Произошла ошибка при использовании формулы расчета цена за место. Признак цены id='.$this->id);
        }

        return $result;
    }

    /*
     * Пересчитываем цену за место
     */
    public function changePlacePrice($place_price = 0, $order) {

        if($this->use_client_do_tariff == true) {
            $client = $order->client;
            if($client == null) {
                return $place_price;
            }else {
                $client_do_tariff = $client->doTariff;
                if($client_do_tariff != null && !empty($client_do_tariff->place_price_formula)) {
                    return $client_do_tariff->calculatePlacePrice($place_price);
                }else {
                    return $place_price;
                }
            }
        }

        if(empty($this->place_price_formula)) {
            return $place_price;
        }else {
            return $this->calculatePlacePrice($place_price);
        }
    }

    /*
     * Пересчитываем итоговую цену за заказ
     */
    public function changeTotalPrice($total_price = 0, $order) {

        $source_price = $order->source_price;

        if($this->use_client_do_tariff == true) {
            $client = $order->client;
            if($client == null) {
                return $total_price;
            }else {
                $client_do_tariff = $client->doTariff;
                if($client_do_tariff != null && !empty($client_do_tariff->order_price_formula)) {
                    return $client_do_tariff->calculateTotalPrice($total_price, $source_price);
                }else {
                    return $total_price;
                }
            }
        }

        if(empty($this->order_price_formula)) {
            return $total_price;
        }else {
            return $this->calculateTotalPrice($total_price, $source_price);
        }
    }
}
