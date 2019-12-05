<?php

use yii\db\Migration;

class m170822_204429_update_table_user_role extends Migration
{
    public function up()
    {
        $this->truncateTable('user_role');
        $this->BatchInsert('user_role', ['id', 'name', 'alias'], [
            [1, 'Root', 'root'],
            [2, 'Администратор', 'admin'],
            [3, 'Оператор направления', 'editor'],
            [4, 'Оператор', 'manager'],
        ]);
    }

    public function down()
    {
        $this->truncateTable('user_role');
        $this->BatchInsert('user_role', ['id', 'name', 'alias'], [
            [1, 'Root', 'root'],
            [2, 'Администратор', 'admin'],
            [3, 'Главный диспетчер', 'editor'],
            [4, 'Диспетчер', 'manager'],
        ]);
    }
}
