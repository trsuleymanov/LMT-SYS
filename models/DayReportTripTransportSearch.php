<?php

namespace app\models;

use Yii;
use yii\base\ErrorException;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\BaseDataProvider;


/*
class DayReportRoundTransport {

    public $transport_id = null;
    public $id_1 = null;
    public $transport_round_is_completed_1 = null;
    //public $date;
    //public $direction_id;
    public $direction_name_1 = null;
    //public $trip_id;
    public $trip_name_1 = null;
    public $trip_date_sended_1 = null;
    //public $trip_sender_id;
    public $trip_sender_fio_1 = null;
    //public $trip_transport_id;
    //public $transport_id;
    public $transport_car_reg_1 = null;
    public $transport_model_1 = null;
    public $transport_places_count_1 = null;
    public $transport_date_sended_1 = null;
    //public $transport_sender_id;
    public $transport_sender_fio_1 = null;
    //public $transport_round_is_completed;
    //public $transport_round_completing_reason_id;
    //public $driver_id;
    public $driver_fio_1 = null;
    public $places_count_sent_1 = null;
    public $child_count_sent_1 = null;
    public $student_count_sent_1 = null;
    public $prize_trip_count_sent_1 = null;
    public $bag_count_sent_1 = null;
    public $suitcase_count_sent_1 = null;
    public $oversized_count_sent_1 = null;
    public $is_not_places_count_sent_1 = null;
    public $proceeds_1 = null;

    public $id_2 = null;
    public $transport_round_is_completed_2 = null;
    //public $date;
    //public $direction_id;
    public $direction_name_2 = null;
    //public $trip_id;
    public $trip_name_2 = null;
    public $trip_date_sended_2 = null;
    //public $trip_sender_id;
    public $trip_sender_fio_2;
    //public $trip_transport_id;
    //public $transport_id;
    public $transport_car_reg_2 = null;
    public $transport_model_2 = null;
    public $transport_places_count_2 = null;
    public $transport_date_sended_2 = null;
    //public $transport_sender_id;
    public $transport_sender_fio_2 = null;
    //public $transport_round_is_completed;
    //public $transport_round_completing_reason_id;
    //public $driver_id;
    public $driver_fio_2 = null;
    public $places_count_sent_2 = null;
    public $child_count_sent_2 = null;
    public $student_count_sent_2 = null;
    public $prize_trip_count_sent_2 = null;
    public $bag_count_sent_2 = null;
    public $suitcase_count_sent_2 = null;
    public $oversized_count_sent_2 = null;
    public $is_not_places_count_sent_2 = null;
    public $proceeds_2 = null;

    public $total_proceeds = null;
}


class DayReportDataProvider extends BaseDataProvider{

    public $params = [];
    public $date = [];
    private $arData = null;


    public function __construct($params, $date)
    {
        $this->params = $params;
        $this->date = $date;

        $this->prepareModels();
    }


    // возвращается количество записей

    protected function prepareModels()
    {
        if($this->arData != null) {
            return $this->arData;
        }

        // нужно получить строки где каждая строка это отправляемая машина на круге и она же возвращаемая
        // одним запросом получаю данные всех строк за сегодня
        $dayReportRoundTransports = [];

        $dayReportTripTransports = DayReportTripTransport::find()
                ->where(['date' => strtotime($this->date)])
                ->orderBy(['trip_date_sended' => SORT_ASC])
                ->all();

        foreach($dayReportTripTransports as $day_report_transport) {

            // записывается в начало в DayReportRoundTransport начало круга
            //if($day_report_transport->transport_round_is_completed == 0) {
            if($day_report_transport->direction_id == 1) {

                // 1-го круга еще не существует -> заполняем начало 1-й круг
                if(!isset($dayReportRoundTransports[$day_report_transport->transport_id.'_1'])) {

                    $transportRound = new DayReportRoundTransport();
                    $dayReportRoundTransports[$day_report_transport->transport_id.'_1']
                        = self::_insertDataToTransportRound($transportRound, 1, $day_report_transport);

                }elseif(// уже существует 1-й круг, но данных начала 1-го круга нет -> заполняем данные начала 1-го круга
                    isset($dayReportRoundTransports[$day_report_transport->transport_id.'_1'])
                    && $dayReportRoundTransports[$day_report_transport->transport_id.'_1']->id_1 == null
                ) {

                    $dayReportRoundTransports[$day_report_transport->transport_id.'_1']
                        = self::_insertDataToTransportRound($dayReportRoundTransports[$day_report_transport->transport_id.'_1'], 1, $day_report_transport);

                }elseif(
                    isset($dayReportRoundTransports[$day_report_transport->transport_id.'_1'])
                    && $dayReportRoundTransports[$day_report_transport->transport_id.'_1']->id_1 > 0
                ) {// уже существует 1-й круг и заполнены данные начала круга -> заполняем начало 2-го круга

                    $transportRound = new DayReportRoundTransport();
                    $dayReportRoundTransports[$day_report_transport->transport_id.'_2']
                        = self::_insertDataToTransportRound($transportRound, 1, $day_report_transport);

                }else {
                    throw new ErrorException('Ошибка разбора данных отчета дня. Возможно существует т/с отправленная на 3-й круг.');
                }


            }else { // движение машины в обратку

                // круга еще не существует -> заполняем "обратку" 1-го круга
                if(!isset($dayReportRoundTransports[$day_report_transport->transport_id.'_1'])) {

                    $transportRound = new DayReportRoundTransport();
                    $dayReportRoundTransports[$day_report_transport->transport_id.'_1']
                        = self::_insertDataToTransportRound($transportRound, 2, $day_report_transport);

                }elseif(// уже существует 1-й круг, но данных "обратки" 1-го круга нет -> заполняем "обратку" 1-го круга
                    isset($dayReportRoundTransports[$day_report_transport->transport_id.'_1'])
                    && $dayReportRoundTransports[$day_report_transport->transport_id.'_1']->id_2 == null
                ) {

                    $dayReportRoundTransports[$day_report_transport->transport_id.'_1']
                        = self::_insertDataToTransportRound($dayReportRoundTransports[$day_report_transport->transport_id.'_1'], 2, $day_report_transport);


                }elseif(// уже существует 1-й круг и есть данные "обратки" 1-го круга -> заполняем "обратку" 2-го круга
                    isset($dayReportRoundTransports[$day_report_transport->transport_id.'_1'])
                    && $dayReportRoundTransports[$day_report_transport->transport_id.'_1']->id_2 > 0
                ) {

                    $dayReportRoundTransports[$day_report_transport->transport_id.'_2']
                        = self::_insertDataToTransportRound($dayReportRoundTransports[$day_report_transport->transport_id.'_2'], 2, $day_report_transport);

                }else {
                    throw new ErrorException('Ошибка разбора данных отчета дня. Возможно существует т/с завершающая 3-й круг за день.');
                }

            }
        }

        foreach($dayReportRoundTransports as $key => $day_report_transport) {
            $dayReportRoundTransports[$key]->total_proceeds = $dayReportRoundTransports[$key]->proceeds_1 + $dayReportRoundTransports[$key]->proceeds_2;
        }

        //echo "dayReportRoundTransports:<pre>"; print_r($dayReportRoundTransports); echo "</pre>";
        $this->arData = $dayReportRoundTransports;

        return $this->arData;
    }

    // возвращается массив ключей "объекта"

    private static function _insertDataToTransportRound(DayReportRoundTransport $transportRound, $part, $day_report_transport) {

        if($part == 1) {

            $transportRound->transport_id = $day_report_transport->transport_id;
            $transportRound->id_1 = $day_report_transport->id;
            $transportRound->transport_round_is_completed_1 = $day_report_transport->transport_round_is_completed;
            $transportRound->direction_name_1 = $day_report_transport->direction_name;
            $transportRound->trip_name_1 = $day_report_transport->trip_name;
            $transportRound->trip_date_sended_1 = $day_report_transport->trip_date_sended;
            $transportRound->trip_sender_fio_1 = $day_report_transport->trip_sender_fio;
            $transportRound->transport_car_reg_1 = $day_report_transport->transport_car_reg;
            $transportRound->transport_model_1 = $day_report_transport->transport_model;
            $transportRound->transport_places_count_1 = $day_report_transport->transport_places_count;
            $transportRound->transport_date_sended_1 = $day_report_transport->transport_date_sended;
            $transportRound->transport_sender_fio_1 = $day_report_transport->transport_sender_fio;
            $transportRound->driver_fio_1 = $day_report_transport->driver_fio;
            $transportRound->places_count_sent_1 = $day_report_transport->places_count_sent;
            $transportRound->child_count_sent_1 = $day_report_transport->child_count_sent;
            $transportRound->student_count_sent_1 = $day_report_transport->student_count_sent;
            $transportRound->prize_trip_count_sent_1 = $day_report_transport->prize_trip_count_sent;
            $transportRound->bag_count_sent_1 = $day_report_transport->bag_count_sent;
            $transportRound->suitcase_count_sent_1 = $day_report_transport->suitcase_count_sent;
            $transportRound->oversized_count_sent_1 = $day_report_transport->oversized_count_sent;
            $transportRound->is_not_places_count_sent_1 = $day_report_transport->is_not_places_count_sent;
            $transportRound->proceeds_1 = $day_report_transport->proceeds;

        }else { //part = 2

            $transportRound->transport_id = $day_report_transport->transport_id;
            $transportRound->id_2 = $day_report_transport->id;
            $transportRound->transport_round_is_completed_2 = $day_report_transport->transport_round_is_completed;
            $transportRound->direction_name_2 = $day_report_transport->direction_name;
            $transportRound->trip_name_2 = $day_report_transport->trip_name;
            $transportRound->trip_date_sended_2 = $day_report_transport->trip_date_sended;
            $transportRound->trip_sender_fio_2 = $day_report_transport->trip_sender_fio;
            $transportRound->transport_car_reg_2 = $day_report_transport->transport_car_reg;
            $transportRound->transport_model_2 = $day_report_transport->transport_model;
            $transportRound->transport_places_count_2 = $day_report_transport->transport_places_count;
            $transportRound->transport_date_sended_2 = $day_report_transport->transport_date_sended;
            $transportRound->transport_sender_fio_2 = $day_report_transport->transport_sender_fio;
            $transportRound->driver_fio_2 = $day_report_transport->driver_fio;
            $transportRound->places_count_sent_2 = $day_report_transport->places_count_sent;
            $transportRound->child_count_sent_2 = $day_report_transport->child_count_sent;
            $transportRound->student_count_sent_2 = $day_report_transport->student_count_sent;
            $transportRound->prize_trip_count_sent_2 = $day_report_transport->prize_trip_count_sent;
            $transportRound->bag_count_sent_2 = $day_report_transport->bag_count_sent;
            $transportRound->suitcase_count_sent_2 = $day_report_transport->suitcase_count_sent;
            $transportRound->oversized_count_sent_2 = $day_report_transport->oversized_count_sent;
            $transportRound->is_not_places_count_sent_2 = $day_report_transport->is_not_places_count_sent;
            $transportRound->proceeds_2 = $day_report_transport->proceeds;
        }

        return $transportRound;
    }


    //@param $part - это 1 или 2 - т.е. это начало круга или "обратка"
    public function sortBy($param) {

        if($this->arData != null) {
            $mod_param = (strpos($param, '-') !== false ? substr($param, 1) : $param);

            $newDayReportRoundTransport = [];
            foreach($this->arData as $key => $dayReportRoundTransport) {
                $newDayReportRoundTransport[$dayReportRoundTransport->$mod_param][] = $dayReportRoundTransport;
            }

            if(strpos($param, '-') !== false) {
                ksort($newDayReportRoundTransport);
            }else {
                krsort($newDayReportRoundTransport);
            }

            $dayReportRoundTransports = [];
            foreach($newDayReportRoundTransport as $param => $keyDayReportRoundTransports) {
                foreach($keyDayReportRoundTransports as $key => $dayReportRoundTransport) {
                    $dayReportRoundTransports[] = $dayReportRoundTransport;
                }
            }

            $this->arData = $dayReportRoundTransports;

        }else {
            return null;
        }
    }


    // на выходе получается массив, где каждый элемент массива это массив(объект) с данными

    protected function prepareTotalCount()
    {
        return count($this->arData);
    }

    protected function prepareKeys($models)
    {
        $key_field = 'transport_id';

        $keys = [];
        foreach($models as $model) {
            if(gettype($model) == "object") {
                $keys[] = $model->$key_field;
            }else {
                $keys[] = $model[$key_field];
            }
        }

        return $keys;
    }
}
*/

