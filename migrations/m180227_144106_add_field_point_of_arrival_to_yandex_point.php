<?php

use yii\db\Migration;

/**
 * Class m180227_144106_add_field_point_of_arrival_to_yandex_point
 */
class m180227_144106_add_field_point_of_arrival_to_yandex_point extends Migration
{
    public function up()
    {
        $this->addColumn('yandex_point', 'point_of_arrival', $this->boolean()->defaultValue(0)->after('long')->comment('Является точкой прибытия'));
    }

    public function down()
    {
        $this->dropColumn('yandex_point', 'point_of_arrival');
    }
}
