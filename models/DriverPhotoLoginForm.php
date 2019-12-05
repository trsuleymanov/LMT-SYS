<?php

namespace app\models;

use Yii;
use yii\base\ErrorException;
use yii\base\Model;
use yii\web\ForbiddenHttpException;


class DriverPhotoLoginForm extends Model
{
    public $device_code; // уникальный код идентифицирующий устройство
    public $driver = null;

    public $_user = null;
    private $max_attempt_count = 3; // максимальное количество попыток неверного ввода пароля
    private $attempt_time = 100; // время пока пользователю будет запрещено пытаться войти на сайт, сек

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



    public function checkDeviceCode($attribute_name, $params) {

        if (empty($this->device_code)) {
            $this->addError($attribute_name, 'Отсутствует код устройства');
            return false;
        }

//        // нельзя авторизовать по магическому коду, т.к. в этом случае нельзя идентифицировать пользователя
//        $magic_device_code = MagicDeviceCode::find()->where(['code' => $this->device_code])->one();
//        if($magic_device_code != null) {
//
//            // сохраняем id магического устройства у водителя для будущей идентификации для работы сокет-сообщений
//            //$driver->magic_device_code_id = $magic_device_code->id;
//            $driver->setField('magic_device_code_id', $magic_device_code->id);
//
//            return true;
//        }

        $driver = Driver::find()
            ->where(['device_code' => $this->device_code])
            ->andWhere(['active' => true])
            ->one();
        if($driver == null) {
            $this->addError($attribute_name, 'С таким кодом водитель не найден');
            return false;
        }

        if($driver->accountability < 1) {
            $this->addError($attribute_name, 'У водителя должна быть установлена подотчетность чтобы была возможность отправлять фото');
            return false;
        }

        $this->driver = $driver;


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
        if (empty($this->_user)) {

            if($this->driver == null) {
                throw new ForbiddenHttpException('Водитель не найден');
            }

            $this->_user = $this->driver->user;
            if(empty($this->_user)) {
                throw new ForbiddenHttpException('Связанный с водителем пользователь не найден');
            }
        }

        return $this->_user;
    }
}
