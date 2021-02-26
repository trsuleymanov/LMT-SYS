<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%yandex_point_category_relation}}".
 *
 * @property int $id
 * @property int $yandex_point_id Яндекс-точка
 * @property int $category_id Категория
 */
class YandexPointCategoryRelation extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%yandex_point_category_relation}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['yandex_point_id', 'category_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'yandex_point_id' => 'Яндекс-точка',
            'category_id' => 'Категория',
        ];
    }
}
