<?php

use yii\db\Migration;

class m170805_155523_delete_table_tr extends Migration
{
    public function up()
    {
        // таблица отправляемых т/с на рейс со старого сайта
        $this->dropTable('tr');
    }

    public function down()
    {
        $this->createTable('tr', [ // список расписаний
            'id' => $this->primaryKey(),
            'tmtb_id' => $this->integer(),
            'car_id' => $this->integer(),
            'driver_id' => $this->integer(),
            'status' => $this->boolean()->defaultValue(0)->comment('1-отправленные рейсы'),
            'in_out' => $this->float(),
            'date_of_change' => $this->integer(),
            'date_of_add' => $this->integer(),
            'date_sended' => $this->integer(),
        ]);
    }
}
