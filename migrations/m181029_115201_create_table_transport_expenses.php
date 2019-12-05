<?php

use yii\db\Migration;

/**
 * Class m181029_115201_create_table_transport_expenses
 */
class m181029_115201_create_table_transport_expenses extends Migration
{
    public function up()
    {
//        1. Вып.список "Тип расходов" (<-- Из выручки/Заказ-наряд/Товарный чек/...)
//        2. Наименование (<-- АЗС, Мойка, стоянка....)
//        3 Сумма, руб
//        4 Вып.список "Чек прикреплен" - Да/Нет
//        5 Наименование продавца (<--)         - это поле не понятно, создам просто текстовое
//        6 Количество
//        7 Баллы
//        8 Вып. Список "Расходы приняты" (Да/Нет)
//        9  Комментарий о принятии расходов
//        10 Вып.список "Способ оплаты" ((<-- Из выручки/Безналично/Перевод на карту/Оплата наличными)
//        11 Дата оплаты
//        12 Комментарий об оплате

        $this->createTable('transport_expenses', [
            'id' => $this->primaryKey(),
            'transport_waybill_id' => $this->integer()->comment('Путевой лист'),
            'expenses_type_id' => $this->integer()->comment('Тип расходов'),
            'expenses_seller_id' => $this->integer()->comment('Наименование'),
            'price' => $this->decimal(8, 2)->defaultValue(0)->comment('Сумма, руб'),
            'check_attached' => $this->boolean()->defaultValue(0)->comment('Чек прикреплен'),
            'expenses_seller_name' => $this->string(100)->comment('Наименование продавца'),
            'count' => $this->smallInteger()->comment('Количество'),
            'points' => $this->smallInteger()->comment('Баллы'),
            'expenses_is_taken' => $this->boolean()->defaultValue(0)->comment('Расходы приняты'),
            'expenses_is_taken_comment' => $this->string(255)->comment('Комментарий о принятии расходов'),
            'payment_method_id' => $this->integer()->comment('Способ оплаты'),
            'payment_date' => $this->integer()->comment('Дата оплаты'),
            'payment_comment' => $this->string(255)->comment('Комментарий к оплате'),

            'created_at' => $this->integer()->comment('Дата создания'),
            'creator_id' => $this->integer()->comment('Создатель'),
            'updated_at' => $this->integer()->comment('Дата изменения'),
            'updator_id' => $this->integer()->comment('Изменитель'),
        ]);
    }

    public function down()
    {
        $this->dropTable('transport_expenses');
    }
}
