<?php

use yii\db\Migration;

/**
 * Создание таблицы "номенклатура" (список деталей)
 */
class m180511_211219_create_table_nomenclature_detail extends Migration
{
    public function up()
    {
        // Поля: ID, наименование, комментарий, место установки (сзади/спереди/без признака),
        // сторона установки (слева, справа, без признака)
        $this->createTable('nomenclature_detail', [
            'id' => $this->primaryKey(),
            'name' => $this->string(50)->comment('Наименование'),
            'comment' => $this->text()->comment('Комментарий'),
            'installation_place' => $this->smallInteger()->comment('Место установки: 0 - без признака, 1 - сзади, 2 - спереди'),
            'installation_side' => $this->smallInteger()->comment(' Сторона установки: 0 - без признака, 1 - слева, 2 - справа'),
        ]);
    }

    public function down()
    {
        $this->dropTable('nomenclature_detail');
    }
}
