<?php

use yii\db\Migration;

/**
 * Class m190228_175500_add_crm_url_to_setting
 */
class m190228_175500_add_crm_url_to_setting extends Migration
{
    public function up()
    {
        $this->addColumn('setting', 'crm_url_for_beeline_ats', $this->string(100)->comment('Ссылка для АТС биллайна на струницу в CRM принимающую сообщения от АТС'));
    }

    public function down()
    {
        $this->dropColumn('setting', 'crm_url_for_beeline_ats');
    }
}
