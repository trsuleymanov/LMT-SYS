<?php

use yii\db\Migration;

/**
 * Class m191216_222019_add_field_loyalty_switch_to_setting
 */
class m191216_222019_add_field_loyalty_switch_to_setting extends Migration
{
    public function up()
    {
        $this->addColumn('setting', 'loyalty_switch', "ENUM('cash_back_on', 'fifth_place_prize') DEFAULT 'fifth_place_prize' AFTER phone_to_confirm_user");
    }

    public function down()
    {
        $this->dropColumn('setting', 'loyalty_switch');
    }
}
