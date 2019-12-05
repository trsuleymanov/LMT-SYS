<?php

use app\models\UserRole;
use yii\db\Migration;

/**
 * Class m180511_174442_add_role_warehouse_turnover
 */
class m180511_174442_add_role_warehouse_turnover extends Migration
{
    public function up()
    {
        $user_role = new UserRole();
        $user_role->name = 'Оборот склада';
        $user_role->alias = 'warehouse_turnover';
        if(!$user_role->save()) {
            throw new \yii\base\ErrorException('Не удалось добавить новую роль оборота склада');
        }
    }

    public function down()
    {
        $role = UserRole::find()->where(['alias' => 'warehouse_turnover'])->one();
        if($role != null) {
            $role->delete();
        }
    }
}
