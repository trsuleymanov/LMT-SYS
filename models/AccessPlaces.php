<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "access_places".
 *
 * @property int $id
 * @property string $module Модуль
 * @property string $page_url Полный урл страницы
 * @property string $page_part Название области страницы к которой нужно установить доступ
 * @property string $name Название доступа (для странице в админке)
 */
class AccessPlaces extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'access_places';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['module'], 'string', 'max' => 10],
            [['page_url', 'name'], 'string', 'max' => 50],
            [['page_part'], 'string', 'max' => 30],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'module' => 'Модуль',
            'page_url' => 'Полный урл страницы',
            'page_part' => 'Название области страницы к которой нужно установить доступ',
            'name' => 'Название доступа (для странице в админке)',
        ];
    }


}
