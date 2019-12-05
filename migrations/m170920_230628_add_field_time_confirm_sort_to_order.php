<?php

use yii\db\Migration;

class m170920_230628_add_field_time_confirm_sort_to_order extends Migration
{
    public function up()
    {
        $this->addColumn('order', 'time_confirm_sort', $this->integer()->comment('Поле сортировки времени подтвеждения')->after('time_confirm'));
    }

    public function down()
    {
        $this->dropColumn('order', 'time_confirm_sort');
    }
}
