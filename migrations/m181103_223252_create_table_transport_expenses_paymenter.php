<?php

use yii\db\Migration;

/**
 * Class m181103_223252_create_table_transport_expenses_paymenter
 */
class m181103_223252_create_table_transport_expenses_paymenter extends Migration
{
    public function up()
    {
        $this->createTable('transport_expenses_paymenter', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->comment('Наименование'),
        ]);
    }

    public function down()
    {
        $this->dropTable('transport_expenses_paymenter');
    }
}
