<?php

use yii\db\Migration;

/**
 * Class m180701_033930_change_field_remainder_in_storage_detail
 */
class m180701_033930_change_field_remainder_in_storage_detail extends Migration
{
    public function up()
    {
        $this->alterColumn('storage_detail', 'remainder', $this->double()->comment('Остаток'));
    }

    public function down()
    {
        $this->alterColumn('storage_detail', 'remainder', $this->smallInteger()->comment('Остаток'));
    }
}
