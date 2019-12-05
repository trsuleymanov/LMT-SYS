<?php

use yii\db\Migration;

/**
 * Class m191109_013340_create_table_log_trip_orders_price_recount
 */
class m191109_013340_create_table_log_trip_orders_price_recount extends Migration
{
    public function up()
    {
        $this->createTable('log_order_price_recount', [ // причины отмены заказа
            'id' => $this->primaryKey(),
            'trip_id' => $this->integer()->comment('Рейс'),
            'trip_link' => $this->string(255)->comment('Ссылка на рейс для ленивых'),
            'order_id' => $this->integer()->comment('Заказ'),
            'old_price' => $this->decimal(8, 2)->defaultValue(0)->comment('Старая цена'),
            'new_price' => $this->decimal(8, 2)->defaultValue(0)->comment('Новая цена'),
            'created_at' => $this->integer()->comment('Время записи')
        ]);
    }

    public function down()
    {
        $this->dropTable('log_order_price_recount');
    }
}
