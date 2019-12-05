<?php

use yii\db\Migration;

/**
 * Class m181017_173727_change_fields_in_passenger
 */
class m181017_173727_change_fields_in_passenger extends Migration
{
    public function up()
    {
        $this->truncateTable('order_passenger');
        $this->truncateTable('passenger');

        $this->dropColumn('passenger', 'child');

        $this->dropColumn('passenger', 'series');
        $this->dropColumn('passenger', 'number');
        $this->addColumn('passenger', 'series_number', $this->string(20)->comment('Серия и номер документа')->after('client_id'));

        $this->dropColumn('passenger', 'surname');
        $this->dropColumn('passenger', 'name');
        $this->dropColumn('passenger', 'patronymic');
        $this->addColumn('passenger', 'fio', $this->string(100)->comment('ФИО')->after('series_number'));

        $this->addColumn('passenger', 'document_type', "ENUM('passport', 'birth_certificate', 'international_passport', 'foreign_passport')");
    }

    public function down()
    {
        $this->addColumn('passenger', 'child', $this->boolean()->defaultValue(0)->comment('0 - Взрослый / 1 - Ребенок (без паспорта)')->after('client_id'));

        $this->dropColumn('passenger', 'series_number');
        $this->addColumn('passenger', 'series', $this->string(4)->comment('Серия паспорта')->after('client_id'));
        $this->addColumn('passenger', 'number', $this->string(6)->comment('Номер паспорта')->after('series'));

        $this->dropColumn('passenger', 'fio');
        $this->addColumn('passenger', 'surname', $this->string(30)->comment('Фамилия')->after('number'));
        $this->addColumn('passenger', 'name', $this->string(30)->comment('Имя')->after('surname'));
        $this->addColumn('passenger', 'patronymic', $this->string(30)->comment('Отчество')->after('name'));

        $this->dropColumn('passenger', 'document_type');
    }
}
