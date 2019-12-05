<?php

use yii\db\Migration;

class m180929_192518_add_field_do_tariff_id_to_informer_office extends Migration
{
    public function up()
    {
        $this->addColumn('informer_office', 'do_tariff_id', $this->integer()->after('cashless_payment')->comment('Признак формирования цены'));
    }

    public function down()
    {
        $this->dropColumn('informer_office', 'do_tariff_id');
    }
}
