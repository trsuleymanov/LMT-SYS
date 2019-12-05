<?php

use yii\db\Migration;

/**
 * Class m180607_005410_delete_fields_from_storage_operation
 */
class m180607_005410_delete_fields_from_storage_operation extends Migration
{
    public function up()
    {
        $this->dropColumn('storage_operation', 'installation_place');
        $this->dropColumn('storage_operation', 'installation_side');
        $this->dropColumn('storage_operation', 'storage_id');
        $this->dropColumn('storage_operation', 'model_id');
    }

    public function down()
    {
        $this->addColumn('storage_operation', 'storage_id', $this->integer()->after('id')->comment('Склад'));
        $this->addColumn('storage_operation', 'model_id', $this->integer()->after('storage_detail_id')->comment('Модель'));
        $this->addColumn('storage_operation', 'installation_place', $this->smallInteger()->after('model_id')->comment('Место установки'));
        $this->addColumn('storage_operation', 'installation_side', $this->smallInteger()->after('installation_place')->comment('Сторона установки'));
    }
}
