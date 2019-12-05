<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property integer $last_login_date
 * @property string $username
 * @property string $auth_key
 * @property string $password_hash
 * @property string $firstname
 * @property string $lastname
 * @property string $email
 * @property string $city
 * @property string $address
 * @property string $phone
 * @property integer $role_id
 * @property string $last_ip
 * @property integer $attempt_count
 * @property integer $attempt_date
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $blocked
 */
class User_temp extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['last_login_date', 'role_id', 'attempt_count', 'attempt_date', 'created_at', 'updated_at', 'blocked'], 'integer'],
            [['last_ip', 'attempt_count', 'attempt_date'], 'required'],
            [['username', 'firstname', 'lastname', 'email', 'city'], 'string', 'max' => 50],
            [['auth_key'], 'string', 'max' => 32],
            [['password_hash', 'address'], 'string', 'max' => 255],
            [['phone', 'last_ip'], 'string', 'max' => 20],
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
        ];
    }
}
