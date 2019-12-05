<?php

use yii\db\Migration;

class m170716_151618_delete_field_is_free_in_table_order extends Migration
{
    public function up()
    {
        $this->dropColumn('order', 'is_free');
    }

    public function down()
    {
        $this->addColumn('order', 'is_free', $this->smallInteger(1)->defaultValue(0)->comment('Призовая поездка')->after('time_air_train_departure'));
    }
}
