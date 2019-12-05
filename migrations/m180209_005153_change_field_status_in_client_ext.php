<?php

use yii\db\Migration;

/**
 * Class m180209_005153_change_field_status_in_client_ext
 */
class m180209_005153_change_field_status_in_client_ext extends Migration
{
    public function up()
    {
        $this->alterColumn('client_ext', 'status', $this->string(15)->defaultValue('')->comment('Статус'));
    }

    public function down()
    {
        $this->alterColumn('client_ext', 'status', $this->smallInteger()->comment('Статус'));
    }
}
