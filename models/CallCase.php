<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "call_case".
 *
 * @property int $id
 * @property string $case_type
 * @property int $order_id Заказ к которому относиться обращение
 * @property int $open_time Время поступления первого звонка по обращению
 * @property int $call_count Количество звонков
 * @property string $status
 * @property int $close_time Время закрытия
 */
class CallCase extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'call_case';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['case_type', 'status'], 'string'],
            [['operand'], 'string', 'max' => 20],
            [['order_id', 'open_time', 'call_count', 'close_time', 'update_time'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'case_type' => 'Тип',
            'order_id' => 'Заказ к которому относиться обращение',
            'open_time' => 'Время поступления первого звонка по обращению',
            'update_time' => 'Время поступления последнего звонка связанного с обращением',
            'operand' => 'Номер операнда',
            'call_count' => 'Количество звонков',
            'status' => 'Статус',
            'close_time' => 'Время закрытия',
        ];
    }

    public static function getTypes($mini = false) {

        if($mini == true) {
            return [
                'administrative_request' => 'административниый запрос',
                'information_request' => 'информационный запрос',
                'operation_with_order' => 'операции c заказом',
                'missed' => 'пропущенный'
            ];
        }else {
            return [
                'administrative_request' => 'Административниый запрос',
                'information_request' => 'Информационный запрос',
                'operation_with_order' => 'Операции c заказом',
                'missed' => 'Пропущенный'
            ];
        }
    }

    public static function getStatuses($mini = false) {

        if($mini == true) {

            return [
                'not_completed' => 'не завершено',
                'adm_completed' => 'завершено в связи с Адм.',
                'inf_completed' => 'завершено в связи с Инф.',
                'missed_completed' => 'завершено в связи с обработкой пропущенного из меню пропущенных',
                'input_call_missed_completed' => 'завершен пропущенный в связи с вх.',
                'output_call_missed_completed' => 'завершен пропущенный в связи с исх.',
                'auto_completed' => 'завершен пропущенный в связи с очисткой после N часов после последнего события',
                'inf_abnormal_call_completed' => 'аномально завершен в связи с Инф.',
                'completed_by_trip_sending' => 'завершен в связи с отправкой рейса'
            ];
        }else {
            return [
                'not_completed' => 'Не завершено',
                'adm_completed' => 'Завершено в связи с Адм.',
                'inf_completed' => 'Завершено в связи с Инф.',
                'missed_completed' => 'Завершено в связи с обработкой пропущенного из меню пропущенных',
                'input_call_missed_completed' => 'Завершен пропущенный в связи с вх.',
                'output_call_missed_completed' => 'Завершен пропущенный в связи с исх.',
                'auto_completed' => 'Завершен пропущенный в связи с очисткой после N часов после последнего события',
                'inf_abnormal_call_completed' => 'Аномально завершен в связи с Инф.',
                'completed_by_trip_sending' => 'Завершен в связи с отправкой рейса'
            ];
        }
    }



    public function setField($field_name, $field_value)
    {
        if(!empty($field_value)) {
            $field_value = htmlspecialchars($field_value);
        }

        if($field_value === false) {
            $sql = 'UPDATE `'.self::tableName().'` SET '.$field_name.' = false WHERE id = '.$this->id;
        }elseif(empty($field_value)) {
            $sql = 'UPDATE `'.self::tableName().'` SET '.$field_name.' = NULL WHERE id = '.$this->id;
        }else {
            $sql = 'UPDATE `'.self::tableName().'` SET '.$field_name.' = "'.$field_value.'" WHERE id = '.$this->id;
        }

        return Yii::$app->db->createCommand($sql)->execute();
    }
}
