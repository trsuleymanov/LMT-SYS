<?php

use yii\db\Migration;

/**
 * Class m191118_214623_add_field_is_reserv_to_trip
 */
class m191118_214623_add_field_is_reserv_to_trip extends Migration
{
    public function up()
    {
        $this->addColumn('trip', 'is_reserv', $this->boolean()->after('use_mobile_app')->defaultValue(false)->comment('Резервный рейс'));
    }

    public function down()
    {
        $this->dropColumn('trip', 'is_reserv');
    }
}
