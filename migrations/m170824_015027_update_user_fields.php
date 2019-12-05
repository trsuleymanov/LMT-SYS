<?php

use yii\db\Migration;

class m170824_015027_update_user_fields extends Migration
{
    public function up()
    {
        $this->alterColumn('user', 'last_ip', $this->string(20)->comment('IP адрес (последнего входа на сайт)'));
        $this->alterColumn('user', 'attempt_count', $this->smallInteger()->defaultValue(0)->comment('Количество неудачных попыток последнего входа на сайт'));
        $this->alterColumn('user', 'attempt_date', $this->integer()->comment('Время последней попытки входа на сайт'));
    }

    public function down()
    {

    }
}
