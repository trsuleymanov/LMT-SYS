<?php

use yii\db\Migration;

class m171022_143304_add_critical_point_to_yandex_point extends Migration
{
    public function up()
    {
        $this->addColumn('yandex_point', 'critical_point', $this->boolean()->defaultValue(0)->comment('Критическая точка'));
    }

    public function down()
    {
        $this->dropColumn('yandex_point', 'critical_point');
    }
}
