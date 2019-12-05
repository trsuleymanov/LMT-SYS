<?php

use yii\db\Migration;

/**
 * Class m180224_011820_add_yandex_fields_to_order_report
 */
class m180224_011820_add_yandex_fields_to_order_report extends Migration
{
    public function up()
    {
        $this->addColumn('order_report', 'yandex_point_from_id', $this->integer()->comment('Точка откуда')->after('point_from_name'));
        $this->addColumn('order_report', 'yandex_point_from_name', $this->string(255)->comment('Название яндекс-точки откуда')->after('yandex_point_from_id'));
        $this->addColumn('order_report', 'yandex_point_from_lat', $this->double()->defaultValue(0)->comment('Широта яндекс-точки откуда')->after('yandex_point_from_name'));
        $this->addColumn('order_report', 'yandex_point_from_long', $this->double()->defaultValue(0)->comment('Долгота яндекс-точки откуда')->after('yandex_point_from_lat'));

        $this->addColumn('order_report', 'yandex_point_to_id', $this->integer()->comment('Точка куда')->after('point_to_name'));
        $this->addColumn('order_report', 'yandex_point_to_name', $this->string(255)->comment('Название яндекс-точки куда')->after('yandex_point_to_id'));
        $this->addColumn('order_report', 'yandex_point_to_lat', $this->double()->defaultValue(0)->comment('Широта яндекс-точки куда')->after('yandex_point_to_name'));
        $this->addColumn('order_report', 'yandex_point_to_long', $this->double()->defaultValue(0)->comment('Долгота яндекс-точки куда')->after('yandex_point_to_lat'));
    }

    public function down()
    {
        $this->dropColumn('order_report', 'yandex_point_from_id');
        $this->dropColumn('order_report', 'yandex_point_from_name');
        $this->dropColumn('order_report', 'yandex_point_from_lat');
        $this->dropColumn('order_report', 'yandex_point_from_long');

        $this->dropColumn('order_report', 'yandex_point_to_id');
        $this->dropColumn('order_report', 'yandex_point_to_name');
        $this->dropColumn('order_report', 'yandex_point_to_lat');
        $this->dropColumn('order_report', 'yandex_point_to_long');
    }
}
