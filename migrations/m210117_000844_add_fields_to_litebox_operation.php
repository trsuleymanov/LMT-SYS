<?php

use yii\db\Migration;

/**
 * Class m210117_000844_add_fields_to_litebox_operation
 */
class m210117_000844_add_fields_to_litebox_operation extends Migration
{
    public function up()
    {
        $this->addColumn('litebox_operation', 'commercial_trip', $this->boolean()->defaultValue(0)->after('order_id'));
        $this->addColumn('litebox_operation', 'direction_id', $this->integer()->after('commercial_trip'));
        $this->addColumn('litebox_operation', 'place_type', "ENUM('fix_price', 'airport', 'adult', 'student', 'child', '') DEFAULT '' AFTER direction_id");
        $this->addColumn('litebox_operation', 'place_price', $this->smallInteger()->defaultValue(0)->after('place_type')->comment('Цена за место'));
    }

    public function down()
    {
        $this->dropColumn('litebox_operation', 'commercial_trip');
        $this->dropColumn('litebox_operation', 'direction_id');
        $this->dropColumn('litebox_operation', 'place_type');
        $this->dropColumn('litebox_operation', 'place_price');
    }
}
