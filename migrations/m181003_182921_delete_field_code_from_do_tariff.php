<?php

use yii\db\Migration;

/**
 * Class m181003_182921_delete_field_code_from_do_tariff
 */
class m181003_182921_delete_field_code_from_do_tariff extends Migration
{
    public function up()
    {
        $this->dropColumn('do_tariff', 'code');
    }

    public function down()
    {
        $this->addColumn('do_tariff', 'code', $this->string(100)->comment('Код команды'));
    }
}
