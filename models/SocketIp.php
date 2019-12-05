<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "socket_ip".
 *
 * @property int $id
 * @property string $ip ip адрес сервера
 * @property string $description описание
 */
class SocketIp extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'socket_ip';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ip'], 'string', 'max' => 22],
            [['description'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ip' => 'ip адрес сервера',
            'description' => 'описание',
        ];
    }
}
