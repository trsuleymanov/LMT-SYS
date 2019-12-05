<?php

namespace app\models;

use Yii;
use yii\base\ErrorException;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;

/**
 * This is the model class for table "waybill".
 *
 */
class TransportWaybill extends \yii\db\ActiveRecord
{
//    public $mileage_dif = 0; // разница в пробегах
//    public $consumption_per_100_km = 0; // расход на 100 км

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'transport_waybill';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [

            [['transport_id', 'driver_id', 'pre_trip_med_check', 'pre_trip_tech_check',
                'after_trip_med_check', 'after_trip_tech_check', 'trip_transport_start', 'trip_transport_end',
                'created_at', 'creator_id',
                'trip_event1_id', 'trip_event2_id', 'trip_event3_id', 'trip_event4_id',
                'trip_event5_id', 'trip_event6_id', 'trip_event7_id', 'trip_event8_id',
                'camera_val', 'camera_driver_val', 'is_visible',
                'set_hand_over_b1_operator_id', 'set_hand_over_b1_time', 'set_hand_over_b2_operator_id', 'set_hand_over_b2_time',
            ], 'integer'],
            [['number'], 'string', 'max' => 10],
            [['changes_history', 'trip_comment', 'klpto_comment',
                'trip_event1_comment', 'trip_event2_comment', 'trip_event3_comment', 'trip_event4_comment',
                'trip_event5_comment', 'trip_event6_comment', 'trip_event7_comment', 'trip_event8_comment',
                'camera_no_record_comment', 'correct_comment', 'fines_gibdd_comment', 'another_fines_comment',

            ], 'string', 'max' => 255],
            [['number', 'date_of_issue', 'transport_id', 'driver_id',
                'departure_time', 'return_time', 'pre_trip_med_check',
                //'pre_trip_med_check_time',
                'pre_trip_tech_check',
                //'pre_trip_tech_check_time',
                'after_trip_med_check',
                // 'after_trip_med_check_time',
                'after_trip_tech_check',
                //'after_trip_tech_check_time',
                //'trip_transport_start', 'trip_transport_end',
                'departure_time', 'mileage_before_departure',
                'return_time', 'mileage_after_departure'], 'required', ],

            [['pre_trip_med_check_time'], 'preTripMedCheckTime', 'skipOnEmpty' => false, 'skipOnError' => false],
            [['pre_trip_tech_check_time'], 'preTripTechCheckTime', 'skipOnEmpty' => false, 'skipOnError' => false],
            [['after_trip_med_check_time'], 'afterTripMedCheckTime', 'skipOnEmpty' => false, 'skipOnError' => false],
            [['after_trip_tech_check_time'], 'afterTripTechCheckTime', 'skipOnEmpty' => false, 'skipOnError' => false],

            ['number', 'checkNumberDateOfIssue', 'skipOnEmpty' => true],

            [['accepted_expenses_from_revenue', 'not_accepted_expenses_from_revenue', 'incoming_requirements',
            'accepted_expenses_all_types',
            'total_net_profit', 'total_actually_given', 'total_failure_to_pay', 'total_fines', 'trips_summ'
            ], 'number'],

            [[
                'waybill_state', 'values_fixed_state', 'gsm', 'klpto', 'hand_over_b2_data', 'hand_over_b1_data',
                'mileage_before_departure', 'mileage_after_departure',
                'hand_over_b1', 'hand_over_b2', 'camera_eduction', 'camera_no_record',
                'accruals_to_issue_for_trip', 'accruals_given_to_hand', 'fines_gibdd', 'another_fines',
                /*'mileage_dif', 'consumption_per_100_km'*/
            ], 'safe'],
        ];
    }

    public function checkNumberDateOfIssue($attribute, $params)
    {
        $date_of_issue = $this->date_of_issue;
        if(preg_match('/^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$/i', $date_of_issue)) {
            $date_of_issue = strtotime($date_of_issue);   // convent '07.11.2016' to unixtime
        }

        $waybill_query = TransportWaybill::find()
            ->where(['number' => $this->number])
            ->andWhere(['date_of_issue' => $date_of_issue]);

        if(isset($this->id) && $this->id > 0) {
            $waybill_query->andWhere(['!=', 'id', $this->id]);
        }

        $waybill = $waybill_query->one();

        if($waybill != null) {
            $this->addError('number', 'Пара Дата-Номер не уникальна');
            $this->addError('date_of_issue', 'Пара Дата-Номер не уникальна');
        }

        return true;
    }


    public function setField($field_name, $field_value)
    {
        switch($field_name) {
            case 'date_of_issue':
                if(isset($field_value) && preg_match('/^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$/i', $field_value)) {
                    $field_value = strtotime($field_value);   // convent '07.11.2016' to unixtime
                }

                // группам расходов:'typical_expenses', 'other_expenses' устанавливаю новую дату оплаты
                $transport_expenses = $this->transportExpenses;
                foreach($transport_expenses as $tr_expenses) {
                    //if(in_array($tr_expenses->expenses_seller_type_id, $aSellerTypes)) {
                    if(in_array($tr_expenses->view_group, ['typical_expenses', 'other_expenses'])) {
                        $tr_expenses->setField('payment_date', $field_value);
                    }
                }

                break;
            case 'hand_over_b1_data':
                if(isset($field_value) && preg_match('/^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$/i', $field_value)) {
                    $field_value = strtotime($field_value);   // convent '07.11.2016' to unixtime
                }
                break;
            case 'hand_over_b2_data':
                if(isset($field_value) && preg_match('/^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$/i', $field_value)) {
                    $field_value = strtotime($field_value);   // convent '07.11.2016' to unixtime
                }
                break;
            case 'camera_eduction':
            case 'camera_no_record':
            case 'accruals_to_issue_for_trip':
            case 'accruals_given_to_hand':
            case 'fines_gibdd':
            case 'another_fines':
                $field_value = str_replace(',', '', $field_value);
                $field_value = str_replace(' ', '', $field_value);
                break;

            case 'hand_over_b1':
            case 'hand_over_b2':
                $field_value = str_replace(',', '', $field_value);
                $field_value = str_replace(' ', '', $field_value);

            break;

            case 'mileage_before_departure':
            case 'mileage_after_departure':
                $field_value = str_replace(' ', '', $field_value);
                break;
            case 'driver_id':
                $driver_id = intval($field_value);

                $driver = Driver::find()->where(['id' => $driver_id])->one();
                if($driver == null) {
                    throw new ForbiddenHttpException('Водитель не найден');
                }
                if(empty($driver->user_id)) {
                    throw new ForbiddenHttpException('У водителя нет связанного пользователя');
                }

                // Нужно для типов расходов: АЗС, Мойка, Стоянка установить полю "Кто оплатил" равное водителю, т.е.
                // чтобы Расход.transport_expenses_paymenter_id = пользователю водителя $driver_id
                //$seller_types = TransportExpensesSellerType::find()->where(['name' => ['АЗС','Мойка','Стоянка']])->all();
                //$aSellerTypes = ArrayHelper::map($seller_types, 'id', 'id');

                // для расходов из 1-й таблицы "типовых расходов" устанавливаю в "Кто оплатил" пользователя водителя.
                $transport_expenses = $this->transportExpenses;
                foreach($transport_expenses as $tr_expenses) {
                    //if(in_array($tr_expenses->expenses_seller_type_id, $aSellerTypes)) {
                    if(in_array($tr_expenses->view_group, ['typical_expenses', 'other_expenses'])) {
                        $tr_expenses->setField('transport_expenses_paymenter_id', $driver->user_id);
                    }
                }

                break;
        }



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

        $res = Yii::$app->db->createCommand($sql)->execute();

        if($field_name == 'trip_transport_start') {
            $this->trip_transport_start = $field_value;
        }
        if($field_name == 'trip_transport_end') {
            $this->trip_transport_end = $field_value;
        }

        switch($field_name) {
            case 'trip_transport_start':
            case 'trip_transport_end':
                // trips_summ

                $trips_summ = 0;
                if($this->trip_transport_start > 0) {
                    $start_day_report_trip_transport = null;
                    $start_trip_transport = $this->tripTransportStart;
                    if($start_trip_transport != null) {
                        $start_day_report_trip_transport = $start_trip_transport->dayReportTripTransport;
                    }
                    if($start_day_report_trip_transport != null) {
                        $trips_summ = $start_day_report_trip_transport->proceeds;
                    }

                }
                if($this->trip_transport_end > 0) {

                    $end_day_report_trip_transport = null;
                    $end_trip_transport = $this->tripTransportEnd;
                    if($end_trip_transport != null) {
                        $end_day_report_trip_transport = $end_trip_transport->dayReportTripTransport;
                    }

                    if($end_day_report_trip_transport != null) {
                        $trips_summ += $end_day_report_trip_transport->proceeds;
                    }
                }

                if($trips_summ != $this->trips_summ) {
                    // exit('записываем в ПЛ trips_summ='.$trips_summ);
                    $this->setField('trips_summ', $trips_summ);
                }
                // else {
                //    exit('записывать в ПЛ нечего $this->trips_summ='.$this->trips_summ.' trip_transport_start=' + $this->trip_transport_start);
                // }
                break;
        }

        return $res;
    }



    public static function getWaybillStates() {
        return [
            'accepted' => 'Принят',
            'not_accepted' => 'Не принят',
            'not_presented' => 'Не представлен'
        ];
    }

    public static function getValuesFixedStates() {
        return [
            'accepted' => 'Принят',
            'not_accepted' => 'Не принят',
            'not_presented' => 'Не представлен'
        ];
    }

    public static function getGsms() {
        return [
            'accepted' => 'Принят',
            'not_accepted' => 'Не принят',
            'not_presented' => 'Не представлен'
        ];
    }

    public static function getKlpto() {

        // 'none','issued_to_driver','passed_by_driver','data_entered'
        // Нет/Выдан водителю/Сдан водителем/Внесены данные
        return [
            'none' => 'Нет',
            'issued_to_driver' => 'Выдан водителю',
            'passed_by_driver' => 'Сдан водителем',
            'data_entered' => 'Внесены данные'
        ];
    }



    public function preTripMedCheckTime($attribute, $params)
    {
        if($this->pre_trip_med_check == true && empty($this->pre_trip_med_check_time)) {
            $this->addError($attribute, 'Необходимо заполнить поле');
        }else {
            return true;
        }
    }
    public function preTripTechCheckTime($attribute, $params)
    {
        if($this->pre_trip_tech_check == true && empty($this->pre_trip_tech_check_time)) {
            $this->addError($attribute, 'Необходимо заполнить поле');
        }else {
            return true;
        }
    }
    public function afterTripMedCheckTime($attribute, $params)
    {
        if($this->after_trip_med_check == true && empty($this->after_trip_med_check_time)) {
            $this->addError($attribute, 'Необходимо заполнить поле');
        }else {
            return true;
        }
    }
    public function afterTripTechCheckTime($attribute, $params)
    {
        if($this->after_trip_tech_check == true && empty($this->after_trip_tech_check_time)) {
            $this->addError($attribute, 'Необходимо заполнить поле');
        }else {
            return true;
        }
    }


    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->created_at = time();
            $this->creator_id = Yii::$app->user->id;
        }

        if(isset($this->date_of_issue) && preg_match('/^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$/i', $this->date_of_issue)) {
            $this->date_of_issue = strtotime($this->date_of_issue);   // convent '07.11.2016' to unixtime
        }

        return parent::beforeSave($insert);
    }

    public function beforeValidate()
    {
        $this->camera_eduction = str_replace(',', '.', $this->camera_eduction);
        $this->hand_over_b1 = str_replace(',', '.', $this->hand_over_b1);
        $this->hand_over_b2 = str_replace(',', '.', $this->hand_over_b2);
        $this->camera_no_record = str_replace(',', '.', $this->camera_no_record);

        $this->accruals_to_issue_for_trip = str_replace(',', '.', $this->accruals_to_issue_for_trip);
        $this->accruals_given_to_hand = str_replace(',', '.', $this->accruals_given_to_hand);
        $this->fines_gibdd = str_replace(',', '.', $this->fines_gibdd);
        $this->another_fines = str_replace(',', '.', $this->another_fines);

        $this->mileage_before_departure = str_replace(' ', '', $this->mileage_before_departure);
        $this->mileage_after_departure = str_replace(' ', '', $this->mileage_after_departure);

        return parent::beforeValidate(); // TODO: Change the autogenerated stub
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'is_visible' => 'Видимость ПЛ',
            'number' => 'Номер',
            'date_of_issue' => 'Дата выдачи',
            'transport_id' => 'Т/с',
            'driver_id' => 'Водитель',

            'trip_comment' => 'Комментарий к рейсу',

            'pre_trip_med_check' => 'Мед. осмотр (предрейсовый)',
            'pre_trip_med_check_time' => 'Дата/Время прохождения мед. осмотра (предрейсовый)',
            'pre_trip_tech_check' => 'Тех. осмотр (предрейсовый)',
            'pre_trip_tech_check_time' => 'Дата/Время прохождения тех. осмотра (предрейсовый)',
            'after_trip_med_check' => 'Мед. осмотр (подрейсовый)',
            'after_trip_med_check_time' => 'Дата/Время прохождения мед. осмотра (подрейсовый)',
            'after_trip_tech_check' => 'Тех. осмотр (подрейсовый)',
            'after_trip_tech_check_time' => 'Дата/Время прохождения тех. осмотра (подрейсовый)',
            'mileage_before_departure' => 'Показания пробега при выезде',
            'mileage_after_departure' => 'Показания пробега при возврате',
            'departure_time' => 'Дата/Время выезда',
            'return_time' => 'Дата/Время возврата',
            'trip_transport_start' => 'Рейс стартовый',
            'trip_transport_end' => 'Рейс обратный',
            'trips_summ' => 'Итого по колонке sum (сумма по рейсам)',
            'created_at' => 'Дата создания',
            'creator_id' => 'Создатель',
            'changes_history' => 'История изменений',

            'waybill_state' => 'ПЛ', // Путевой лист - Принят/Не принят/Не представлен
            'values_fixed_state' => 'ЗН', // Заказ-наряд - Принят/Не принят/Не представлен
            'gsm' => 'ГСМ', // ГСМ (предоставление фотографий, подтверждающих корректность заправки) - Принят/Не принят/Не представлен
            'klpto' => 'Движение КЛПТО', // Нет/Выдан водителю/Сдан водителем/Внесены данные
            'klpto_comment' => 'Примечание к КЛПТО',

            'trip_event1_id' => 'Событие 1',
            'trip_event1_comment' => 'Примечание к событию 1',
            'trip_event2_id' => 'Событие 2',
            'trip_event2_comment' => 'Примечание к событию 2',
            'trip_event3_id' => 'Событие 3',
            'trip_event3_comment' => 'Примечание к событию 3',
            'trip_event4_id' => 'Событие 4',
            'trip_event4_comment' => 'Примечание к событию 4',
            'trip_event5_id' => 'Событие 5',
            'trip_event5_comment' => 'Примечание к событию 5',
            'trip_event6_id' => 'Событие 6',
            'trip_event6_comment' => 'Примечание к событию 6',
            'trip_event7_id' => 'Событие 7',
            'trip_event7_comment' => 'Примечание к событию 7',
            'trip_event8_id' => 'Событие 8',
            'trip_event8_comment' => 'Примечание к событию 8',

            'accepted_expenses_from_revenue' => 'Сумма принятых расходов из выручки',
            'not_accepted_expenses_from_revenue' => 'Сумма непринятых расходов из выручки',
            'incoming_requirements' => 'Входящие требования на общую сумму',
            'accepted_expenses_all_types' => 'Принятые расходы всех типов',
            'camera_val' => 'По камерам',
            'camera_driver_val' => 'Из них указано водителем',
            'camera_eduction' => 'Вычет, руб',
            'camera_no_record' => 'Без записи, руб',
            'camera_no_record_comment' => 'Без записи, расшифровка',

            'hand_over_b1' => 'сдано B1',
            'hand_over_b1_data' => 'Дата (когда сдано B1)',
            'hand_over_b2' => 'сдано B2',
            'hand_over_b2_data' => 'Дата (когда сдано B2)',
            'correct_comment' => 'Расшифровка данных коррекции',

            'accruals_to_issue_for_trip' => 'К выдаче на рейс',
            'accruals_given_to_hand' => 'Выдано на руки',
            'fines_gibdd' => 'Штрафы ГИБДД',
            'fines_gibdd_comment' => 'Комментарий к штрафам ГИБДД',
            'another_fines' => 'Прочие штрафы',
            'another_fines_comment' => 'Комментарий к штрафам ГИБДД',

            'total_net_profit' => 'Чистая прибыль',
            'total_actually_given' => 'Фактически выдано',
            'total_failure_to_pay' => 'Недосдача',
            'total_fines' => 'Штрафы к оплате',

//            'mileage_dif' => 'Пробег на круге',
//            'consumption_per_100_km' => 'Расход топлива на 100 км',

            'set_hand_over_b1_operator_id' => 'Оператор установивший сумму оплату b1',
            'set_hand_over_b1_time' => 'Время установки суммы оплаты b1',
            'set_hand_over_b2_operator_id' => 'Оператор установивший сумму оплату b2',
            'set_hand_over_b2_time' => 'Время установки суммы оплаты b2',
        ];
    }


    public function scenarios()
    {
        $scenarios = parent::scenarios();

        $scenarios['create'] = [
            'number',
            'date_of_issue',
            'transport_id',
            'driver_id',
        ];

        $scenarios['update_result_fields'] = [
            'accepted_expenses_from_revenue',
            'not_accepted_expenses_from_revenue',
            'incoming_requirements',
            'accepted_expenses_all_types',
            'total_net_profit',
            'total_actually_given',
            'total_failure_to_pay',
            'total_fines',
        ];

        return $scenarios;
    }


    public function getTransportExpenses()
    {
        return $this->hasMany(TransportExpenses::className(), ['transport_waybill_id' => 'id']);
    }


    public function getTransport()
    {
        return $this->hasOne(Transport::className(), ['id' => 'transport_id']);
    }

    public function getDriver()
    {
        return $this->hasOne(Driver::className(), ['id' => 'driver_id']);
    }

    public function getTripTransportStart()
    {
        return $this->hasOne(TripTransport::className(), ['id' => 'trip_transport_start']);
    }

    public function getTripTransportEnd()
    {
        return $this->hasOne(TripTransport::className(), ['id' => 'trip_transport_end']);
    }

    public function getTripEvent1()
    {
        return $this->hasOne(TransportWaybillTripEvents::className(), ['id' => 'trip_event1_id']);
    }
    public function getTripEvent2()
    {
        return $this->hasOne(TransportWaybillTripEvents::className(), ['id' => 'trip_event2_id']);
    }
    public function getTripEvent3()
    {
        return $this->hasOne(TransportWaybillTripEvents::className(), ['id' => 'trip_event3_id']);
    }
    public function getTripEvent4()
    {
        return $this->hasOne(TransportWaybillTripEvents::className(), ['id' => 'trip_event4_id']);
    }
    public function getTripEvent5()
    {
        return $this->hasOne(TransportWaybillTripEvents::className(), ['id' => 'trip_event5_id']);
    }
    public function getTripEvent6()
    {
        return $this->hasOne(TransportWaybillTripEvents::className(), ['id' => 'trip_event6_id']);
    }
    public function getTripEvent7()
    {
        return $this->hasOne(TransportWaybillTripEvents::className(), ['id' => 'trip_event7_id']);
    }
    public function getTripEvent8()
    {
        return $this->hasOne(TransportWaybillTripEvents::className(), ['id' => 'trip_event8_id']);
    }

    public function getCreator() {
        return $this->hasOne(User::className(), ['id' => 'creator_id']);
    }
    public function getHandOverB1Operator() {
        return $this->hasOne(User::className(), ['id' => 'set_hand_over_b1_operator_id']);
    }
    public function getHandOverB2Operator() {
        return $this->hasOne(User::className(), ['id' => 'set_hand_over_b2_operator_id']);
    }


    /*
     * Пересчитываем некоторые поля Путевого листа
     */
    public function updateResultFields() {

        $this->scenario = 'update_result_fields';

        // Сумма принятых расходов из выручки - т.е. собираю все суммы расходов у которых отмечен "Тип расходов" - "Из выручки" и expenses_is_taken=true
        $this->accepted_expenses_from_revenue = 0;

        // Сумма непринятых расходов из выручки - т.е. собираю все суммы расходов у которых отмечен "Тип расходов" - "Из выручки" и expenses_is_taken=false
        $this->not_accepted_expenses_from_revenue = 0;

        // Входящие требования на общую сумму - т.е. собираю все суммы расходов у которых отмечен "Тип расходов" - НЕ "Из выручки"
        $this->incoming_requirements = 0;

        // Итого принятые расходы всех типов - собираю сумму всех расходов где expenses_is_taken=true
        $this->accepted_expenses_all_types = 0;

        // Чистая прибыль по ПЛ - Итого по колонке Sum - Итого принятые расходы всех типов - Без записи - К выдаче за рейс
        $this->total_net_profit = 0; // total_net_profit = accepted_expenses_all_types - camera_no_record - accruals_to_issue_for_trip

        // Фактически выдано - Значение из поля Выдано на руки
        $this->total_actually_given = 0; // total_actually_given = accruals_given_to_hand

        // Недосдача = Итого по колонке Sum - Сумма принятых расходов из выручки + Вычет - Без записи - Сдано В1 - Сдано В2
        // Недосдача =  Итого по колонке Sum - Сумма принятых расходов из выручки + Вычет - Без записи - Сдано В1 - Сдано В2
        $this->total_failure_to_pay = 0; // total_failure_to_pay = accepted_expenses_from_revenue + camera_eduction - camera_no_record - hand_over_b1 - hand_over_b2

        // Штрафы к оплате  - Сумма Штрафы ГИБДД + Прочие штрафы
        $this->total_fines = 0;   // total_fines = fines_gibdd + another_fines


        $expenses = $this->transportExpenses;
        //$expenses_types = TransportExpensesTypes::find()->all();
        //$aExpensesTypes = ArrayHelper::map($expenses_types, 'id', 'name');
        $aPaymentMethods = ArrayHelper::map(TransportPaymentMethods::find()->all(), 'id', 'name');
        // payment_method_id

        //echo "aExpensesTypes: <pre>"; print_r($aExpensesTypes); echo "</pre>"; exit;


        foreach($expenses as $tr_expense) {

            if(isset($aPaymentMethods[$tr_expense->payment_method_id]) && $aPaymentMethods[$tr_expense->payment_method_id] == 'Из выручки') {

                if($tr_expense->expenses_is_taken == true) {
                    $this->accepted_expenses_from_revenue += doubleval($tr_expense->price);
                }else {
                    $this->not_accepted_expenses_from_revenue += doubleval($tr_expense->price);
                }

            }else {
                $this->incoming_requirements += doubleval($tr_expense->price);
            }

            if($tr_expense->expenses_is_taken == true) {
                $this->accepted_expenses_all_types += doubleval($tr_expense->price);
            }
        }

        // total_net_profit = accepted_expenses_all_types - camera_no_record - accruals_to_issue_for_trip
        // Чистая прибыль по ПЛ = Итого по колонке Sum - Итого принятые расходы всех типов - Без записи - К выдаче за рейс
        $this->total_net_profit = $this->trips_summ - $this->accepted_expenses_all_types - doubleval($this->camera_no_record) - doubleval($this->accruals_to_issue_for_trip);

        // total_actually_given = accruals_given_to_hand
        $this->total_actually_given = doubleval($this->accruals_given_to_hand);

        // total_failure_to_pay = accepted_expenses_from_revenue + camera_eduction - camera_no_record - hand_over_b1 - hand_over_b2
        // Недосдача = Итого по колонке Sum - Сумма принятых расходов из выручки + Вычет - Без записи - Сдано В1 - Сдано В2
        $this->total_failure_to_pay = $this->trips_summ - $this->accepted_expenses_from_revenue + doubleval($this->camera_eduction) - doubleval($this->camera_no_record) - doubleval($this->hand_over_b1) - doubleval($this->hand_over_b2);

        // total_fines = fines_gibdd + another_fines
        $this->total_fines = doubleval($this->fines_gibdd) + doubleval($this->another_fines);

        if(!$this->save()) {
            throw new ErrorException('Не удалось обновить результаты в путевом листе');
        }

        return true;
    }


    public function getShortTovxrash() {

        $sql = '
            SELECT tr_ex.transport_waybill_id as id, CONCAT(tr_ex.`price`, "-", if(ex_types.id is not null, ex_types.name, "нет"), tr_ex.doc_number) as text
            FROM `transport_expenses` tr_ex
            LEFT JOIN `transport_expenses_types` ex_types ON ex_types.id = tr_ex.expenses_type_id
            WHERE tr_ex.view_group="incoming_payment_requests"
              AND tr_ex.transport_waybill_id='.$this->id.'
            LIMIT 1
        ';
        $result = Yii::$app->db->createCommand($sql)->queryOne();

        return $result['text'];
    }

    public function getTovxrash($use_html = true) {

        $aIncomingTransportExpenses = [];
        $aIncomingTransportExpensesIds = [];
        $transport_expenses = $this->transportExpenses;
        foreach($transport_expenses as $tr_expenses) {
            if($tr_expenses->view_group == 'incoming_payment_requests') {
                $aIncomingTransportExpenses[] = $tr_expenses;
                $aIncomingTransportExpensesIds[$tr_expenses->id] = $tr_expenses->id;
            }
        }


        $aTrExpensesDetailsWorks = [];
        $aTrExpensesDetailsDetails = [];
        $aDetails = TransportExpensesDetailing::find()->where(['expense_id' => $aIncomingTransportExpensesIds])->all();
        foreach($aDetails as $detail) {
            if($detail->type == 'work_services') {
                $aTrExpensesDetailsWorks[$detail->expense_id][$detail->id] = $detail;
            }else {
                $aTrExpensesDetailsDetails[$detail->expense_id][$detail->id] = $detail;
            }
        }

        $aTrExpensesRows = [];
        foreach($aIncomingTransportExpenses as $tr_expenses) {

            // expenses_seller_type_id

            $tr_expenses_string =
                $tr_expenses->price.'**'
                .($tr_expenses->sellerType != null ? $tr_expenses->sellerType->name : '').'**:'
                .($tr_expenses->expenses_type_id > 0 ? $tr_expenses->type->name : 'нет')
                .$tr_expenses->doc_number.'-'; //{сумма расхода1}-{ДО1№ДО1}-

            $aWorks = [];
            if(isset($aTrExpensesDetailsWorks[$tr_expenses->id])) {
                foreach($aTrExpensesDetailsWorks[$tr_expenses->id] as $detail_id => $detail) {
                    $aWorks[] = $detail->price.'-'.$detail->name; // {ценаработы1-наименованиеработы1},
                }
            }
            $tr_expenses_string .= implode(', ', $aWorks). '; ';

            $aDetails = [];
            if(isset($aTrExpensesDetailsDetails[$tr_expenses->id])) {
                foreach($aTrExpensesDetailsDetails[$tr_expenses->id] as $detail_id => $detail) {
                    $aDetails[] = $detail->price.'-'.$detail->name; // {ценаработы1-наименованиеработы1},
                }
            }
            $tr_expenses_string .= implode(', ', $aDetails);

            if($use_html == true) {
                $aTrExpensesRows[] = '<div style="margin-bottom: 8px;">' . $tr_expenses_string . '</div>';
            }else {
                $aTrExpensesRows[] = $tr_expenses_string;
            }
        }

        return implode('', $aTrExpensesRows);
    }

    public function createTypicalExpenses() {

        $transport_expenses_seller_types = TransportExpensesSellerType::find()->all();
        $aTransportExpensesSellerTypes = ArrayHelper::map($transport_expenses_seller_types, 'name', 'id');

        $driver = $this->driver;

        $transport_expenses[0] = new TransportExpenses();
        $transport_expenses[0]->view_group = 'typical_expenses';
        $transport_expenses[0]->expenses_seller_type_id = $aTransportExpensesSellerTypes['АЗС'];// тип продавца
        //$transport_expenses[0]->count = 1;
        $transport_expenses[0]->payment_method_id = 1; // Из выручки (для групп таблиц: типовые и прочие) + платил - водитель + дата оплаты = дата документа
        $transport_expenses[0]->payment_date = $this->date_of_issue; // дата оплаты
        if($driver != null && $driver->user_id > 0) {
            $transport_expenses[0]->transport_expenses_paymenter_id = $driver->user_id;
        }

        $transport_expenses[1] = new TransportExpenses();
        $transport_expenses[1]->view_group = 'typical_expenses';
        $transport_expenses[1]->expenses_seller_type_id = $aTransportExpensesSellerTypes['Мойка'];
        //$transport_expenses[1]->count = 1;
        $transport_expenses[1]->payment_method_id = 1; // Из выручки
        $transport_expenses[1]->payment_date = $this->date_of_issue;
        if($driver != null && $driver->user_id > 0) {
            $transport_expenses[1]->transport_expenses_paymenter_id = $driver->user_id;
        }

        $transport_expenses[2] = new TransportExpenses();
        $transport_expenses[2]->view_group = 'typical_expenses';
        $transport_expenses[2]->expenses_seller_type_id = $aTransportExpensesSellerTypes['Стоянка'];
        //$transport_expenses[2]->count = 1;
        $transport_expenses[2]->payment_method_id = 1; // Из выручки
        $transport_expenses[2]->payment_date = $this->date_of_issue;
        if($driver != null && $driver->user_id > 0) {
            $transport_expenses[2]->transport_expenses_paymenter_id = $driver->user_id;
        }

        foreach($transport_expenses as $tr_expense) {
            $tr_expense->transport_waybill_id = $this->id;
            if(!$tr_expense->save(false)) {
                throw new \ErrorException('Не удалось создать пустой расход');
            }
        }

        return true;
    }
}
