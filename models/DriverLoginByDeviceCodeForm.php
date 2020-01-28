<?php

namespace app\models;

use Yii;
use yii\base\ErrorException;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;


class DriverLoginByDeviceCodeForm extends Model
{
    //public $transport_access_key;
    public $device_code; // уникальный код идентифицирующий устройство

    public $active_trip_transport = null;
    public $_user = null;
    private $max_attempt_count = 3; // максимальное количество попыток неверного ввода пароля
    private $attempt_time = 100; // время пока пользователю будет запрещено пытаться войти на сайт

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            //['transport_access_key', 'checkTransportAccessKey', 'skipOnEmpty' => false],
            ['device_code', 'checkDeviceCode', 'skipOnEmpty' => false],
        ];
    }

//    public function attributeLabels()
//    {
//        return [
//            'transport_access_key' => 'ключ доступа к машине',
//        ];
//    }


    public function checkDeviceCode($attribute_name, $params) {

        if (empty($this->device_code)) {
            $this->addError($attribute_name, 'Отсутствует код устройства');
            return false;
        }

        // путь 1:
        // ищем водителя которому принадлежит этот код
        // ищем активный рейс с таким водителем

        // путь 2:
        // ищем магическое устройство с этим кодом
        // затем ищем водителя с таким магическим устройством
        // затем ищем активный рейс с таким водителем

        $driver = Driver::find()->where(['device_code' => $this->device_code])->one();
        if($driver == null) {
            $magic_device_code = MagicDeviceCode::find()->where(['code' => $this->device_code])->one();
            if($magic_device_code != null) {
                $driver = Driver::find()->where(['magic_device_code_id' => $magic_device_code->id])->one();
            }
        }

        $active_trip_transport = null;
        if($driver != null) {

            // сегодняшние рейсы
            $today_trips = Trip::find()->where(['date' => strtotime(date('d.m.Y'))])->all();

            // нахожу все! сегодняшние trip_transports привязанные к текущему водителю, но еще не отправленные
            // date_sended
            $this->active_trip_transport = TripTransport::find()
                ->where(['IN', 'trip_id', ArrayHelper::map($today_trips, 'id', 'id')])
                ->andWhere(['driver_id' => $driver->id])
                //->andWhere(['status_id' => 0])
                ->andWhere(['<', 'date_sended', time() + 1440])
                ->one();
            if($this->active_trip_transport == null) {
                $this->addError($attribute_name, 'Сегодня водитель не записан на рейсы');
            }else {
                return true;
            }

        }else {
            $this->addError($attribute_name, 'Водитель с таким кодом не найден');
        }


        return true;
    }


    public function login($withCookie = true)
    {
        $has_error = false;
        $user = $this->getUser();

        if($user->blocked == 1 && $user->last_ip == Yii::$app->request->getUserIP()) {
            $time = $user->attempt_date + $this->attempt_time - time();
            if($time > 0) {
                $has_error = true;
                Yii::$app->session->setFlash('error', 'Вы превысили максимальное количество попыток входа. Вы можете попробовать еще раз через '.$time.' секунд');
            }
        }

        if(!$has_error)
        {
            if ($this->validate()) {
                $user->last_ip = Yii::$app->request->getUserIP();
                $user->attempt_count = 0;
                $user->last_login_date = time();
                $user->token = Yii::$app->security->generateRandomString();
                $user->save(false);

                if($withCookie) {
                    return $user->loginByCookie();
                }else {
                    return $user->loginWithoutCookie();
                }

            } else {

                if ($user->last_ip == Yii::$app->request->getUserIP()) {
                    $user->attempt_count = $user->attempt_count + 1;
                } else {
                    $user->attempt_count = 1;
                }
                $user->last_ip = Yii::$app->request->getUserIP();
                $user->attempt_date = time();

                if ($user->attempt_count >= $this->max_attempt_count) {
                    $user->blocked = 1;
                } else {
                    $user->blocked = 0;
                }

                $user->save(false);
            }
        }
        return false;
    }


    public function getUser()
    {
        if(empty($this->active_trip_transport)) {
            $this->active_trip_transport = TripTransport::find()->where(['access_key' => $this->transport_access_key])->one();
            if(empty($this->active_trip_transport)) {
                throw new ForbiddenHttpException('Транспорт не найден');
            }
        }

        if (empty($this->_user)) {

            $driver = $this->active_trip_transport->driver;
            if($driver == null) {
                throw new ForbiddenHttpException('Связанный с транспортом водитель не найден');
            }

            $this->_user = $driver->user;
            if(empty($this->_user)) {
                throw new ForbiddenHttpException('Связанный с водителем пользователь не найден');
            }
        }

        return $this->_user;
    }

    public function getTripTransport() {
        return $this->active_trip_transport;
    }
}