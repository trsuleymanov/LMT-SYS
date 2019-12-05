<?php

// определение координат: http://www.wemakemaps.com/ru/koordinata-po-adresu

use yii\db\Migration;
use app\models\City;
use yii\helpers\ArrayHelper;

class m170915_214813_create_table_yandex_point extends Migration
{
    /*
     * После выполнения up() не забудь залить в таблицу еще точек выполнив команду: php yii yandex-point/import-file
     */
    public function up()
    {
        $this->createTable('yandex_point', [ // Точки (Ориентиры) с яндекс-координатами
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->comment('Название'),
            'city_id' => $this->integer()->comment('Город'),
            'lat' => $this->double()->defaultValue(0)->comment('Широта'),
            'long' => $this->double()->defaultValue(0)->comment('Долгота'),
        ]);

        $this->addColumn('order', 'client_position_from_lat', $this->double()->after('point_id_from')->comment('Желаемое место посадки клиента - широта'));
        $this->addColumn('order', 'client_position_from_long', $this->double()->after('client_position_from_lat')->comment('Желаемое место посадки клиента - долгота'));

        $this->addColumn('order', 'yandex_point_from', $this->integer()->after('client_position_from_long')->comment('Точка откуда'));
        $this->addColumn('order', 'yandex_point_to', $this->integer()->after('point_id_to')->comment('Точка куда'));
    }

    public function down()
    {
        $this->dropColumn('order', 'yandex_point_from');
        $this->dropColumn('order', 'yandex_point_to');
        $this->dropColumn('order', 'client_position_from_lat');
        $this->dropColumn('order', 'client_position_from_long');

        $this->dropTable('yandex_point');
    }
}
