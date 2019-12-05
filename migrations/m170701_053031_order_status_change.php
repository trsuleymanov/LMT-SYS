<?php

use yii\db\Migration;

class m170701_053031_order_status_change extends Migration
{
    public function up()
    {
        $this->truncateTable('order_status');
        $this->BatchInsert('order_status',['name', 'code'], [
            ['Записан', 'created'],
            ['Отменен', 'canceled'],
            ['Отправлен', 'sent'],
        ]);

        $this->update('order', ['status_id' => NULL]);
    }

    public function down()
    {
        $this->truncateTable('order_status');
        $this->BatchInsert('order_status',['name', 'code'], [
            ['Отменен: не едет', 'cancel_not_go'],
            ['Отменен: уехал другим рейсом', 'cancel_left_another_flight'],
            ['Отменен: уехал с другой фирмой', 'cancel_left_with_another_company'],
            ['Отменен: не берет трубку', 'cancel_not_answer_the_phone'],
            ['Отменен: другая причина', 'cancel_another_reason'],
            ['Записан, но не подтвержден', 'not_confirmed'],
            ['Записан и подтвержден', 'confirmed'],
            ['Записан, подтвержден и сидит в машине', 'confirmed_and_sits_in_car'],
            ['Записан, подтвержден, выехал в машине', 'confirmed_and_left_in_car'],
        ]);
    }
}
