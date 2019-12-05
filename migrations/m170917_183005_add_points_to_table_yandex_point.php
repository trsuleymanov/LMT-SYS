<?php

use yii\db\Migration;
use app\models\City;
use yii\helpers\ArrayHelper;

class m170917_183005_add_points_to_table_yandex_point extends Migration
{
    public function up()
    {
        // Загрузка 12 меток Казани
        $city = City::find()->where(['name' => "Казань"])->one();
        $this->BatchInsert('yandex_point', ['name', 'city_id', 'lat', 'long'], [
            ['ЖД Восстания', $city->id, '55.843009', '49.080178'],
            ['ЖД Центральный', $city->id, '55.788253', '49.100212'],
            ['Кольцо', $city->id, '55.786367', '49.124793'],
            ['Павлюхина Филармония', $city->id, '55.772733', '49.141575'],
            ['Павлюхина Роторная', $city->id, '55.765440', '49.148104'],
            ['Павлюхина Ипподром', $city->id, '55.788157', '49.195189'],
            ['Танковое кольцо', $city->id, '55.752377', '49.161984'],
            ['Деревня Универсиады', $city->id, '55.743507', '49.184418'],
            ['РКБ', $city->id, '55.743507', '49.184418'],
            ['Казанский Аэропорт', $city->id, '55.607423', '49.300795'],
            ['Сокуры', $city->id, '55.621974', '49.392509'],
            ['пер. Лаишево', $city->id, '55.805695', '48.941714'],
        ]);
    }

    public function down()
    {
        $city = City::find()->where(['name' => "Казань"])->one();
        $this->delete('yandex_point', [
            'city_id' => $city->id
        ]);
    }
}
