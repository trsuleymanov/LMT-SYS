<?php

namespace app\models;

use Yii;
use app\models\City;
use app\models\Formula;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "transport".
 *
 * @property integer $id
 * @property string $model
 * @property string $sh_model
 * @property string $car_reg
 * @property integer $places_count
 * @property string $color
 * @property integer $created_at
 * @property integer $updated_at
 */
class Transport extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'transport';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['model', 'sh_model', 'places_count', 'car_reg', 'base_city_id'], 'required'],
            [['places_count', 'created_at', 'base_city_id', 'formula_id', 'created_at', 'updated_at', 'car_reg'], 'integer'],
            [['model', 'color'], 'string', 'max' => 50],
            [['sh_model', /*'car_reg'*/], 'string', 'max' => 20],
            [['active', 'regular', 'accountability'], 'boolean']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'active' => 'Т/с активно',
            'accountability' => 'Подотчетность',
            'regular' => 'Регулярное т/с',
            'model' => 'Марка',
            'sh_model' => 'Сокращенное название',
            'car_reg' => 'Гос. номер',
            'places_count' => 'Количество мест',
            'color' => 'Цвет',
            'base_city_id' => 'Город базирования',
            'formula_id' => 'Формула расчета процента',
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

    public function getBaseCity()
    {
        return $this->hasOne(City::className(), ['id' => 'base_city_id']);
    }

    public function getFormula()
    {
        return $this->hasOne(Formula::className(), ['id' => 'formula_id']);
    }

    public function getName() {
        return $this->model.' (рег.номер '.$this->car_reg.')';
    }

    public function getName2() {
        return $this->model.' ('.$this->car_reg.')';
    }

    public function getName3() {
        return $this->sh_model.' ('.$this->car_reg.')';
    }

    public function getName4() {
        return $this->car_reg . ' '. $this->color . ' ' . $this->model;
    }

    public function getName5() {
        return $this->sh_model.' ('.$this->car_reg.') '. $this->color;
    }


    public function getCar_reg_places_count() {
        //return $this->car_reg.' - '.$this->places_count;
        return $this->sh_model.' ('.$this->car_reg . ') '. $this->color . ' - ' . $this->places_count;
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

    public static function getEfficiencyData($date) {

        $transports = Transport::find()->where(['regular' => 1])->all();

        // по каждой машине:
        //   - нахожу кол-во завершенных кругов за последнии 30 дней. И делю число на 30. Сортирую по полученному числу все машины.
        //   - за последние 11 дней для каждого дня:
        //      - нахожу круги. Для каждого круга извлекаю:
        //          - имя водителя
        //          - выручка на круге
        //          - отправки круга: направления имя, рейса имя,
        //          - завершения круга: направления имя, рейса имя,

        $unixtime_date = strtotime($date);

        // рассчитаем коэффециенты эффективности т/с
        $sql = '
          SELECT circle.id, circle.transport_id, circle.total_proceeds
          FROM `'.DayReportTransportCircle::tableName().'` circle
          LEFT JOIN `'.DayReportTripTransport::tableName().'` notbase_tr
          ON notbase_tr.id = circle.notbase_city_day_report_id
          WHERE
            circle.transport_id IN('.implode(',', ArrayHelper::map($transports, 'id', 'id')).')
            AND circle.state = 1
            AND notbase_tr.`date` >='.($unixtime_date - 30*86400).' AND notbase_tr.`date` <= '.$unixtime_date;

        $aResults = \Yii::$app->db->createCommand($sql)->queryAll();

        $aTransportCirclesId = [];
        $aTransportsCountCircles = [];
        foreach($aResults as $aResult) {
            if(isset($aTransportsCountCircles[$aResult['transport_id']])) {
                $aTransportsCountCircles[$aResult['transport_id']]++;
            }else {
                $aTransportsCountCircles[$aResult['transport_id']] = 1;
            }
            $aTransportCirclesId[$aResult['transport_id']][$aResult['id']] = $aResult['total_proceeds'];
        }
        arsort($aTransportsCountCircles);

        // для каждого цикла транспортов для каждого дня рассчитаем результат по формуле привязанной к машине и
        // рассчитаем среднее значения результата за 30 дней для каждой машины
        //echo "aTransportCirclesId:<pre>"; print_r($aTransportCirclesId); echo "</pre>";

        // все формулы привязанные ко всем машинам
        $formulas = Formula::find()->where(['id' => ArrayHelper::map($transports, 'formula_id', 'formula_id')])->all();
        $aFormulas = ArrayHelper::index($formulas, 'id');
        $aTransportsFormula = [];
        foreach($transports as $transport) {
            if(isset($aFormulas[$transport->formula_id])) {
                $aTransportsFormula[$transport->id] = $aFormulas[$transport->formula_id];
            }
        }

        $aTransportsFormulaResults = [];
        $aTransportsFormulaResultsCount = [];
        foreach($aTransportCirclesId as $transport_id => $aCircleTotalProceeds) {
            if(isset($aTransportsFormula[$transport_id])) {
                $formula = $aTransportsFormula[$transport_id];
                $circle_total_proceeds = current($aCircleTotalProceeds);
                $circle_formula_result = $formula->getResult($circle_total_proceeds);
                if(isset($aTransportsFormulaResults[$transport_id])) {
                    $aTransportsFormulaResults[$transport_id] += $circle_formula_result;
                    $aTransportsFormulaResultsCount[$transport_id] += 1;
                }else {
                    $aTransportsFormulaResults[$transport_id] = $circle_formula_result;
                    $aTransportsFormulaResultsCount[$transport_id] = 1;
                }
            }
        }

        foreach($aTransportsFormulaResults as $transport_id => $circle_formula_sum_results) {
            $aTransportsFormulaResults[$transport_id] = $circle_formula_sum_results/$aTransportsFormulaResultsCount[$transport_id];
        }

        //echo "aTransportsFormulaResults:<pre>"; print_r($aTransportsFormulaResults); echo "</pre>";



        // найдем законченные циклы машин за последние 11 дней
        $sql = '
          SELECT circle.id, notbase_tr.`date`, circle.transport_id, circle.total_proceeds,
              base_tr.trip_name as base_trip, notbase_tr.trip_name as notbase_trip,
              base_tr.direction_name as base_direction, notbase_tr.direction_name as notbase_direction,
              base_driver.fio as base_driver_fio, notbase_driver.fio as notbase_driver_fio
          FROM `'.DayReportTransportCircle::tableName().'` circle
          LEFT JOIN `'.DayReportTripTransport::tableName().'` base_tr
          ON base_tr.id = circle.base_city_day_report_id
          LEFT JOIN `'.DayReportTripTransport::tableName().'` notbase_tr
          ON notbase_tr.id = circle.notbase_city_day_report_id
          LEFT JOIN `'.TripTransport::tableName().'` base_trip_transport
          ON base_trip_transport.id = base_tr.trip_transport_id
          LEFT JOIN `'.TripTransport::tableName().'` notbase_trip_transport
          ON notbase_trip_transport.id = notbase_tr.trip_transport_id
          LEFT JOIN `'.Driver::tableName().'` base_driver
          ON base_driver.id = base_trip_transport.driver_id
          LEFT JOIN `'.Driver::tableName().'` notbase_driver
          ON notbase_driver.id = notbase_trip_transport.driver_id
          WHERE
            circle.transport_id IN('.implode(',', ArrayHelper::map($transports, 'id', 'id')).')
            AND circle.state = 1
            AND notbase_tr.`date` >='.($unixtime_date - 30*86400).' AND notbase_tr.`date` <= '.$unixtime_date;
        $aResults = \Yii::$app->db->createCommand($sql)->queryAll();


        // разложим цикли по дням
        $aTransportsData = [];
        foreach($aResults as $aResult) {
            $aTransportsData[$aResult['transport_id']][$aResult['date']][] = $aResult;
        }
        // отсортируем по дням
//        $aDates = Transport::getEfficiencyDates($date);
//        foreach($aTransportsData as $transport_id => $aTransportData) {
//            $aNewTransportData = [];
//            foreach($aDates as $key => $date) {
//                if(isset($aTransportData[$date])) {
//                    $aNewTransportData[$date] = $aTransportData[$date];
//                }
//            }
//            $aTransportsData[$transport_id] = $aTransportData;
//        }


        // соберем/сгруппируем данные финальные и отсортируем по эффективности
        $aNewTransportsData = [];
        $aTransports = ArrayHelper::index($transports, 'id');
        $aEfficiencies = [];
        foreach($aTransports as $transport_id => $transport) {

            $efficiency =  (isset($aTransportsCountCircles[$transport_id]) ? round($aTransportsCountCircles[$transport_id]/30, 2) : 0);

            $aNewTransportsData[] = [
                'transport_id' => $transport_id,
                'transport' => $transport->sh_model.' '.$transport->car_reg,
                'efficiency' => $efficiency,
                'average_30_formula_result' => (isset($aTransportsFormulaResults[$transport_id]) ? $aTransportsFormulaResults[$transport_id] : 0),
                'dates' => (isset($aTransportsData[$transport_id]) ? $aTransportsData[$transport_id] : [])
            ];
            $aEfficiencies[] = $efficiency;
        }
        arsort($aEfficiencies);

        $aTransportsData = [];
        foreach($aEfficiencies as $key => $efficiency) {
            $aTransportsData[] = $aNewTransportsData[$key];
        }

        //echo "aTransportsData:<pre>"; print_r($aTransportsData); echo "</pre>";

        foreach($aTransportsData as $key => $aTransportData) {
            $average_total_proceeds = 0;
            $circles_count = 0;
            if(count($aTransportData['dates']) > 0) {
                foreach ($aTransportData['dates'] as $aTransportDayCircles) {
                    foreach($aTransportDayCircles as $aTransportDayCircle) {
                        $average_total_proceeds += $aTransportDayCircle['total_proceeds'];
                        $circles_count++;
                    }
                }
                $average_total_proceeds =  round($average_total_proceeds/$circles_count, 2);
            }
            $aTransportsData[$key]['average_total_proceeds'] = $average_total_proceeds;
        }


        return $aTransportsData;
    }


    public static function getEfficiencyDates($date) {

        $aDates = [];
        $aDates[10] = strtotime($date);
        for ($i = 9; $i >= 0; $i--) {
            $aDates[$i] = $aDates[10] - 86400*(10 - $i);
        }
        ksort($aDates);

        return $aDates;
    }


    public static function getEfficiencyDayCell($aData, $data) {

        $html = '';
        if(isset($aData['dates'][$data])) {
            $aCircles = $aData['dates'][$data];

            //echo "aCircles:<pre>"; print_r($aCircles); echo "</pre>";

            foreach($aCircles as $aCircle) {
                if(empty($aCircle['base_trip'])) {
                    $html .=
                        '<div style="white-space: nowrap;">' . $aCircle['notbase_trip'] . ' ' . $aCircle['notbase_direction'] . ' - круг завершен</div>';
                    $html .= '<div style="white-space: nowrap;">'.$aCircle['notbase_driver_fio'].'</div>';
                }else {
                    $html .=
                        '<div style="white-space: nowrap;">' . $aCircle['base_trip'] . ' ' . $aCircle['base_direction'] . ', ' . $aCircle['notbase_trip'] . ' ' . $aCircle['notbase_direction'] . ', </div>';
                    $html .= '<div style="white-space: nowrap;">'.(mb_strlen($aCircle['base_driver_fio'], 'UTF-8') > 12 ? trim(mb_substr($aCircle['base_driver_fio'], 0, 12, 'UTF-8')).'..' : $aCircle['base_driver_fio']).'</div>';
                }
                $html .= '<div style="white-space: nowrap;">'.$aCircle['total_proceeds'].'</div>';
            }
        }

        return $html;
    }
}
