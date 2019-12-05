<?php

use yii\db\Migration;

/**
 * Class m190409_233341_add_index_to_order
 */
class m190409_233341_add_index_to_order extends Migration
{
    public function up()
    {
        $this->createIndex('client_id', 'order', 'client_id');
        $this->createIndex('additional_phone_1', 'order', 'additional_phone_1');
        $this->createIndex('additional_phone_2', 'order', 'additional_phone_2');
        $this->createIndex('additional_phone_3', 'order', 'additional_phone_3');
    }

    public function down()
    {
        $this->dropIndex('client_id', 'order');
        $this->dropIndex('additional_phone_1', 'order');
        $this->dropIndex('additional_phone_2', 'order');
        $this->dropIndex('additional_phone_3', 'order');
    }
}
