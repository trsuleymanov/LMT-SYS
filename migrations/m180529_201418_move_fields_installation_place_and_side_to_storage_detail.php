<?php

use yii\db\Migration;


class m180529_201418_move_fields_installation_place_and_side_to_storage_detail extends Migration
{
    public function up()
    {
        $this->dropColumn('nomenclature_detail', 'installation_place');
        $this->dropColumn('nomenclature_detail', 'installation_side');

        $this->addColumn('storage_detail', 'installation_place', $this->smallInteger()->after('model_id')->comment('Место установки'));
        $this->addColumn('storage_detail', 'installation_side', $this->smallInteger()->after('installation_place')->comment('Сторона установки'));
    }

    public function down()
    {
        $this->addColumn('nomenclature_detail', 'installation_place', $this->smallInteger()->after('comment')->comment('Место установки'));
        $this->addColumn('nomenclature_detail', 'installation_side', $this->smallInteger()->after('installation_place')->comment('Сторона установки'));

        $this->dropColumn('storage_detail', 'installation_place');
        $this->dropColumn('storage_detail', 'installation_side');
    }
}
