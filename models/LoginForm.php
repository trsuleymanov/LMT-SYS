<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\ForbiddenHttpException;

/**
 * LoginForm is the model behind the login form.
 */
class LoginForm extends Model
{
    public $username;
    public $email;
    public $password;
    public $rememberMe = true;
    //public $operator_subscription_id;

    private $_user = false;
    private $max_attempt_count = 3; // максимальное количество попыток неверного ввода пароля
    private $attempt_time = 100; // время пока пользователю будет запрещено пытаться войти на сайт


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [[/*'username',*/ 'password'], 'required'],
            //[['operator_subscription_id',], 'integer'],
            [['username', 'email'], 'checkLogin'],
            ['email', 'email'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    public function attributeLabels()
    {
        return [
            //'operator_subscription_id' => 'СИП-Аккаунт',
            'username' => 'Оператор',
            'email' => 'Email',
            'password' => 'Пароль',
        ];
    }

    public function checkLogin($attribute_name, $params)
    {
        if (empty($this->username) && empty($this->email)) {

            $this->addError($attribute_name, 'Вы не указали логин пользователя');

            return false;
        }

        return true;
    }

    /**
     * Logs in a user using the provided username and password.
     * @return boolean whether the user is logged in successfully
     */
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


    /*
     * Проверка не заблокирован ли пользователь с таким логином и ip
     */
//    public function isLoginBlocked()
//    {
//
//    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {

            if(!empty($this->username)) {
                $this->_user = User::findByUsername($this->username);
            }else {
                $this->_user = User::findByEmail($this->email);
            }
        }

        return $this->_user;
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Неправильный пароль.');
            }
        }
    }
}
