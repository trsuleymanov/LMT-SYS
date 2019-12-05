<?php

use yii\db\Migration;

/**
 * Class m180302_051538_change_map_scale_fields_in_city
 */
class m180302_051538_change_map_scale_fields_in_city extends Migration
{
    public function up()
    {
        $this->alterColumn('city', 'main_point_show_scale',  $this->smallInteger(6)->defaultValue(12)->comment('Масштаб фокусировки точки')->after('search_scale'));
        $this->renameColumn('city', 'main_point_show_scale', 'point_focusing_scale');

        $this->renameColumn('city', 'accessory_point_show_scale', 'all_points_show_scale');

    }

    public function down()
    {
        $this->renameColumn('city', 'point_focusing_scale', 'main_point_show_scale');
        $this->alterColumn('city', 'main_point_show_scale',  $this->smallInteger(6)->defaultValue(12)->comment('Масштаб карты на котором появляется выделенная точка')->after('search_scale'));

        $this->renameColumn('city', 'all_points_show_scale', 'accessory_point_show_scale');
    }
}
