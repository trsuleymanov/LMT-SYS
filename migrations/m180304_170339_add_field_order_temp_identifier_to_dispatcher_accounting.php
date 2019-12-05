<?php

use yii\db\Migration;

/**
 * Class m180304_170339_add_field_order_temp_identifier_to_dispatcher_accounting
 */
class m180304_170339_add_field_order_temp_identifier_to_dispatcher_accounting extends Migration
{
    public function up()
    {
        $this->addColumn('dispatcher_accounting', 'order_temp_identifier', $this->string(32)->after('order_id')->comment('Временный идентификатор заказа до момента создания в базе данных'));
    }

    public function down()
    {
        $this->dropColumn('dispatcher_accounting', 'order_temp_identifier');
    }
}
