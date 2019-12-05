<?php

use yii\db\Migration;
use app\models\Street;
use app\models\Point;
use app\models\Direction;
use yii\base\ErrorException;

class m170824_162620_add_rows_to_tables_street_point extends Migration
{
    public function up()
    {
        $direction = Direction::find()->where(['sh_name' => 'АК'])->one();

        $street1 = Street::find()->where(['name' => "N/A - по умолчанию", 'city_id' => $direction->city_to])->one();
        if($street1 == null) {
            //$sql = 'INSERT INTO `'.Street::tableName().'`(city_id, `name`) VALUES('. $direction->city_to.', "N/A - по умолчанию")';
            $street = new Street();
            $street->name = "N/A - по умолчанию";
            $street->city_id = $direction->city_to;
            if(!$street->save()) {
                throw new ErrorException('Не удалось создать улицу');
            }
        }

        $street2 = Street::find()->where(['name' => "N/A - по умолчанию", 'city_id' => $direction->city_from])->one();
        if($street2 == null) {
            //$sql = 'INSERT INTO `'.Street::tableName().'`(city_id, `name`) VALUES('. $direction->city_from.', "N/A - по умолчанию")';
            $street = new Street();
            $street->name = "N/A - по умолчанию";
            $street->city_id = $direction->city_from;
            if(!$street->save()) {
                throw new ErrorException('Не удалось создать улицу');
            }
        }

        $point = Point::find()->where(['name' => "АВ - Автовокзал", 'city_id' => $direction->city_from])->one();
        if($point == null) {
            //$sql = 'INSERT INTO `'.Point::tableName().'`(city_id, `name`) VALUES('. $direction->city_from.', "N/A - по умолчанию")';
            $point = new Point();
            $point->name = "АВ - Автовокзал";
            $point->city_id = $direction->city_from;
            $point->active = 1;
            if(!$point->save()) {
                throw new ErrorException('Не удалось создать точку');
            }
        }
    }

    public function down()
    {

    }
}
