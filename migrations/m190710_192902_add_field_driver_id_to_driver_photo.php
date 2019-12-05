<?php

use yii\db\Migration;

/**
 * Class m190710_192902_add_field_driver_id_to_driver_photo
 */
class m190710_192902_add_field_driver_id_to_driver_photo extends Migration
{
    public function up()
    {
        $this->addColumn('driver_photo', 'driver_id', $this->integer()->comment('Водитель')->after('user_id'));
    }

    public function down()
    {
        $this->dropColumn('driver_photo', 'driver_id');
    }
}
