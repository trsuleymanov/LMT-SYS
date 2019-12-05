<?php

use yii\db\Migration;

class m171109_183828_add_fields_yandex_point_from_to_order extends Migration
{
    public function up()
    {
        $this->addColumn('order', 'yandex_point_from_name', $this->string(255)->comment('Название яндекс-точки откуда')->after('yandex_point_from'));
        $this->addColumn('order', 'yandex_point_from_lat', $this->double()->defaultValue(0)->comment('Широта яндекс-точки откуда')->after('yandex_point_from_name'));
        $this->addColumn('order', 'yandex_point_from_long', $this->double()->defaultValue(0)->comment('Долгота яндекс-точки откуда')->after('yandex_point_from_lat'));

        $this->addColumn('order', 'yandex_point_to_name', $this->string(255)->comment('Название яндекс-точки куда')->after('yandex_point_to'));
        $this->addColumn('order', 'yandex_point_to_lat', $this->double()->defaultValue(0)->comment('Широта яндекс-точки куда')->after('yandex_point_to_name'));
        $this->addColumn('order', 'yandex_point_to_long', $this->double()->defaultValue(0)->comment('Долгота яндекс-точки куда')->after('yandex_point_to_lat'));
    }

    public function down()
    {
        $this->dropColumn('order', 'yandex_point_from_long');
        $this->dropColumn('order', 'yandex_point_from_lat');
        $this->dropColumn('order', 'yandex_point_from_name');

        $this->dropColumn('order', 'yandex_point_to_long');
        $this->dropColumn('order', 'yandex_point_to_lat');
        $this->dropColumn('order', 'yandex_point_to_name');
    }
}
