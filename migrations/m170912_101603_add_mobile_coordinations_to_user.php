<?php

use yii\db\Migration;

class m170912_101603_add_mobile_coordinations_to_user extends Migration
{
    public function up()
    {
        $this->addColumn('user', 'lat', $this->double()->defaultValue(0)->after('attempt_date')->comment('Широта'));
        $this->addColumn('user', 'long', $this->double()->defaultValue(0)->after('lat')->comment('Долгота'));
        $this->addColumn('user', 'lat_long_ping_at', $this->integer()->after('long')->comment('Время получения координат lat и long'));
    }

    public function down()
    {
        $this->dropColumn('user', 'lat');
        $this->dropColumn('user', 'long');
        $this->dropColumn('user', 'lat_long_ping_at');
    }
}
