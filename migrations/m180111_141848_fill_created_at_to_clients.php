<?php

use yii\db\Migration;

class m180111_141848_fill_created_at_to_clients extends Migration
{
    public function up()
    {
        $sql = 'UPDATE `client` SET created_at='.time().', updated_at='.time();
        Yii::$app->db->createCommand($sql)->execute();
    }

    public function down()
    {
        $sql = 'UPDATE `client` SET created_at=NULL, updated_at=NULL';
        Yii::$app->db->createCommand($sql)->execute();
    }
}
