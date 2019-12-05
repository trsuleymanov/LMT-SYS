<?php

use yii\db\Migration;

/**
 * Class m190925_022410_add_fields_to_cashback_setting
 */
class m190925_022410_add_fields_to_cashback_setting extends Migration
{
    public function up()
    {
        $this->addColumn('cashback_setting', 'has_cashback_for_prepayment', $this->boolean()->defaultValue(0)->comment('Кэш-бэк предоплаты')->after('with_commercial_trips'));
        $this->addColumn('cashback_setting', 'has_cashback_for_nonprepayment', $this->boolean()->defaultValue(0)->comment('Обычный кэш-бэк')->after('has_cashback_for_prepayment'));
    }

    public function down()
    {
        $this->dropColumn('cashback_setting', 'has_cashback_for_prepayment');
        $this->dropColumn('cashback_setting', 'has_cashback_for_nonprepayment');
    }
}
