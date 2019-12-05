<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "chat_message".
 *
 * @property integer $id
 * @property integer $created_at
 * @property integer $lifetime
 * @property integer $to_the_begining
 * @property string $message
 */
class ChatMessage extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'chat_message';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_at', 'expiration_time', 'to_the_begining', 'dialog_id', 'user_id'], 'integer'],
            [['message'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'dialog_id' => 'Диалог',
            'user_id' => 'Пользователь',
            'created_at' => 'Дата создания',
            //'lifetime' => 'Время жизни',
            'expiration_time' => 'Время истечения действия сообщения',
            'to_the_begining' => 'Переместить в начало',
            'message' => 'Сообщение',
        ];
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        SocketDemon::sendOutBrowserMessage(
            'all_site_pages',
            [''],
            'updateChat()',
            ''
        );
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
