<?php

use app\models\Access;
use app\models\AccessPlaces;
use yii\db\Migration;

/**
 * Class m210227_014401_add_new_access_for_waybill
 */
class m210227_014401_add_new_access_for_waybill extends Migration
{
    public function up()
    {
        $access_place = new AccessPlaces();
        $access_place->module = 'waybill';
        $access_place->page_url = '.';
        $access_place->page_part = 'access_to_delivery_of_proceeds';
        $access_place->name = 'Доступ к информации о сдаче выручки';
        $access_place->save();

        $access1 = new Access();
        $access1->id_access_places = $access_place->id;
        $access1->user_role_id = 1;
        $access1->access = 1;
        $access1->save();

        $access2 = new Access();
        $access2->id_access_places = $access_place->id;
        $access2->user_role_id = 2;
        $access2->access = 1;
        $access2->save();
    }

    public function down()
    {
        $access_place = AccessPlaces::find()->where(['page_part' => 'access_to_delivery_of_proceeds'])->one();
        if($access_place != null) {
            $sql = 'DELETE FROM `access` WHERE id_access_places='.$access_place->id;
            Yii::$app->db->createCommand($sql)->execute();

            $access_place->delete();
        }
    }
}
