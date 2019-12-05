<?php

use app\models\DetailName;
use app\models\NomenclatureDetail;
use yii\db\Migration;
use yii\helpers\ArrayHelper;

/**
 * Class m180828_192639_change_field_nomenclature_detail_name
 */
class m180828_192639_change_field_nomenclature_detail_name extends Migration
{
    public function up()
    {
        //$this->dropColumn('nomenclature_detail', 'name');
        $this->addColumn('nomenclature_detail', 'detail_name_id', $this->integer()->after('id')->comment('Наименование'));

        $nomenclature_details = NomenclatureDetail::find()->all();
        $details_names = DetailName::find()->all();
        $aDetailsNames = ArrayHelper::map($details_names, 'name', 'id');
        foreach($nomenclature_details as $nomenclature_detail) {
            $nomenclature_detail->detail_name_id = $aDetailsNames[$nomenclature_detail->name];
            if(!$nomenclature_detail->save()) {
                throw new \yii\web\ForbiddenHttpException('Не удалось сохранить номенклатуру detail_name_id='.$aDetailsNames[$nomenclature_detail->name].' nomenclature_detail_id='.$nomenclature_detail->id);
            }
        }
    }

    public function down()
    {
        $this->dropColumn('nomenclature_detail', 'detail_name_id');
    }
}
