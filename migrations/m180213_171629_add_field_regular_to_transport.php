<?php

use yii\db\Migration;

/**
 * Class m180213_171629_add_field_regular_to_transport
 */
class m180213_171629_add_field_regular_to_transport extends Migration
{
    public function up()
    {
        $this->addColumn('transport', 'regular', $this->boolean()->defaultValue(true)->comment('Регулярен')->after('active'));
    }

    public function down()
    {
        $this->dropColumn('transport', 'regular');
    }
}
