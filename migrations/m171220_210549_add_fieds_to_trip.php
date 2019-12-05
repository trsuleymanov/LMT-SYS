<?php

use yii\db\Migration;

class m171220_210549_add_fieds_to_trip extends Migration
{
    public function up()
    {
        $this->addColumn('trip', 'date_start_sending', $this->integer()->after('end_time')->comment('Время начала отправки машины'));
        $this->addColumn('trip', 'start_sending_user_id', $this->integer()->after('date_start_sending')->comment('Пользователь(оператор) начавший отправку машины'));

        $this->dropColumn('trip', 'sent_date');
        $this->addColumn('trip', 'sended_user_id', $this->integer()->after('date_sended')->comment('Пользователь(оператор) отправивший машину'));
    }

    public function down()
    {
        $this->dropColumn('trip', 'date_start_sending');
        $this->dropColumn('trip', 'start_sending_user_id');

        $this->addColumn('trip', 'sent_date', $this->integer()->after('end_time')->comment('Дата отправки'));
        $this->dropColumn('trip', 'sended_user_id');
    }
}
