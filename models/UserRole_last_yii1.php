<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * This is the model class for table "user_roles".
 *
 * The followings are the available columns in table 'user_roles':
 *
 * @property integer $id
 * @property string $name
 * @property string $code
 * @property string $description
 */
class UserRole extends \yii\db\ActiveRecord {

    /**
     * @return string the associated database table name
     */
    public static function tableName() {
        return 'user_role';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name, code, description', 'length', 'max' => 255),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, name, code, description', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array();
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'name' => 'Роль',
            'code' => 'Code',
            'description' => 'Описание',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    /*public function search() {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('code', $this->code, true);
        $criteria->compare('description', $this->description, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public function selectItems() {
        $model = self::model()->findAll();
        $items = array();
        foreach ($model as $row) {
            if (Yii::app()->user->role != 'root' && $row->code == 'root')
                continue;
            $items[$row->id] = $row->name;
        }
        return $items;
    }

    public function getAllRoles() {

        if (!Yii::app()->user->checkAccess('root')) {
            $roles = self::model()->findAll('code != "root"');
        } else {
            $roles = self::model()->findAll();
        }

        return $roles;
    }*/
}