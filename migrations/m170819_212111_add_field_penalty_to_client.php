<?php

use yii\db\Migration;

class m170819_212111_add_field_penalty_to_client extends Migration
{
    public function up()
    {
        $this->addColumn('client', 'penalty', $this->smallInteger()->defaultValue(0)->after('prize_trip_count')->comment('Кол-во штрафов'));
    }

    public function down()
    {
        $this->dropColumn('client', 'penalty');
    }
}
