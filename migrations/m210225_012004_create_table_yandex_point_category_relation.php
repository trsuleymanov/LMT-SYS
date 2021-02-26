<?php

use yii\db\Migration;

/**
 * Class m210225_012004_create_table_yandex_point_category_relation
 */
class m210225_012004_create_table_yandex_point_category_relation extends Migration
{
    public function up()
    {
        $this->createTable('yandex_point_category_relation', [
            'yandex_point_id' => $this->integer()->comment('Яндекс-точка'),
            'category_id' => $this->integer()->comment('Категория'),
        ]);
    }

    public function down()
    {
        $this->dropTable('yandex_point_category_relation');
    }
}
