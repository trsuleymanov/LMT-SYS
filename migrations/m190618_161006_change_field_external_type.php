<?php

use yii\db\Migration;

/**
 * Class m190618_161006_change_field_external_type
 */
class m190618_161006_change_field_external_type extends Migration
{
    public function up()
    {
        $this->dropColumn('order', 'external_type');
        $this->addColumn('order', 'external_type', "ENUM('client_site', 'application') AFTER external_id");

        $sql = 'UPDATE `order` SET `external_type`="client_site" WHERE external_id > 0';
        Yii::$app->db->createCommand($sql)->execute();
    }

    public function down()
    {
        $this->dropColumn('order', 'external_type');
        $this->addColumn('order', 'external_type', "ENUM('client_server_client_ext', 'client_server_request') AFTER external_id");
    }

}
