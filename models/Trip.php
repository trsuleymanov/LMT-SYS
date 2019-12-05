<?php

namespace app\models;

use app\components\Helper;
use app\models\core\QueryWithSave;
use app\widgets\IncomingOrdersWidget;
use Yii;
use app\models\Direction;
use app\models\Order;
use app\models\OrderStatus;
use app\models\ScheduleTrip;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use app\models\Transport;
use app\models\Schedule;
use app\models\TripTransport;
use yii\web\ForbiddenHttpException;
use yii\base\ErrorException;
use app\models\DispatcherAccounting;

/**
 * Рейсы
 *
 *  !чтение данных по рейсам не должно происходить напрямую через Trip_::find(), а должно происходить
 *  только через функции текущей модели
 *
 * @property integer $id
 * @property string $name
 * @property integer $date
 * @property integer $direction_id
 * @property string $start_time
 * @property string $mid_time
 * @property string $end_time
 * @property integer $created_at
 * @property integer $updated_at
 */
class Trip extends \yii\db\ActiveRecord
{
	public $point_interval = 20; // интервал между точками

	private $_tripTransportList = null;

	public static function getTrips($unixdate, $direction_id)
	{
		return self::getTripsQuery($unixdate, $direction_id)->all();
	}


	public static function getTripsQuery($unixdate, $direction_id) {

		$unixdate = intval($unixdate);
		$correct_unixdate = strtotime(date('d.m.Y', $unixdate));

		$trip = self::find()
			->where(['direction_id' => $direction_id])
			->andWhere(['date' => $correct_unixdate])
			->one();
		if($trip == null) {
			self::createStandartTripList($correct_unixdate, $direction_id);
		}
		return self::find()
			->where(['direction_id' => $direction_id])
			->andWhere(['date' => $correct_unixdate])
			->orderBy(['end_time'=>SORT_ASC]);

//		return (new Query())
//			->from(self::tableName())
//			->leftJoin(TripTransport::tableName(), TripTransport::tableName().'.trip_id='.self::tableName().'.id')
//			->where(['direction_id' => $direction_id])
//			->andWhere(['date' => $correct_unixdate])
//			->orderBy(['end_time' => SORT_ASC]);
	}

