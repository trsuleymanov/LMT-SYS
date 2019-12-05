<?php

use yii\db\Migration;

/**
 * Class m190305_151853_delete_field_is_mobile_from_order
 */
class m190305_151853_delete_field_is_mobile_from_order extends Migration
{
    public function up()
    {
        $this->dropColumn('order', 'is_mobile');
    }

    public function down()
    {
        $this->addColumn('order', 'is_mobile', $this->boolean()->defaultValue(0)->comment('Заявка создана в приложении')->after('id'));
    }
}
