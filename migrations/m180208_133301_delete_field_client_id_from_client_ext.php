<?php

use yii\db\Migration;

/**
 * Class m180208_133301_delete_field_client_id_from_client_ext
 */
class m180208_133301_delete_field_client_id_from_client_ext extends Migration
{
    public function up()
    {
        $this->dropColumn('client_ext', 'client_id');
    }

    public function down()
    {
        $this->addColumn('client_ext', 'client_id', $this->integer()->comment('Клиента id')->after('time'));
    }
}
