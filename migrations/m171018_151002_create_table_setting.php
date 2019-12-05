<?php

use yii\db\Migration;

class m171018_151002_create_table_setting extends Migration
{
    public function up()
    {
        $this->createTable('setting', [
            'id' => $this->primaryKey(),
            'create_orders_yesterday' => $this->smallInteger(1)->defaultValue(0)->comment('Разрешено создание заказов вчерашним днем')
        ]);
    }

    public function down()
    {
        $this->dropTable('setting');
    }
}
