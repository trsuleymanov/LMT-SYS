<?php

use yii\db\Migration;

/**
 * Class m180604_230327_add_fields_installation_place_and_side_to_storage_operation
 */
class m180604_230327_add_fields_installation_place_and_side_to_storage_operation extends Migration
{
    public function up()
    {
        $this->addColumn('storage_operation', 'installation_place', $this->smallInteger()->after('model_id')->comment('Место установки'));
        $this->addColumn('storage_operation', 'installation_side', $this->smallInteger()->after('installation_place')->comment('Сторона установки'));
    }

    public function down()
    {
        $this->dropColumn('storage_operation', 'installation_place');
        $this->dropColumn('storage_operation', 'installation_side');
    }
}
