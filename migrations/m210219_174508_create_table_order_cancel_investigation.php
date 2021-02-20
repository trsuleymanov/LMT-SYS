<?php

use yii\db\Migration;

/**
 * Class m210219_174508_create_table_order_cancel_investigation
 */
class m210219_174508_create_table_order_cancel_investigation extends Migration
{
    public function up()
    {
        $this->createTable('order_cancel_investigation', [
            'id' => $this->primaryKey(),
            'order_id' => $this->integer()->comment('Заказ'),
            'trip_id' => $this->integer()->comment('Рейс'),
            'client_id' => $this->integer()->comment('Клиент'),
            'data' => $this->integer()->comment('Дата расследования'),
            'rejection_reason' => $this->text()->comment('Реальная причина отказа'),
            'how_client_left' => $this->text()->comment('На чем уехал клиент'),
            'complaints_and_wishes' => $this->text()->comment('Жалобы и пожелания'),
            'updated_at' => $this->integer()->comment('Время сохранения'),
        ]);
    }

    public function down()
    {
        $this->dropTable('order_cancel_investigation');
    }
}
