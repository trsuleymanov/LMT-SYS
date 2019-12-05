<?php

use yii\db\Migration;

/**
 * Class m190310_223033_add_field_is_visible_to_transport_waybill
 */
class m190310_223033_add_field_is_visible_to_transport_waybill extends Migration
{
    public function up()
    {
        $this->addColumn('transport_waybill', 'is_visible', $this->boolean()->defaultValue(true)->comment('Видимость ПЛ')->after('id'));
    }

    public function down()
    {
        $this->dropColumn('transport_waybill', 'is_visible');
    }
}
