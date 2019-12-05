<?php

use app\models\UserRole;
use yii\db\Migration;
use yii\web\ForbiddenHttpException;

/**
 * Class m190727_220527_create_table_app_lock
 */
class m190727_220527_create_table_app_lock extends Migration
{
    /**
     * @return bool|void
     * @throws ForbiddenHttpException
     */
    public function up()
    {
//        $this->createTable('app_lock', [
//            'id' => $this->primaryKey(),
//            'name' => $this->string(50)->comment('Имя'),
//            'role' => $this->string(50)->comment('Роль'),
//            'status' => "ENUM('start', 'unlock', 'finish')",
//            'created_at' => $this->integer()->comment('Время пинга'),
//        ]);

        $this->createTable('working_shift', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->comment('Пользователь'),
            // КА день, АК день, Ночная смена, Трубки => ka_day, ak_day, night, tube
            'shift_type' => "ENUM('ka_day', 'ak_day', 'night', 'tube')",
            'start_time' => $this->integer()->comment('Время начала смены'),
            'finish_time' => $this->integer()->comment('Время завершения смены'),
        ]);

        $this->createTable('working_shift_unlocking_time', [
            'id' => $this->primaryKey(),
            'working_shift_id' => $this->integer()->comment('Смена'),
            'created_at' => $this->integer()->comment('Время разблокировки'),
        ]);

        $this->addColumn('user_role', 'controlled', $this->boolean()->comment('Контролируемая роль'));
        $user_roles = UserRole::find()->where(['alias' => ['editor', 'manager']])->all();
        foreach ($user_roles as $user_role) {
            $user_role->controlled = true;
            if(!$user_role->save(false)) {
                throw new ForbiddenHttpException('Не удалось сохранить роль');
            }
        }
    }

    public function down()
    {
        $this->dropColumn('user_role', 'user_id');
        $this->dropTable('working_shift_unlocking_time');
        $this->dropTable('working_shift');
    }
}
