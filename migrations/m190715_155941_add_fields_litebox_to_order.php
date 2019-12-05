<?php

use yii\db\Migration;

/**
 * Class m190715_155941_add_fields_litebox_to_order
 */
class m190715_155941_add_fields_litebox_to_order extends Migration
{
    public function up()
    {
        $this->addColumn('order', 'litebox_uuid', $this->string(36)->comment('uuid операции, возвращенный сервером Litebox')->after('paid_time'));
        // ФН: litebox_fn_number - 16
        // ФД: litebox_fiscal_document_number - 10
        // ?ФПД: litebox_fiscal_receipt_number - 10
        // ?ФПД: litebox_fiscal_document_attribute
        $this->addColumn('order', 'litebox_fn_number', $this->string(16)->comment('ФН номер (номер фискального накопителя)')->after('litebox_uuid'));
        $this->addColumn('order', 'litebox_fiscal_document_number', $this->string(10)->comment('Фискальный номер документа')->after('litebox_fn_number'));
        //$this->addColumn('order', 'litebox_fiscal_receipt_number', $this->string(10)->comment('Номер чека в смене')->after('litebox_fiscal_document_number'));
        $this->addColumn('order', 'litebox_fiscal_document_attribute', $this->string(10)->comment('Фискальный признак документа')->after('litebox_fiscal_document_number'));

    }

    public function down()
    {
        $this->dropColumn('order', 'litebox_uuid');
        $this->dropColumn('order', 'litebox_fn_number');
        $this->dropColumn('order', 'litebox_fiscal_document_number');
        // $this->dropColumn('order', 'litebox_fiscal_receipt_number');
        $this->dropColumn('order', 'litebox_fiscal_document_attribute');
    }
}
