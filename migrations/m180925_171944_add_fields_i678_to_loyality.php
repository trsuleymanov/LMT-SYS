<?php

use yii\db\Migration;

/**
 * Class m180925_171944_add_fields_i678_to_loyality
 */
class m180925_171944_add_fields_i678_to_loyality extends Migration
{
    public function up()
    {
        $this->addColumn('loyality', 'past_i6', $this->double()->comment('прошлое: частота поездок (среднее время между началами кругов)')->after('past_i5'));
        $this->addColumn('loyality', 'past_i7', $this->double()->comment('прошлое: время возврата (среднее время между началом круга и предполагаемым завершением либо реальным завершением круга)')->after('past_i6'));
        $this->addColumn('loyality', 'past_i8', $this->double()->comment('прошлое: отношение реально завершенных к общему количеству кругов')->after('past_i7'));

        $this->addColumn('loyality', 'present_i6', $this->double()->comment('настоящее: частота поездок (среднее время между началами кругов)')->after('present_i5'));
        $this->addColumn('loyality', 'present_i7', $this->double()->comment('настоящее: время возврата (среднее время между началом круга и предполагаемым завершением либо реальным завершением круга)')->after('present_i6'));
        $this->addColumn('loyality', 'present_i8', $this->double()->comment('настоящее: отношение реально завершенных к общему количеству кругов')->after('present_i7'));

        $this->addColumn('loyality', 'total_i6', $this->double()->comment('суммарное: частота поездок (среднее время между началами кругов)')->after('total_i5'));
        $this->addColumn('loyality', 'total_i7', $this->double()->comment('суммарное: время возврата (среднее время между началом круга и предполагаемым завершением либо реальным завершением круга)')->after('total_i6'));
        $this->addColumn('loyality', 'total_i8', $this->double()->comment('суммарное: отношение реально завершенных к общему количеству кругов')->after('total_i7'));
    }

    public function down()
    {
        $this->dropColumn('loyality', 'past_i6');
        $this->dropColumn('loyality', 'past_i7');
        $this->dropColumn('loyality', 'past_i8');

        $this->dropColumn('loyality', 'present_i6');
        $this->dropColumn('loyality', 'present_i7');
        $this->dropColumn('loyality', 'present_i8');

        $this->dropColumn('loyality', 'total_i6');
        $this->dropColumn('loyality', 'total_i7');
        $this->dropColumn('loyality', 'total_i8');
    }
}
