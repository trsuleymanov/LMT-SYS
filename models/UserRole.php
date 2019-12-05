<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_role".
 *
 * @property integer $id
 * @property string $name
 * @property string $alias
 * @property string $description
 */
class UserRole extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_role';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'alias'], 'string', 'max' => 100],
            [['description'], 'string', 'max' => 255],
            [['name', 'alias'], 'required'],
            [['controlled'], 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'alias' => 'Псевдоним (на английском) ',
            'description' => 'Описание',
            'controlled' => 'Контролируемый', // пользователи роли контролируются через приложение управления временем на сменах
        ];
    }
}
