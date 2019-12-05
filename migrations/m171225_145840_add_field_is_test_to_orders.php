<?php

use yii\db\Migration;

class m171225_145840_add_field_is_test_to_orders extends Migration
{
    public function up()
    {
        $this->addColumn('order', 'is_test', $this->boolean()->defaultValue(false)->after('relation_order_id')->comment('Тестовый заказ'));
    }

    public function down()
    {
        $this->dropColumn('order', 'is_test');
    }
}
