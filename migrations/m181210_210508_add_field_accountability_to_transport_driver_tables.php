<?php

use yii\db\Migration;

/**
 * Class m181210_210508_add_field_accountability_to_transport_driver_tables
 */
class m181210_210508_add_field_accountability_to_transport_driver_tables extends Migration
{
    public function up()
    {
        $this->addColumn('transport', 'accountability', $this->boolean()->defaultValue(1)->comment('Подотчетность')->after('active'));
        $this->addColumn('driver', 'accountability', $this->boolean()->defaultValue(1)->comment('Подотчетность')->after('active'));
    }

    public function down()
    {
        $this->dropColumn('transport', 'accountability');
        $this->dropColumn('driver', 'accountability');
    }

}
