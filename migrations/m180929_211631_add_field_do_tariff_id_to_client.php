<?php

use yii\db\Migration;

/**
 * Class m180929_211631_add_field_do_tariff_id_to_client
 */
class m180929_211631_add_field_do_tariff_id_to_client extends Migration
{
    public function up()
    {
        $this->addColumn('client', 'do_tariff_id', $this->integer()->after('name')->comment('Признак формирования цены'));
    }

    public function down()
    {
        $this->dropColumn('client', 'do_tariff_id');
    }
}
