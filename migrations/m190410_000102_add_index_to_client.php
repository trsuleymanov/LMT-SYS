<?php

use yii\db\Migration;

/**
 * Class m190410_000102_add_index_to_client
 */
class m190410_000102_add_index_to_client extends Migration
{
    public function up()
    {
        $this->createIndex('home_phone', 'client', 'home_phone');
        $this->createIndex('alt_phone', 'client', 'alt_phone');
    }

    public function down()
    {
        $this->dropIndex('home_phone', 'client');
        $this->dropIndex('alt_phone', 'client');
    }
}
