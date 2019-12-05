<?php

use yii\db\Migration;

/**
 * Class m190212_190702_change_table_operator_beeline_subscription
 */
class m190212_190702_change_table_operator_beeline_subscription extends Migration
{
    public function up()
    {
        $this->dropTable('operator_beeline_subscription');

        $this->createTable('operator_beeline_subscription', [
            'id' => $this->primaryKey(),
            'name' => $this->string(50)->comment('Название аккаунта (отображается в форме входа)'),
            'operator_id' => $this->integer()->comment('Оператор (пользователь) подписанный к АТС'),
            'status' => $this->string(20)->defaultValue("ONLINE")->comment('Статус'),
            'minutes' => $this->integer()->defaultValue(0)->comment('Количество доступный минут'),
            'subscription_id' => $this->string(38)->comment('Код подписки в АТС'),
            'mobile_ats_login' => $this->string(100)->comment('Логин в АТС - поле targetId в АТС'),
            'expire_at' => $this->integer()->comment('Время когда истекает действие подписки'),
        ]);
    }

    public function down()
    {
        $this->dropTable('operator_beeline_subscription');

        $this->createTable('operator_beeline_subscription', [
            'id' => $this->primaryKey(),
            'operator_id' => $this->integer()->comment('Оператор (пользователь) подписанный к АТС'),
            'status' => $this->string(20)->defaultValue("ONLINE")->comment('Статус'),
            'subscription_id' => $this->string(38)->comment('Код подписки в АТС'),
            'mobile_ats_login' => $this->string(100)->comment('Логин в АТС - поле targetId в АТС'),
            'expire_at' => $this->integer()->comment('Время когда истекает действие подписки'),
        ]);
    }
}
