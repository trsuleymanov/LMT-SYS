<?php

use yii\db\Migration;

class m180116_232124_add_field_formula_id_to_transport extends Migration
{
    public function up()
    {
        $this->addColumn('transport', 'formula_id', $this->integer()->comment('Формула расчета процента')->after('base_city_id'));
    }

    public function down()
    {
        $this->dropColumn('transport', 'formula_id');
    }
}
