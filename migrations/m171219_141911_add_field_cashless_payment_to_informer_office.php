<?php

use yii\db\Migration;

class m171219_141911_add_field_cashless_payment_to_informer_office extends Migration
{
    public function up()
    {
        $this->addColumn('informer_office', 'cashless_payment', $this->boolean()->defaultValue(false)->comment('Безналичная оплата')->after('name'));
    }

    public function down()
    {
        $this->dropColumn('informer_office', 'cashless_payment');
    }
}
