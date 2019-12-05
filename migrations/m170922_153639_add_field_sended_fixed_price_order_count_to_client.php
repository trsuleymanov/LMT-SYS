<?php

use yii\db\Migration;

class m170922_153639_add_field_sended_fixed_price_order_count_to_client extends Migration
{
    public function up()
    {
        $this->addColumn('client', 'sended_fixed_price_order_count', $this->smallInteger()->defaultValue(0)->after('sended_prize_trip_count')->comment('Количество отправленных заказов с фиксированной ценой'));
    }

    public function down()
    {
        $this->dropColumn('client', 'sended_fixed_price_order_count');
    }
}
