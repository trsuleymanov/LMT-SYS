<?php

use yii\db\Migration;

/**
 * Class m190715_214821_create_table_litebox_operation
 */
class m190715_214821_create_table_litebox_operation extends Migration
{
    public function up()
    {
//        id
//        order_id,
//        uuid (36),
//        status, - enum['wait', 'done', 'fail']
//        status_setting_time
//        sell_at,
//        sell_refund_at
//        fn_number - string(16)
//        fiscal_document_number - string(10)
//        fiscal_document_attribute - string(10)

        $this->createTable('litebox_operation', [
            'id' => $this->primaryKey(),
            'order_id' => $this->integer()->comment('Заказ'),
            'sell_at' => $this->integer()->comment('Время отправки запроса типа "Приход"'),
            'sell_uuid' => $this->string(36)->comment('uuid операции "Возврат прихода", возвращенный сервером Litebox'),
            'sell_status' => "ENUM('wait', 'done', 'fail')",
            'sell_status_setting_time' => $this->integer()->comment('Время установка статуса'),
            'fn_number' => $this->string(16)->comment('ФН номер (номер фискального накопителя)'),
            'fiscal_document_number' => $this->string(10)->comment('Фискальный номер документа'),
            'fiscal_document_attribute' => $this->string(10)->comment('Фискальный признак документа'),

            'sell_refund_at' => $this->integer()->comment('Время отправки запроса типа "Возврат прихода"'),
            'sell_refund_uuid' => $this->string(36)->comment('uuid операции "Возврат прихода", возвращенный сервером Litebox'),
            'sell_refund_status' => "ENUM('wait', 'done', 'fail')",
            'sell_refund_status_setting_time' => $this->integer()->comment('Время установка статуса'),
        ]);
    }

    public function down()
    {
        $this->dropTable('litebox_operation');
    }
}
