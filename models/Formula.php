<?php

namespace app\models;

use Yii;
use yii\web\ForbiddenHttpException;
use yii\base\ErrorException;
use ParseError;


/**
 * This is the model class for table "formula".
 *
 * @property integer $id
 * @property integer $name
 * @property string $formula
 * @property integer $created_at
 * @property integer $updated_at
 */
class Formula extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'formula';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 50],
            [['formula'], 'string', 'max' => 255],
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
            'formula' => 'Формула',
            'created_at' => 'Время создания формулы',
            'updated_at' => 'Время изменения формулы',
        ];
    }

    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->created_at = time();
        }else {
            $this->updated_at = time();
        }

        return parent::beforeSave($insert);
    }



    public function getResult($ARG = 0) {

        $result = 'Не определен';

        try {
            eval($this->formula);
        }catch(ParseError $e) {
            throw new $e('Произошла ошибка при использовании формулы formula_id='.$this->id);
        }

        return $result;
    }
}
