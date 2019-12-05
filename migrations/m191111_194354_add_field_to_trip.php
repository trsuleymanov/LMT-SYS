<?php

use yii\db\Migration;

/**
 * Class m191111_194354_add_field_to_trip
 */
class m191111_194354_add_field_to_trip extends Migration
{
    public function up()
    {
        $this->addColumn('trip', 'date_issued_by_operator', $this->integer()->comment('Дата выпуска рейса оператором')->after('start_sending_user_id'));
        $this->addColumn('trip', 'issued_by_operator_id', $this->integer()->comment('Оператор, отправка т/с которого выпустила рейс')->after('date_issued_by_operator'));
        $this->addColumn('trip', 'has_free_places', $this->boolean()->defaultValue(false)->comment('Есть свободные места в одном из т/с')->after('issued_by_operator_id'));
    }

    public function down()
    {
        $this->dropColumn('trip', 'has_free_places');
        $this->dropColumn('trip', 'date_issued_by_operator');
        $this->dropColumn('trip', 'issued_by_operator_id');
    }
}
