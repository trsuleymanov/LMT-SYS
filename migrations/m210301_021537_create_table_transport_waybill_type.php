<?php

use yii\db\Migration;

/**
 * Class m210301_021537_create_table_transport_waybill_type
 */
class m210301_021537_create_table_transport_waybill_type extends Migration
{
    public function up()
    {
        $this->createTable('transport_waybill_type', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->comment('Название'),
            'allow_minus_opearation' => $this->boolean()->defaultValue(false)->comment('Разрешены отрицательные суммы денег')
        ]);

        $this->addColumn('transport_waybill', 'transport_waybill_type_id', $this->integer()->after('id')->comment('Тип путевого листа'));
    }

    public function down()
    {
        $this->dropColumn('transport_waybill', 'transport_waybill_type_id');

        $this->dropTable('transport_waybill_type');
    }
}
