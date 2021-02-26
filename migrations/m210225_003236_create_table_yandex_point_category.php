<?php

use yii\db\Migration;

/**
 * Class m210225_003236_create_table_yandex_point_category
 */
class m210225_003236_create_table_yandex_point_category extends Migration
{
    public function up()
    {
        $this->createTable('yandex_point_category', [
            'id' => $this->primaryKey(),
            'name' => $this->string(50)->comment('Название')
        ]);
    }

    public function down()
    {
        $this->dropTable('yandex_point_category');
    }
}
