<?php

use yii\db\Migration;

/**
 * Class m190517_180404_add_field_cashback_to_client
 */
class m190517_180404_add_field_cashback_to_client extends Migration
{
    public function up()
    {
        $this->addColumn('client', 'cashback', $this->decimal(8, 2)->defaultValue(0)->comment('Кэш-бэк счет')->after('rating'));
    }

    public function down()
    {
        $this->dropColumn('client', 'cashback');
    }
}
