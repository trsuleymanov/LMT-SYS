<?php

use yii\db\Migration;

/**
 * Class m180701_005617_change_field_count_in_storage_operation
 */
class m180701_005617_change_field_count_in_storage_operation extends Migration
{
    public function up()
    {
        $this->alterColumn('storage_operation', 'count', $this->double()->comment('Количество'));
    }

    public function down()
    {
        $this->alterColumn('storage_operation', 'count', $this->smallInteger()->comment('Количество'));
    }
}
