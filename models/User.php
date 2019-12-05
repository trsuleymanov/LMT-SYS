<?php

namespace app\models;

use PHPUnit\Framework\Exception;
use Yii;
use yii\base\ErrorException;
use yii\base\NotSupportedException;
use app\models\UserRole;
use yii\web\ForbiddenHttpException;

class User extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
    //public static $duration = 900;// время сеанса пользователя в секундах
    public $password;

    public static function getCookieAuthKey() {
        return (isset($_COOKIE['user_auth']) ? $_COOKIE['user_auth'] : null);
    }

    public static function getUserByCookie() {

        $user_id = self::getCookieId();
        if(empty($user_id)) {
            return null;
        }

        return User::find()->where(['id' => $user_id])->one();
    }

    public static function getCookieId() {
        return (isset($_COOKIE['user_id']) ? $_COOKIE['user_id'] : null);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(["token" => $token]);
    }

    /**
     * Finds user by username
     *
     * @param  string      $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }

    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email]);
    }

    public function rules()
    {
        return [
            [['username', 'firstname', 'lastname', 'role_id', 'password', 'lat', 'long', 'seans_duration_finish'], 'required'],
            [['lat', 'long'], 'double', 'min' => 0.0001],
            [['password'], 'string', 'min' => 6, 'max' => 20],
            [['username', 'email'], 'unique'],
            [['last_login_date', 'role_id', 'attempt_count', 'attempt_date', 'created_at', 'updated_at', 'blocked',
                'auth_seans_finish', 'seans_duration_finish', 'socket_ip_id'], 'integer'],
            //[['last_ip', 'attempt_count', 'attempt_date'], 'required'],
            [['username', 'firstname', 'lastname', 'email', 'city'], 'string', 'max' => 50],
            //['username', 'checkUsername'],
            [['auth_key'], 'string', 'max' => 32],
            [['password_hash', 'address', 'token'], 'string', 'max' => 255],
            [['phone', 'last_ip'], 'string', 'max' => 20],
            //[['seans_duration_finish'], 'safe']
        ];
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'last_login_date' => 'Время последней попытки входа на сайт',
            'username' => 'Логин',
            'auth_key' => 'Auth Key',
            'auth_seans_finish' => 'Время окончания сеанса пользователя',
            'seans_duration_finish' => 'Интервал времени истечения сеанса пользователя в секундах',
            'token' => 'Токен устройства',
            'password' => 'Пароль',
            'password_hash' => 'Password Hash',
            'firstname' => 'Имя',
            'lastname' => 'Фамилия',
            'email' => 'Электронная почта',
            'city' => 'Город',
            'address' => 'Адрес',
            'phone' => 'Телефон',
            'role_id' => 'Роль',
            'last_ip' => 'IP адрес (последнего входа на сайт)',
            'attempt_count' => 'Количество неудачных попыток последнего входа на сайт',
            'attempt_date' => 'Время последней попытки входа на сайт',
            'created_at' => 'Время создания',
            'updated_at' => 'Время изменения',
            'blocked' => 'Заблокирован',
            'lat' => 'Широта',
            'long' => 'Долгота',
            'lat_long_ping_at' => 'Время получения координат lat и long',
            'socket_ip_id' => 'Сокет ip-адрес по которому браузер соединяется с сервером'
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();

        $scenarios['create_user_like_driver'] = [
            'username', 'lastname', 'firstname', 'role_id'
        ];

        $scenarios['create_update'] = [
            'username', 'firstname', 'lastname', 'email', 'city', 'address', 'phone', 'role_id',
            'socket_ip_id', 'blocked', 'seans_duration_finish'
        ];

        $scenarios['set_password'] = [
            'password', 'password_hash'
        ];

        $scenarios['set_location'] = [
            'lat', 'long', 'lat_long_ping_at'
        ];

        return $scenarios;
    }

    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->created_at = time();

            $this->password = Yii::$app->getSecurity()->generateRandomString(6);
            $this->setPasswordHash($this->password);

        }else {
            $this->updated_at = time();
        }

        return parent::beforeSave($insert);
    }

    public function setPasswordHash($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * Validates password
     *
     * @param  string  $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return \Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->getSecurity()->generateRandomString();
    }

    public function getFullname() {
        return $this->lastname . ' ' . $this->firstname;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserRole()
    {
        return $this->hasOne(UserRole::className(), ['id' => 'role_id']);
    }


    public function getSocketIp()
    {
        return $this->hasOne(SocketIp::className(), ['id' => 'socket_ip_id']);
    }

    // обновление времени жизни куков (если пользователь активен)

    public function getFio() {
        return $this->lastname.' '.$this->firstname;
    }

    public function loginByCookie() {

        if($this->auth_seans_finish > time()) {
            throw new ForbiddenHttpException('Этот пользователь уже зарегистрирован в системе. Войдите под другим именем');
        }else {

            // !!! Если пользователь ранее был залогинен (вчера), но не вышел из системы сам, то произведем
            // логирование его выхода задним числом:
            if ($this->auth_seans_finish > 0 && $this->userRole->alias != 'root') {
                DispatcherAccounting::createLog('system_drop_out', 0, $this->auth_seans_finish, $this->id); // логируем потерю Аутентификации пользователя
            }

            $this->auth_seans_finish = time() + $this->seans_duration_finish;
            //$this->setField('auth_seans_finish', $this->auth_seans_finish);
            $this->auth_key = \Yii::$app->security->generateRandomString();
            //$this->setField('auth_key', $this->auth_key);
            $this->last_login_date = time();
            if(!$this->save(false)) {
                throw new ForbiddenHttpException('Не удалось сохранить пользователя');
            }

            //echo "this:<pre>"; print_r($this); echo "</pre>";

            // вынужден также логинить "Yii-пользователя" потому что нужен общий для браузера и для
            // мобильного api метод получения id текущего пользователя, т.е. Yii::$app->user->id
            Yii::$app->user->login($this, $this->seans_duration_finish);

            setcookie('user_id', $this->id, $this->auth_seans_finish, '/');
            setcookie('user_auth', $this->auth_key, $this->auth_seans_finish, '/');

            if($this->userRole != null) {
                Yii::$app->session->set('role_alias', $this->userRole->alias);
                Yii::$app->session->set('role_id', $this->userRole->id);
            }else {
                Yii::$app->session->set('role_alias', '');
                Yii::$app->session->set('role_id', '');
            }

            if($this->userRole->alias != 'root') {
                DispatcherAccounting::createLog('login', 0, time(), $this->id); // логируем Аутентификацию
            }

            return true;
        }
    }

    public function setField($field_name, $field_value)
    {
        if(!empty($field_value)) {
            $field_value = htmlspecialchars($field_value);
        }

        if($field_value === false) {
            $sql = 'UPDATE '.self::tableName().' SET '.$field_name.' = false WHERE id = '.$this->id;
        }elseif(empty($field_value)) {
            $sql = 'UPDATE '.self::tableName().' SET '.$field_name.' = NULL WHERE id = '.$this->id;
        }else {
            $sql = 'UPDATE '.self::tableName().' SET '.$field_name.' = "'.$field_value.'" WHERE id = '.$this->id;
        }

        return Yii::$app->db->createCommand($sql)->execute();
    }

    public static function tableName()
    {
        return '{{%user}}';
    }

    public function loginWithoutCookie() {

        if($this->userRole != null) {
            Yii::$app->session->set('role_alias', $this->userRole->alias);
            Yii::$app->session->set('role_id', $this->userRole->id);
        }else {
            Yii::$app->session->set('role_alias', '');
            Yii::$app->session->set('role_id', '');
        }

        if ($this->auth_seans_finish > 0 && $this->userRole->alias != 'root') {
            DispatcherAccounting::createLog('system_drop_out', 0, $this->auth_seans_finish, $this->id); // логируем потерю Аутентификации пользователя
        }

        $this->auth_seans_finish = time() + $this->seans_duration_finish;
        //$this->setField('auth_seans_finish', $this->auth_seans_finish);

        $this->auth_key = \Yii::$app->security->generateRandomString();
        //$this->setField('auth_key', $this->auth_key);

        $this->last_login_date = time();

        if(!$this->save(false)) {
            throw new ForbiddenHttpException('Не удалось сохранить пользователя');
        }

        if($this->userRole->alias != 'root') {
            DispatcherAccounting::createLog('login', 0, time(), $this->id); // логируем Аутентификацию
        }

        return true;
    }

    public function updateLoginByCookie() {

        $this->auth_seans_finish = time() + $this->seans_duration_finish;
        $this->setField('auth_seans_finish', $this->auth_seans_finish);

        Yii::$app->user->login($this, $this->seans_duration_finish);

        setcookie('user_auth', $_COOKIE['user_auth'], $this->auth_seans_finish, '/');
        setcookie('user_id', $_COOKIE['user_id'], $this->auth_seans_finish, '/');

        if($this->userRole != null) {
            Yii::$app->session->set('role_alias', $this->userRole->alias);
            Yii::$app->session->set('role_id', $this->userRole->id);
        }else {
            Yii::$app->session->set('role_alias', '');
            Yii::$app->session->set('role_id', '');
        }
    }

    public function logoutByCookie($system_logout = false) {

        if ($this->userRole->alias != 'root') {

            if($system_logout == true && $this->auth_seans_finish > 0) {
                DispatcherAccounting::createLog('system_drop_out', 0, $this->auth_seans_finish, $this->id); // логируем потерю Аутентификации пользователя
            }

            if($system_logout == false) {
                DispatcherAccounting::createLog('logout'); // логируем потерю Аутентификации пользователя
            }
        }

        $this->setField('auth_seans_finish', 0);

        self::miniLogoutByCookie();
    }

    public function logoutWithoutCookie($system_logout = false) {

        if ($this->userRole->alias != 'root') {

            if($system_logout == true && $this->auth_seans_finish > 0) {
                DispatcherAccounting::createLog('system_drop_out', 0, $this->auth_seans_finish, $this->id); // логируем потерю Аутентификации пользователя
            }

            if($system_logout == false) {
                DispatcherAccounting::createLog('logout'); // логируем потерю Аутентификации пользователя
            }
        }

        $this->setField('auth_seans_finish', 0);
    }


    public static function miniLogoutByCookie() {

        // после завершения тестирования работы включения/отключения подписок, этот код надо включить!!!
//        if(Yii::$app->user != null) {
//            $operator_subscription = OperatorBeelineSubscription::find()
//                ->where(['operator_id' => Yii::$app->user->id])
//                ->one();
//            if ($operator_subscription != null) {
//                $operator_subscription->deleteFromAts();
//                $operator_subscription->setStatus('OFFLINE'); // в АТС статус устанавливается неподписке, а СИПу
//            }
//        }

        if(isset($_COOKIE['user_auth'])) {
            setcookie('user_auth', $_COOKIE['user_auth'], (time() - 1), '/');
        }
        if(isset($_COOKIE['user_id'])) {
            setcookie('user_id', $_COOKIE['user_id'], (time() - 1), '/');
        }

        Yii::$app->user->logout(true);//выходим и для Yii-пользователя
        Yii::$app->session->set('role_alias', '');
        Yii::$app->session->set('role_id', '');
    }


    public function getPhotoAccessCode() {

        if(empty($this->token)) {
            throw new ErrorException('Нельзя сгенерировать код доступа для загрузки фото, т.к. токен доступа пуст');
        }

        return md5($this->token.'slktRic9i_akre');
    }
}
