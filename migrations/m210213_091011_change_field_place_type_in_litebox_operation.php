<?php

use yii\db\Migration;

/**
 * Class m210213_091011_change_field_place_type_in_litebox_operation
 */
class m210213_091011_change_field_place_type_in_litebox_operation extends Migration
{
    public function up()
    {
        $this->alterColumn('litebox_operation', 'place_type', "ENUM('fix_price', 'airport', 'unified', 'adult', 'student', 'child', 'parcel', '') DEFAULT ''");
    }

    public function down()
    {
        $this->alterColumn('litebox_operation', 'place_type', "ENUM('fix_price', 'airport', 'adult', 'student', 'child', '') DEFAULT ''");
    }
}
