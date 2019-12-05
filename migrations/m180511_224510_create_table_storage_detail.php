<?php

use yii\db\Migration;

/**
 * Создание таблицы "детали на складе"
 */
class m180511_224510_create_table_storage_detail extends Migration
{
    public function up()
    {
//        ID,
//        наименование запчасти из номенклатуры,
//        принадлежность к модели т/с из Модели т/с,
//        состояние запчасти из "Состояние запчасти" ,
//        происхождение из "Происхождения",
//        Код запчасти,
//        Место на складе,
//        величина измерения,
//        остаток,
//        Комментарий,
//        Местонахождение запчасти из Списка складов,
//        дата последнего обновления позиции

        $this->createTable('storage_detail', [
            'id' => $this->primaryKey(),
            'storage_id' => $this->integer()->comment('Склад'),
            'nomenclature_detail_id' => $this->integer()->comment('Запчасть из номенклатуры'),
            'model_id' => $this->integer()->comment('Модель т/с'),
            'detail_state_id' => $this->integer()->comment('Состояние запчасти'),
            'detail_origin_id' => $this->integer()->comment('Происхождение детали'),
            'detail_code' => $this->string(50)->comment('Код запчасти'),
            'storage_place_count' => $this->smallInteger()->comment('Мест на складе'),
            'measurement_value' => $this->string(10)->comment('Единица измерения'),
            'remainder' => $this->smallInteger()->comment('Остаток'),
            'comment' => $this->text()->comment('Комментарий'),
            'updated_at' => $this->integer()->comment('Время изменения'),
        ]);
    }

    public function down()
    {
        $this->dropTable('storage_detail');
    }
}
