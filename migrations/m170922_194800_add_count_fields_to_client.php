<?php

use yii\db\Migration;

class m170922_194800_add_count_fields_to_client extends Migration
{
    public function up()
    {
        $this->addColumn('client', 'canceled_order_count', $this->smallInteger()->defaultValue(0)->after('sended_fixed_price_order_count')->comment('Количество отмененных заказов'));
        $this->addColumn('client', 'sended_is_not_places_order_count', $this->smallInteger()->defaultValue(0)->after('canceled_order_count')->comment('Количество отправленных посылок'));
    }

    public function down()
    {
        $this->dropColumn('client', 'canceled_order_count');
        $this->dropColumn('client', 'sended_is_not_places_order_count');
    }
}
