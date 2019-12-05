<?php

namespace app\models;

use SebastianBergmann\CodeCoverage\Driver\Driver;
use Yii;
use yii\base\ErrorException;
use yii\base\Model;
use yii\web\ForbiddenHttpException;


class DriverLoginForm extends Model
{
    public $transport_access_key;
    public $device_code; // уникальный код идентифицирующий устройство

    public $_trip_transport = null;
    public $_user = null;
    private $max_attempt_count = 3; // максимальное количество попыток неверного ввода пароля
    private $attempt_time = 100; // время пока пользователю будет запрещено пытаться войти на сайт

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            ['transport_access_key', 'checkTransportAccessKey', 'skipOnEmpty' => false],
            ['device_code', 'checkDeviceCode', 'skipOnEmpty' => false],
        ];
    }

    public function attributeLabels()
    {
        return [
            'transport_access_key' => 'ключ доступа к машине',
        ];
    }

    public function checkTransportAccessKey($attribute_name, $params)
    {
        if (empty($this->transport_access_key)) {
            $this->addError($attribute_name, 'Вы не указали идентификатор доступа');
            return false;
        }

        if (!preg_match('/^[0-9]{10}$/', $this->transport_access_key)) {
            $this->addError($attribute_name, 'Идентификатор должен состоять из 10 цифр');
            return false;
        }

        $this->_trip_transport = TripTransport::find()->where(['access_key' => $this->transport_access_key])->one();
        if($this->_trip_transport == null) {
            $this->addError($attribute_name, 'Машина с таким идентификатором не найдена');
            return false;
        }

        return true;
    }


    public function checkDeviceCode($attribute_name, $params) {

        if (empty($this->device_code)) {
            $this->addError($attribute_name, 'Отсутствует код устройства');
            return false;
        }

        //$driver = Driver::find()->where([])->one();
        if(!empty($this->_trip_transport)) {
            $driver = $this->_trip_transport->driver;
            if($driver == null) {
                throw new ErrorException('Водитель связанный с идентификатором не найден');
            }


            $magic_device_code = MagicDeviceCode::find()->where(['code' => $this->device_code])->one();
            if($magic_device_code != null) {

                // сохраняем id магического устройства у водителя для будущей идентификации для работы сокет-сообщений
                //$driver->magic_device_code_id = $magic_device_code->id;
                $driver->setField('magic_device_code_id', $magic_device_code->id);

                return true;
            }

            if(empty($driver->device_code)) {
                $this->addError($attribute_name, 'На сервере не установлен код устройства для водителя');
            }

            if($driver->device_code != $this->device_code) {
                $this->addError($attribute_name, 'Код устройства неверен');
            }
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

                //return Yii::$app->user->login($user, $this->rememberMe ? 60 * 15 : 0);
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
        if(empty($this->_trip_transport)) {
            $this->_trip_transport = TripTransport::find()->where(['access_key' => $this->transport_access_key])->one();
            if(empty($this->_trip_transport)) {
                throw new ForbiddenHttpException('Транспорт не найден');
            }
        }

        if (empty($this->_user)) {

            $driver = $this->_trip_transport->driver;
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
        return $this->_trip_transport;
    }
}
