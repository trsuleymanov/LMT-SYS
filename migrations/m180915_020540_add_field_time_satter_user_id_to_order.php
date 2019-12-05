<?php

use yii\db\Migration;


class m180915_020540_add_field_time_satter_user_id_to_order extends Migration
{
    public function up()
    {
        $this->addColumn('order', 'time_satter_user_id', $this->integer()->after('time_sat')->comment('Пользователь нажавший кнопку "Посадить"'));
    }

    public function down()
    {
        $this->dropColumn('order', 'time_satter_user_id');
    }
}
