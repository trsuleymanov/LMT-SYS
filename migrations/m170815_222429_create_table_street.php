<?php

use yii\db\Migration;

class m170815_222429_create_table_street extends Migration
{
    public function up()
    {
        $this->createTable('street', [
            'id' => $this->primaryKey(),
            'city_id' => $this->integer()->comment('Город'),
            'name' => $this->string(50)->comment('Название'),
        ]);

        $this->addColumn('order', 'street_id_from', $this->integer()->comment('Улица откуда')->after('direction_id'));
        $this->addColumn('order', 'street_id_to', $this->integer()->comment('Улица куда')->after('time_air_train_arrival'));
    }

    public function down()
    {
        $this->dropColumn('order', 'street_id_from');
        $this->dropColumn('order', 'street_id_to');

        $this->dropTable('street');
    }
}
