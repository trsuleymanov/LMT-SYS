<?php

use yii\db\Migration;

/**
 * Class m180929_214508_add_field_source_price_to_order
 */
class m180929_214508_add_field_source_price_to_order extends Migration
{
    public function up()
    {
        $this->addColumn('order', 'source_price', $this->decimal(8, 2)->defaultValue(0)->comment('Цена установленная в источнике')->after('confirmed_time_satter_user_id'));
    }

    public function down()
    {
        $this->dropColumn('order', 'source_price');
    }
}
