<?php

use yii\db\Migration;

/**
 * Class m190228_002000_add_field_updated_time_to_call_case
 */
class m190228_002000_add_field_updated_time_to_call_case extends Migration
{
    public function up()
    {
        $this->addColumn('call_case', 'update_time', $this->integer()->comment('Время поступления последнего звонка')->after('open_time'));
    }

    public function down()
    {
        $this->dropColumn('call_case', 'update_time');
    }
}
