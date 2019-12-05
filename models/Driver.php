<?php

namespace app\models;

use Yii;
use app\models\Transport;
use app\models\User;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;

/**
 * This is the model class for table "driver".
 *
 * @property integer $id
 * @property string $fio
 * @property string $mobile_phone
 * @property string $home_phone
 * @property integer $primary_transport_id
 * @property integer $secondary_transport_id
 * @property integer $created_at
 * @property integer $updated_at
 */
class Driver extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'driver';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fio', 'primary_transport_id', ], 'required'],
            [['primary_transport_id', 'secondary_transport_id', 'created_at', 'updated_at', 'user_id', 'magic_device_code_id'], 'integer'],
            [['fio'], 'string', 'max' => 100],
            [['mobile_phone'], 'string', 'max' => 15],
            [['home_phone'], 'string', 'max' => 20],
            ['device_code', 'unique'],
            ['device_code', 'string', 'min' => 15, 'max' => 17],
            [['active', 'accountability'], 'boolean']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'active' => 'Активен',
            'accountability' => 'Подотчетность',
            'fio' => 'ФИО',
            'user_id' => 'Пользователь',
            'mobile_phone' => 'Мобильный телефон',
            'home_phone' => 'Домашний телефон',
            'primary_transport_id' => 'Основное транспортное средство',
            'secondary_transport_id' => 'Дополнительное транспортное средство',
            'device_code' => 'Уникальный код мобильного устройства',
            'magic_device_code_id' => 'Магический код мобильного устройства',
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrimaryTransport()
    {
        return $this->hasOne(Transport::className(), ['id' => 'primary_transport_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSecondaryTransport()
    {
        return $this->hasOne(Transport::className(), ['id' => 'secondary_transport_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }


    public function getMagicDevice()
    {
        return $this->hasOne(MagicDeviceCode::className(), ['id' => 'magic_device_code_id']);
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

    public static function getOnlineDriversData() {

        $user_roles = UserRole::find()->where(['alias' => 'driver'])->all();
        $users = User::find()
            ->where(['>=', 'lat_long_ping_at', time() - 600])
            ->andWhere(['role_id' => ArrayHelper::map($user_roles, 'id', 'id')])
            ->all();

        echo "<br />Онлайн-юзеры с ролью водителей:<br />";
        foreach($users as $user) {
            echo " - ".$user->id." ".$user->username."<br />";
        }
        echo "<hr />";

        // 838 ПЖ, ФИО водителя, АК 7:30.
        $drivers = Driver::find()->where(['user_id' => ArrayHelper::map($users, 'id', 'id')])->all();
        $aUsersIdDrivers = ArrayHelper::index($drivers, 'user_id');
        //echo "drivers:<pre>"; print_r($drivers); echo "</pre>"; exit;

        // сегодняшние рейсы еще не отправленные, но уже стартовавшие
//        $trips = Trip::find()
//            ->where(['date' => strtotime(date("d.m.Y"))])
//            ->andWhere(['date_sended' => NULL])
//            ->andWhere(['>', 'date_start_sending', 0])
//            ->andWhere(['use_mobile_app' => 1])
//            ->all();
//        $aTrips = ArrayHelper::index($trips, 'id');
        //echo "trips:<pre>"; print_r($trips); echo "</pre>"; exit;

        // для найденных рейсов нахожу trip_transports в которых есть водители из списка 1
        $trip_transports = TripTransport::find()
            //->where(['trip_id' => ArrayHelper::map($trips, 'id', 'id')])
            ->where(['driver_id' => ArrayHelper::map($drivers, 'id', 'id')])
            //->andWhere(['status_id' => 0])
            ->all();
        $aDriversIdTripTransports = ArrayHelper::index($trip_transports, 'driver_id');
        //echo "trip_transports:<pre>"; print_r($trip_transports); echo "</pre>"; exit;

        $trips = Trip::find()->where(['id' => ArrayHelper::map($trip_transports, 'trip_id', 'trip_id')])->all();
        $aTrips = ArrayHelper::index($trips, 'id');

        $transports = Transport::find()
            ->where(['id' => ArrayHelper::map($trip_transports, 'transport_id', 'transport_id')])
            ->all();
        $aTransports = ArrayHelper::index($transports, 'id');

        $usersData = [];
        foreach($users as $user) {

            $driver = isset($aUsersIdDrivers[$user->id]) ? $aUsersIdDrivers[$user->id] : null;
            if($driver == null) {
                continue;
            }

            if(!isset($aDriversIdTripTransports[$driver->id])) {
                //throw new ForbiddenHttpException('Водитель на рейсе не найден. ');
                echo 'Водитель '.$driver->id.' на рейсе не найден. <br />';
                continue;
            }
            $trip_transport = $aDriversIdTripTransports[$driver->id];
            $transport = $aTransports[$trip_transport->transport_id];
            $trip = $aTrips[$trip_transport->trip_id];

            $usersData[] = [
                'id' => $user->id,
                'long' => $user->long,
                'lat' => $user->lat,
                'lastname' => $user->lastname,
                'firstname' => $user->firstname,
                'phone' => $user->phone,
                'transport_car_reg' => $transport->car_reg,
                'transport_sh_model' => $transport->sh_model,
                'driver_fio' => $driver->fio,
                'direction_sh_name' => $trip->direction->sh_name,
                'trip_id' => $trip->id,
                'trip_name' => $trip->name,
                'trip_date' => date("d.m.Y", $trip->date)
            ];
        }

        return $usersData;
    }
}
