<?php

use app\models\NomenclatureDetail;
use yii\db\Migration;

/**
 * Class m180828_172723_create_table_detail_name
 */
class m180828_172723_create_table_detail_name extends Migration
{
    public function up()
    {
        $this->createTable('detail_name', [
            'id' => $this->primaryKey(),
            'name' => $this->string(50)->comment('Наименование')->unique(),
        ]);

        $aDetailsNames = [];
        $nomenclature_details = NomenclatureDetail::find()->all();
        foreach($nomenclature_details as $detail) {
            $aDetailsNames[$detail->name][0] = $detail->name;
        }

        $this->BatchInsert('detail_name',
            ['name'],
            $aDetailsNames
        );
    }

    public function down()
    {
        $this->dropTable('detail_name');
    }
}
