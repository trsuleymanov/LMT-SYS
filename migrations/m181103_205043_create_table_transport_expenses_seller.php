<?php

use yii\db\Migration;

/**
 * Class m181103_205043_create_table_transport_expenses_seller
 */
class m181103_205043_create_table_transport_expenses_seller extends Migration
{
    public function up()
    {
        $this->createTable('transport_expenses_seller', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->comment('Наименование'),
        ]);
    }

    public function down()
    {
        $this->dropTable('transport_expenses_seller');
    }
}
