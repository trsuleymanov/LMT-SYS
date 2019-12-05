<?php

use yii\db\Migration;

class m170701_062718_create_table_cancellation_reason extends Migration
{
    public function up()
    {
        $this->createTable('order_cancellation_reason', [ // причины отмены заказа
            'id' => $this->primaryKey(),
            'name' => $this->string(50)->comment('Название'),
            'code' => $this->string(50)->comment('Код'),
        ]);

        $this->BatchInsert('order_cancellation_reason',
            ['name', 'code'],
            [
                ['не едет', 'not_go'],
                ['уехал другим рейсом', 'left_another_flight'],
                ['уехал с другой фирмой', 'left_with_another_company'],
                ['не берет трубку', 'not_answer_the_phone'],
                ['другая причина', 'another_reason'],
            ]
        );
    }

    public function down()
    {
        $this->dropTable('order_cancellation_reason');
    }
}
