<?php

use yii\db\Migration;

class m171008_203031_add_column_commercial_to_tariff extends Migration
{
    public function up()
    {
        $this->addColumn('tariff', 'commercial', $this->boolean()->defaultValue(0)->comment('Коммерческий тариф'));
    }

    public function down()
    {
        $this->dropColumn('tariff', 'commercial');
    }
}
