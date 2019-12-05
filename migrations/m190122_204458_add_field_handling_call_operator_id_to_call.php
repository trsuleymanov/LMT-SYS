<?php

use yii\db\Migration;

/**
 * Class m190122_204458_add_field_handling_call_operator_id_to_call
 */
class m190122_204458_add_field_handling_call_operator_id_to_call extends Migration
{
    public function up()
    {
        $this->addColumn('call', 'handling_call_operator_id',  $this->integer()->comment('Оператор (пользователь) принявший/создавший вызов')->after('client_phone'));
    }

    public function down()
    {
        $this->dropColumn('call', 'handling_call_operator_id');
    }
}
