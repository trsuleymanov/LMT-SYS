<?php

use yii\db\Migration;

/**
 * Class m190830_212456_add_field_super_tariff_used_to_yandex_point
 */
class m190830_212456_add_field_super_tariff_used_to_yandex_point extends Migration
{
    public function up()
    {
        $this->addColumn('yandex_point', 'super_tariff_used', $this->boolean()->defaultValue(0)->after('critical_point')->comment('Применяется супер тариф'));
    }

    public function down()
    {
        $this->dropColumn('yandex_point', 'super_tariff_used');
    }
}
