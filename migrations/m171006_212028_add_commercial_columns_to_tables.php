<?php

use yii\db\Migration;

class m171006_212028_add_commercial_columns_to_tables extends Migration
{
    public function up()
    {
        $this->addColumn('trip', 'commercial', $this->boolean()->defaultValue(0)->comment('Коммерческий рейс')->after('direction_id'));
    }

    public function down()
    {
        $this->dropColumn('trip', 'commercial');
    }
}
