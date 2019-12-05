<?php

use yii\db\Migration;

/**
 * Class m190302_234916_add_external_fields_to_orders
 */
class m190302_234916_add_external_fields_to_orders extends Migration
{
    public function up()
    {
        $this->dropColumn('order', 'client_ext_id');
        $this->renameColumn('order', 'client_server_ext_id', 'external_id');
        $this->addColumn('order', 'external_type', "ENUM('client_server_client_ext', 'client_server_request') AFTER external_id");

        $sql = 'UPDATE `order` SET `external_type`="client_server_client_ext" WHERE external_id > 0';
        Yii::$app->db->createCommand($sql)->execute();
    }

    public function down()
    {
        $this->renameColumn('order', 'external_id', 'client_server_ext_id');
        $this->dropColumn('order', 'external_type');
        $this->addColumn('order', 'client_ext_id', $this->integer()->comment('Заявка')->after('id'));
    }
}
