<?php

use app\models\AccessPlaces;
use yii\db\Migration;

/**
 * Class m191026_175009_add_ejsv_access_to_access
 */
class m191026_175009_add_ejsv_access_to_access extends Migration
{
    public function up()
    {
        $access_place = new AccessPlaces();
        $access_place->module = 'site';
        $access_place->page_url = '/';
        $access_place->page_part = 'ejsv';
        $access_place->name = 'ЭЖСВ';
        if(!$access_place->save(false)) {
            throw new ErrorException('Не удается создать доступ к ЭЖСВ');
        }
    }

    public function down()
    {
        $access_place = AccessPlaces::find()->where(['page_part' => 'ejsv'])->one();
        $access_place->delete();
    }
}
