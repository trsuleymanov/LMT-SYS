<?php

use yii\db\Migration;

class m170908_171221_add_field_relation_order_id_to_table_order extends Migration
{
    public function up()
    {
        $this->addColumn('order', 'relation_order_id', $this->integer()->after('has_penalty')->comment('Связанный заказ'));
    }

    public function down()
    {
        $this->dropColumn('order', 'relation_order_id');
    }
}
