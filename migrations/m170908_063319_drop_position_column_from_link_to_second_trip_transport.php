<?php

use yii\db\Migration;

class m170908_063319_drop_position_column_from_link_to_second_trip_transport extends Migration
{
    public function safeUp()
    {

    }

    public function safeDown()
    {
        echo "m170908_063319_drop_position_column_from_link_to_second_trip_transport cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170908_063319_drop_position_column_from_link_to_second_trip_transport cannot be reverted.\n";

        return false;
    }
    */
}
