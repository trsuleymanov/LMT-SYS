<?php

use yii\db\Migration;

/**
 * Class m180615_234536_little_changes_in_order
 */
class m180615_234536_little_changes_in_order extends Migration
{
    public function up()
    {
        $this->alterColumn('order', 'client_id', $this->integer()->comment('Клиент'));
        $this->alterColumn('order', 'additional_phone_2', $this->string(20)->comment('Дополнительный телефон 2'));
        $this->alterColumn('order', 'additional_phone_3', $this->string(20)->comment('Дополнительный телефон 3'));
    }

    public function down()
    {

    }
}
