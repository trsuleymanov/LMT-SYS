<?php

use yii\db\Migration;
use app\models\City;
use yii\web\ForbiddenHttpException;

/**
 * Class m180123_154045_add_coordinates_to_cities
 */
class m180123_154045_add_coordinates_to_cities extends Migration
{
    /**
     * @throws ErrorException
     * @throws ForbiddenHttpException
     */
    public function up()
    {
        $city_1 = City::find()->where(['name' => 'Казань'])->one();
        if($city_1 == null) {
            throw new ForbiddenHttpException('Казань не найдена');
        }
        $city_1->center_lat = 55.79;
        $city_1->center_long  = 49.11;
        if(!$city_1->save(false)) {
            throw new ErrorException('Не удалось сохранить Казань');
        }

        $city_2 = City::find()->where(['name' => 'Альметьевск'])->one();
        if($city_2 == null) {
            throw new ForbiddenHttpException('Альметьевск не найдена');
        }
        $city_2->center_lat = 54.9;
        $city_2->center_long  = 52.3;
        if(!$city_2->save(false)) {
            throw new ErrorException('Не удалось сохранить Альметьевск');
        }
    }

    public function down()
    {
        $city_1 = City::find()->where(['name' => 'Казань'])->one();
        if($city_1 == null) {
            throw new ForbiddenHttpException('Казань не найдена');
        }
        $city_1->center_lat = 0;
        $city_1->center_long  = 0;
        if(!$city_1->save(false)) {
            throw new ErrorException('Не удалось сохранить Казань');
        }

        $city_2 = City::find()->where(['name' => 'Альметьевск'])->one();
        if($city_2 == null) {
            throw new ForbiddenHttpException('Альметьевск не найдена');
        }
        $city_2->center_lat = 0;
        $city_2->center_long  = 0;
        if(!$city_2->save(false)) {
            throw new ErrorException('Не удалось сохранить Альметьевск');
        }
    }
}
