<?php

use yii\db\Migration;

/**
 * Class m180707_195613_change_field_commercial_in_trip
 */
class m180707_195613_change_field_commercial_in_trip extends Migration
{
    public function up()
    {
        $this->alterColumn('trip', 'commercial', $this->boolean()->defaultValue(0)->comment('Коммерческий рейс')->after('direction_id'));
    }

    public function down()
    {
        $this->alterColumn('trip', 'commercial', $this->boolean()->defaultValue(0)->comment('Коммерческий рейс')->after('direction_id'));
    }
}
