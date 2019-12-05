<?php

use yii\db\Migration;

/**
 * Class m181223_224605_add_field_operator_id_to_call
 */
class m181223_224605_add_field_operator_id_to_call extends Migration
{
    public function up()
    {
        $this->addColumn('call', 'operator_id',  $this->integer()->comment('id Оператора')->after('ats_user_id'));
    }

    public function down()
    {
        $this->dropColumn('call', 'operator_id');
    }
}
