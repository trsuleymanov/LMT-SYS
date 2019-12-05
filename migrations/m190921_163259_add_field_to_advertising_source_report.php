<?php

use yii\db\Migration;

/**
 * Class m190921_163259_add_field_to_advertising_source_report
 */
class m190921_163259_add_field_to_advertising_source_report extends Migration
{
    public function up()
    {
        $this->addColumn('advertising_source_report', 'operator_user_id', $this->integer()->comment('Оператор')->after('advertising_source_id'));
    }

    public function down()
    {
        $this->dropColumn('advertising_source_report', 'operator_user_id');
    }
}
