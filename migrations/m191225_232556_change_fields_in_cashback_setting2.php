<?php

use yii\db\Migration;

/**
 * Class m191225_232556_change_fields_in_cashback_setting2
 */
class m191225_232556_change_fields_in_cashback_setting2 extends Migration
{
    public function up()
    {
        $this->dropColumn('cashback_setting', 'has_cashback_for_prepayment');
        $this->dropColumn('cashback_setting', 'has_cashback_for_nonprepayment');

        $this->addColumn('cashback_setting', 'cashback_type', "ENUM('with_prepayment', 'without_prepayment') DEFAULT 'without_prepayment' AFTER with_commercial_trips");
    }

    public function down()
    {
        $this->addColumn('cashback_setting', 'has_cashback_for_prepayment', $this->boolean()->defaultValue(0)->comment('Кэш-бэк предоплаты')->after('with_commercial_trips'));
        $this->addColumn('cashback_setting', 'has_cashback_for_nonprepayment', $this->boolean()->defaultValue(0)->comment('Обычный кэш-бэк')->after('has_cashback_for_prepayment'));

        $this->dropColumn('cashback_setting', 'cashback_type');
    }
}
