<?php

use yii\db\Migration;

class m171026_215850_add_field_map_scale_to_city extends Migration
{
    public function up()
    {
        $this->addColumn('city', 'map_scale', $this->smallInteger()->defaultValue(10)->comment('Масштаб яндекс карты')->after('center_long'));
    }

    public function down()
    {
        $this->dropColumn('city', 'map_scale');
    }
}
