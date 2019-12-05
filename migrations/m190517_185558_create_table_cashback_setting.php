<?php

use yii\db\Migration;

/**
 * Class m190517_185558_create_table_cashback_setting
 */
class m190517_185558_create_table_cashback_setting extends Migration
{
    public function up()
    {
        $this->createTable('cashback_setting', [
            'id' => $this->primaryKey(),
            'start_date' => $this->integer()->comment('Дата начала использования'),
            'order_accrual_percent' => $this->smallInteger()->defaultValue(0)->comment('Процент начисления за заказ'),
            'order_penalty_percent' => $this->smallInteger()->defaultValue(0)->comment('Процент штафа с заказа'),
            'hours_before_start_trip_for_penalty' => $this->smallInteger()->defaultValue(0)->comment('Часы до начала рейса являющиеся условием начисления штрафа'),
            'with_commercial_trips' => $this->boolean()->defaultValue(0)->comment('Да/Нет - накапливать ли кэш-бэк во время коммерческих рейсов'),
        ]);
    }

    public function down()
    {
        $this->dropTable('cashback_setting');
    }
}
