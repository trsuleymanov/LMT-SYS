<?php

use yii\db\Migration;

class m180220_212157_add_yandex_map_fields_to_city extends Migration
{
    public function up()
    {
        $this->addColumn('city', 'search_scale', $this->smallInteger(6)->defaultValue(16)->comment('Приближение карты при поиске')->after('map_scale'));
        $this->addColumn('city', 'main_point_show_scale', $this->smallInteger(6)->defaultValue(12)->comment('Масштаб карты на котором появляется выделенная точка')->after('search_scale'));
        $this->addColumn('city', 'accessory_point_show_scale', $this->smallInteger(6)->defaultValue(15)->comment('Масшаб карты на котором появляются все точки')->after('main_point_show_scale'));
    }

    public function down()
    {
        $this->dropColumn('city', 'search_scale');
        $this->dropColumn('city', 'main_point_show_scale');
        $this->dropColumn('city', 'accessory_point_show_scale');
    }
}
