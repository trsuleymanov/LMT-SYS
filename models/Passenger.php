<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "passport".
 *
 * @property int $id
 * @property int $client_id Клиент
 * @property boolean $child Ребенок
 * @property string $series Серия
 * @property string $number Номер
 * @property string $surname Фамилия
 * @property string $name Имя
 * @property string $patronymic Отчество
 * @property int $date_of_birth Дата рождения
 * @property string $citizenship Гражданство
 * @property int $gender Пол
 */
class Passenger extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'passenger';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
//            [['surname', 'name', 'date_of_birth', 'citizenship', 'gender'], 'required'],
            [['gender', 'date_of_birth', 'document_type', 'fio', 'series_number',], 'required'],
//            [['series', 'number'], 'required', 'when' => function ($model) {
//                return $model->child == 0;
//            }],

            [['gender', 'client_id', /*'child'*/], 'integer'],
            //[['series'], 'string', 'max' => 4],
            //[['number'], 'string', 'max' => 6],
            [['series_number', ], 'string', 'max' => 20],
            //[['surname', 'name', 'patronymic'], 'string', 'max' => 30],
            [['citizenship'], 'string', 'max' => 50],
            [['fio'], 'string', 'max' => 100],
            //[['series'], 'unique', 'targetAttribute' => ['series', 'number']],
            [['document_type'], 'string'],
            ['date_of_birth', 'checkDateOfBirth']
        ];
    }

    public function checkDateOfBirth($attribute, $params)
    {
        if(isset($this->date) && !preg_match('/^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$/i', $this->date)) {
            $this->addError($attribute, 'Дата рождения должна быть в формате 01.12.2000');
        }
    }

    public function beforeValidate()
    {
        if(isset($this->date_of_birth) && preg_match('/^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$/i', $this->date_of_birth)) {
            $this->date_of_birth = strtotime($this->date_of_birth);
        }

        return parent::beforeValidate();
    }


//    public function scenarios()
//    {
//        $scenarios = parent::scenarios();
//
//        $scenarios['save_child_data'] = [
//            'client_id',
//            'child',
//            'series',
//            'number',
//            'surname',
//            'name',
//            'patronymic',
//            'date_of_birth',
//            'citizenship',
//            'gender',
//        ];
//
//        $scenarios['save_passport_data'] = [
//            'client_id',
//            'child',
//            'series',
//            'number',
//            'surname',
//            'name',
//            'patronymic',
//            'date_of_birth',
//            'citizenship',
//            'gender',
//        ];
//
//        return $scenarios;
//    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'client_id' => 'Клиент',
            //'child' => 'Ребенок',
            //'series' => 'Серия паспорта',
            //'number' => 'Номер паспорта',
            'series_number' => 'Серия и номер документа',
//            'surname' => 'Фамилия',
//            'name' => 'Имя',
//            'patronymic' => 'Отчество',
            'document_type' => 'Тип документа',
            'fio' => 'ФИО',
            'date_of_birth' => 'Дата рождения',
            'citizenship' => 'Гражданство',
            'gender' => 'Пол',
        ];
    }

    public static function getGenders() {
        return [
            0 => 'Женский', // женский
            1 => 'Мужской'  // мужской
        ];
    }

    public static function getDocumentTypes() {

        return [
            'passport' => 'Паспорт',
            'birth_certificate' => 'Свидетельство о рождении',
            'international_passport' => 'Заграничный паспорт',
            'foreign_passport' => 'Иностранный паспорт',
        ];
    }

    public static function getDocumentTypesPlaceholders() {

        return [
            'passport' => '1234 123456',
            'birth_certificate' => 'VI-БА № 123456',
            'international_passport' => '12 321123',
            'foreign_passport' => '',
        ];
    }





    public function getClient()
    {
        return $this->hasOne(Client::className(), ['id' => 'client_id']);
    }
}