/**
 * DayReportTripTransportSearch represents the model behind the search form about `app\models\DayReportTripTransport`.
 */
class DayReportTripTransportSearch extends DayReportTripTransport
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'date', 'direction_id', 'trip_date_sended', 'trip_sender_id', 'trip_transport_id',
                'transport_sender_id', 'driver_id', 'places_count_sent',
                'child_count_sent', 'student_count_sent', 'prize_trip_count_sent', 'bag_count_sent',
                'suitcase_count_sent', 'oversized_count_sent', 'is_not_places_count_sent', 'no_record',
                'airport_places_count_sent'
            ], 'integer'],

            [['direction_name', 'trip_name', 'transport_car_reg', 'transport_model', 'driver_fio',
                'trip_sender_fio', 'transport_places_count', 'transport_sender_fio',
                'transport_round_is_completed', 'transport_round_completing_reason_id',
                'transport_date_sended', 'airport_count_sent', 'fix_price_count_sent', 'transport_id',
                'trip_id'
            ], 'safe'],

            [['proceeds', 'paid_summ'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function searchDriverAccounting($params) {

        $query = DayReportTripTransport::find()
            ->leftJoin('trip', '`trip`.`id` = `day_report_trip_transport`.`trip_id`')
            ->select([
                '`day_report_trip_transport`.*, CONCAT('.Trip::tableName().'.date,'.Trip::tableName().'.end_time) AS trip_end_time',
            ]);

        // сортировка по умолчанию не устанавливается!!!
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                //'defaultOrder' => ['CONCAT('.Trip::tableName().'.date'.','.Trip::tableName().'.end_time'.')' => SORT_DESC]
                //'defaultOrder' => ['`trip_end_time`' => SORT_DESC],
                //'defaultOrder' => ['direction_id' => SORT_DESC],
            ],
            'pagination' => [
                'pageSize' => Yii::$app->session->get('table-rows', 20)
            ],
        ]);


        $dataProvider->setSort([
            'attributes' => array_merge($dataProvider->getSort()->attributes, [
                //'defaultOrder' => ['trip_end_time' => SORT_DESC],
                'trip_id' => [
//                    'asc' => ['CONCAT('.Trip::tableName().'.date'.','.Trip::tableName().'.end_time'.')' => SORT_ASC],
//                    'desc' => ['CONCAT('.Trip::tableName().'.date'.','.Trip::tableName().'.end_time'.')' => SORT_DESC]
                    'asc' => ['trip_end_time' => SORT_ASC],
                    'desc' => ['trip_end_time' => SORT_DESC],
                ],
            ])
        ]);

