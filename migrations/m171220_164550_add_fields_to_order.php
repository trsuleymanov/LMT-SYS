<?php

use yii\db\Migration;

class m171220_164550_add_fields_to_order extends Migration
{
    public function up()
    {
        $this->addColumn('order', 'fact_trip_transport_car_reg', $this->string(20)->after('fact_trip_transport_id')->comment('Гос. номер т/с'));
        $this->addColumn('order', 'client_name', $this->string(50)->after('client_id')->comment('ФИО клиента'));
    }

    public function down()
    {
        $this->dropColumn('order', 'fact_trip_transport_car_reg');
        $this->dropColumn('order', 'client_name');
    }
}
