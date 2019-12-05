<?php

use yii\db\Migration;

/**
 * Class m191107_023656_add_field_canceled_by_to_order
 */
class m191107_023656_add_field_canceled_by_to_order extends Migration
{
    public function up()
    {
        $this->addColumn('order', 'canceled_by', "ENUM('client', 'operator', 'auto') after cancellation_clicker_id");

        $sql = 'UPDATE `order` SET `canceled_by`="operator" WHERE status_id=2';
        Yii::$app->db->createCommand($sql)->execute();
    }

    public function down()
    {
        $this->dropColumn('order', 'canceled_by');
    }
}