//        $dataProvider->setSort([
//            'attributes' => array_merge($dataProvider->getSort()->attributes, [
//                //'defaultOrder' => ['transport_date_sended' => SORT_DESC],
//                'defaultOrder' => [Trip::tableName().'.date' => SORT_DESC],
//            ])
//        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }


        $query->andFilterWhere([
//            'id' => $this->id,
//            //'date' => $this->date,
//            'direction_id' => $this->direction_id,
//            'trip_id' => $this->trip_id,
//            'trip_date_sended' => $this->trip_date_sended,
//            'trip_sender_id' => $this->trip_sender_id,
//            'trip_transport_id' => $this->trip_transport_id,
            'transport_id' => $this->transport_id,
//            'transport_date_sended' => $this->transport_date_sended,
//            'transport_sender_id' => $this->transport_sender_id,
            'transport_places_count' => $this->transport_places_count,
//            'transport_round_is_completed' => $this->transport_round_is_completed,
//            'transport_round_completing_reason_id' => $this->transport_round_completing_reason_id,
            'driver_id' => $this->driver_id,
            //'informer_office_id' => $this->informer_office_id,
            'places_count_sent' => $this->places_count_sent,
            'student_count_sent' => $this->student_count_sent,
            'child_count_sent' => $this->child_count_sent,
            'prize_trip_count_sent' => $this->prize_trip_count_sent,
            'airport_count_sent' => $this->airport_count_sent,
            'airport_places_count_sent' => $this->airport_places_count_sent,
            'fix_price_count_sent' => $this->fix_price_count_sent,
//            'bag_count_sent' => $this->bag_count_sent,
//            'suitcase_count_sent' => $this->suitcase_count_sent,
//            'oversized_count_sent' => $this->oversized_count_sent,
            'is_not_places_count_sent' => $this->is_not_places_count_sent,
            'proceeds' => $this->proceeds,
            'paid_summ' => $this->paid_summ,
        ]);


        $query
