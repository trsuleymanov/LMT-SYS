<?php

use yii\db\Migration;

/**
 * Class m191112_145614_add_places_count_to_trip_transport
 */
class m191112_145614_add_places_count_to_trip_transport extends Migration
{
    public function up()
    {
        $this->addColumn('trip_transport', 'total_places_count', $this->smallInteger()->defaultValue(0)->comment('Мест в машине')->after('transport_id'));
        $this->addColumn('trip_transport', 'used_places_count', $this->smallInteger()->defaultValue(0)->comment('Занято мест в машине')->after('total_places_count'));
    }

    public function down()
    {
        $this->dropColumn('trip_transport', 'used_places_count');
        $this->dropColumn('trip_transport', 'total_places_count');
    }
}
