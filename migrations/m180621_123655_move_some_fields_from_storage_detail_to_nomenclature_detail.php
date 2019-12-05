<?php

use yii\db\Migration;


class m180621_123655_move_some_fields_from_storage_detail_to_nomenclature_detail extends Migration
{
    // в номенклатуре должны быть поля: наименование, место, сторона, код, принадлежность, единица измерения.
    public function up()
    {
        $this->dropColumn('storage_detail', 'installation_place'); // место
        $this->dropColumn('storage_detail', 'installation_side'); // сторона
        $this->dropColumn('storage_detail', 'detail_code'); // код
        $this->dropColumn('storage_detail', 'model_id'); // принадлежность
        $this->dropColumn('storage_detail', 'measurement_value'); // единица измерения

        $this->addColumn('nomenclature_detail', 'measurement_value_id', $this->integer()->after('name')->comment('Единица измерения'));
        $this->addColumn('nomenclature_detail', 'detail_code', $this->string(50)->after('measurement_value_id')->comment('Код запчасти'));
        $this->addColumn('nomenclature_detail', 'model_id', $this->integer()->after('detail_code')->comment('Модель т/с'));
        $this->addColumn('nomenclature_detail', 'installation_place', $this->smallInteger()->after('model_id')->comment('Место установки'));
        $this->addColumn('nomenclature_detail', 'installation_side', $this->smallInteger()->after('installation_place')->comment('Сторона установки'));
    }

    public function down()
    {
        $this->addColumn('storage_detail', 'measurement_value', $this->string(10)->after('storage_place_count')->comment('Единица измерения'));
        $this->addColumn('storage_detail', 'detail_code', $this->string(50)->after('detail_origin_id')->comment('Код запчасти'));
        $this->addColumn('storage_detail', 'model_id', $this->integer()->after('nomenclature_detail_id')->comment('Модель т/с'));
        $this->addColumn('storage_detail', 'installation_place', $this->smallInteger()->after('model_id')->comment('Место установки'));
        $this->addColumn('storage_detail', 'installation_side', $this->smallInteger()->after('installation_place')->comment('Сторона установки'));

        $this->dropColumn('nomenclature_detail', 'installation_place'); // место
        $this->dropColumn('nomenclature_detail', 'installation_side'); // сторона
        $this->dropColumn('nomenclature_detail', 'detail_code'); // код
        $this->dropColumn('nomenclature_detail', 'model_id'); // принадлежность
        $this->dropColumn('nomenclature_detail', 'measurement_value'); // единица измерения
    }
}
