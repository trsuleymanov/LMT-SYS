<?php

use yii\db\Migration;


class m180125_193921_add_operator_fields_to_client_ext extends Migration
{
    public function up()
    {
        $this->addColumn('client_ext', 'start_processing_operator_id', $this->integer()->comment('Оператор первым начавшим обрабатывать заявку')->after('client_fio'));
        $this->addColumn('client_ext', 'start_processing_time', $this->integer()->comment('Время первого нажатия на кнопку "Обработать" заявку')->after('start_processing_operator_id'));
    }

    public function down()
    {
        $this->dropColumn('client_ext', 'start_processing_operator_id');
        $this->dropColumn('client_ext', 'start_processing_time');
    }
}
