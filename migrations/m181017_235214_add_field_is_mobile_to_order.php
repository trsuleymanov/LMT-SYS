<?php

use yii\db\Migration;

/**
 * Class m181017_235214_add_field_is_mobile_to_order
 */
class m181017_235214_add_field_is_mobile_to_order extends Migration
{
    public function up()
    {
        $this->addColumn('order', 'is_mobile', $this->boolean()->defaultValue(0)->comment('Заявка создана в приложении')->after('id'));
    }

    public function down()
    {
        $this->dropColumn('order', 'is_mobile');
    }
}
