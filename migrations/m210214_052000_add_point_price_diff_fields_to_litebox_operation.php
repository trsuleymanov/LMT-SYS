<?php

use yii\db\Migration;

/**
 * Class m210214_052000_add_point_price_diff_fields_to_litebox_operation
 */
class m210214_052000_add_point_price_diff_fields_to_litebox_operation extends Migration
{
    public function up()
    {
        $this->addColumn('litebox_operation', 'point_from_price_diff', $this->smallInteger()->defaultValue(0)->after('place_type')->comment('Скидка/наценка за место на точке посадки'));
        $this->addColumn('litebox_operation', 'point_to_price_diff', $this->smallInteger()->defaultValue(0)->after('point_from_price_diff')->comment('Скидка/наценка за место на точке высадки'));
    }

    public function down()
    {
        $this->dropColumn('litebox_operation', 'point_from_price_diff');
        $this->dropColumn('litebox_operation', 'point_to_price_diff');
    }
}
