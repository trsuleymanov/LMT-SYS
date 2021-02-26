<?php

use yii\db\Migration;

/**
 * Class m210226_003110_add_field_active_to_yandex_point
 */
class m210226_003110_add_field_active_to_yandex_point extends Migration
{
    public function up()
    {
        $this->addColumn('yandex_point', 'active', $this->boolean()->defaultValue(1)->after('id')->comment('Активна'));
    }

    public function down()
    {
        $this->dropColumn('yandex_point', 'active');
    }
}
