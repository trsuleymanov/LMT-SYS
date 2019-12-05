<?php

use yii\db\Migration;

/**
 * Class m181127_180641_change_field_count_in_transport_expenses
 */
class m181127_180641_change_field_count_in_transport_expenses extends Migration
{
    public function up()
    {
        $this->alterColumn('transport_expenses', 'count', $this->decimal(8, 2)->defaultValue(0)->comment('Количество'));
    }

    public function down()
    {
        $this->alterColumn('transport_expenses', 'count', $this->smallInteger()->comment('Количество'));
    }
}
