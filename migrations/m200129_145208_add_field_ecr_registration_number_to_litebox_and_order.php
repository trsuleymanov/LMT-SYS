<?php

use yii\db\Migration;

/**
 * Class m200129_145208_add_field_ecr_registration_number_to_litebox_and_order
 */
class m200129_145208_add_field_ecr_registration_number_to_litebox_and_order extends Migration
{
    public function up()
    {
        $this->addColumn('litebox_operation', 'ecr_registration_number', $this->string(16)->comment('Регистрационный номер ККТ')->after('fiscal_document_number'));
        $this->addColumn('order', 'litebox_ecr_registration_number', $this->string(16)->comment('Регистрационный номер ККТ')->after('litebox_fiscal_document_number'));
    }

    public function down()
    {
        $this->dropColumn('litebox_operation', 'ecr_registration_number');
        $this->dropColumn('order', 'litebox_ecr_registration_number');
    }
}
