<?php

use yii\db\Migration;

class m171120_214139_add_fields_to_yandex_point_table extends Migration
{
    public function up()
    {
        $this->addColumn('yandex_point', 'creator_id', $this->integer()->comment('Пользователь создавший точку'));
        $this->addColumn('yandex_point', 'created_at', $this->integer()->comment('Время создания точки'));
        $this->addColumn('yandex_point', 'updater_id', $this->integer()->comment('Пользователь последним редактировавший точку'));
        $this->addColumn('yandex_point', 'updated_at', $this->string(255)->comment('Время последнего редактирования точки'));

    }

    public function down()
    {
        $this->dropColumn('yandex_point', 'creator_id');
        $this->dropColumn('yandex_point', 'created_at');
        $this->dropColumn('yandex_point', 'updater_id');
        $this->dropColumn('yandex_point', 'updated_at');
    }
}
