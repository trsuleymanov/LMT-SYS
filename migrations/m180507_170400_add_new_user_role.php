<?php

use app\models\UserRole;
use yii\db\Migration;

/**
 * Class m180507_170400_add_new_user_role
 */
class m180507_170400_add_new_user_role extends Migration
{
    public function up()
    {
        $user_role = new UserRole();
        $user_role->name = 'Оператор графика';
        $user_role->alias = 'graph_operator';
        if(!$user_role->save()) {
            throw new \yii\base\ErrorException('Не удалось добавить новую роль оператора графика');
        }
    }

    public function down()
    {
        $role = UserRole::find()->where(['alias' => 'graph_operator'])->one();
        if($role != null) {
            $role->delete();
        }
    }
}
