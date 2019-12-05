<?php

use yii\db\Migration;

/**
 * Class m180611_224837_create_table_passport
 */
class m180611_224837_create_table_passenger extends Migration
{
    public function up()
    {
        $this->createTable('passenger', [
            'id' => $this->primaryKey(),
            'client_id' => $this->integer()->defaultValue(0)->comment('Клиент'),
            'child' => $this->boolean()->defaultValue(0)->comment('0 - Взрослый / 1 - Ребенок (без паспорта)'),
            'series' => $this->string(4)->comment('Серия паспорта'),
            'number' => $this->string(6)->comment('Номер паспорта'),
            'surname' => $this->string(30)->comment('Фамилия'),
            'name' => $this->string(30)->comment('Имя'),
            'patronymic' => $this->string(30)->comment('Отчество'),
            'date_of_birth' => $this->integer()->comment('Дата рождения'),
            'citizenship' => $this->string(50)->comment('Гражданство'), // нужен список предложений
            'gender' => $this->boolean()->defaultValue(0)->comment('Пол') // нужен список конечный в модели
        ]);
    }

    public function down()
    {
        $this->dropTable('passenger');
    }
}
