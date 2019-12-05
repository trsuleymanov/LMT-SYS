<?php

use yii\db\Migration;

/**
 * Class m180317_160936_add_field_seans_duration_finish_to_user
 */
class m180317_160936_add_field_seans_duration_finish_to_user extends Migration
{
    public function up()
    {
        $this->addColumn('user', 'seans_duration_finish', $this->integer()->defaultValue(900)->after('auth_seans_finish')->comment('Интервал времени истечения сеанса пользователя в секундах'));
    }

    public function down()
    {
        $this->dropColumn('user', 'seans_duration_finish');
    }
}
