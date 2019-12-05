<?php

use yii\db\Migration;

class m180107_130553_add_field_alias_to_yandex_point extends Migration
{
    public function up()
    {
        $this->addColumn('yandex_point', 'alias', $this->string(10)->defaultValue('')->comment('Доп.поле означающее принадлежность точки к чему-либо, например к аэропорту')->after('critical_point'));
    }

    public function down()
    {
        $this->dropColumn('yandex_point', 'alias');
    }
}
