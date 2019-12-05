<?php

use yii\db\Migration;

class m170819_232201_add_field_has_penalty_to_order extends Migration
{
    public function up()
    {
        $this->addColumn('order', 'has_penalty', $this->boolean()->defaultValue(0)->after('fact_trip_transport_id')->comment('Наличие штрафа'));
    }

    public function down()
    {
        $this->dropColumn('order', 'has_penalty');
    }
}
