<?php

namespace app\models;

use Yii;
use yii\base\ErrorException;
use yii\web\ForbiddenHttpException;

/**
 * Модель "лога действий диспетчеров"
 *
 * @property integer $id
 * @property string $operation_type
 * @property integer $dispetcher_id
 * @property integer $created_at
 * @property integer $order_id
 */
class DispatcherAccounting extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'dispatcher_accounting';
    }

    //                              'cancel_trip_sended', 0, 0, 0, $this->id
    public static function createLog($operation_type, $order_id = 0, $operation_time = 0, $user_id = 0, $value = '', $order_temp_identifier = '') {

        $operationTypes = array_keys(self::getOperationTypes());

        if(!in_array($operation_type, $operationTypes)) {
            throw new ErrorException('Неизвестный тип операции логирования operation_type='.$operation_type);
        }

        if($order_id == 0 && in_array($operation_type, ['order_create', 'order_update', 'order_confirm', 'order_cancel', 'order_sat_to_transport', 'order_unsat_from_transport'])) {
            throw new ErrorException('Для данного типа операции необходимо передать order_id');
        }

        $dispatcher_accounting = new DispatcherAccounting();
        $dispatcher_accounting->operation_type = $operation_type;

        if($user_id > 0) {
            $dispatcher_accounting->dispetcher_id = $user_id;
        }elseif(isset(Yii::$app->user) && Yii::$app->user->id > 0) {
            $dispatcher_accounting->dispetcher_id = Yii::$app->user->id;
        }else {
            $dispatcher_accounting->dispetcher_id = 0;
            //throw new ErrorException('Ошибка логирования - пользователь не найден');
        }

        $dispatcher_accounting->created_at = ($operation_time > 0 ? $operation_time : time());
        if($order_id > 0) {
            $dispatcher_accounting->order_id = $order_id;
        }

        if(!empty($value)) {
            $dispatcher_accounting->value = strval($value);
        }

        if(!empty($order_temp_identifier)) {
            $dispatcher_accounting->order_temp_identifier = $order_temp_identifier;
        }

        if(!$dispatcher_accounting->save()) {
            throw new ForbiddenHttpException('Не удается создать лог действия');
        }


        return true;
    }

    public static function getOperationTypes() {

        return [
            'order_create' => 'Первичная запись',
            'order_update' => 'Редактирование заказа',
            'order_confirm' => 'Подтверждение заказа',
            'order_cancel' => 'Удаление заказа',

            'order_sat_to_transport' => 'Посадка в машину',
            'order_unsat_from_transport' => 'Высадка из машины',

            'order_checked_last_orders' => 'Проверка заказа на дубликаты',

            'trip_transport_create' => 'Постановка на рейс т/с', // не найдены
            'trip_transport_delete' => 'Снятие т/с с рейса',
            //'trip_transport_send',
            'trip_transport_confirm' => 'Подтверждение т/с',

            'trip_transport_change_driver' => 'Смена водителя',
            'trip_transport_send' => 'Отправка (выпуск) т/с',

            'trip_start_sending' => 'Начало отправления рейса',
            'trip_issued_by_operator' => 'Выпуск рейса',
            'trip_send' => 'Закрытие рейса',

            'login' => 'Вход в систему',
            'logout' => 'Выход из системы',
            'system_drop_out' => 'System Drop Out',

            'double_click' => 'Двойное нажатие',
            'cancel_trip_sended' => 'Отмена отправки рейса и всех отправленных машин',

            'open_print_modal' => 'Открытия модального окна для печати',

            'handling_client_server_request' => 'Обработка электронной заявки'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['dispetcher_id', 'created_at', 'order_id', /*'call_appeal_id'*/], 'integer'],
            [['operation_type'], 'string', 'max' => 30],
            [['value'], 'string', 'max' => 40],
            [['order_temp_identifier'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'operation_type' => 'Тип операции',
            'dispetcher_id' => 'Оператор (пользователь) совершивший действие',
            'created_at' => 'Время совершения действия',
            //'call_appeal_id' => 'Обращение',
            'order_id' => 'id Заказа',
            'value' => 'Доп.поле',
            'order_temp_identifier' => 'Временный идентификатор заказа до момента создания в базе данных'
        ];
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

    public static function setFields($aDispatcherAccountingsId, $field_name, $field_value)
    {
        if(!empty($field_value)) {
            $field_value = htmlspecialchars($field_value);
        }

        if($field_value === false) {
            $sql = 'UPDATE `'.self::tableName().'` SET '.$field_name.' = false WHERE id IN ('.implode(',', $aDispatcherAccountingsId).')';
        }elseif(empty($field_value)) {
            $sql = 'UPDATE `'.self::tableName().'` SET '.$field_name.' = NULL WHERE id IN ('.implode(',', $aDispatcherAccountingsId).')';
        }else {
            $sql = 'UPDATE `'.self::tableName().'` SET '.$field_name.' = "'.$field_value.'" WHERE id IN ('.implode(',', $aDispatcherAccountingsId).')';
        }
        return Yii::$app->db->createCommand($sql)->execute();
    }


//    public function afterSave($insert, $changedAttributes)
//    {
//        parent::afterSave($insert, $changedAttributes);
//
//        // когда сохраняется действие оператора, то проверяется что в этот момент у оператора не идет телефонный разговор.
//        // Если разговор не идет, то создается обращения (может создаваться) связанное с последним контактом (с последним звонком).
//        if(!empty($this->dispetcher_id)) {
//
//            $active_call = null;
//
//            // если есть незавершенный/висячий контакт
//            $not_closes_contact = CallContact::find()
//                ->where(['operator_user_id' => $this->dispetcher_id])
//                ->andWhere(['completed_at' => NULL])
//                ->one();
//            // то ищем звонок связанный с контактом, у которого разговор уже начался
//            if($not_closes_contact != null) {
//                $active_call = Call::find()
//                    ->where(['call_contact_id' => $not_closes_contact->id])
//                    ->andWhere(['<', 'ats_answer_time', time()])
//                    ->one();
//            }
//
//            // если отсутствует разговор по телефону в этот момент у оператора, то
//            // связываем текущее действие оператора с предыдущим Обращением (с последним законченным телефонным разговором)
//            if($active_call == null) {
//
//                if(in_array($this->operation_type, [
//                        'order_create',  // 'Первичная запись'
//                        'order_update',  // 'Редактирование заказа',
//                        'order_confirm', // 'Подтверждение заказа'
//                        'order_cancel', // 'Удаление заказа'
//                        'order_sat_to_transport', // 'Посадка в машину'
//                        'order_unsat_from_transport', // 'Высадка из машины'
//                        'order_checked_last_orders' // 'Проверка заказа на дубликаты'
//                    ])) {
//
//                    //'administrative_request','information_request','operation_with_order'
//                    $prev_appeal = CallAppeal::find()
//                        ->where(['operator_user_id' => $this->dispetcher_id])
//                        ->andWhere(['type' => 'operation_with_order'])
//                        ->orderBy(['id' => SORT_DESC])
//                        ->one();
//                    if($prev_appeal != null) {
//                        $this->setField('call_appeal_id', $prev_appeal->id);
//                    }
//                }
//            }
//
//        }
//    }

    public function getUser() {
        return $this->hasOne(User::className(), ['id' => 'dispetcher_id']);
    }
}
