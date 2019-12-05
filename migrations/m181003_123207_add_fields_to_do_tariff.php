<?php

use yii\db\Migration;

/**
 * Class m181003_123207_add_fields_to_do_tariff
 */
class m181003_123207_add_fields_to_do_tariff extends Migration
{
    public function up()
    {
        $this->addColumn('do_tariff', 'place_price_formula', $this->string(255)->after('description')->comment('Формула расчета цены за место'));
        $this->addColumn('do_tariff', 'use_fix_price', $this->boolean()->defaultValue(0)->after('place_price_formula')->comment('Устанавливать фикс.цену'));
        $this->addColumn('do_tariff', 'order_price_formula', $this->string(255)->after('use_fix_price')->comment('Формула расчета итоговой цены заказа'));
        $this->addColumn('do_tariff', 'order_comment', $this->string(255)->after('order_price_formula')->comment('Примечание к заказу'));
        $this->addColumn('do_tariff', 'use_client_do_tariff', $this->boolean()->defaultValue(0)->after('order_comment')->comment('Использовать признак клиента'));
    }

    public function down()
    {
        $this->dropColumn('do_tariff', 'place_price_formula');
        $this->dropColumn('do_tariff', 'order_price_formula');
        $this->dropColumn('do_tariff', 'use_fix_price');
        $this->dropColumn('do_tariff', 'order_comment');
        $this->dropColumn('do_tariff', 'use_client_do_tariff');
    }
}
