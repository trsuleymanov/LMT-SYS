<?php

use yii\db\Migration;

/**
 * Class m180125_173652_delete_field_order_id_from_clientext
 */
class m180125_173652_delete_field_order_id_from_clientext extends Migration
{
    public function up()
    {
        $this->dropColumn('client_ext', 'order_id');
    }

    public function down()
    {
        $this->addColumn('client_ext', 'order_id', $this->integer()->comment('Заказ ')->after('client_fio'));
    }
}
