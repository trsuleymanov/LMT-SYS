<?php

use yii\db\Migration;

/**
 * Class m190305_153239_add_field_external_created_at_to_order
 */
class m190305_153239_add_field_external_created_at_to_order extends Migration
{
    public function up()
    {
        $this->addColumn('order', 'external_created_at', $this->integer()->comment('Время создания заявки на внешнем сервере')->after('external_type'));
    }

    public function down()
    {
        $this->dropColumn('order', 'external_created_at');
    }

}
