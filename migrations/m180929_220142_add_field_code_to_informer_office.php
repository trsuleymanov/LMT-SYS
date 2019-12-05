<?php

use yii\db\Migration;

/**
 * Class m180929_220142_add_field_code_to_informer_office
 */
class m180929_220142_add_field_code_to_informer_office extends Migration
{
    public function up()
    {
        $this->addColumn('informer_office', 'code', $this->string(50)->after('name')->comment('Код источника (нужен для работы ПО)'));
    }

    public function down()
    {
        $this->dropColumn('informer_office', 'code');
    }
}
