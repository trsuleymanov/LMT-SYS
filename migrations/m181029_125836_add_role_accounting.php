<?php

use app\models\UserRole;
use yii\db\Migration;


/**
 * Class m181029_125836_add_role_accounting
 */
class m181029_125836_add_role_accounting extends Migration
{
    public function up()
    {
//        $user_role = new UserRole();
//        $user_role->name = 'Учет';
//        $user_role->alias = 'accounting';
//        if(!$user_role->save()) {
//            throw new ErrorException('Не удалось создать роль "Учет"');
//        }
    }

    public function down()
    {
//        $user_role = UserRole::find()->where(['alias' => 'accounting'])->one();
//        if(!$user_role->delete()) {
//            throw new ErrorException('Не удалось удалить роль "Учет"');
//        }
    }
}
