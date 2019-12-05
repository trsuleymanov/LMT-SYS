<?php

use yii\db\Migration;


class m181023_211148_add_fields_time_to_get_together_to_yandex_point extends Migration
{
    public function up()
    {
        $this->addColumn('yandex_point', 'time_to_get_together_short', $this->integer()->comment('Относительное время от ВРПТ до конечной базовой точки рейса коротких рейсов')->after('alias'));
        $this->addColumn('yandex_point', 'time_to_get_together_long', $this->integer()->comment('Относительное время от ВРПТ до конечной базовой точки рейса длинных рейсов')->after('time_to_get_together_short'));
    }

    public function down()
    {
        $this->dropColumn('yandex_point', 'time_to_get_together_short');
        $this->dropColumn('yandex_point', 'time_to_get_together_long');
    }
}