	public static function createStandartTripList($unixdate, $direction_id)
	{
		$schedule = Schedule::find()
			->where(['<=', 'start_date', $unixdate])
			->andWhere(['direction_id' => $direction_id])
			->orderBy(['start_date' => SORT_DESC])
			->one();
		if($schedule == null) {
			return [];
		}
		$schedule_trips = $schedule->scheduleTrips;
		if(count($schedule_trips) == 0) {
			throw new ForbiddenHttpException('Рейсов для расписания "'.$schedule->name.'" не найдено');
		}


		$trips = [];
		foreach($schedule_trips as $schedule_trip) {
			$trip = new Trip();
			$trip->name = $schedule_trip->name;
			$trip->direction_id = $direction_id;
			$trip->start_time = $schedule_trip->start_time;
			$trip->mid_time = $schedule_trip->mid_time;
			$trip->end_time = $schedule_trip->end_time;

			// получаем дату+время в формате unixtime
			$trip->date = strtotime(date('d.m.Y', $unixdate));
			$trip->created_at = time();

			$trips[] = $trip;
		}

		$rows = ArrayHelper::getColumn($trips, 'attributes');

		return Yii::$app->db->createCommand()->batchInsert(self::tableName(), $trip->attributes(), $rows)->execute();
	}

	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'trip';
	}

	public static function changeTimesOfTrip($trip_id, $startTime, $midTime=null, $endTime=null){

		$trip = self::find()->where(['id' => $trip_id])->one();

		//$trip_times = self::getTimesOfTrip($trip_id,$startTime, $midTime, $endTime);
		$trip_times = $trip->getTimesOfTrip($startTime, $midTime, $endTime);

		$trip->start_time = $trip_times['start_time'];
		$trip->mid_time = $trip_times['mid_time'];
		$trip->end_time = $trip_times['end_time'];

		if(!$trip->save()){
			return null;
		} else {
			return $trip;
		}
	}


	public static function _getDriversList($drivers, $selected_transport_id = 0, $selected_driver_id = 0) {

		if($selected_transport_id > 0){

			$preliminary_result = [];
			if($selected_driver_id > 0){
				$preliminary_result[] = Driver::findOne($selected_driver_id);// получение объекта "выбранного водителя"

				// удаление выбранного водителя из списка незадействованных водителей
				foreach($drivers as $k => $item){
					if($item->id == $selected_driver_id){
						unset($drivers[$k]);
						break;
					}
				}
			}

			$first_second_drivers_query = Driver::find()->where(['active' => 1])
				->andWhere([
					'OR',
					['primary_transport_id' => $selected_transport_id],
					['secondary_transport_id' => $selected_transport_id],
				]);
			if($selected_driver_id > 0){
				$first_second_drivers_query = $first_second_drivers_query->andWhere(['!=', 'id', $selected_driver_id]);
			}
			$first_second_drivers = $first_second_drivers_query->orderBy(['fio'=>'ASC'])->all();

			foreach($first_second_drivers as $first_second_driver) {
				foreach($drivers as $k => $item){
					if($item->id == $first_second_driver->id){
						unset($drivers[$k]);
						break;
					}
				}
			}

			$total_result = [];
			$total_result = array_merge($total_result, $first_second_drivers);
			$total_result = array_merge($total_result, $drivers);

		} else {
			$total_result = $drivers;
		}

		return $total_result;
	}


	public static function getEmptyDriversOnDirectionOfDate($trip_id, $selected_transport_id = null, $selected_driver_id = null)
	{
		$trip = self::findOne($trip_id);
		if($trip == null) {
			throw new ForbiddenHttpException('Рейс не найден');
		}

		$trip_transports = $trip->tripTransports;

		// список водителей, не задействованных пока на этом дне-направлении
		$drivers_query = Driver::find()->where(['active' => 1]);
		if(count($trip_transports) > 0) {
			$drivers_query = $drivers_query
				->andWhere('id NOT IN ('.implode(',',ArrayHelper::map($trip_transports, 'driver_id', 'driver_id')).')');
		}
		$drivers = $drivers_query->orderBy(['fio'=>'ASC'])->all();


//		if($selected_transport_id > 0){
//
//			$preliminary_result = [];
//			if($selected_driver_id){
//				$preliminary_result[] = Driver::findOne($selected_driver_id);// получение объекта "выбранного водителя"
//
//				// удаление выбранного водителя из списка незадействованных водителей
//				foreach($drivers as $k => $item){
//					if($item->id == $selected_driver_id){
//						unset($drivers[$k]);
//						break;
//					}
//				}
//			}
//
//			$first_second_drivers_query = Driver::find()->where(['active' => 1])
//				->andWhere([
//					'OR',
//					['primary_transport_id' => $selected_transport_id],
//					['secondary_transport_id' => $selected_transport_id],
//				]);
//			if(!empty($selected_driver_id)) {
//				$first_second_drivers_query = $first_second_drivers_query->andWhere(['!=', 'id', $selected_driver_id]);
//			}
//			$first_second_drivers = $first_second_drivers_query->orderBy(['fio'=>'ASC'])->all();
//
//			foreach($first_second_drivers as $first_second_driver) {
//				foreach($drivers as $k => $item){
//					if($item->id == $first_second_driver->id){
//						unset($drivers[$k]);
//						break;
//					}
//				}
//			}
//
//			$total_result = [];
//			$total_result = array_merge($total_result, $first_second_drivers);
//			$total_result = array_merge($total_result, $drivers);
//
//		} else {
//			$total_result = $drivers;
//		}

		$total_result = Trip::_getDriversList($drivers, $selected_transport_id, $selected_driver_id);

		return $total_result;
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['date', 'direction_id', 'created_at', 'updated_at',
				'date_start_sending', 'start_sending_user_id',
				'date_sended', 'sended_user_id', 'commercial', 'date_issued_by_operator',
                'issued_by_operator_id', 'has_free_places',], 'integer'],
			[['name'], 'string', 'max' => 50],
			[['start_time', 'mid_time', 'end_time'], 'string', 'max' => 5],
			[['date', 'direction_id', 'name', 'start_time', 'mid_time', 'end_time'], 'required'],
			[['use_mobile_app', 'is_reserv'], 'boolean'],
			[['point_interval'], 'safe']
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'name' => 'Название',
			'date' => 'Дата',
			'direction_id' => 'Направление',
			'commercial' => 'Коммерческий рейс',
			'start_time' => 'Начало сбора',
			'mid_time' => 'Середина сбора',
			'end_time' => 'Конец сбора',
			'date_start_sending' => 'Время начала отправки машины',
			'start_sending_user_id' => 'Пользователь(оператор) начавший отправку машины',
            'date_issued_by_operator' => 'Дата выпуска рейса оператором',
            'issued_by_operator_id' => 'Оператор, отправка т/с которого выпустила рейс',
            'has_free_places' => 'Есть свободные места в одном из т/с',
			'date_sended' => 'Дата отправки рейса',
			'sended_user_id' => 'Пользователь(оператор) отправивший машину',
			'use_mobile_app' => 'Режим работы рейса: 0 - без водительского приложения, 1 - с водительским приложением',
			'created_at' => 'Время создания',
			'updated_at' => 'Время изменения',
			'point_interval' => 'Интервал между точками',
            'is_reserv' => 'Резервный рейс'
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

	public function afterSave($insert, $changedAttributes)
	{
		parent::afterSave($insert, $changedAttributes);

		SocketDemon::updateMainPages($this->id, $this->date);
		if(!empty($this->date_start_sending) && empty($this->date_sended)) {
			IncomingOrdersWidget::updateActiveTripsModal();
		}
	}


	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getDirection()
	{
		return $this->hasOne(Direction::className(), ['id' => 'direction_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getOrders()
	{
		return $this->hasMany(Order::className(), ['trip_id' => 'id'])->andWhere(['>', 'status_id', 0]);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getTripTransports()
	{
		return $this->hasMany(TripTransport::className(), ['trip_id' => 'id'])->orderBy(['sort' => SORT_DESC]);
	}


	public function getTripTransportList() {

		$aTripTransports = [];

		if($this->_tripTransportList == null) {

			$count_transports_places = 0;
			$trip_transports = $this->tripTransports;
			if (count($trip_transports) > 0) {
				foreach ($trip_transports as $trip_transport) {
					$transport = $trip_transport->transport;
					$count_transports_places += $transport->places_count;
					if (empty($trip_transport->date_sended)) {
						$aTripTransports[$trip_transport->id] = $transport->name4;
					}
				}
			}

			$this->_tripTransportList = $aTripTransports;
		}

		return $this->_tripTransportList;
	}


	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getDayReportTripTransports()
	{
		return $this->hasMany(DayReportTripTransport::className(), ['trip_id' => 'id']);
	}

	public function getTariff() {

		return Tariff::find()
			->where(['<=', 'start_date', $this->date])
			->andWhere(['commercial' => intval($this->commercial)])
			->orderBy(['start_date' => SORT_DESC])
			->one();
	}

//	public function getCashbackSetting() {
//
//		return CashbackSetting::find()
//			->where(['<=', 'start_date', $this->date])
//			->andWhere(['with_commercial_trips' => intval($this->commercial)])
//			->orderBy(['start_date' => SORT_DESC])
//			->one();
//	}

	public function getTimesOfTrip($startTime, $midTime=null, $endTime=null){

		$trip_start_time = $this->start_time;
		$trip_mid_time = $this->mid_time;
		//$trip_end_time = $this->end_time;

		$diffStart = self::diffTimesInMinutes($trip_start_time, $startTime);

		$start_time = $startTime;

		if($midTime !== null && self::diffTimesInMinutes($startTime,$midTime) > 0){
			$mid_time = $midTime;
		} else {
			if($diffStart > 0){
				$mid_time = self::addMinutesToTime($trip_mid_time, $diffStart);
			} else {
				$mid_time = self::distractMinutesFromTime($trip_mid_time, -$diffStart);
			}
		}

		$end_time = null;

		if($endTime !== null && self::diffTimesInMinutes($mid_time,$endTime) > 0 && self::diffTimesInMinutes($startTime,$endTime) > 0){
			$end_time = $endTime;
		} else {
			if($midTime === null){
				if($diffStart > 0){
					$end_time = self::addMinutesToTime($this->end_time, $diffStart);
				} else if($diffStart < 0){
					$end_time = self::distractMinutesFromTime($this->end_time, -$diffStart);
				}
			} else {
				//$end_time = $trip_end_time;
			}
		}

		if($endTime !== null && $end_time === null){
			$end_time = $endTime;
		}

		if($end_time && self::diffTimesInMinutes($mid_time,$end_time) < 0){
			$end_time = self::addMinutesToTime($mid_time, self::diffTimesInMinutes($start_time,$mid_time));
		}

		return [
			'start_time' => $start_time,
			'mid_time' => $mid_time,
			'end_time' => $end_time
		];
	}

	private static function diffTimesInMinutes($time1, $time2){

		$diffSec = strtotime($time2) - strtotime($time1);

		$diffMin = $diffSec / 60;

		return $diffMin;

	}

	private static function addMinutesToTime($time, $addedMinutes){
		$input = $time;
		$result = date('H:i',strtotime($input.' + '."$addedMinutes".' min'));
		return $result;
	}

	private static function distractMinutesFromTime($time, $distarctedMinutes){
		$input = $time;
		$result = date('H:i',strtotime($input.' - '."$distarctedMinutes".' min'));
		return $result;
	}

	public function getFreeDirectionDateTransports(){

		// найдем машины отправленные на текущем дне-направлении
		$aSendedTransportsIds = [];
		$dayTripsIds = ArrayHelper::map(Trip::find()->where(['date' => $this->date, 'direction_id' => $this->direction_id])->all(), 'id', 'id');
		if(count($dayTripsIds) > 0) {
			$trip_transports = TripTransport::find()
				->where(['IN', 'trip_id', $dayTripsIds])
				->andWhere(['>', 'date_sended', 0])
				->all();
			$aSendedTransportsIds = ArrayHelper::map($trip_transports, 'transport_id', 'transport_id');
		}

		// машины добавленные на текущий рейс
		$aTripTransportsIds = ArrayHelper::map($this->tripTransports, 'transport_id', 'transport_id');

		// машины из вторых рейсов
		$aSecondTransportsIds = SecondTripTransport::getDayTransportsIds($this->date);

		// свободные машины - это все машины, кроме отправленных на дне-направлении, кроме уже добавленных на
		// текущем рейсе, и с присоединением вторичного транспорта к выборке
		$aExcludeTransportsIds = $aSendedTransportsIds + $aTripTransportsIds;
		foreach($aExcludeTransportsIds as $transport_id) {
			if(isset($aSecondTransportsIds[$transport_id])) {
				unset($aExcludeTransportsIds[$transport_id]);
			}
		}

		if(count($aExcludeTransportsIds) > 0) {
			return Transport::find()
				->where(['NOT IN', 'id', $aExcludeTransportsIds])
				->andWhere(['active' => 1])
				->orderBy(['CONVERT(car_reg,SIGNED)' => SORT_ASC])
				->all();
		}else {
			return Transport::find()
				->where(['active' => 1])
				->orderBy(['CONVERT(car_reg,SIGNED)' => SORT_ASC])
				->all();
		}
	}



	/*
	 * Проверка на возможность объединения рейсов
	 */
	public static function canMerge($trips) {

		if(count($trips) == 0) {
			throw new ForbiddenHttpException('Не переданы рейсы');
		}

		if(count($trips) > 3) {
			throw new ForbiddenHttpException('Нельзя объединять больше 3-х рейсов');
		}


		// Заказы которые уже "посажены" в т/с лишают привязываемый/удаляемый рейс права быть объединенным с основным рейсом
		$orders = Order::find()->where(['trip_id' => ArrayHelper::map($trips, 'id', 'id')])->all();
		foreach($orders as $order) {
			if(!empty($order->time_sat)) {
				$bad_trip = $order->trip;
				throw new ForbiddenHttpException('На рейсе "'.$bad_trip->name.'"" есть посаженный в т/с пассажир');
			}
		}

		// Машины которые отправлены, лишают привязываемые/удаляемый рейс права быть объединенным с основным рейсом
		$trip_transports = TripTransport::find()->where(['trip_id' => ArrayHelper::map($trips, 'id', 'id')])->all();
		foreach($trip_transports as $trip_transport) {
			if($trip_transport->date_sended > 0) {
				$bad_trip = $trip_transport->trip;
				throw new ForbiddenHttpException('На рейсе "'.$bad_trip->name.'" есть отправленные машины');
			}
		}

		return true;
	}

	/*
	 * При слиянии рейсов не должно возникать Forbiden-ошибок, иначе слияние не закончится, а новый пустой рейс создаться...
	 */
	public function mergeTrips($trips) {

		// у рейсов у которых точки совпадаются в текущим результирующим рейсов - не изменяются заказы и машины,
		// у остальных рейсов сбрасываются у т/с машины, сбрасываются у заказов рейсов ВПРП, подт-е и КЗМ

		// Сброс заказов и транспортов(trip_transports) рейсов у которых точки не совпадают с текущим рейсом
		$resetTrips = [];
		foreach($trips as $trip) {
			if($trip->start_time != $this->start_time
				|| $trip->mid_time != $this->mid_time
				|| $trip->end_time != $this->end_time
				|| intval($trip->commercial) != intval($this->commercial)
			) {
				$resetTrips[] = $trip;
			}
		}

		//echo "this:<pre>"; print_r($this); echo "</pre>";
		//echo 'trips:<pre>'; print_r(ArrayHelper::map($resetTrips, 'id', 'id')); echo '</pre>'; exit;

		$resetOrders = [];
		$resetTripTransports = [];
		if(count($resetTrips) > 0) {
			$resetOrders = Order::find()->where(['IN', 'trip_id', ArrayHelper::map($resetTrips, 'id', 'id')])->all();
			$resetTripTransports = TripTransport::find()->where(['IN', 'trip_id', ArrayHelper::map($resetTrips, 'id', 'id')])->all();
		}

		if(count($resetOrders) > 0) {
			Order::resetOrders(ArrayHelper::map($resetOrders, 'id', 'id'));
		}
		if(count($resetTripTransports) > 0) {
			$aResetTripTransportsId = ArrayHelper::map($resetTripTransports, 'id', 'id');
			TripTransport::setFields($aResetTripTransportsId, 'confirmed', NULL);
			TripTransport::setFields($aResetTripTransportsId, 'confirmed_date_time', NULL);
			TripTransport::setFields($aResetTripTransportsId, 'confirmed_user_id', NULL);
		}


		// Привязка всех транспортов(trip_transports) и заказов к текущему рейсу.
		$all_orders = Order::find()->where(['IN', 'trip_id', ArrayHelper::map($trips, 'id', 'id')])->all();
		$all_trip_transports = TripTransport::find()->where(['IN', 'trip_id', ArrayHelper::map($trips, 'id', 'id')])->all();
		if(count($all_orders) > 0) {
			$aAllOrders = ArrayHelper::map($all_orders, 'id', 'id');
			Order::setFields($aAllOrders, 'trip_id', $this->id);

			$orders = Order::find()->where(['IN', 'id', $aAllOrders])->all(); // лучше заново создать модели с обновленными данными по рейсу
			foreach($orders as $order) {

				$price = $order->calculatePrice;
				if($price != $order->price) {
					$order->setField('price', $price);
				}

//				if($order->status_id == 2) { // canceled
//
//					$penalty_cash_back = $order->getCalculatePenaltyCashBack($price);
//					if($penalty_cash_back != $order->penalty_cash_back) {
//						$order->setField('penalty_cash_back', $penalty_cash_back);
//					}
//					if($order->accrual_cash_back > 0) {
//						$order->setField('accrual_cash_back', 0);
//					}
//
//				}else {
//
//					$accrual_cash_back = ($order->status_id == 2 ? 0 : $order->getCalculateAccrualCashBack($price));
//					if($accrual_cash_back != $order->accrual_cash_back) {
//						$order->setField('accrual_cash_back', $accrual_cash_back);
//					}
//					if($order->penalty_cash_back > 0) {
//						$order->setField('penalty_cash_back', 0);
//					}
//				}


				$time_confirm = $order->getYandexPointTimeConfirm();
				if($time_confirm > 0) {
					$order->setField('time_confirm_auto', $time_confirm);
				}
			}

		}

		if(count($all_trip_transports) > 0) {
			$aAllTripTransports = ArrayHelper::map($all_trip_transports, 'id', 'id');
			TripTransport::setFields($aAllTripTransports, 'trip_id', $this->id);
		}

		// удаление "слитых" рейсов
		$sql = 'DELETE FROM `'.Trip::tableName().'` WHERE id IN('.implode(',', ArrayHelper::map($trips, 'id', 'id')).')';
		Yii::$app->db->createCommand($sql)->execute();


		// может оказаться что были слиты в один рейс одинаковые машины, в этом случае:
		// первая машина из всех дублей остается неприкосаемой
		// остальные дубли машины удаляются и их заказы переносяться в первую машину дубль
		$this->mergeTransports();


		SocketDemon::updateMainPages($this->id, $this->date);

		$update_incoming_orders_widget = false;
		foreach($trips as $trip) {
			if($trip != null && !empty($trip->date_start_sending) && empty($trip->date_sended)) {
				$update_incoming_orders_widget = true;
				break;
			}
		}
		if($update_incoming_orders_widget) {
			IncomingOrdersWidget::updateActiveTripsModal();
		}

		return true;
	}

	/*
	 * Одинаковые на рейсе машины (после слияния рейсов) "объединяются"
	 */
	public function mergeTransports() {

		$trip_transports = $this->tripTransports;
		$aTripTransports = [];
		foreach($trip_transports as $trip_transport) {
			$aTripTransports[$trip_transport->transport_id][] = $trip_transport;
		}
		foreach($aTripTransports as $transport_id => $aTripTransports) {
			if(count($aTripTransports) > 1) { // значит есть дубли
				$main_trip_transport = $aTripTransports[0];
				$aSecondTripTransports = array_slice($aTripTransports, 1);
				$main_trip_transport->joinTripTransports($aSecondTripTransports); // присоединение второстепенных трип_транспортов к основному
			}
		}
	}


	public function getTransportDriverInfo(){

		$trip_transport_list = $this->tripTransports;

		$result = [];
		foreach($trip_transport_list as $item){
			$result[] = $item->getTransportDriverInfo();
		}

		return $result;
	}

	public function getEmptyTransports($trip_id=null, $transport_id=null){

		if($trip_id === null){
			if(!$this->isNewRecord){
				$rec_id = $this->id;
				//$model = $this;
			} else {
				return false;
			}
		} else {
			$rec_id = $trip_id;
			//$model = self::findOne($rec_id);
		}

		$trip_transport_list = TripTransport::find()->where(['trip_id'=>$rec_id])->all();

		$result = [];

		$allCars = Transport::find()
			->where(['active' => 1])
			->orderBy(['car_reg'=>'ASC'])
			->all();

		foreach($allCars as $car){
			$found = false;
			foreach($trip_transport_list as $item){
				if($item->transport_id == $car->id){
					$found = true;
					break;
				}
			}

			if(!$found){
				$result[] = $car;
			}
		}

		return $result;
	}

	public function getEmptyDrivers($trip_id=null,$transport_id=null, $selected_driver_id=null){
		if($trip_id === null){
			if(!$this->isNewRecord){
				$rec_id = $this->id;
				//$model = $this;
			} else {
				return false;
			}
		} else {
			$rec_id = $trip_id;
			//$model = self::findOne($rec_id);
		}

		$trip_transport_list = TripTransport::find()->where(['trip_id'=>$rec_id])->all();

		$result = [];


		$allDrivers = Driver::find()->orderBy(['fio'=>'ASC'])->all();

		foreach($allDrivers as $driver){
			$found = false;
			foreach($trip_transport_list as $item){
				if($item->driver_id == $driver->id){
					$found = true;
					break;
				}
			}

			if(!$found){
				$result[] = $driver;
			}
		}


		if($transport_id){

			$first_drivers = Driver::find()->where(['primary_transport_id'=>$transport_id])->orderBy(['fio'=>'ASC'])->all();
			$second_drivers = Driver::find()->where(['secondary_transport_id'=>$transport_id])->orderBy(['fio'=>'ASC'])->all();

			$first_indexes = [];
			$second_indexes = [];



			foreach($first_drivers as $first_driver){

				foreach($result as $k=>$item){
					if($item->id == $first_driver->id){

						$first_indexes[] = $item;
						unset($result[$k]);
						break;
					}
				}
			}

			foreach($second_drivers as $second_driver){

				foreach($result as $k=>$item){
					if($item->id == $second_driver->id){

						$second_indexes[] = $item;
						unset($result[$k]);
						break;
					}
				}
			}


			$selected_driver = [];

			if($selected_driver_id){
				$selected_driver[] = Driver::findOne($selected_driver_id);

				foreach($result as $k=>$item){
					if($item->id == $selected_driver_id){


						unset($result[$k]);
						break;
					}
				}


				//			foreach($first_indexes as $k=>$item){
				//				if($item->id == $selected_driver_id){
				//
				//
				//					$selected_driver = [];
				//					break;
				//				}
				//			}
				//
				//			if(count($selected_driver)){
				//				foreach($second_indexes as $k=>$item){
				//					if($item->id == $selected_driver_id){
				//
				//
				//						$selected_driver = [];
				//						break;
				//					}
				//				}
				//			}
			}



			$preliminary_result = array_merge($first_indexes,$second_indexes);

			$preliminary_result = array_merge($preliminary_result,$selected_driver);

			$preliminary_result = array_merge($preliminary_result,$result);

			$total_result = &$preliminary_result;


			//		foreach($preliminary_result as $k=>$item){
			//			$found = false;
			//			for($i=0; $i < $k; $i++){
			//				if($item->id == $preliminary_result[$i]->id){
			//					$found = true;
			//					break;
			//				}
			//			}
			//
			//			if(!$found){
			//				$total_result[] = $item;
			//			}
			//		}

		} else {
			$total_result = &$result;
		}


		return $total_result;
	}

	public function updatePostTripTransports($post)
	{
		$post_transport_ids = isset($post['transport_ids']) ? $post['transport_ids'] : [];
		$post_driver_ids = isset($post['driver_ids']) ? $post['driver_ids'] : [];
		$post_sorts = isset($post['sorts']) ? $post['sorts'] : [];
		$post_ids = isset($post['tt_id']) ? $post['tt_id'] : [];

		$trip_transports = $this->tripTransports;
		foreach($trip_transports as $trip_transport){
			if(!in_array($trip_transport->id, $post_ids)){
				$trip_transport->delete();
			}
		}

		if(is_array($post_ids) && count($post_ids)){
			foreach($post_ids as $k=> $tt_id){
				if($tt_id){

					$item = TripTransport::findOne($tt_id);
					$item->transport_id = $post_transport_ids[$k];
					$item->driver_id = $post_driver_ids[$k];
					$item->sort = $post_sorts[$k];

					if(!$item->save()){
						throw new ForbiddenHttpException('Не удалось сохранить trip_transport 1');
					}

				} else {

					$new_trip_transport = new TripTransport();
					$new_trip_transport->trip_id = $this->id;
					$new_trip_transport->transport_id = intval($post_transport_ids[$k]);
					$new_trip_transport->driver_id = intval($post_driver_ids[$k]);
					$new_trip_transport->sort = intval($post_sorts[$k]);

					// если у рейса уже начат процесс отправки транспорта, то для новой машины генерируем ключ доступа
//					if(!empty($new_trip_transport->trip->date_start_sending)) {
//						$new_trip_transport->access_key = $new_trip_transport->generateAccessKey();
//					}

					if (!$new_trip_transport->save()) {
						throw new ForbiddenHttpException('Не удалось сохранить trip_transport 2');
					}

				}
			}
		}


		return true;
	}


	public function startSending() {

		$this->date_start_sending = time();
		$this->start_sending_user_id = Yii::$app->user->id;
		if(!$this->save(false)) {
			throw new ErrorException('Не удалось сохранить рейс');
		}

		// подтвержденным машинам на рейсе генерируется ключ доступа
		$trip_transports = $this->tripTransports;
		foreach($trip_transports as $trip_transport) {
			if($trip_transport->confirmed == 1) {
				$trip_transport->access_key = $trip_transport->generateAccessKey();
				$trip_transport->setField('access_key', $trip_transport->access_key);
			}
		}

		$trip_operation = new TripOperation();
		$trip_operation->type = 'start_send';
		$trip_operation->comment = 'Начало отправки рейса '.($this->direction_id==1 ? 'АК ' : 'КА ').$this->name;
		if(!$trip_operation->save(false)) {
			throw new ErrorException('Не удалось сохранить в историю операцию с рейсом');
		}

		SocketDemon::updateMainPages($this->id, $this->date);
		IncomingOrdersWidget::updateActiveTripsModal();

		return true;
	}


	/*
	 * Отправка рейса и пересчет разных счетчиков, логирование и т.п.
	 */
    public function send() {

        $sapi = php_sapi_name();
        if ($sapi=='cli') { // это консольный запуск
            $current_user = null;
        }else {
            $current_user = Yii::$app->user->identity;
        }

        // 0. состояние рейса
        $this->date_sended = time();
        if($current_user != null) {
            $this->sended_user_id = $current_user->id;
        }
        if(!$this->save(false)) {
            throw new ErrorException('Не удалось сохранить рейс');
        }


        // 1. Когда заказы переходят в статус "Отправлен", то в таблице клиентов пересчитываются: order_count++, prize_trip_count?++
        //$fact_orders_without_canceled = $this->factOrdersWithoutCanceled;
        $order_status = OrderStatus::getByCode('sent');
        $fact_orders_without_canceled = Order::find()->where(['trip_id' => $this->id])->andWhere(['status_id' => $order_status->id])->all();
        $aClients = [];
        if(count($fact_orders_without_canceled) > 0) {
            $clients = Client::find()->where(['id' => ArrayHelper::map($fact_orders_without_canceled, 'client_id', 'client_id')])->all();
            if(count($clients) > 0) {
                $aClients = ArrayHelper::index($clients, 'id');
            }
        }
        foreach($fact_orders_without_canceled as $fact_order) {

            $client = $aClients[$fact_order->client_id];
            if($fact_order->prize_trip_count > 0) {
                $client->current_year_sended_prize_places += $fact_order->prize_trip_count;
            }

            if($fact_order->informerOffice != null && $fact_order->informerOffice->cashless_payment == 1) {
                $client->current_year_sended_informer_beznal_places += $fact_order->places_count;
                $client->current_year_sended_informer_beznal_orders += 1;

            }elseif($fact_order->is_not_places == 1) { // или счетчик "посылок" (нет места) инкрементируется
                $client->current_year_sended_isnotplaces_orders++;

            }elseif($fact_order->use_fix_price == 1) { // или увеличивается счетчик мест отправленных фикс. заказов
                $client->current_year_sended_fixprice_places += $fact_order->places_count;
                $client->current_year_sended_fixprice_orders += 1;
            }

            if(!$client->save(false)) {
                throw new ForbiddenHttpException('Не удалось сохранить клиента');
            }

            if($client != null) {
                $client->recountSendedCanceledReliabilityCounts($fact_order, 1, $fact_order->places_count, 0 , 0);
            }
        }



        // 2. "логируем" данные в отчет дня
        $direction = $this->direction;
        $trip_transports = TripTransport::find()->where(['trip_id' => $this->id])->all();
        $aDayReportTripTrasports = [];
        $aDrivers = [];


        if(count($trip_transports) > 0) {
            foreach ($trip_transports as $trip_transport) {

                $transport = $trip_transport->transport;
                $driver = $trip_transport->driver;
                $aDrivers[$driver->id] = $driver;


                $day_report_trip_transport = new DayReportTripTransport();
                $day_report_trip_transport->date = $this->date;
                $day_report_trip_transport->direction_id = $direction->id;
                $day_report_trip_transport->direction_name = $direction->sh_name;
                $day_report_trip_transport->trip_id = $this->id;
                $day_report_trip_transport->trip_name = $this->name;
                $day_report_trip_transport->trip_transport_id = $trip_transport->id;
                $day_report_trip_transport->transport_id = $transport->id;
                $day_report_trip_transport->transport_car_reg = $transport->car_reg;
                $day_report_trip_transport->transport_model = $transport->model;
                $day_report_trip_transport->transport_places_count = $transport->places_count;
                $day_report_trip_transport->transport_date_sended = $trip_transport->date_sended;


                $day_report_trip_transport->transport_sender_id = $trip_transport->sender_id;
                if($trip_transport->sender_id > 0) {
                    $sender = User::find()->where(['id' => $trip_transport->sender_id])->one();
                    $day_report_trip_transport->transport_sender_fio = $sender->lastname.' '.$sender->firstname;
                }

                //$day_report_trip_transport->transport_sender_id = Yii::$app->user->id;
                //$day_report_trip_transport->transport_sender_fio = $current_user->lastname.' '.$current_user->firstname;

                $day_report_trip_transport->driver_id = $driver->id;
                $day_report_trip_transport->driver_fio = $driver->fio;

                $day_report_trip_transport->places_count_sent = 0;
                $day_report_trip_transport->child_count_sent = 0;
                $day_report_trip_transport->student_count_sent = 0;
                $day_report_trip_transport->prize_trip_count_sent = 0;
                $day_report_trip_transport->bag_count_sent = 0;
                $day_report_trip_transport->suitcase_count_sent = 0;
                $day_report_trip_transport->oversized_count_sent = 0;
                $day_report_trip_transport->is_not_places_count_sent = 0;
                $day_report_trip_transport->proceeds = 0;
                $day_report_trip_transport->airport_count_sent = 0;
                $day_report_trip_transport->airport_places_count_sent = 0;
                $day_report_trip_transport->fix_price_count_sent = 0;

                $day_report_trip_transport->no_record = 0;
                $informer_office = InformerOffice::find()->where(['code' => 'without_record'])->one();
                if($informer_office == null) {
                    throw new ForbiddenHttpException('Источник "Без записи" не найден');
                }


                foreach($fact_orders_without_canceled as $fact_order) {

                    // нужно считать заказы только текущей машины
                    if($fact_order->fact_trip_transport_id != $trip_transport->id) {
                        continue;
                    }

                    $day_report_trip_transport->places_count_sent += $fact_order->places_count;
                    $day_report_trip_transport->child_count_sent += $fact_order->child_count;
                    $day_report_trip_transport->student_count_sent += $fact_order->student_count;
                    $day_report_trip_transport->prize_trip_count_sent += $fact_order->prize_trip_count;
                    $day_report_trip_transport->bag_count_sent += $fact_order->bag_count;
                    $day_report_trip_transport->suitcase_count_sent += $fact_order->suitcase_count;
                    $day_report_trip_transport->oversized_count_sent += $fact_order->oversized_count;
                    $day_report_trip_transport->is_not_places_count_sent += $fact_order->is_not_places;
                    $day_report_trip_transport->proceeds += $fact_order->price;
                    $day_report_trip_transport->paid_summ += $fact_order->paid_summ;

                    if($fact_order->informer_office_id == $informer_office->id) {
                        $day_report_trip_transport->no_record++;
                    }

                    $yandexPointTo = $fact_order->yandexPointTo;
                    $yandexPointFrom = $fact_order->yandexPointFrom;
                    if(
                        ($yandexPointTo != null && $yandexPointTo->alias == 'airport')
                        || ($yandexPointFrom != null && $yandexPointFrom->alias == 'airport')
                    ) { // едут в аэропорт или из аэропорта
                        $day_report_trip_transport->airport_count_sent++;
                        $day_report_trip_transport->airport_places_count_sent += $fact_order->places_count;
                    }

                    if($fact_order->use_fix_price == 1) {
                        $day_report_trip_transport->fix_price_count_sent++;
                    }
                }

                $day_report_trip_transport->trip_date_sended = $this->date_sended;


                if($current_user != null) {
                    $day_report_trip_transport->trip_sender_id = $current_user->id;
                    $day_report_trip_transport->trip_sender_fio = $current_user->lastname . ' ' . $current_user->firstname;
                }

                if(!$day_report_trip_transport->save(false)) {
                    throw new ErrorException('Не удалось сохранить информацию в отчет отображаемого дня');
                }
                $aDayReportTripTrasports[$trip_transport->id] = $day_report_trip_transport;



                // записываем в "круги" отправленную машину
                $trip_start_time = $this->date + Helper::convertHoursMinutesToSeconds($this->start_time);
                $transport_circle = DayReportTransportCircle::find()
                    ->where(['transport_id' => $transport->id, 'state' => 0])
                    ->andWhere(['<', 'base_city_trip_start_time', $trip_start_time])
                    ->orderBy(['id' => SORT_DESC])
                    ->one();

                // если отправляемая машина выезжает из города базирования, то создаем новый круг.
                if($direction->city_from == $transport->base_city_id) {

                    // если ранее с этой машиной уже был круг и он не закрыт, то закрываем его
                    $old_transport_circle = $transport_circle;
                    if($old_transport_circle != null) {
                        $old_transport_circle->state = 1;
                        $old_transport_circle->time_setting_state = time();
                        if(!$transport_circle->save()) {
                            throw new ForbiddenHttpException('Не удалось закрыть старый круг машины');
                        }
                    }

                    $transport_circle = new DayReportTransportCircle();
                    $transport_circle->transport_id = $transport->id;
                    $transport_circle->base_city_trip_id = $this->id;
                    $transport_circle->base_city_trip_start_time = $trip_start_time;
                    $transport_circle->base_city_day_report_id = $day_report_trip_transport->id;
                    $transport_circle->state = 0;
                    $transport_circle->time_setting_state = time();
                    $transport_circle->total_proceeds = $day_report_trip_transport->proceeds;
                    $transport_circle->total_paid_summ = $day_report_trip_transport->paid_summ;

                }else { // иначе завершаем старый круг (или создаем новый круг с завершением)

                    if($transport_circle == null) {
                        $transport_circle = new DayReportTransportCircle();
                        $transport_circle->transport_id = $transport->id;
                        $transport_circle->notbase_city_trip_id = $this->id;
                        $transport_circle->notbase_city_trip_start_time = $trip_start_time;
                        $transport_circle->notbase_city_day_report_id = $day_report_trip_transport->id;
                        $transport_circle->state = 1;
                        $transport_circle->time_setting_state = time();
                        $transport_circle->total_proceeds = $day_report_trip_transport->proceeds;
                        $transport_circle->total_paid_summ = $day_report_trip_transport->paid_summ;
                    }else {
                        $transport_circle->transport_id = $transport->id;
                        $transport_circle->notbase_city_trip_id = $this->id;
                        $transport_circle->notbase_city_trip_start_time = $trip_start_time;
                        $transport_circle->notbase_city_day_report_id = $day_report_trip_transport->id;
                        $transport_circle->state = 1;
                        $transport_circle->time_setting_state = time();
                        $transport_circle->total_proceeds += $day_report_trip_transport->proceeds;
                        $transport_circle->total_paid_summ += $day_report_trip_transport->paid_summ;
                    }
                }

                if(!$transport_circle->save()) {
                    throw new ForbiddenHttpException('Не удалось сохранить запись машины в таблице кругов');
                }

            }
        }


        // 3. логируем отправленные заказы OrderReport
        $aOrdersReports = [];
        foreach($fact_orders_without_canceled as $fact_order) {

            $day_report_trip_transport = $aDayReportTripTrasports[$fact_order->fact_trip_transport_id];

            $aOrdersReports[] = [
                'day_report_trip_transport_id' => $day_report_trip_transport->id,
                'date_sended' => $day_report_trip_transport->date,
                'order_id' => $fact_order->id,
                'client_id' => $fact_order->client_id,
                'client_name' => ($fact_order->client != null ? $fact_order->client->name : ''),
                'date' => $fact_order->date,
                'direction_id' => $fact_order->direction_id,
                'direction_name' => ($direction != null ? $direction->sh_name : ''),

                'street_id_from' => $fact_order->street_id_from,
                'street_from_name' => ($fact_order->streetFrom != null ? $fact_order->streetFrom->name : ''),
                'point_id_from' => $fact_order->point_id_from,
                'point_from_name' => ($fact_order->pointFrom != null ? $fact_order->pointFrom->name : ''),

                'yandex_point_from_id' => $fact_order->yandex_point_from_id,
                'yandex_point_from_name' => $fact_order->yandex_point_from_name,
                'yandex_point_from_lat' => $fact_order->yandex_point_from_lat,
                'yandex_point_from_long' => $fact_order->yandex_point_from_long,

                'time_air_train_arrival' => $fact_order->time_air_train_arrival,
                'street_id_to' => $fact_order->street_id_to,
                'street_to_name' => ($fact_order->streetTo != null ? $fact_order->streetTo->name : ''),
                'point_id_to' => $fact_order->point_id_to,
                'point_to_name' => ($fact_order->pointTo != null ? $fact_order->pointTo->name : ''),

                'yandex_point_to_id' => $fact_order->yandex_point_to_id,
                'yandex_point_to_name' => $fact_order->yandex_point_to_name,
                'yandex_point_to_lat' => $fact_order->yandex_point_to_lat,
                'yandex_point_to_long' => $fact_order->yandex_point_to_long,


                'time_air_train_departure' => $fact_order->time_air_train_departure,
                'trip_id' => $fact_order->trip_id,
                'trip_name' => ($fact_order->trip != null ? $fact_order->trip->name : ''),
                'informer_office_id' => $fact_order->informer_office_id,
                'informer_office_name' => ($fact_order->informerOffice != null ? $fact_order->informerOffice->name : ''),
                'is_not_places' => $fact_order->is_not_places,
                'places_count' => $fact_order->places_count,
                'student_count' => $fact_order->student_count,
                'child_count' => $fact_order->child_count,
                'bag_count' => $fact_order->bag_count,
                'suitcase_count' => $fact_order->suitcase_count,
                'oversized_count' => $fact_order->oversized_count,
                'prize_trip_count' => $fact_order->prize_trip_count,
                'comment' => $fact_order->comment,
                'additional_phone_1' => $fact_order->additional_phone_1,
                'additional_phone_2' => $fact_order->additional_phone_2,
                'additional_phone_3' => $fact_order->additional_phone_3,
                'time_sat' => $fact_order->time_sat,
                'use_fix_price' => $fact_order->use_fix_price,
                'price' => $fact_order->price,
                'time_confirm' => $fact_order->time_confirm,
                'is_confirmed' => $fact_order->is_confirmed,
                'first_writedown_click_time' => $fact_order->first_writedown_click_time,
                'first_writedown_clicker_id' => $fact_order->first_writedown_clicker_id,
                'first_writedown_clicker_name' => ($fact_order->firstWritedownClicker != null ? $fact_order->firstWritedownClicker->fio : ''),
                'first_confirm_click_time' => $fact_order->first_confirm_click_time,
                'first_confirm_clicker_id' => $fact_order->first_confirm_clicker_id,
                'first_confirm_clicker_name' => ($fact_order->firstConfirmClicker != null ? $fact_order->firstConfirmClicker->fio : ''),
                'radio_confirm_now' => $fact_order->radio_confirm_now,
                'radio_group_1' => $fact_order->radio_group_1,
                'radio_group_2' => $fact_order->radio_group_2,
                'radio_group_3' => $fact_order->radio_group_3,
                'confirm_selected_transport' => $fact_order->confirm_selected_transport,
                'fact_trip_transport_id' => $fact_order->fact_trip_transport_id,
                'fact_trip_transport_car_reg' => ($transport != null ? $transport->car_reg : ''),
                'fact_trip_transport_color' => ($transport != null ? $transport->color : ''),
                'fact_trip_transport_model' => ($transport != null ? $transport->model : ''),
                'fact_trip_transport_driver_id' => ($driver != null ? $driver->id : ''),
                'fact_trip_transport_driver_fio' => ($driver != null ? $driver->fio : ''),
                'has_penalty' => $fact_order->has_penalty,
                'relation_order_id' => $fact_order->relation_order_id,
            ];
        }
        if(count($aOrdersReports) > 0) {

            Yii::$app->db->createCommand()->BatchInsert(
                OrderReport::tableName(),
                [
                    'day_report_trip_transport_id',
                    'date_sended',
                    'order_id',
                    'client_id',
                    'client_name',
                    'date',
                    'direction_id',
                    'direction_name',

                    'street_id_from',
                    'street_from_name',
                    'point_id_from',
                    'point_from_name',

                    'yandex_point_from_id',
                    'yandex_point_from_name',
                    'yandex_point_from_lat',
                    'yandex_point_from_long',

                    'time_air_train_arrival',

                    'street_id_to',
                    'street_to_name',
                    'point_id_to',
                    'point_to_name',

                    'yandex_point_to_id',
                    'yandex_point_to_name',
                    'yandex_point_to_lat',
                    'yandex_point_to_long',

                    'time_air_train_departure',
                    'trip_id',
                    'trip_name',
                    'informer_office_id',
                    'informer_office_name',
                    'is_not_places',
                    'places_count',
                    'student_count',
                    'child_count',
                    'bag_count',
                    'suitcase_count',
                    'oversized_count',
                    'prize_trip_count',
                    'comment',
                    'additional_phone_1',
                    'additional_phone_2',
                    'additional_phone_3',
                    'time_sat',
                    'use_fix_price',
                    'price',
                    'time_confirm',
                    //'time_vpz',
                    'is_confirmed',
                    'first_writedown_click_time',
                    'first_writedown_clicker_id',
                    'first_writedown_clicker_name',
                    'first_confirm_click_time',
                    'first_confirm_clicker_id',
                    'first_confirm_clicker_name',
                    'radio_confirm_now',
                    'radio_group_1',
                    'radio_group_2',
                    'radio_group_3',
                    'confirm_selected_transport',
                    'fact_trip_transport_id',
                    'fact_trip_transport_car_reg',
                    'fact_trip_transport_color',
                    'fact_trip_transport_model',
                    'fact_trip_transport_driver_id',
                    'fact_trip_transport_driver_fio',
                    'has_penalty',
                    'relation_order_id',
                ],
                $aOrdersReports
            )->execute();
        }


        // 4. клиентам всех заказов (в том числе отменных) пересчитываем счета кэш-бэков
        // 5. и обращения связанные со всеми заказами рейса (в том числе отмененными) закрываются
        $trip_orders = $this->orders;
        if(count($trip_orders) > 0) {

            // not_completed -> completed_by_trip_sending
            $sql = 'UPDATE `'.CallCase::tableName().'` SET status = "completed_by_trip_sending" WHERE order_id IN('.implode(',', ArrayHelper::map($trip_orders, 'id', 'id')).')';
            Yii::$app->db->createCommand($sql)->execute();

            // где-то здесь нужно пересчитать для заказов: accrual_cash_back, penalty_cash_back,
            //    used_cash_back-это пока не используется
            /*
            foreach ($trip_orders as $trip_order) {

                if($trip_order->status_id == 2) { // canceled

                    // это нужно считать не при закрытии рейса, а при отмене рейса

//                    $penalty_cash_back = $trip_order->getCalculatePenaltyCashBack($trip_order->price);
//                    if($penalty_cash_back != $trip_order->penalty_cash_back) {
//                        $trip_order->setField('penalty_cash_back', $penalty_cash_back);
//                        $trip_order->penalty_cash_back = $penalty_cash_back;
//                    }
//                    if($trip_order->accrual_cash_back > 0) {
//                        $trip_order->setField('accrual_cash_back', 0);
//                        $trip_order->accrual_cash_back = 0;
//                    }

                }else {

                    $accrual_cash_back = $trip_order->getCalculateAccrualCashBack($trip_order->price);
                    if($accrual_cash_back != $trip_order->accrual_cash_back) {
                        $trip_order->setField('accrual_cash_back', $accrual_cash_back);
                        $trip_order->accrual_cash_back = $accrual_cash_back;
                    }
                    if($trip_order->penalty_cash_back > 0) {
                        $trip_order->setField('penalty_cash_back', 0);
                        $trip_order->penalty_cash_back = 0;
                    }
                }
            }

            $clients = Client::find()->where(['id' => ArrayHelper::map($trip_orders, 'client_id', 'client_id')])->all();
            if(count($clients) > 0) {
                $aClients = ArrayHelper::index($clients, 'id');
                foreach ($trip_orders as $trip_order) {
                    if(isset($aClients[$trip_order->client_id])) {

                        $client = $aClients[$trip_order->client_id];
                        if($trip_order->accrual_cash_back > 0) {
                            $client->cashback += $trip_order->accrual_cash_back;
                        }

                        if($trip_order->penalty_cash_back > 0) {
                            $client->cashback -= $trip_order->penalty_cash_back;
                        }

                        if($trip_order->used_cash_back > 0) {
                            $client->cashback -= $trip_order->used_cash_back;
                        }

                        if($trip_order->accrual_cash_back > 0 || $trip_order->penalty_cash_back > 0 || $trip_order->used_cash_back > 0) {
                            $client->setField('cashback', $client->cashback);
                        }
                    }
                }
            }*/
        }


        // 6. логирование операции отправки рейса - в операции с рейсами
        $trip_operation = new TripOperation();
        $trip_operation->type = 'send';
        $trip_operation->comment = 'Отправка рейса '.($this->direction_id==1 ? 'АК ' : 'КА ').$this->name;
        if(!$trip_operation->save(false)) {
            throw new ErrorException('Не удалось сохранить в историю операцию с рейсом');
        }

        // 7. опять логирование отправки рейса, как действия оператора
        DispatcherAccounting::createLog('trip_send'); // логируем Отправку рейса


        // 8. сообщение в браузеры по сокетам
        SocketDemon::updateMainPages($this->id, $this->date);
        IncomingOrdersWidget::updateActiveTripsModal();


        return true;
    }


    public function cancelSend() {

        // 0. состояние рейса
        $this->date_sended = NULL;
        $this->sended_user_id = NULL;

        $this->date_issued_by_operator = null;
        $this->issued_by_operator_id = NULL;

        $this->date_start_sending = NULL;
        $this->start_sending_user_id = NULL;
        if(!$this->save(false)) {
            throw new ErrorException('Не удалось сохранить рейс');
        }


        // 1. Когда заказы переходят в статус "Отправлен" (т.е. наоборот), то в таблице клиентов пересчитываются: order_count, prize_trip_count
        $order_status = OrderStatus::getByCode('sent');
        $fact_orders_without_canceled = Order::find()->where(['trip_id' =>$this->id])->andWhere(['status_id' => $order_status->id])->all();
        $aClients = [];
        if(count($fact_orders_without_canceled) > 0) {
            $clients = Client::find()->where(['id' => ArrayHelper::map($fact_orders_without_canceled, 'client_id', 'client_id')])->all();
            if(count($clients) > 0) {
                $aClients = ArrayHelper::index($clients, 'id');
            }
        }
        foreach($fact_orders_without_canceled as $fact_order) {

            $client = $aClients[$fact_order->client_id];

            if($fact_order->prize_trip_count > 0) {
                $client->current_year_sended_prize_places -= $fact_order->prize_trip_count;
            }

            if($fact_order->informerOffice != null && $fact_order->informerOffice->cashless_payment == 1) {

                $client->current_year_sended_informer_beznal_places -= $fact_order->places_count;
                $client->current_year_sended_informer_beznal_orders -= 1;

            }elseif($fact_order->is_not_places == 1) { // или счетчик "посылок" (нет места) инкрементируется

                $client->current_year_sended_isnotplaces_orders--;

            }elseif($fact_order->use_fix_price == 1) { // или увеличивается счетчик мест отправленных фикс. заказов

                $client->current_year_sended_fixprice_places -= $fact_order->places_count;
                $client->current_year_sended_fixprice_orders -= 1;
            }

            if(!$client->save(false)) {
                throw new ForbiddenHttpException('Не удалось сохранить клиента');
            }

            if($client != null) {
                $client->recountSendedCanceledReliabilityCounts($fact_order, -1, -$fact_order->places_count, 0 , 0);
            }
        }


        // 2. "логируем" данные в отчет дня - наоборот
        $day_report_trip_transports = DayReportTripTransport::find()->where(['trip_id' => $this->id])->all();
        if(count($day_report_trip_transports) == 0) {
            throw new ErrorException('day_report_trip_transport не найден');
        }

        // переписываем DayReportTransportCircle
        foreach($day_report_trip_transports as $day_report_trip_transport) {

            $transport_circle = DayReportTransportCircle::find()->where(['base_city_day_report_id' => $day_report_trip_transport->id])->one();
            if($transport_circle == null) {
                $transport_circle = DayReportTransportCircle::find()->where(['notbase_city_day_report_id' => $day_report_trip_transport->id])->one();
                if($transport_circle == null) {
                    throw new ErrorException('Не найдено записи transport_circle для day_report_trip_transport id='.$day_report_trip_transport->id);
                }

                if(empty($transport_circle->base_city_day_report_id)) {
                    // таблица цикла заполнена только справа, значит удаляем запись $transport_circle
                    $transport_circle->delete();
                }else {
                    // все что справа - очищаем, все что слева - оставляем. И итоговые значения "инвертируем"
                    $transport_circle->notbase_city_trip_id = NULL;
                    $transport_circle->notbase_city_trip_start_time = NULL;
                    $transport_circle->notbase_city_day_report_id = NULL;

                    $transport_circle->state = 0;
                    $transport_circle->time_setting_state = time();
                    $transport_circle->total_proceeds = $transport_circle->total_proceeds - $day_report_trip_transport->proceeds;

                    if(!$transport_circle->save()) {
                        throw new ForbiddenHttpException('Не удалось сохранить запись машины в таблице кругов');
                    }
                }

            }else {

                if(empty($transport_circle->notbase_city_day_report_id)) {
                    // таблица цикла заполнена только слева, значит удаляем запись $transport_circle
                    $transport_circle->delete();
                }else {
                    // все что слева - очищаем, все что справа - оставляем. И итоговые значения "инвертируем"
                    $transport_circle->base_city_trip_id = NULL;
                    $transport_circle->base_city_trip_start_time = NULL;
                    $transport_circle->base_city_day_report_id = NULL;

                    $transport_circle->state = 0;
                    $transport_circle->time_setting_state = time();
                    $transport_circle->total_proceeds = $transport_circle->total_proceeds - $day_report_trip_transport->proceeds;

                    if(!$transport_circle->save()) {
                        throw new ForbiddenHttpException('Не удалось сохранить запись машины в таблице кругов');
                    }
                }
            }

            // удаляем day_report_trip_transport
            $day_report_trip_transport->delete();
        }


        // 3. удаляем логи отправленных заказов OrderReport
        $canceled_order_status = OrderStatus::getByCode('canceled');
        $fact_orders = Order::find()
            ->where(['trip_id' => $this->id])
            ->andWhere(['!=', 'status_id', $canceled_order_status->id])
            ->all();
        if(count($fact_orders) > 0) {
            $sql = 'DELETE FROM `'.OrderReport::tableName().'` WHERE order_id IN('.implode(',', ArrayHelper::map($fact_orders, 'id', 'id')).')';
            Yii::$app->db->createCommand($sql)->execute();
        }


        // 4. клиентам всех заказов (в том числе отменных) пересчитываем счета кэш-бэков
        // 5. и обращения связанные со всеми заказами рейса (в том числе отмененными) закрываются
        $trip_orders = $this->orders;
        if(count($trip_orders) > 0) {
            $sql = 'UPDATE `'.CallCase::tableName().'` SET status = "not_completed" WHERE order_id IN('.implode(',', ArrayHelper::map($trip_orders, 'id', 'id')).')';
            Yii::$app->db->createCommand($sql)->execute();

            // где-то здесь нужно пересчитать для заказов: accrual_cash_back, penalty_cash_back,
            //      used_cash_back-это пока не используется
            /*
            $clients = Client::find()->where(['id' => ArrayHelper::map($trip_orders, 'client_id', 'client_id')])->all();
            if(count($clients) > 0) {

                $aClients = ArrayHelper::index($clients, 'id');
                foreach ($trip_orders as $trip_order) {
                    if(isset($aClients[$trip_order->client_id])) {

                        $client = $aClients[$trip_order->client_id];
                        if($trip_order->accrual_cash_back > 0) {
                            $client->cashback -= $trip_order->accrual_cash_back;
                        }

                        if($trip_order->penalty_cash_back > 0) {
                            $client->cashback += $trip_order->penalty_cash_back;
                        }

                        if($trip_order->used_cash_back > 0) {
                            $client->cashback += $trip_order->used_cash_back;
                        }

                        if($trip_order->accrual_cash_back > 0 || $trip_order->penalty_cash_back > 0 || $trip_order->used_cash_back > 0) {
                            $client->setField('cashback', $client->cashback);
                        }
                    }
                }
            }


            foreach ($trip_orders as $trip_order) {

                if($trip_order->penalty_cash_back > 0) {
                    $trip_order->setField('penalty_cash_back', 0);
                }
                if($trip_order->accrual_cash_back > 0) {
                    $trip_order->setField('accrual_cash_back', 0);
                }
            }*/
        }


        // 6. логирование операции отправки рейса - в операции с рейсами
        $trip_operation = new TripOperation();
        $trip_operation->type = 'cancel_send';
        $trip_operation->comment = 'Отмена отправки рейса '.($this->direction_id==1 ? 'АК ' : 'КА ').$this->name;
        if(!$trip_operation->save(false)) {
            throw new ErrorException('Не удалось сохранить в историю операцию с рейсом');
        }

        // 7. опять логирование отправки рейса, как действия оператора
        DispatcherAccounting::createLog('cancel_trip_sended', 0, 0, 0, $this->id);


        // 8. сообщение в браузеры по сокетам
        SocketDemon::updateMainPages($this->id, $this->date);
        // IncomingOrdersWidget::updateActiveTripsModal();

        return true;
    }


	public function sendOld() {

		if($this->canSend() == false) {
			throw new ForbiddenHttpException('Рейс не может быть отправлен');
		}

		$this->date_sended = time();
		$this->sended_user_id = Yii::$app->user->id;
		if(!$this->save(false)) {
			throw new ErrorException('Не удалось сохранить рейс');
		}

		// пересчет отмененных заказов
		$cancel_order_status = OrderStatus::getByCode('canceled');
		$cancel_orders = Order::find()
			->where(['trip_id' => $this->id])
			->andWhere(['status_id' => $cancel_order_status->id])
			->all();
		foreach($cancel_orders as $order) {
			$client = $order->client;
			if($client != null) {
				$client->canceled_orders_places_count = $client->canceled_orders_places_count + $order->places_count;
				$client->setField('canceled_orders_places_count', $client->canceled_orders_places_count);

//                $client->cashback = $client->cashback - $order->penalty_cash_back;
//                $client->setField('cashback', $client->cashback);
			}
		}

		// клиентам всех заказов (в том числе отменных) пересчитываем счета кэш-бэков
		$trip_orders = $this->orders;
//		$aClientsOrders = [];
//		foreach($trip_orders as $order) {
//			$aClientsOrders[$order->client_id][] = $order;
//		}
//		$clients = Client::find()->where(['id' => ArrayHelper::map($trip_orders, 'client_id', 'client_id')])->all();
//		foreach($clients as $client) {
//			foreach($aClientsOrders[$client->id] as $order) {
//				$client->cashback = $client->cashback
//					+ $order->accrual_cash_back
//					- $order->penalty_cash_back
//					- $order->used_cash_back;
//			}
//			$client->setField('cashback', $client->cashback);
//			$client->setField('sync_date', null);
//		}

		// обращения связанные со всеми заказами рейса (в том числе отмененными) закрываются
		//$orders = Order::find()->where(['trip_id' => $this->id])->all();
		if(count($trip_orders) > 0) {
			// not_completed -> completed_by_trip_sending
			$sql = 'UPDATE `'.CallCase::tableName().'` SET status = "completed_by_trip_sending" WHERE order_id IN('.implode(',', ArrayHelper::map($trip_orders, 'id', 'id')).')';
			Yii::$app->db->createCommand($sql)->execute();

            // где-то здесь нужно пересчитать для заказов: accrual_cash_back, penalty_cash_back,
            //    used_cash_back-это пока не используется
            foreach ($trip_orders as $trip_order) {

				if($trip_order->status_id == 2) { // canceled

					$penalty_cash_back = $trip_order->getCalculatePenaltyCashBack($trip_order->price);
					if($penalty_cash_back != $trip_order->penalty_cash_back) {
                        $trip_order->setField('penalty_cash_back', $penalty_cash_back);
                        $trip_order->penalty_cash_back = $penalty_cash_back;
					}
					if($trip_order->accrual_cash_back > 0) {
                        $trip_order->setField('accrual_cash_back', 0);
                        $trip_order->accrual_cash_back = 0;
					}

				}else {

					$accrual_cash_back = $trip_order->getCalculateAccrualCashBack($trip_order->price);
					if($accrual_cash_back != $trip_order->accrual_cash_back) {
                        $trip_order->setField('accrual_cash_back', $accrual_cash_back);
                        $trip_order->accrual_cash_back = $accrual_cash_back;
					}
					if($trip_order->penalty_cash_back > 0) {
                        $trip_order->setField('penalty_cash_back', 0);
                        $trip_order->penalty_cash_back = 0;
					}
				}
            }


			$clients = Client::find()->where(['id' => ArrayHelper::map($trip_orders, 'client_id', 'client_id')])->all();
			if(count($clients) > 0) {

			    $aClients = ArrayHelper::index($clients, 'id');
			    foreach ($trip_orders as $trip_order) {
                    if(isset($aClients[$trip_order->client_id])) {

                        $client = $aClients[$trip_order->client_id];
                        if($trip_order->accrual_cash_back > 0) {
                            $client->cashback += $trip_order->accrual_cash_back;
                        }

                        if($trip_order->penalty_cash_back > 0) {
                            $client->cashback -= $trip_order->penalty_cash_back;
                        }

                        if($trip_order->used_cash_back > 0) {
                            $client->cashback -= $trip_order->used_cash_back;
                        }

                        if($trip_order->accrual_cash_back > 0 || $trip_order->penalty_cash_back > 0 || $trip_order->used_cash_back > 0) {
                            $client->setField('cashback', $client->cashback);
                        }
                    }
                }
            }

		}


		$day_report_trip_transports = $this->dayReportTripTransports;
		foreach($day_report_trip_transports as $day_report_trip_transport) {
			$day_report_trip_transport->trip_date_sended = $this->date_sended;
			$day_report_trip_transport->trip_sender_id = Yii::$app->user->id;

			$current_user = User::findOne(Yii::$app->user->id);
			$day_report_trip_transport->trip_sender_fio = $current_user->lastname.' '.$current_user->firstname;
			if(!$day_report_trip_transport->save(false)) {
				throw new ErrorException('Не удалось создать запись в отчет отображаемого дня');
			}
		}


		$trip_operation = new TripOperation();
		$trip_operation->type = 'send';
		$trip_operation->comment = 'Отправка рейса '.($this->direction_id==1 ? 'АК ' : 'КА ').$this->name;
		if(!$trip_operation->save(false)) {
			throw new ErrorException('Не удалось сохранить в историю операцию с рейсом');
		}


		SocketDemon::updateMainPages($this->id, $this->date);
		IncomingOrdersWidget::updateActiveTripsModal();

		return true;
	}

	public function cancelSendOld() {

		$this->date_sended = NULL;
		$this->sended_user_id = NULL;
		$this->date_start_sending = NULL;
		//$this->start_sending_user_id = NULL;
		if(!$this->save(false)) {
			throw new ErrorException('Не удалось сохранить рейс');
		}


		// пересчет отмененных заказов
		$cancel_order_status = OrderStatus::getByCode('canceled');
		$cancel_orders = Order::find()
			->where(['trip_id' => $this->id])
			->andWhere(['status_id' => $cancel_order_status->id])
			->all();
		foreach($cancel_orders as $order) {
			$client = $order->client;
			if($client != null) {
				$client->canceled_orders_places_count = $client->canceled_orders_places_count - $order->places_count;
				$client->setField('canceled_orders_places_count', $client->canceled_orders_places_count);
			}
		}


		// клиентам всех заказов (в том числе отменных) пересчитываем счета кэш-бэков
		$trip_orders = $this->orders;
//		$aClientsOrders = [];
//		foreach($trip_orders as $order) {
//			$aClientsOrders[$order->client_id][] = $order;
//		}
//		$clients = Client::find()->where(['id' => ArrayHelper::map($trip_orders, 'client_id', 'client_id')])->all();
//		foreach($clients as $client) {
//			foreach($aClientsOrders[$client->id] as $order) {
//				$client->cashback = $client->cashback
//					- $order->accrual_cash_back
//					+ $order->penalty_cash_back
//					+ $order->used_cash_back;
//			}
//			$client->setField('cashback', $client->cashback);
//			$client->setField('sync_date', null);
//		}


        foreach ($trip_orders as $trip_order) {

            if($trip_order->status_id == 2) { // canceled

                $penalty_cash_back = $trip_order->getCalculatePenaltyCashBack($trip_order->price);
                if($penalty_cash_back != $trip_order->penalty_cash_back) {
                    $trip_order->setField('penalty_cash_back', $penalty_cash_back);
                }
                if($trip_order->accrual_cash_back > 0) {
                    $trip_order->setField('accrual_cash_back', 0);
                }

            }else {

                $accrual_cash_back = $trip_order->getCalculateAccrualCashBack($trip_order->price);
                if($accrual_cash_back != $trip_order->accrual_cash_back) {
                    $trip_order->setField('accrual_cash_back', $accrual_cash_back);
                }
                if($trip_order->penalty_cash_back > 0) {
                    $trip_order->setField('penalty_cash_back', 0);
                }
            }
        }

		// у обращений связанных со всеми заказами рейса (в том числе отмененными) отменяется закрытие
		// completed_by_trip_sending -> not_completed
		// $orders = Order::find()->where(['trip_id' => $this->id])->all();
		if(count($trip_orders) > 0) {
			$sql = 'UPDATE `'.CallCase::tableName().'` SET status = "not_completed" WHERE order_id IN('.implode(',', ArrayHelper::map($trip_orders, 'id', 'id')).')';
			Yii::$app->db->createCommand($sql)->execute();

            // где-то здесь нужно пересчитать для заказов: accrual_cash_back, penalty_cash_back,
            //      used_cash_back-это пока не используется

            $clients = Client::find()->where(['id' => ArrayHelper::map($trip_orders, 'client_id', 'client_id')])->all();
            if(count($clients) > 0) {

                $aClients = ArrayHelper::index($clients, 'id');
                foreach ($trip_orders as $trip_order) {
                    if(isset($aClients[$trip_order->client_id])) {

                        $client = $aClients[$trip_order->client_id];
                        if($trip_order->accrual_cash_back > 0) {
                            $client->cashback -= $trip_order->accrual_cash_back;
                        }

                        if($trip_order->penalty_cash_back > 0) {
                            $client->cashback += $trip_order->penalty_cash_back;
                        }

                        if($trip_order->used_cash_back > 0) {
                            $client->cashback += $trip_order->used_cash_back;
                        }

                        if($trip_order->accrual_cash_back > 0 || $trip_order->penalty_cash_back > 0 || $trip_order->used_cash_back > 0) {
                            $client->setField('cashback', $client->cashback);
                        }
                    }
                }
            }


            foreach ($trip_orders as $trip_order) {

                if($trip_order->penalty_cash_back > 0) {
                    $trip_order->setField('penalty_cash_back', 0);
                }
                if($trip_order->accrual_cash_back > 0) {
                    $trip_order->setField('accrual_cash_back', 0);
                }
            }
		}

		$day_report_trip_transports = $this->dayReportTripTransports;
		foreach($day_report_trip_transports as $day_report_trip_transport) {
			$day_report_trip_transport->trip_date_sended = NULL;
			$day_report_trip_transport->trip_sender_id = NULL;

			$day_report_trip_transport->trip_sender_fio = NULL;
			if(!$day_report_trip_transport->save(false)) {
				throw new ErrorException('Не удалось изменить запись в отчет отображаемого дня');
			}
		}

		DispatcherAccounting::createLog('cancel_trip_sended', 0, 0, 0, $this->id);


		$trip_operation = new TripOperation();
		$trip_operation->type = 'cancel_send';
		$trip_operation->comment = 'Отмена отправки рейса '.($this->direction_id==1 ? 'АК ' : 'КА ').$this->name;
		if(!$trip_operation->save(false)) {
			throw new ErrorException('Не удалось сохранить в историю операцию с рейсом');
		}


		SocketDemon::updateMainPages($this->id, $this->date);

		return true;
	}

	/*
	public function canSend() {

		if(empty($this->date_start_sending)) {
			return false;
		}

		// все заказы на рейсе должны быть подтверждены и посажены или отменены
		$cancel_order_status = OrderStatus::getByCode('canceled');
		$trip_orders = $this->orders;
		foreach($trip_orders as $trip_order) {
			if((empty($trip_order->time_confirm) || empty($trip_order->time_sat)) && $trip_order->status_id != $cancel_order_status->id) {
				return false;
			}
		}

		// все машины на рейсе должны быть отправлены
		$trip_transports = $this->tripTransports;
		foreach($trip_transports as $trip_transport) {
			if(empty($trip_transport->date_sended)) {
				return false;
			}
		}

		return true;
	}*/


	public function canUpdateOrders() {

		// если есть на рейсе "отправленные заказы", то цены не пересчитываются - прерывание создаем
		$sent_order_status = OrderStatus::getByCode('sent');
		$sent_orders = Order::find()->where(['trip_id' => $this->id, 'status_id' => $sent_order_status->id])->all();
		if(count($sent_orders)> 0) {
			//throw new ForbiddenHttpException('Нельзя обновить цену заказам, т.к. на рейсе есть отправленные заказы в количестве: '.count($sent_orders).' шт.');
			return false;
		}

		return true; // если не произошло прерывание, то вернется true
	}

	public function updateOrders() {

		// если в функции canUpdateOrders прерывания не произошло, то всем заказам (созданным или отменным)
		// переписываем цену и сбрасываем ряд полей
		$trip_orders = $this->orders;
		if(count($trip_orders) > 0) {
			foreach($trip_orders as $order) {

				$price = $order->calculatePrice;
				if($order->price != $price) {
					$order->setField('price', $price); // сохранили в базу
				}

//				$accrual_cash_back = ($order->status_id == 2 ? 0 : $order->getCalculateAccrualCashBack($price));
//				if($order->accrual_cash_back != $accrual_cash_back) {
//					$order->setField('accrual_cash_back', $accrual_cash_back); // сохранили в базу
//				}

//				if($order->status_id == 2) { // canceled
//
//					$penalty_cash_back = $order->getCalculatePenaltyCashBack($price);
//					if($penalty_cash_back != $order->penalty_cash_back) {
//						$order->setField('penalty_cash_back', $penalty_cash_back);
//					}
//					if($order->accrual_cash_back > 0) {
//						$order->setField('accrual_cash_back', 0);
//					}
//
//				}else {
//
//					$accrual_cash_back = $order->getCalculateAccrualCashBack($price);
//					if($accrual_cash_back != $order->accrual_cash_back) {
//						$order->setField('accrual_cash_back', $accrual_cash_back);
//					}
//					if($order->penalty_cash_back > 0) {
//						$order->setField('penalty_cash_back', 0);
//					}
//				}
			}

			Order::resetOrders(ArrayHelper::map($trip_orders, 'id', 'id'));
		}

		return true;
	}

	public static function setCommercialTrips($trips_ids) {

		$trips = Trip::find()->where(['IN', 'id', $trips_ids])->all();
		if(count($trips) == null) {
			throw new ForbiddenHttpException('Рейсы не найдены');
		}

		Trip::updateAll(['commercial' => true, 'updated_at' => time()], ['IN', 'id', ArrayHelper::map($trips, 'id', 'id')]);

		// в заказах рейсов должны быть обновлены данные: цена, кол-во призовых поездок.
		$trips_orders = Order::find()->where(['IN', 'trip_id', ArrayHelper::map($trips, 'id', 'id')])->all();
		if(count($trips_orders) > 0) {
			foreach($trips_orders as $order) {
				$prize_trip_count = $order->prizeTripCount;
				if($order->prize_trip_count != $prize_trip_count) {
					$order->setField('prize_trip_count', $prize_trip_count);
				}

				$price = $order->calculatePrice;
				if($order->price != $price) {
					$order->setField('price', $price);
				}

//				$accrual_cash_back = ($order->status_id == 2 ? 0 : $order->getCalculateAccrualCashBack($price));
//				if($accrual_cash_back != $order->accrual_cash_back) {
//					$order->setField('accrual_cash_back', $accrual_cash_back);
//				}

//				if($order->status_id == 2) { // canceled
//
//					$penalty_cash_back = $order->getCalculatePenaltyCashBack($price);
//					if($penalty_cash_back != $order->penalty_cash_back) {
//						$order->setField('penalty_cash_back', $penalty_cash_back);
//					}
//					if($order->accrual_cash_back > 0) {
//						$order->setField('accrual_cash_back', 0);
//					}
//
//				}else {
//
//					$accrual_cash_back = $order->getCalculateAccrualCashBack($price);
//					if($accrual_cash_back != $order->accrual_cash_back) {
//						$order->setField('accrual_cash_back', $accrual_cash_back);
//					}
//					if($order->penalty_cash_back > 0) {
//						$order->setField('penalty_cash_back', 0);
//					}
//				}
			}
		}

		$trip_operation = new TripOperation();
		$trip_operation->type = 'set_commercial';
		$trip_operation->comment = 'Установка ком-х рейсов '
			.($trips[0]->direction_id==1 ? 'АК ' : 'КА ').': '.implode(', ', ArrayHelper::map($trips, 'name', 'name'));
		if(!$trip_operation->save(false)) {
			throw new ErrorException('Не удалось сохранить в историю операцию с рейсом');
		}


		foreach($trips as $trip) {
			SocketDemon::updateMainPages($trip->id, $trip->date);
		}

		return true;
	}

	public static function unsetCommercialTrips($trips_ids) {

		$trips = Trip::find()->where(['IN', 'id', $trips_ids])->all();
		if(count($trips) == null) {
			throw new ForbiddenHttpException('Рейсы не найдены');
		}

		Trip::updateAll(['commercial' => false, 'updated_at' => time()], ['IN', 'id', ArrayHelper::map($trips, 'id', 'id')]);

		// в заказах рейсов должны быть обновлены данные: цена, кол-во призовых поездок.
		$trips_orders = Order::find()->where(['IN', 'trip_id', ArrayHelper::map($trips, 'id', 'id')])->all();
		if(count($trips_orders) > 0) {
			foreach($trips_orders as $order) {
				$prize_trip_count = $order->prizeTripCount;
				if($order->prize_trip_count != $prize_trip_count) {
					$order->setField('prize_trip_count', $prize_trip_count);
				}

				$price = $order->calculatePrice;
				if($price != $order->price) {
					$order->setField('price', $price);
				}

//				$accrual_cash_back = ($order->status_id == 2 ? 0 : $order->getCalculateAccrualCashBack($price));
//				if($accrual_cash_back != $order->accrual_cash_back) {
//					$order->setField('accrual_cash_back', $accrual_cash_back);
//				}

//				if($order->status_id == 2) { // canceled
//
//					$penalty_cash_back = $order->getCalculatePenaltyCashBack($price);
//					if($penalty_cash_back != $order->penalty_cash_back) {
//						$order->setField('penalty_cash_back', $penalty_cash_back);
//					}
//					if($order->accrual_cash_back > 0) {
//						$order->setField('accrual_cash_back', 0);
//					}
//
//				}else {
//
//					$accrual_cash_back = $order->getCalculateAccrualCashBack($price);
//					if($accrual_cash_back != $order->accrual_cash_back) {
//						$order->setField('accrual_cash_back', $accrual_cash_back);
//					}
//					if($order->penalty_cash_back > 0) {
//						$order->setField('penalty_cash_back', 0);
//					}
//				}
			}
		}


		$trip_operation = new TripOperation();
		$trip_operation->type = 'unset_commercial';
		$trip_operation->comment = 'Отмена ком-х рейсов '
			.($trips[0]->direction_id==1 ? 'АК ' : 'КА ').': '.implode(', ', ArrayHelper::map($trips, 'name', 'name'));
		if(!$trip_operation->save(false)) {
			throw new ErrorException('Не удалось сохранить в историю операцию с рейсом');
		}

		foreach($trips as $trip) {
			SocketDemon::updateMainPages($trip->id, $trip->date);
		}


		return true;
	}

    public function getStartTimeUnixtime() {

        $aHoursMinutes = explode(':', $this->start_time);

        return $this->date + 3600*intval($aHoursMinutes[0]) + 60*intval($aHoursMinutes[1]);
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

        $res = Yii::$app->db->createCommand($sql)->execute();

        return $res;
    }
}
