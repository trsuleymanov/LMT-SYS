<?php

use yii\db\Migration;

class m170728_142116_add_field_is_sended_to_table_trip extends Migration
{
    public function up()
    {
        $this->addColumn('trip', 'date_sended', $this->integer()->after('transport_id')->comment('Дата/время отправки рейса'));
    }

    public function down()
    {
        $this->dropColumn('trip', 'date_sended');
    }
}
