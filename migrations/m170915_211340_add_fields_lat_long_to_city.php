<?php

use yii\db\Migration;

class m170915_211340_add_fields_lat_long_to_city extends Migration
{
    public function up()
    {
        $this->addColumn('city', 'center_lat', $this->double()->defaultValue(0)->after('name')->comment('Широта'));
        $this->addColumn('city', 'center_long', $this->double()->defaultValue(0)->after('center_lat')->comment('Долгота'));
    }

    public function down()
    {
        $this->dropColumn('city', 'center_lat');
        $this->dropColumn('city', 'center_long');
    }
}
