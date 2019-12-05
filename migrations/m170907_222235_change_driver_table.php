<?php

use yii\db\Migration;

class m170907_222235_change_driver_table extends Migration
{
    public function up()
    {
        $this->addColumn('driver', 'user_id', $this->integer()->comment('Пользователь')->after('fio'));

        $this->BatchInsert('user_role',
            ['name', 'alias',],
            [
                ['Водитель', 'driver'],
            ]
        );
    }

    public function down()
    {
        $this->dropColumn('driver', 'user_id');

        $this->delete('user_role', [
            'alias' => 'driver'
        ]);
    }
}
