<?php

use yii\db\Migration;

class m170805_161853_delete_field_transport_id_in_table_trip extends Migration
{
    public function up()
    {
        $this->dropColumn('trip', 'transport_id');
    }

    public function down()
    {
        $this->addColumn('trip', 'transport_id', $this->integer()->comment('Транспорт')->after('sent_date'));
    }
}
