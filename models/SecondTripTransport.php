<?php

namespace app\models;

use Yii;
use app\models\OrderStatus;
use app\models\Client;
use app\models\Tariff;
use app\models\Point;
use app\models\Trip;
use app\models\TripTransport;
use app\models\Direction;
use app\models\InformerOffice;
use app\models\Street;
use app\models\Transport;
use app\models\Driver;
use yii\base\ErrorException;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;
use app\components\Helper;
use yii\db\Query;

/**
 * This is the model class for table "second_trip_transport".
 *
 * @property integer $id
 * @property integer $transport_id
 * @property integer $date
 */
class SecondTripTransport extends \yii\db\ActiveRecord
{
	public static function getDayTransportsIds($unixdate) {

		return ArrayHelper::map(SecondTripTransport::find()->where(['date' => $unixdate])->all(), 'transport_id', 'transport_id');
	}

    public static function getEmptyTransports($onDate, $selected_transport_id = null){

		// все машины которых нет во вторичных т/с попадают в массив result[]
		if(!is_numeric($onDate)){
			$date_unix = strtotime($onDate);
		} else {
			$date_unix = $onDate;
		}

		$second_trip_transport_list = SecondTripTransport::find()->where(['date'=>$date_unix])->all();
		$result = [];
		$allCars = Transport::find()
			->where(['active' => 1])
			->orderBy(['car_reg'=>'ASC'])
			->all();

		foreach($allCars as $car){
			$found = false;
			foreach($second_trip_transport_list as $item){
				if($item->transport_id == $car->id){
					$found = true;
					break;
				}
			}
			if(!$found){
				$result[] = $car;
			}
		}


		$selected_transport = [];
		if($selected_transport_id){
			$selected_transport[] = Transport::findOne($selected_transport_id);
			foreach($result as $k=>$item){
				if($item->id == $selected_transport_id){
					unset($result[$k]);
					break;
				}
			}
		}

		$result = array_merge($selected_transport,$result);

		return $result;
    }

    public static function updatePostSecondTripTransports($aTransportIds, $aSecondTripTransportIds, $unixdate)
    {
		$unixdate = intval($unixdate);

		// обновляем машин id в существующих second_trip_transport_ids
		if(count($aSecondTripTransportIds) > 0) {
			$second_trip_transports = SecondTripTransport::find()->where(['id' => $aSecondTripTransportIds])->all();
			$aModels = ArrayHelper::index($second_trip_transports, 'id');
			foreach($aSecondTripTransportIds as $key => $id) {
				if(isset($aTransportIds[$key]) && intval($aTransportIds[$key]) > 0) {
					$model = $aModels[$id];
					$model->transport_id = intval($aTransportIds[$key]);
					if(!$model->save(false)) {
						throw new ForbiddenHttpException('Не удалось сохранить второй транспорт');
					}
				}
			}
		}

		// удаляем вторые транспорта id которых не пришли из формы
		$sql = 'DELETE FROM `'.SecondTripTransport::tableName().'` WHERE `date`="'.$unixdate.'"';
		if(count($aSecondTripTransportIds) > 0) {
			$sql .= 'AND id NOT IN('.implode(',', $aSecondTripTransportIds).')';
		}
		Yii::$app->db->createCommand($sql)->execute();

		// создаем вторые транспорта id которых еще не существует в базе данных
		if(count($aTransportIds) > 0) {
			$aInserts = [];
			foreach($aTransportIds as $key => $transport_id) {
				$transport_id = intval($transport_id);
				if($transport_id > 0 && !isset($aSecondTripTransportIds[$key])) {
					$aInserts[] = [$transport_id, $unixdate];
				}
			}
			Yii::$app->db->createCommand()
				->BatchInsert(SecondTripTransport::tableName(), ['transport_id', 'date'], $aInserts)
				->execute();
		}


		// для простоты обновлю все страницы Состав рейса этого дня + главная и расстановка
		$day_trips = Trip::find()->where(['date' => $unixdate])->all();
		foreach($day_trips as $trip) {
			SocketDemon::updateMainPages($trip->id, $unixdate);
		}

        return true;
    }

	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'second_trip_transport';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['transport_id', 'date', 'active'], 'integer'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'transport_id' => 'Transport ID',
			'date' => 'Date',
			'active' => 'Активность'
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getTransport()
	{
		return $this->hasOne(Transport::className(), ['id' => 'transport_id']);
	}
}
