<?php

namespace app\models;

use Yii;
use app\models\Schedule;

/**
 * This is the model class for table "schedule_trip".
 */
class ScheduleTrip extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'schedule_trip';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['schedule_id'], 'integer'],
            [['name'], 'string', 'max' => 50],
            [['start_time', 'mid_time', 'end_time'], 'string', 'max' => 5],
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
            'schedule_id' => 'Расписание',
            'start_time' => 'Начало сбора',
            'mid_time' => 'Середина сбора',
            'end_time' => 'Конец сбора',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSchedule()
    {
        return $this->hasOne(Schedule::className(), ['id' => 'schedule_id']);
    }

    public function afterSave($insert, $changedAttributes)
    {
        $this->schedule->deleteNotUsedTrips();

        parent::afterSave($insert, $changedAttributes);
    }

    public function afterDelete()
    {
        $this->schedule->deleteNotUsedTrips();

        parent::afterDelete();
    }

    public static function getSheduleTripListByShedule($schedule_id){
	
	$ScheduleTripList = ScheduleTrip::find()->where(['schedule_id'=>$schedule_id])->all();
	return $ScheduleTripList;
	
    }
}
