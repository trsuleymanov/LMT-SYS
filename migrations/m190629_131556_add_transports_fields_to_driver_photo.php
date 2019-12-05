<?php

use yii\db\Migration;

/**
 * Class m190629_131556_add_transports_fields_to_driver_photo
 */
class m190629_131556_add_transports_fields_to_driver_photo extends Migration
{
    public function up()
    {
        $this->addColumn('driver_photo', 'transport_id', $this->integer()->comment('Машина')->after('user_id'));
        $this->addColumn('driver_photo', 'transport_car_reg', $this->string(20)->comment('Гос. номер машины')->after('transport_id'));
    }

    public function down()
    {
        $this->dropColumn('driver_photo', 'transport_id');
        $this->dropColumn('driver_photo', 'transport_car_reg');
    }
}
