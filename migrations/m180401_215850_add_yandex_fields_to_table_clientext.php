<?php

use yii\db\Migration;

/**
 * Class m180401_215850_add_yandex_fields_to_table_clientext
 */
class m180401_215850_add_yandex_fields_to_table_clientext extends Migration
{
    public function up()
    {
        $this->addColumn('client_ext', 'places_count', $this->smallInteger(2)->defaultValue(0)->comment('Количество мест всего')->after('time'));

        $this->addColumn('client_ext', 'yandex_point_from_id', $this->integer()->comment('Точка откуда')->after('places_count'));
        $this->addColumn('client_ext', 'yandex_point_from_name', $this->string(255)->comment('Название яндекс-точки откуда')->after('yandex_point_from_id'));
        $this->addColumn('client_ext', 'yandex_point_from_lat', $this->double()->defaultValue(0)->comment('Широта яндекс-точки откуда')->after('yandex_point_from_name'));
        $this->addColumn('client_ext', 'yandex_point_from_long', $this->double()->defaultValue(0)->comment('Долгота яндекс-точки откуда')->after('yandex_point_from_lat'));

        $this->addColumn('client_ext', 'yandex_point_to_id', $this->integer()->comment('Точка куда')->after('yandex_point_from_lat'));
        $this->addColumn('client_ext', 'yandex_point_to_name', $this->string(255)->comment('Название яндекс-точки куда')->after('yandex_point_to_id'));
        $this->addColumn('client_ext', 'yandex_point_to_lat', $this->double()->defaultValue(0)->comment('Широта яндекс-точки куда')->after('yandex_point_to_name'));
        $this->addColumn('client_ext', 'yandex_point_to_long', $this->double()->defaultValue(0)->comment('Долгота яндекс-точки куда')->after('yandex_point_to_lat'));

    }

    public function down()
    {
        $this->dropColumn('client_ext', 'places_count');

        $this->dropColumn('client_ext', 'yandex_point_from_id');
        $this->dropColumn('client_ext', 'yandex_point_from_name');
        $this->dropColumn('client_ext', 'yandex_point_from_lat');
        $this->dropColumn('client_ext', 'yandex_point_from_long');

        $this->dropColumn('client_ext', 'yandex_point_to_id');
        $this->dropColumn('client_ext', 'yandex_point_to_name');
        $this->dropColumn('client_ext', 'yandex_point_to_lat');
        $this->dropColumn('client_ext', 'yandex_point_to_long');
    }
}
