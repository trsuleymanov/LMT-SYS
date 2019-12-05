<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "trip_operation".
 *
 * @property int $id
 * @property int $created_at Время проведения операции
 * @property int $user_id Кем произведена операция
 * @property string $type
 * @property string $comment
 * @property int $delta Разница во времени начальной точки между рейсами при редактировании или слиянии рейсов
 */
class TripOperation extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trip_operation';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_at', 'user_id', 'delta'], 'integer'],
            [['type'], 'string'],
            [['comment'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'created_at' => 'Время операции',
            'user_id' => 'Кем проведена',
            'type' => 'Тип операции',
            'comment' => 'Комментарий',
            'delta' => 'Дельта, сек',
            //'delta' => 'Разница во времени начальной точки между рейсами при редактировании или слиянии рейсов',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->created_at = time();
            $sapi = php_sapi_name();
            if ($sapi!='cli') { // это консольный запуск
                $this->user_id = Yii::$app->user->identity->id;
            }
        }

        return parent::beforeSave($insert);
    }

    public static function getOperations() {

        return [
            'create' => 'Создание',
            'update' => 'Редактирование',
            'merge' => 'Объединение',
            'set_commercial' => 'Установка коммерческих рейсов',
            'unset_commercial' => 'Отмена коммерческих рейсов',
            'start_send' => 'Начало отправки',
            'issued_by_operator' => 'Рейс выпущен',
            'send' => 'Закрытие рейса (рутом/автоматически)',
            'cancel_send' => 'Отмена отправки'
        ];
    }
}