//            ->andFilterWhere(['like', 'direction_name', $this->direction_name])
//            ->andFilterWhere(['like', 'trip_name', $this->trip_name])
//            ->andFilterWhere(['like', 'trip_sender_fio', $this->trip_sender_fio])
//            ->andFilterWhere(['like', 'transport_car_reg', $this->transport_car_reg])
//            ->andFilterWhere(['like', 'transport_model', $this->transport_model])
//            ->andFilterWhere(['like', 'transport_sender_fio', $this->transport_sender_fio])
            ->andFilterWhere(['like', 'driver_fio', $this->driver_fio]);

        if (!is_null($this->trip_id) && strpos($this->trip_id, '-') !== false) {
            list($dateStart, $dateEnd) = explode('-', $this->trip_id);
            $query->andFilterWhere([
                'BETWEEN', Trip::tableName() . '.date', strtotime($dateStart), strtotime($dateEnd) + 3600 * 24 - 1
            ]);
        }

        if (!is_null($this->transport_date_sended) && strpos($this->transport_date_sended, '-') !== false) {
            list($dateStart, $dateEnd) = explode('-', $this->transport_date_sended);
            $query->andFilterWhere([
                'BETWEEN', $this->tableName() . '.transport_date_sended', strtotime($dateStart), strtotime($dateEnd) + 3600 * 24 - 1
            ]);
        }

        // DayReportTripTransportSearch[transport_id]=Фиат
        //if(isset($this->transport_id) && !empty($this->transport_id)) {
        //$start = strpos($this->transport_id, '(');
        //$transport_model = substr($this->transport_id, 0, $start);
        //}


        return $dataProvider;
    }

}
