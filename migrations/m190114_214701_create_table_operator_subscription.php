<?php

use yii\db\Migration;

/**
 * Class m190114_214701_create_table_operator_subscription
 */
class m190114_214701_create_table_operator_subscription extends Migration
{
    public function up()
    {
        $this->createTable('operator_beeline_subscription', [
            'id' => $this->primaryKey(),
            'operator_id' => $this->integer()->comment('Оператор (пользователь) подписанный к АТС'),
            'subscription_id' => $this->string(38)->comment('Код подписки в АТС'),
            'mobile_ats_login' => $this->string(100)->comment('Логин в АТС - поле targetId в АТС'),
            'expire_at' => $this->integer()->comment('Время когда истекает действие подписки'),
        ]);

        $this->dropTable('beeline_subscription');

        $this->dropColumn('user', 'mobile_ats_login');
    }

    public function down()
    {
        $this->dropTable('operator_beeline_subscription');

        $this->createTable('beeline_subscription', [
            'id' => $this->primaryKey(),
            'subscription_id' => $this->string(38)->comment('Код подписки в АТС'),
            'mobile_ats_login' => $this->string(100)->comment('Логин в АТС - поле targetId в АТС'),
            'expire_at' => $this->integer()->comment('Время когда истекает действие подписки'),
        ]);

        $this->addColumn('user', 'mobile_ats_login', $this->string(30)->after('username')->comment('Логин в мобильной АТС'));
    }
}
