<?php

use app\models\YandexPoint;
use yii\db\Migration;

/**
 * Class m210213_082713_rename_airport_to_unified_in_yandex_point
 */
class m210213_082713_rename_airport_to_unified_in_yandex_point extends Migration
{
    public function up()
    {
        $sql = "UPDATE `yandex_point` SET alias='unified' WHERE alias='airport'; ";
        Yii::$app->db->createCommand($sql)->execute();
    }

    public function down()
    {
        $sql = "UPDATE `yandex_point` SET alias='airport' WHERE alias='unified'; ";
        Yii::$app->db->createCommand($sql)->execute();
    }
}
