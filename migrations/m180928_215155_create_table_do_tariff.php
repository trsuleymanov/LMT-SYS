<?php

use yii\db\Migration;


class m180928_215155_create_table_do_tariff extends Migration
{
    public function up()
    {
        $this->createTable('do_tariff', [
            'id' => $this->primaryKey(),
            'code' => $this->string(100)->comment('Код команды'),
            'description' => $this->string(255)->comment('Команда'),
            'tariff_type' => "ENUM('order', 'client')",
            'created_at' => $this->integer()->comment('Создан'),
            'updated_at' => $this->integer()->comment('Изменен'),
        ]);

        $this->BatchInsert('do_tariff',
            ['description', 'code', 'tariff_type', 'created_at',],
            [
                ['Примени тариф по умолчанию, включая коммерческие рейсы', 'source_price', 'order', time()],
                ['Примени тариф по умолчанию, сделай надбавку в 50 рублей', 'source_price_plus_50_rub', 'order', time()],
                ['Установи фикс.цену = 0', 'fix_price_0', 'order', time()],
                ['Установи фикс.цену равно пришедшей цене', 'fix_source_price', 'order', time()],
                ['Добавь в примечания строку "Оплачен аванс, уточнить"', 'add_comment_avans', 'order', time()],
                ['Смотри в клиента, уточни команду', 'use_client_tariff', 'order', time()],
                ['Назначь наценку к стандартному тарифу в 200 руб', 'source_price_plus_200_rub', 'order', time()],
                ['Удвой стандартный тариф', 'double_source_price', 'order', time()],
                ['Установи фикс.цену = 1000', 'fix_price_1000_rub', 'client', time()],
            ]
        );
    }

    public function down()
    {
        $this->dropTable('do_tariff');
    }
}
