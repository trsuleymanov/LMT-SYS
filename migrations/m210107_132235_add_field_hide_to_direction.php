<?php

use yii\db\Migration;

/**
 * Class m210107_132235_add_field_hide_to_direction
 */
class m210107_132235_add_field_hide_to_direction extends Migration
{
    public function up()
    {
        $this->addColumn('direction', 'hide', $this->boolean()->defaultValue(0)->after('distance'));
    }

    public function down()
    {
        $this->dropColumn('direction', 'hide');
    }
}
