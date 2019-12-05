<?php

use yii\db\Migration;
use yii\web\ForbiddenHttpException;

/**
 * Class m180123_150807_add_mobile_informer_office
 */
class m180123_150807_add_mobile_informer_office extends Migration
{
    public function up()
    {
        $informer_office = new \app\models\InformerOffice();
        $informer_office->name = 'Мобильное приложение';
        if(!$informer_office->save()) {
            throw new ForbiddenHttpException('Не удалось создать источник "Мобильное приложение"');
        }
    }

    public function down()
    {
        $informer_office = \app\models\InformerOffice::find()->where(['name' => 'Мобильное приложение'])->one();
        if($informer_office == null) {
            throw new ForbiddenHttpException('Не найден источник "Мобильное приложение" для удаления');
        }
        $informer_office->delete();
    }
}
