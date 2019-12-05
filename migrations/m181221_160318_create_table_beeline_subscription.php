<?php

use yii\db\Migration;


class m181221_160318_create_table_beeline_subscription extends Migration
{
    public function up()
    {
//        id
//        subscription_id	- subscriptionId в АТС
//        mobile_ats_login    - targetId в АТС
//        expire_at - время когда истекает подписка

        // 78f31e9f-1066-4457-88da-6246292d9ca4
        $this->createTable('beeline_subscription', [
            'id' => $this->primaryKey(),
            'subscription_id' => $this->string(38)->comment('Код подписки в АТС'),
            'mobile_ats_login' => $this->string(100)->comment('Логин в АТС - поле targetId в АТС'),
            'expire_at' => $this->integer()->comment('Время когда истекает действие подписки'),
        ]);
    }

    public function down()
    {
        $this->dropTable('beeline_subscription');
    }
}
