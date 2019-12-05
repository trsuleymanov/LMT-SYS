<?php

use yii\db\Migration;

/**
 * Class m180701_002752_add_field_count_is_double_to_detail_measurement_value
 */
class m180701_002752_add_field_count_is_double_to_detail_measurement_value extends Migration
{
    public function up()
    {
        $this->addColumn('detail_measurement_value', 'count_is_double', $this->boolean()->defaultValue(false)->after('name')->comment('Единицы измерения имеют дробную часть'));
    }

    public function down()
    {
        $this->dropColumn('detail_measurement_value', 'count_is_double');
    }
}
