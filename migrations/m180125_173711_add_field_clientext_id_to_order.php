<?php

use yii\db\Migration;

/**
 * Class m180125_173711_add_field_clientext_id_to_order
 */
class m180125_173711_add_field_clientext_id_to_order extends Migration
{
    public function up()
    {
        $this->addColumn('order', 'client_ext_id', $this->integer()->comment('Заявка')->after('id'));
    }

    public function down()
    {
        $this->dropColumn('order', 'client_ext_id');
    }
}
