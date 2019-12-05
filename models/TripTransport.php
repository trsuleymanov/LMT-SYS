<?php

namespace app\models;

use app\components\Helper;
use app\models\Order;
use app\models\OrderReport;
use app\models\OrderStatus;
use app\models\SecondTripTransport;
use app\models\Trip;
use app\models\User;
use app\widgets\IncomingOrdersWidget;
use Yii;
use app\models\Driver;
use app\models\Transport;
use yii\base\ErrorException;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;


/**
 * This is the model class for table "trip_transport".
 *
 * @property integer $id
 * @property integer $transport_id
 * @property integer $driver_id
 * @property integer $trip_id
 */
class TripTransport extends \yii\db\ActiveRecord
{
	public static function setFields($aTripTransportsId, $field_name, $field_value)
	{
		if(!empty($field_value)) {
			$field_value = htmlspecialchars($field_value);
		}

		if($field_value === false) {
			$sql = 'UPDATE `'.self::tableName().'` SET '.$field_name.' = false WHERE id IN ('.implode(',', $aTripTransportsId).')';
		}elseif(empty($field_value)) {
			$sql = 'UPDATE `'.self::tableName().'` SET '.$field_name.' = NULL WHERE id IN ('.implode(',', $aTripTransportsId).')';
		}else {
			$sql = 'UPDATE `'.self::tableName().'` SET '.$field_name.' = "'.$field_value.'" WHERE id IN ('.implode(',', $aTripTransportsId).')';
		}

		return Yii::$app->db->createCommand($sql)->execute();
	}

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'trip_transport';
    }

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['transport_id', 'driver_id', 'trip_id'], 'required'],
			[['transport_id', 'driver_id', 'trip_id', 'status_id', 'confirmed_user_id',
				'confirmed_date_time', 'set_date_time', 'set_user_id', 'date_sended', 'sort', 'sender_id',
                'total_places_count', 'used_places_count'], 'integer'],
			[['access_key'], 'string', 'max' => 10],
			[['confirmed'],'boolean']
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'access_key' => 'Ключ доступа в приложение для водителя',
			'transport_id' => 'Машина',
            'total_places_count' => 'Мест в машине',
            'used_places_count' => 'Занято мест в машине',
			'driver_id' => 'Водитель',
			'trip_id' => 'Рейс',
			'status_id' => 'Статус поездки',
			'date_sended' => 'Дата/время отправки',
			'sender_id' => 'Отправитель',
			'confirmed_user_id' => 'Кем получено подтверждение',
			'confirmed_date_time' => 'Когда получено подтверждение',
			'set_date_time' => 'Когда произвелась постановка на рейс',
			'confirmed' => 'Потверждён',
			'set_user_id' => 'Кем поставлен на рейс',
			'sort' => 'Сортировка'
		];
	}
	
	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getTrip()
	{
		return $this->hasOne(Trip::className(), ['id' => 'trip_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getTransport()
	{
		return $this->hasOne(Transport::className(), ['id' => 'transport_id']);
	}

	public function getDayReportTripTransport()
	{
		return $this->hasOne(DayReportTripTransport::className(), ['trip_transport_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getDriver()
	{
		return $this->hasOne(Driver::className(), ['id' => 'driver_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getSetUser()
	{
		return $this->hasOne(User::className(), ['id' => 'set_user_id']);
	}

	public function getSender()
	{
		return $this->hasOne(User::className(), ['id' => 'sender_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getConfirmedUser()
	{
		return $this->hasOne(User::className(), ['id' => 'confirmed_user_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getFactOrders()
	{
		return $this->hasMany(Order::className(), ['fact_trip_transport_id' => 'id']);
	}

	public function afterDelete()
	{
		$order_status = OrderStatus::getByCode('created');

		$aFactOrdersId = ArrayHelper::map($this->factOrdersWithoutCanceled, 'id', 'id');
		if(count($aFactOrdersId) > 0) {
			Yii::$app->db->createCommand('UPDATE `order` SET fact_trip_transport_id = NULL, confirm_selected_transport = 0, time_sat = NULL, status_id = '.$order_status->id.', updated_at = '.time().' WHERE id IN ('.implode(',', $aFactOrdersId).')')->execute();
		}

		// сообщим браузерам что надо обновить страницу рейсов
		if($this->trip_id > 0) {
			$trip = $this->trip;
			SocketDemon::updateMainPages($trip->id, $trip->date);
		}

		$trip = $this->trip;
		if($trip != null && !empty($trip->date_start_sending) && empty($trip->date_sended)) {
			IncomingOrdersWidget::updateActiveTripsModal();
		}

		parent::afterDelete();
	}


	public function beforeSave($insert)
	{
		if($this->isNewRecord){
			$this->set_date_time = strtotime('now');
			$this->set_user_id = Yii::$app->user->id;

			DispatcherAccounting::createLog('trip_transport_create'); // логируем Постановку на рейс т/с
		}

        $this->total_places_count = $this->transport->places_count;

		// сообщим браузерам что надо обновить страницу рейсов
		if($this->trip_id > 0) {
			$trip = $this->trip;
			SocketDemon::updateMainPages($trip->id, $trip->date);
		}

		return parent::beforeSave($insert);
	}

	public function afterSave($insert, $changedAttributes)
	{
		parent::afterSave($insert, $changedAttributes);

		$trip = $this->trip;
		if($trip != null && !empty($trip->date_start_sending) && empty($trip->date_sended)) {
			IncomingOrdersWidget::updateActiveTripsModal();
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

		// сообщим браузерам что надо обновить страницу рейсов
		if($this->trip_id > 0) {
			$trip = $this->trip;
			SocketDemon::updateMainPages($trip->id, $trip->date);
		}

		return Yii::$app->db->createCommand($sql)->execute();
	}


	public function setConfirm($confirmed = true){

		if($confirmed){

			if(empty($this->driver_id)) {
				throw new ForbiddenHttpException('Нельзя подтвердить т/с без водителя');
			}

			$prevTransportDuplicatesCount = $this->prevTransportDuplicatesCount;
			if($prevTransportDuplicatesCount > 0) {
				$second_trip_transport = SecondTripTransport::find()->where(['transport_id' => $this->transport_id, 'date' => $this->trip->date])->one();
				if($second_trip_transport != null) {
					if($prevTransportDuplicatesCount > 1) {
						throw new ForbiddenHttpException('Нельзя подтвердить повторно использованных дублирующий транспорт второго рейса');
					}
				}else {
					throw new ForbiddenHttpException('Нельзя подтвердить дублирующий транспорт');
				}
			}

			if(!$this->confirmed){
				$this->confirmed = true;
				$this->confirmed_date_time = strtotime('now');
				$this->confirmed_user_id = Yii::$app->user->id;
				$this->access_key = $this->generateAccessKey();
			}
		} else {
			if($this->confirmed){
				$this->confirmed = false;
				$this->confirmed_date_time = null;
				$this->confirmed_user_id = null;
				//$this->access_key = '';
				$this->deleteAccessKey();
			}
		}

		if(!$this->save(false)){
			return false;
		} else {

			// сообщим браузерам что надо обновить страницу рейсов
			if($this->trip_id > 0) {
				$trip = $this->trip;
				SocketDemon::updateMainPages($trip->id, $trip->date);
			}

			return true;
		}
	}



	// вместе с ключем удаляется и токен у связанного пользователя
	public function deleteAccessKey() {
		$this->setField('access_key', '');
		$driver = $this->driver;
		if($driver != null) {
			$user = $driver->user;
			if($user != null) {
				$user->setField('token', '');
			}

			if($driver->magic_device_code_id > 0) {
				$driver->setField('magic_device_code_id', '');
			}
		}
	}

	public function isDuplicatedInDirection() {

		// нахожу рейс
		$trip = $this->trip;

		// нахожу все рейсы этого направления-дня идущие до текущего рейса
		$direction_trips = Trip::find()
			->where(['direction_id' => $trip->direction_id])
			->andWhere(['date' => $trip->date])
			->andWhere(['<', 'start_time', $trip->start_time])
			->all();

		if(count($direction_trips) == 0) {
			return false;
		}

		// нахожу наличие хоть одного trip_transport с таким же transport_id из выбранных рейсов
		$trip_transport = TripTransport::find()
			->where(['transport_id' => $this->transport_id])
			->andWhere(['IN', 'trip_id', ArrayHelper::map($direction_trips, 'id', 'id')])
			->one();

		return ($trip_transport != null);
	}

	public function getPrevTransportDuplicatesCount() {

		// нахожу рейс
		$trip = $this->trip;
		if($trip == null) {
			return 0;
		}

		// нахожу все рейсы этого направления-дня идущие до текущего рейса
		$direction_trips = Trip::find()
			->where(['direction_id' => $trip->direction_id])
			->andWhere(['date' => $trip->date])
			->andWhere(['<', 'end_time', $trip->end_time])
			->all();

		if(count($direction_trips) == 0) {
			return false;
		}

		$trip_transports = TripTransport::find()
			->where(['transport_id' => $this->transport_id])
			->andWhere(['IN', 'trip_id', ArrayHelper::map($direction_trips, 'id', 'id')])
			->all();

		return count($trip_transports);
	}

	/*
	 * Функция возвращает класс состояния транспорта для отображения в рейсах на главной странице и на стр-це Расстановки
	 *
	 * @return string
	 */

	public function getStatusClass() {

		if(!empty($this->date_sended > 0)) {

            // $class = 'sended'; // отправленная машина
		    if($this->used_places_count < $this->total_places_count) {
                $class = 'sended_with_free_places';
            }else {
                $class = 'sended_without_free_places';
            }

		}else {

			$prevTransportDuplicatesCount = $this->prevTransportDuplicatesCount;
			if($prevTransportDuplicatesCount > 0) {
				// или 4 или 5 или 6

				// наличие транспорта во вторых рейсах
				$second_trip_transport = SecondTripTransport::find()
					->where([
						'transport_id' => $this->transport_id,
						'date' => $this->trip->date
					])->one();
				if($second_trip_transport == null) {
					$class = 'duplicate'; // дубликат
				}else {
					if($prevTransportDuplicatesCount == 1) {
						$class = 'second-transport'; //транспорт второго рейса при первом вхождении
					}else {
						$class = 'duplicate-second-transport'; // дубликат машины второго рейса
					}
				}

			}else {
				// или 1 или 2
				if($this->confirmed == 1) {
					$class = 'confirmed';  // подтвержденная машина
				}else {
					$class = 'unconfirmed';  // неподтвержденная машина
				}
			}
		}

		return $class;
	}


	public function getFactOrdersWithoutCanceled() {

		$canceled_order_status = OrderStatus::getByCode('canceled');

		return Order::find()
			->where(['fact_trip_transport_id' => $this->id])
			->andWhere(['!=', 'status_id', $canceled_order_status->id])
			->all();
	}

	public function getFactCanceledOrders() {

		$canceled_order_status = OrderStatus::getByCode('canceled');

		return Order::find()
			->where(['fact_trip_transport_id' => $this->id])
			->andWhere(['status_id' => $canceled_order_status->id])
			->all();
	}

	public function getFactPlacesCount() {

		$fact_orders = $this->factOrdersWithoutCanceled;

		$places = 0;
		if(count($fact_orders) > 0) {
			foreach ($fact_orders as $fact_order) {
				$places += $fact_order->places_count;
			}
		}

		return $places;
	}

	public function getConfirmFactPlacesCount() {

		$fact_orders = $this->factOrdersWithoutCanceled;

		$places = 0;
		if(count($fact_orders) > 0) {
			foreach ($fact_orders as $fact_order) {
				if(!empty($fact_order->confirmed_time_sat)) {
					$places += $fact_order->places_count;
				}
			}
		}

		return $places;
	}

	public function getFactKZMPlacesCount() {

		$fact_orders = $this->factOrdersWithoutCanceled;

		$places = 0;
		if(count($fact_orders) > 0) {
			foreach ($fact_orders as $fact_order) {
				if($fact_order->confirm_selected_transport == 1) {
					$places += $fact_order->places_count;
				}
			}
		}

		return $places;
	}

	public function getFactSatPlacesCount() {

		$fact_orders = $this->factOrdersWithoutCanceled;

		$places = 0;
		if(count($fact_orders) > 0) {
			foreach ($fact_orders as $fact_order) {
				if(!empty($fact_order->time_sat)) {
					$places += $fact_order->places_count;
				}
			}
		}

		return $places;
	}


	public function getStatusName()
	{
		if($this->status_id == 1) {
			return 'отправлен';
		}else {
			if($this->confirmed == 1) {
				return 'подтвержден';
			}else {
				return 'не подтвержден';
			}
		}
	}


    public function getTransportDriverInfo($trip_transport_id=null){
		if($trip_transport_id === null){
			if(!$this->isNewRecord){
				$rec_id = $this->id;
				$model = $this;
			} else {
				return false;
			}
		} else {
			$rec_id = $trip_transport_id;
			$model = self::findOne($rec_id);
		}


		$driver = Driver::find()->where(['id'=>$model->driver_id])->one();

		if($driver){

			$driver_info = Driver::find()->where(['id'=>$model->driver_id])->asArray()->one();

			$transport_primary = Transport::find()
				->where(['id'=>$driver->primary_transport_id])
				//->andWhere(['active' => 1])
				->asArray()
				->one();//$driver->getPrimaryTransport()->asArray();
			$transport_secondary = Transport::find()
				->where(['id'=>$driver->secondary_transport_id])
				//->andWhere(['active' => 1])
				->asArray()
				->one();//$driver->getSecondaryTransport()->asArray();

			$driver_info['primary_transport'] = $transport_primary;
			$driver_info['secondary_transport'] = $transport_secondary;
		} else {
			$driver_info = null;
		}

		$transport_info = Transport::find()
			->where(['id'=>$model->transport_id])
			//->andWhere(['active' => 1])
			->asArray()
			->one();

		if($transport_info){
			$transport_info['to_show'] = $transport_info['sh_model'].'('.$transport_info['car_reg'].')';
		}

		// уже нет сил этот кошмар править, на потом...
		$trip_transport = TripTransport::find()->where(['id'=>$model->id])->one();
		$modelAsArray = TripTransport::find()->where(['id'=>$model->id])->asArray()->one();

		$tripAsArray = Trip::find()->where(['id'=>$model->trip_id])->asArray()->one();
		$setOnTripUserAsArray = User::find()->where(['id'=>$model->set_user_id])->asArray()->one();
		if($model->confirmed_user_id){
			$confirmedUserAsArray = User::find()->where(['id'=>$model->confirmed_user_id])->asArray()->one();
		} else {
			$confirmedUserAsArray = null;
		}

		$direction = Direction::find()->where(['id'=>$tripAsArray['direction_id']])->asArray()->one();

		$tripAsArray['direction'] = $direction;

		$result = [
			'driver' =>$driver_info,
			'transport' => $transport_info,
			'id' => $rec_id,
			'trip_id' => $model->trip_id,
			'confirmed' => $model->confirmed,
			'trip_transport' => $modelAsArray,
			'trip_transport_ob' => $trip_transport,
			'trip' => $tripAsArray,
			'set_on_trip_user' => $setOnTripUserAsArray, 'confirmed_user'=>$confirmedUserAsArray];

		return $result;
	
    }

	public function generateAccessKey() {

		$this->access_key = '';
		for($i = 0; $i < 10; $i++) {
			$this->access_key .= rand(0, 9);
		}

		return $this->access_key;
	}


    /*
     * Отправка машины (загрузились пассажиры и машина поехала по рейсу)
     */
    public function send() {

//  - нужно чтобы логировалось:
//     - Дата, время, оператор выпуска (отправки) т/с
//     - Неотмененным заказам - статус "Отправлено" и время отправки

        // если есть на рейсе заказы не посаженные и не привязанные к текущей машине,
        // а текущая машина является последней не отправленной и машиной, то нельзя отправлять.
        $created_order_status = OrderStatus::getByCode('created');
        $trip_created_orders = Order::find()
            ->where(['trip_id' => $this->trip_id, 'status_id' => $created_order_status->id])
            ->andWhere([
                'or',
                ['!=', 'fact_trip_transport_id', $this->id],
                ['fact_trip_transport_id' => NULL]
            ])
            ->all();

        if(count($trip_created_orders) > 0) {
            // ищем хоть один не отправленный транспорт кроме текущего
            $not_sended_trip_transports = TripTransport::find()
                ->where(['trip_id' => $this->trip_id])
                ->andWhere([
                    'or',
                    ['date_sended' => 0],
                    ['date_sended' => NULL]
                ])
                ->andWhere(['!=', 'id', $this->id])->one();
            if($not_sended_trip_transports == null) {
                throw new ForbiddenHttpException('Нельзя отправить последнюю на рейсе машину при наличии непосаженных пассажиров');
            }
        }

        if(empty($this->transport->base_city_id)) {
            throw new ForbiddenHttpException('Транспортному средству необходимо установить город базирования');
        }


        $trip = $this->trip;
        $fact_orders_without_canceled = $this->factOrdersWithoutCanceled;
        if(count($fact_orders_without_canceled) == 0)
        {
            // отправляем пустую машину
            $this->status_id = 1;
            $this->date_sended = time();
            $this->sender_id = Yii::$app->user->id;
            //$this->access_key = ''; // сброс идентификатора доступа
            if(!$this->save(false)) {
                throw new ErrorException('Не удалось сохранить изменения в trip_transport');
            }

        }else {

            $aNotConfirmTimeSat = [];
            foreach($fact_orders_without_canceled as $fact_order) {
                if(empty($fact_order->time_sat)) {
                    throw new ForbiddenHttpException('У вас есть непосаженные пассажиры, прикрепленные к этой машине');
                }
                if(empty($fact_order->confirmed_time_sat)) {
                    $aNotConfirmTimeSat[$fact_order->id] = $fact_order->id;
                }
            }

            // все заказы привязанные "фактически" к отправляемой машине перевожу в статус "Отправлен"
            $order_status = OrderStatus::getByCode('sent');
            $aFactOrdersId = ArrayHelper::map($fact_orders_without_canceled, 'id', 'id');
            if(count($aFactOrdersId) > 0) {
                Yii::$app->db->createCommand('UPDATE `order` SET status_id = ' . $order_status->id . ', status_setting_time=' . time() . ', updated_at=' . time() . ' WHERE id IN (' . implode(',', $aFactOrdersId) . ')')->execute();
            }

            // всем заказам с неподтвержденной посадкой устанавливаем подтвержденность посадки
            if(count($aNotConfirmTimeSat) > 0) {
                Yii::$app->db->createCommand('UPDATE `order` SET confirmed_time_sat = ' . time() . ' WHERE id IN (' . implode(',', $aNotConfirmTimeSat) . ')')->execute();
            }


            $this->status_id = 1;
            $this->date_sended = time();
            $this->sender_id = Yii::$app->user->id;
            //$this->access_key = ''; // сброс идентификатора доступа
            if(!$this->save(false)) {
                throw new ErrorException('Не удалось сохранить изменения в trip_transport');
            }
        }

        DispatcherAccounting::createLog('trip_transport_send'); // логируем Отправку т/с


        // удаляются все машины-дубли на текущем дне-направлении. И заказы привязанные к этим дублям отвязываются.
        $day_direction_trips = Trip::find()->where(['date' => $trip->date, 'direction_id' => $trip->direction_id])->all();
        if(count($day_direction_trips) > 0) {
            $doubles_trip_transports = TripTransport::find()
                ->where(['transport_id' => $this->transport_id])
                ->andWhere(['IN', 'trip_id', ArrayHelper::map($day_direction_trips, 'id', 'id')])
                ->andWhere(['!=', 'id', $this->id])
                ->andWhere(['date_sended' => NULL])
                ->all();

            // отвязываем заказы от этих "машин"
            if(count($doubles_trip_transports) > 0) {
                $doublesTripTransportsIds = ArrayHelper::map($doubles_trip_transports, 'id', 'id');
                $orders = Order::find()->where(['IN', 'fact_trip_transport_id', $doublesTripTransportsIds])->all();
                if (count($orders) > 0) {
                    Order::setFields(ArrayHelper::map($orders, 'id', 'id'), 'fact_trip_transport_id', '');
                    Order::setFields(ArrayHelper::map($orders, 'id', 'id'), 'confirm_selected_transport', false);
                    Order::setFields(ArrayHelper::map($orders, 'id', 'id'), 'time_sat', NULL);
                }
            }

            // удаляем машины
            if(count($doubles_trip_transports) > 0) {
                $sql = 'DELETE FROM `' . TripTransport::tableName() . '` WHERE id IN (' . implode(',', $doublesTripTransportsIds) . ')';
                Yii::$app->db->createCommand($sql)->execute();
            }


            // если на дне-направлении уже есть одна отправленная такая же машина, то
            // деактивируется машина в таблице "вторых рейсов" при наличии таковой
            $doubles_sended_trip_transports = TripTransport::find()
                ->where(['transport_id' => $this->transport_id])
                ->andWhere(['IN', 'trip_id', ArrayHelper::map($day_direction_trips, 'id', 'id')])
                ->andWhere(['!=', 'id', $this->id])
                ->andWhere(['>', 'date_sended', 0])
                ->all();
            if(count($doubles_sended_trip_transports) > 0) {
                $second_trip_transport = SecondTripTransport::find()->where([
                    'transport_id' => $this->transport_id,
                    'date' => $trip->date
                ])->one();
                if($second_trip_transport != null) {
                    $second_trip_transport->active = false;
                    if(!$second_trip_transport->save(false)) {
                        throw new ForbiddenHttpException('Не удалось сохранить транспорт второго рейса');
                    }
                }
            }
        }

        // если на рейсе больше нет не отправленных машин, то перевожу рейс в статус "выпущено оператором"
        // ищем хоть один не отправленный транспорт кроме текущего
        $not_sended_trip_transport = TripTransport::find()
            ->where(['trip_id' => $this->trip_id])
            ->andWhere([
                'or',
                ['date_sended' => 0],
                ['date_sended' => NULL]
            ])
            ->one();
        if($not_sended_trip_transport == null) {

            // проверяем есть ли свободные места в отправленных т/с
            $sended_trip_transports = TripTransport::find()
                ->where(['trip_id' => $this->trip_id])
                ->all();

            $has_free_places = false;
            foreach ($sended_trip_transports as $trip_transport) {
                $transport = $trip_transport->transport;
                if($transport->places_count > $trip_transport->factKZMPlacesCount) {
                    $has_free_places = true;
                    break;
                }
            }


            $trip->date_issued_by_operator = time();
            $trip->issued_by_operator_id = Yii::$app->user->id;
            $trip->has_free_places = $has_free_places;
            if(!$trip->save(false)) {
                throw new ForbiddenHttpException('Не удалось сохранить рейс');
            }

            // логирование операции выпуска рейса - в операции с рейсами
            $trip_operation = new TripOperation();
            $trip_operation->type = 'issued_by_operator';
            $trip_operation->comment = 'Выпуск рейса '.($trip->direction_id==1 ? 'АК ' : 'КА ').$trip->name;
            if(!$trip_operation->save(false)) {
                throw new ErrorException('Не удалось сохранить в историю операцию с рейсом');
            }

            // логирование действия оператора
            DispatcherAccounting::createLog('trip_issued_by_operator'); // логируем Отправку т/с
        }



        // отправляем в приложение водителю сообщение "Погрузка завершена"
        if($this->driver != null) {

            $magic_code = '';
            $aMesData = [
                'message_type' => 'message',
                'message' => 'Погрузка завершена',
            ];
            if($this->driver->magicDevice != null) {
                $magic_code = $this->driver->magicDevice->code;
                SocketDemon::sendOutDeviceMessageInstant($magic_code, $aMesData);
            }
            if(!empty($this->driver->device_code) && $this->driver->device_code != $magic_code) {
                SocketDemon::sendOutDeviceMessageInstant($this->driver->device_code, $aMesData);
            }
        }


        return true;
    }


	/*
	 * Отправка машины (загрузились пассажиры и машина поехала по рейсу)
	 */
	public function sendOld()
	{
		// если есть на рейсе заказы не посаженные и не привязанные к текущей машине,
		// а текущая машина является последней не отправленной и машиной, то нельзя отправлять.
		$created_order_status = OrderStatus::getByCode('created');
		$trip_created_orders = Order::find()
			->where(['trip_id' => $this->trip_id, 'status_id' => $created_order_status->id])
			->andWhere([
				'or',
				['!=', 'fact_trip_transport_id', $this->id],
				['fact_trip_transport_id' => NULL]
			])
			->all();

		if(count($trip_created_orders) > 0) {
			// ищем хоть один не отправленный транспорт кроме текущего
			$not_sended_trip_transports = TripTransport::find()
				->where(['trip_id' => $this->trip_id])
				->andWhere([
					'or',
					['date_sended' => 0],
					['date_sended' => NULL]
				])
				->andWhere(['!=', 'id', $this->id])->one();
			if($not_sended_trip_transports == null) {
				throw new ForbiddenHttpException('Нельзя отправить последнюю на рейсе машину при наличии непосаженных пассажиров');
			}
		}

		if(empty($this->transport->base_city_id)) {
			throw new ForbiddenHttpException('Транспортному средству необходимо установить город базирования');
		}


		$trip = $this->trip;
		$fact_orders_without_canceled = $this->factOrdersWithoutCanceled;
		if(count($fact_orders_without_canceled) == 0)
		{
			// отправляем пустую машину
			$this->status_id = 1;
			$this->date_sended = time();
			$this->sender_id = Yii::$app->user->id;
			//$this->access_key = ''; // сброс идентификатора доступа
			if(!$this->save(false)) {
				throw new ErrorException('Не удалось сохранить изменения в trip_transport');
			}

		}else {

			$aNotConfirmTimeSat = [];
			foreach($fact_orders_without_canceled as $fact_order) {
				if(empty($fact_order->time_sat)) {
					throw new ForbiddenHttpException('У вас есть непосаженные пассажиры, прикрепленные к этой машине');
				}
				if(empty($fact_order->confirmed_time_sat)) {
					$aNotConfirmTimeSat[$fact_order->id] = $fact_order->id;
				}
			}

			// все заказы привязанные "фактически" к отправляемой машине перевожу в статус "Отправлен"
			$order_status = OrderStatus::getByCode('sent');
			$aFactOrdersId = ArrayHelper::map($fact_orders_without_canceled, 'id', 'id');
			if(count($aFactOrdersId) > 0) {
				Yii::$app->db->createCommand('UPDATE `order` SET status_id = ' . $order_status->id . ', status_setting_time=' . time() . ', updated_at=' . time() . ' WHERE id IN (' . implode(',', $aFactOrdersId) . ')')->execute();
			}

			// всем заказам с неподтвержденной посадкой устанавливаем подтвержденность посадки
			if(count($aNotConfirmTimeSat) > 0) {
				Yii::$app->db->createCommand('UPDATE `order` SET confirmed_time_sat = ' . time() . ' WHERE id IN (' . implode(',', $aNotConfirmTimeSat) . ')')->execute();
			}


			// Когда заказы переходят в статус "Отправлен", то в таблице клиентов пересчитываются: order_count++, prize_trip_count?++
			foreach($fact_orders_without_canceled as $fact_order) {

				$client = $fact_order->client;

				if($fact_order->prize_trip_count > 0) {
					//$client->sended_prize_trip_count += $fact_order->prize_trip_count;
					//$client->setField('sended_prize_trip_count', $client->sended_prize_trip_count);

					$client->current_year_sended_prize_places += $fact_order->prize_trip_count;
					$client->setField('current_year_sended_prize_places', $client->current_year_sended_prize_places);
				}


				if($fact_order->informerOffice != null && $fact_order->informerOffice->cashless_payment == 1) {

					//$client->sended_informer_beznal_orders_places_count += $fact_order->places_count;
					//$client->setField('sended_informer_beznal_orders_places_count', $client->sended_informer_beznal_orders_places_count);

					$client->current_year_sended_informer_beznal_places += $fact_order->places_count;
					$client->setField('current_year_sended_informer_beznal_places', $client->current_year_sended_informer_beznal_places);

					$client->current_year_sended_informer_beznal_orders += 1;
					$client->setField('current_year_sended_informer_beznal_orders', $client->current_year_sended_informer_beznal_orders);

				}elseif($fact_order->is_not_places == 1) { // или счетчик "посылок" (нет места) инкрементируется

					//$client->sended_is_not_places_order_count++;
					//$client->setField('sended_is_not_places_order_count', $client->sended_is_not_places_order_count);

					$client->current_year_sended_isnotplaces_orders++;
					$client->setField('current_year_sended_isnotplaces_orders', $client->current_year_sended_isnotplaces_orders);

				}elseif($fact_order->use_fix_price == 1) { // или увеличивается счетчик мест отправленных фикс. заказов

					//$client->sended_fixprice_orders_places_count += $fact_order->places_count;
					//$client->setField('sended_fixprice_orders_places_count', $client->sended_fixprice_orders_places_count);

					$client->current_year_sended_fixprice_places += $fact_order->places_count;
					$client->setField('current_year_sended_fixprice_places', $client->current_year_sended_fixprice_places);

					$client->current_year_sended_fixprice_orders += 1;
					$client->setField('current_year_sended_fixprice_orders', $client->current_year_sended_fixprice_orders);

				}else { // или увеличивается общий счетчик отправленных мест

					//$client->sended_orders_places_count += $fact_order->places_count;
					//$client->setField('sended_orders_places_count', $client->sended_orders_places_count);
				}

				// пересчитываются общие счетчики
				//$client->current_year_sended_places += $fact_order->places_count;
				//$client->setField('current_year_sended_places', $client->current_year_sended_places);

				//$client->current_year_sended_orders += 1;
				//$client->setField('current_year_sended_orders', $client->current_year_sended_orders);

				//Client::recountSendedCanceledReliabilityCounts($client->id, 1, $fact_order->places_count, 0 , 0);

				if($client != null) {
					$client->recountSendedCanceledReliabilityCounts($fact_order, 1, $fact_order->places_count, 0 , 0);
				}


			}

			// получаем свещую информацию по заказам и по связанным заявкам пересчитываем статус
//			$fact_orders = $this->factOrdersWithoutCanceled;
//			foreach($fact_orders as $fact_order) {
//				$fact_order->setClientExtStatus();
//			}


			$this->status_id = 1;
			$this->date_sended = time();
			$this->sender_id = Yii::$app->user->id;
			//$this->access_key = ''; // сброс идентификатора доступа
			if(!$this->save(false)) {
				throw new ErrorException('Не удалось сохранить изменения в trip_transport');
			}
		}


		// отдельно пройдемся по отмененным заказам и увелим счетчик отмененных заказов/мест
//		$fact_cancelled_orders = $this->factCanceledOrders;
//
//		$aFactCanceledOrdersId = ArrayHelper::map($fact_cancelled_orders, 'id', 'id');
//		if(count($aFactCanceledOrdersId) > 0) {
//			Yii::$app->db->createCommand('UPDATE `order` SET date_sended=' . time() . ' WHERE id IN (' . implode(',', $aFactCanceledOrdersId) . ')')->execute();
//		}
//
//		foreach($fact_cancelled_orders as $fact_canceled_order) {
//			$client = $fact_canceled_order->client;
//			$client->current_year_canceled_orders++;
//			$client->setField('current_year_canceled_orders', $client->current_year_canceled_orders);
//
//			$client->current_year_canceled_places += $fact_canceled_order->places_count;;
//			$client->setField('current_year_canceled_places', $client->current_year_canceled_places);
//		}



		// удаляются все машины-дубли на текущем дне-направлении. И заказы привязанные к этим дублям отвязываются.
		$day_direction_trips = Trip::find()->where(['date' => $trip->date, 'direction_id' => $trip->direction_id])->all();
		if(count($day_direction_trips) > 0) {
			$doubles_trip_transports = TripTransport::find()
				->where(['transport_id' => $this->transport_id])
				->andWhere(['IN', 'trip_id', ArrayHelper::map($day_direction_trips, 'id', 'id')])
				->andWhere(['!=', 'id', $this->id])
				->andWhere(['date_sended' => NULL])
				->all();

			// отвязываем заказы от этих "машин"
			if(count($doubles_trip_transports) > 0) {
				$doublesTripTransportsIds = ArrayHelper::map($doubles_trip_transports, 'id', 'id');
				$orders = Order::find()->where(['IN', 'fact_trip_transport_id', $doublesTripTransportsIds])->all();
				if (count($orders) > 0) {
					Order::setFields(ArrayHelper::map($orders, 'id', 'id'), 'fact_trip_transport_id', '');
					Order::setFields(ArrayHelper::map($orders, 'id', 'id'), 'confirm_selected_transport', false);
					Order::setFields(ArrayHelper::map($orders, 'id', 'id'), 'time_sat', NULL);
				}
			}

			// удаляем машины
			if(count($doubles_trip_transports) > 0) {
				$sql = 'DELETE FROM `' . TripTransport::tableName() . '` WHERE id IN (' . implode(',', $doublesTripTransportsIds) . ')';
				Yii::$app->db->createCommand($sql)->execute();
			}


			// если на дне-направлении уже есть одна отправленная такая же машина, то
			// деактивируется машина в таблице "вторых рейсов" при наличии таковой
			$doubles_sended_trip_transports = TripTransport::find()
				->where(['transport_id' => $this->transport_id])
				->andWhere(['IN', 'trip_id', ArrayHelper::map($day_direction_trips, 'id', 'id')])
				->andWhere(['!=', 'id', $this->id])
				->andWhere(['>', 'date_sended', 0])
				->all();
			if(count($doubles_sended_trip_transports) > 0) {
				$second_trip_transport = SecondTripTransport::find()->where([
					'transport_id' => $this->transport_id,
					'date' => $trip->date
				])->one();
				if($second_trip_transport != null) {
					$second_trip_transport->active = false;
					if(!$second_trip_transport->save(false)) {
						throw new ForbiddenHttpException('Не удалось сохранить транспорт второго рейса');
					}
				}
			}
		}



		// "логируем" данные
		$direction = $trip->direction;
		$transport = $this->transport;
		$driver = $this->driver;
		$current_user = User::findOne(Yii::$app->user->id);

		$day_report_trip_transport = new DayReportTripTransport();
		$day_report_trip_transport->date = $trip->date;
		$day_report_trip_transport->direction_id = $direction->id;
		$day_report_trip_transport->direction_name = $direction->sh_name;
		$day_report_trip_transport->trip_id = $trip->id;
		$day_report_trip_transport->trip_name = $trip->name;
		$day_report_trip_transport->trip_transport_id = $this->id;
		$day_report_trip_transport->transport_id = $transport->id;
		$day_report_trip_transport->transport_car_reg = $transport->car_reg;
		$day_report_trip_transport->transport_model = $transport->model;
		$day_report_trip_transport->transport_places_count = $transport->places_count;
		$day_report_trip_transport->transport_date_sended = $this->date_sended;
		$day_report_trip_transport->transport_sender_id = Yii::$app->user->id;
		$day_report_trip_transport->transport_sender_fio = $current_user->lastname.' '.$current_user->firstname;
//		if($round_is_completed == true) {
//			$day_report_trip_transport->transport_round_is_completed = true;
//			$day_report_trip_transport->transport_round_completing_reason_id = $transport_round_completing_reason;
//		}
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

		if(!$day_report_trip_transport->save(false)) {
			throw new ErrorException('Не удалось сохранить информацию в отчет отображаемого дня');
		}



		// записываем в "круги" отправленную машину

		$trip_start_time = $trip->date + Helper::convertHoursMinutesToSeconds($trip->start_time);
		$transport_circle = DayReportTransportCircle::find()
			->where(['transport_id' => $this->transport->id, 'state' => 0])
			->andWhere(['<', 'base_city_trip_start_time', $trip_start_time])
			->orderBy(['id' => SORT_DESC])
			->one();

		// если отправляемая машина выезжает из города базирования, то создаем новый круг.
		if($trip->direction->city_from == $this->transport->base_city_id) {

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
			$transport_circle->transport_id = $this->transport->id;
			$transport_circle->base_city_trip_id = $trip->id;
			$transport_circle->base_city_trip_start_time = $trip_start_time;
			$transport_circle->base_city_day_report_id = $day_report_trip_transport->id;
			$transport_circle->state = 0;
			$transport_circle->time_setting_state = time();
			$transport_circle->total_proceeds = $day_report_trip_transport->proceeds;
            $transport_circle->total_paid_summ = $day_report_trip_transport->paid_summ;

		}else { // иначе завершаем старый круг (или создаем новый круг с завершением)

			if($transport_circle == null) {
				$transport_circle = new DayReportTransportCircle();
				$transport_circle->transport_id = $this->transport->id;
				$transport_circle->notbase_city_trip_id = $trip->id;
				$transport_circle->notbase_city_trip_start_time = $trip_start_time;
				$transport_circle->notbase_city_day_report_id = $day_report_trip_transport->id;
				$transport_circle->state = 1;
				$transport_circle->time_setting_state = time();
				$transport_circle->total_proceeds = $day_report_trip_transport->proceeds;
                $transport_circle->total_paid_summ = $day_report_trip_transport->paid_summ;
			}else {
				$transport_circle->transport_id = $this->transport->id;
				$transport_circle->notbase_city_trip_id = $trip->id;
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


		// логируем отправленные заказы OrderReport
		$aOrdersReports = [];
		foreach($fact_orders_without_canceled as $fact_order) {

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
				// 'time_vpz' => $fact_order->time_vpz, - это и есть поле first_writedown_click_time
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


		// отправляем в приложение сообщение "Погрузка завершена"
//		if($driver != null && (!empty($driver->device_code) || $driver->magic_device_code_id > 0)) {
//			SocketDemon::sendOutDeviceMessageInstant($driver->device_code, 'message', 'Погрузка завершена');
//		}
		if($driver != null) {
//			if($driver->magicDevice != null) {
//				SocketDemon::sendOutDeviceMessageInstant($driver->magicDevice->code, 'message', 'Погрузка завершена');
//				SocketDemon::sendOutDeviceMessageInstant($driver->device_code, 'message', 'Погрузка завершена');
//			}elseif(!empty($driver->device_code)) {
//				SocketDemon::sendOutDeviceMessageInstant($driver->device_code, 'message', 'Погрузка завершена');
//			}

			// отправляем сообщение на все возможные устройства пользователя
			$magic_code = '';
			$aMesData = [
				'message_type' => 'message',
				'message' => 'Погрузка завершена',
			];
			if($driver->magicDevice != null) {
				$magic_code = $driver->magicDevice->code;
				//SocketDemon::sendOutDeviceMessageInstant($magic_code, 'message', 'Погрузка завершена');
				SocketDemon::sendOutDeviceMessageInstant($magic_code, $aMesData);
			}
			if(!empty($driver->device_code) && $driver->device_code != $magic_code) {
				//SocketDemon::sendOutDeviceMessageInstant($driver->device_code, 'message', 'Погрузка завершена');
				SocketDemon::sendOutDeviceMessageInstant($driver->device_code, $aMesData);
			}
		}

		// SocketDemon::sendOutDeviceMessageInstant($driver->device_code, 'message', $text, $user_fio);
		return true;
	}


	/*
	 * Отмена отправки машины
	 */
    public function cancelSend()
    {
        $fact_orders = $this->factOrdersWithoutCanceled;
        if(count($fact_orders) == 0)
        {
            // отменяем отправку пустой машины
            $this->status_id = 0;
            $this->date_sended = NULL;
            $this->sender_id = NULL;
            $this->access_key = $this->generateAccessKey(); // установка идентификатора доступа
            if(!$this->save(false)) {
                throw new ErrorException('Не удалось сохранить изменения в trip_transport');
            }

        }else {

            // все отправленные заказы привязанные "фактически" к текущей машине перевожу в статус "Записан"
            $created_order_status = OrderStatus::getByCode('created');
            $aFactOrdersId = ArrayHelper::map($fact_orders, 'id', 'id');
            if (count($aFactOrdersId) > 0) {
                Yii::$app->db->createCommand('UPDATE `order` SET status_id = ' . $created_order_status->id . ', status_setting_time=' . time() . ', updated_at=' . time() . ' WHERE id IN (' . implode(',', $aFactOrdersId) . ')')->execute();
            }

            $this->status_id = 0;
            $this->date_sended = NULL;
            $this->sender_id = NULL;
            $this->access_key = $this->generateAccessKey(); // установка идентификатора доступа
            if(!$this->save(false)) {
                throw new ErrorException('Не удалось сохранить изменения в trip_transport');
            }
        }

        // не существует лока отмены отправки т/с
        //DispatcherAccounting::createLog('trip_transport_send'); // логируем Отправку т/с


        return true;
    }


	public function cancelSendOld()
	{
		$fact_orders = $this->factOrdersWithoutCanceled;
		if(count($fact_orders) == 0)
		{
			// отменяем отправку пустой машины
			$this->status_id = 0;
			$this->date_sended = NULL;
			$this->sender_id = NULL;
			$this->access_key = $this->generateAccessKey(); // установка идентификатора доступа
			if(!$this->save(false)) {
				throw new ErrorException('Не удалось сохранить изменения в trip_transport');
			}

		}else {

			// все отправленные заказы привязанные "фактически" к текущей машине перевожу в статус "Записан"
			//$send_order_status = OrderStatus::getByCode('sent');
			$created_order_status = OrderStatus::getByCode('created');
			$aFactOrdersId = ArrayHelper::map($fact_orders, 'id', 'id');
			if(count($aFactOrdersId) > 0) {
				Yii::$app->db->createCommand('UPDATE `order` SET status_id = ' . $created_order_status->id . ', status_setting_time=' . time() . ', updated_at=' . time() . ' WHERE id IN (' . implode(',', $aFactOrdersId) . ')')->execute();
			}

			// Когда заказы переходят в статус "Записан", то в таблице клиентов пересчитываются: order_count--, prize_trip_count?--
			foreach($fact_orders as $fact_order) {

				$client = $fact_order->client;

				if($fact_order->prize_trip_count > 0) {
					//$client->sended_prize_trip_count -= $fact_order->prize_trip_count;
					//$client->setField('sended_prize_trip_count', $client->sended_prize_trip_count);

					$client->current_year_sended_prize_places -= $fact_order->prize_trip_count;
					$client->setField('current_year_sended_prize_places', $client->current_year_sended_prize_places);
				}


				if($fact_order->informerOffice != null && $fact_order->informerOffice->cashless_payment == 1) {

					//$client->sended_informer_beznal_orders_places_count -= $fact_order->places_count;
					//$client->setField('sended_informer_beznal_orders_places_count', $client->sended_informer_beznal_orders_places_count);

					$client->current_year_sended_informer_beznal_places -= $fact_order->places_count;
					$client->setField('current_year_sended_informer_beznal_places', $client->current_year_sended_informer_beznal_places);

					$client->current_year_sended_informer_beznal_orders -= 1;
					$client->setField('current_year_sended_informer_beznal_orders', $client->current_year_sended_informer_beznal_orders);

				}elseif($fact_order->is_not_places == 1) { // или счетчик "посылок" (нет места) декрементируется

					//$client->sended_is_not_places_order_count--;
					//$client->setField('sended_is_not_places_order_count', $client->sended_is_not_places_order_count);

					$client->current_year_sended_isnotplaces_orders--;
					$client->setField('current_year_sended_isnotplaces_orders', $client->current_year_sended_isnotplaces_orders);

				}elseif($fact_order->use_fix_price == 1) { // или уменьшается счетчик мест отправленных фикс. заказов

					//$client->sended_fixprice_orders_places_count -= $fact_order->places_count;
					//$client->setField('sended_fixprice_orders_places_count', $client->sended_fixprice_orders_places_count);

					$client->current_year_sended_fixprice_places -= $fact_order->places_count;
					$client->setField('current_year_sended_fixprice_places', $client->current_year_sended_fixprice_places);

					$client->current_year_sended_fixprice_orders -= 1;
					$client->setField('current_year_sended_fixprice_orders', $client->current_year_sended_fixprice_orders);

				}else { // или уменьшается общий счетчик отправленных мест

					//$client->sended_orders_places_count -= $fact_order->places_count;
					//$client->setField('sended_orders_places_count', $client->sended_orders_places_count);
				}

				// пересчитываются общие счетчики
				//$client->current_year_sended_places -= $fact_order->places_count;
				//$client->setField('current_year_sended_places', $client->current_year_sended_places);

				//$client->current_year_sended_orders -= 1;
				//$client->setField('current_year_sended_orders', $client->current_year_sended_orders);

				//Client::recountSendedCanceledReliabilityCounts($client->id, -1, -$fact_order->places_count, 0 , 0);

				if($client != null) {
					$client->recountSendedCanceledReliabilityCounts($fact_order, -1, -$fact_order->places_count, 0 , 0);
				}
			}

			// отдельно пройдемся по отмененным заказам и увелим счетчик отмененных заказов/мест
//			$fact_cancelled_orders = $this->factCanceledOrders;
//			$aFactCanceledOrdersId = ArrayHelper::map($fact_cancelled_orders, 'id', 'id');
//			if(count($aFactCanceledOrdersId) > 0) {
//				Yii::$app->db->createCommand('UPDATE `order` SET date_sended=NULL WHERE id IN (' . implode(',', $aFactCanceledOrdersId) . ')')->execute();
//			}

//			foreach($fact_cancelled_orders as $fact_canceled_order) {
//				$client = $fact_canceled_order->client;
//				$client->current_year_canceled_orders--;
//				$client->setField('current_year_canceled_orders', $client->current_year_canceled_orders);
//
//				$client->current_year_canceled_places -= $fact_canceled_order->places_count;;
//				$client->setField('current_year_canceled_places', $client->current_year_canceled_places);
//			}




			// получаем свещую информацию по заказам и по связанным заявкам пересчитываем статус
//			$fact_orders = $this->factOrdersWithoutCanceled;
//			foreach($fact_orders as $fact_order) {
//				$fact_order->setClientExtStatus();
//			}


			$this->status_id = 0;
			$this->date_sended = NULL;
			$this->sender_id = NULL;
			$this->access_key = $this->generateAccessKey(); // установка идентификатора доступа
			if(!$this->save(false)) {
				throw new ErrorException('Не удалось сохранить изменения в trip_transport');
			}
		}



		// $day_report_trip_transport
		// была ошибка с дублями, именно из-за нее могло образоваться 2 одинаковых $day_report_trip_transport
		$day_report_trip_transports = DayReportTripTransport::find()->where(['trip_transport_id' => $this->id])->all();
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


		// удаляем логи отправленных заказов OrderReport
		if(count($fact_orders) > 0) {
			$sql = 'DELETE FROM `'.OrderReport::tableName().'` WHERE order_id IN('.implode(',', ArrayHelper::map($fact_orders, 'id', 'id')).')';
			Yii::$app->db->createCommand($sql)->execute();
		}

		return true;
	}


	/*
	 * Присоединение второстепенных трип_транспортов к основному
	 */
	public function joinTripTransports($aSecondTripTransports) {

		// во избежание переполнения машины и для неусложнение логики все посаженные
		// заказы связанные с второстепенными т/с - высаживаются
		$aSecondOrders = Order::find()
			->where(['IN', 'fact_trip_transport_id', ArrayHelper::map($aSecondTripTransports, 'id', 'id')])
			->all();

		if(count($aSecondOrders) > 0) {
			$aResetOrdersIds = ArrayHelper::map($aSecondOrders, 'id', 'id');
			Order::resetOrders($aResetOrdersIds);
			Order::setFields($aResetOrdersIds, 'fact_trip_transport_id', $this->id);
		}

		// второстепенные машины (т.е. машины-дубли) удаляю
		$sql = 'DELETE FROM `'.TripTransport::tableName().'` WHERE id IN('.implode(',', ArrayHelper::map($aSecondTripTransports, 'id', 'id')).')';
		Yii::$app->db->createCommand($sql)->execute();

		return true;
	}

}
