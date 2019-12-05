<?php
use yii\db\Migration;


class m180527_192226_add_field_model_id_to_storage_operation extends Migration
{
    public function up()
    {
        $this->addColumn('storage_operation', 'model_id', $this->integer()->after('storage_detail_id')->comment('Модель'));
    }

    public function down()
    {
        $this->dropColumn('storage_operation', 'model_id');
    }
}
