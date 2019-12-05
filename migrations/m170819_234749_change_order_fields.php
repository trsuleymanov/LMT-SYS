<?php

use yii\db\Migration;

class m170819_234749_change_order_fields extends Migration
{
    public function up()
    {
        $this->dropColumn('client', 'order_count');
        $this->addColumn('client', 'sended_order_count', $this->smallInteger()->defaultValue(0)->after('rating')->comment('Количество отправленных заказов'));

        $this->dropColumn('client', 'prize_trip_count');
        $this->addColumn('client', 'sended_prize_trip_count', $this->smallInteger()->defaultValue(0)->after('sended_order_count')->comment('Количество отправленных призовых поездок'));
    }

    public function down()
    {
        $this->dropColumn('client', 'sended_order_count');
        $this->addColumn('client', 'order_count', $this->smallInteger()->defaultValue(0)->after('rating')->comment('Количество заказов'));

        $this->dropColumn('client', 'sended_prize_trip_count');
        $this->addColumn('client', 'prize_trip_count', $this->smallInteger()->defaultValue(0)->after('order_count')->comment('Количество призовых поездок'));
    }
}
