<?php

use app\models\Tariff;
use yii\db\Migration;

/**
 * Class m190829_205739_change_tariff
 */
class m190829_205739_change_tariff extends Migration
{
    public function up()
    {
        $this->renameColumn('tariff', 'common_price', 'unprepayment_common_price');
        $this->renameColumn('tariff', 'student_price', 'unprepayment_student_price');
        $this->renameColumn('tariff', 'baby_price', 'unprepayment_baby_price');
        $this->renameColumn('tariff', 'aero_price', 'unprepayment_aero_price');
        $this->renameColumn('tariff', 'parcel_price', 'unprepayment_parcel_price');
        $this->renameColumn('tariff', 'loyal_price', 'unprepayment_loyal_price');
        $this->addColumn('tariff', 'unprepayment_reservation_cost', $this->decimal(8,2)->defaultValue(0)->comment('Стоимость бронирования без предоплаты')->after('unprepayment_common_price'));


        $this->addColumn('tariff', 'prepayment_common_price', $this->decimal(8,2)->defaultValue(0)->comment('Общая стоимость проезда с предоплатой'));
        $this->addColumn('tariff', 'prepayment_student_price', $this->decimal(8,2)->defaultValue(0)->comment('Стоимость студенческого проезда с предоплатой'));
        $this->addColumn('tariff', 'prepayment_baby_price', $this->decimal(8,2)->defaultValue(0)->comment('Стоимость детского проезда с предоплатой'));
        $this->addColumn('tariff', 'prepayment_aero_price', $this->decimal(8,2)->defaultValue(0)->comment('Стоимость поездки в/из аэропорта с предоплатой'));
        $this->addColumn('tariff', 'prepayment_parcel_price', $this->decimal(8,2)->defaultValue(0)->comment('Стоимость провоза посылки (без места) с предоплатой'));
        $this->addColumn('tariff', 'prepayment_loyal_price', $this->decimal(8,2)->defaultValue(0)->comment('Стоимость призовой поездки с предоплатой'));
        $this->addColumn('tariff', 'prepayment_reservation_cost', $this->decimal(8,2)->defaultValue(0)->comment('Стоимость бронирования с предоплатой'));

        $this->addColumn('tariff', 'superprepayment_common_price', $this->decimal(8,2)->defaultValue(0)->comment('Общая стоимость проезда с супер-предоплатой'));
        $this->addColumn('tariff', 'superprepayment_student_price', $this->decimal(8,2)->defaultValue(0)->comment('Стоимость студенческого проезда с супер-предоплатой'));
        $this->addColumn('tariff', 'superprepayment_baby_price', $this->decimal(8,2)->defaultValue(0)->comment('Стоимость детского проезда с супер-предоплатой'));
        $this->addColumn('tariff', 'superprepayment_aero_price', $this->decimal(8,2)->defaultValue(0)->comment('Стоимость поездки в/из аэропорта с супер-предоплатой'));
        $this->addColumn('tariff', 'superprepayment_parcel_price', $this->decimal(8,2)->defaultValue(0)->comment('Стоимость провоза посылки (без места) с супер-предоплатой'));
        $this->addColumn('tariff', 'superprepayment_loyal_price', $this->decimal(8,2)->defaultValue(0)->comment('Стоимость призовой поездки с супер-предоплатой'));
        $this->addColumn('tariff', 'superprepayment_reservation_cost', $this->decimal(8,2)->defaultValue(0)->comment('Стоимость бронирования с супер-предоплатой'));

    }

    public function down()
    {
        $this->renameColumn('tariff', 'unprepayment_common_price', 'common_price');
        $this->renameColumn('tariff', 'unprepayment_student_price', 'student_price');
        $this->renameColumn('tariff', 'unprepayment_baby_price', 'baby_price');
        $this->renameColumn('tariff', 'unprepayment_aero_price', 'aero_price');
        $this->renameColumn('tariff', 'unprepayment_parcel_price', 'parcel_price');
        $this->renameColumn('tariff', 'unprepayment_loyal_price', 'loyal_price');
        $this->dropColumn('tariff', 'unprepayment_reservation_cost');


        $this->dropColumn('tariff', 'prepayment_common_price');
        $this->dropColumn('tariff', 'prepayment_student_price');
        $this->dropColumn('tariff', 'prepayment_baby_price');
        $this->dropColumn('tariff', 'prepayment_aero_price');
        $this->dropColumn('tariff', 'prepayment_parcel_price');
        $this->dropColumn('tariff', 'prepayment_loyal_price');
        $this->dropColumn('tariff', 'prepayment_reservation_cost');

        $this->dropColumn('tariff', 'superprepayment_common_price');
        $this->dropColumn('tariff', 'superprepayment_student_price');
        $this->dropColumn('tariff', 'superprepayment_baby_price');
        $this->dropColumn('tariff', 'superprepayment_aero_price');
        $this->dropColumn('tariff', 'superprepayment_parcel_price');
        $this->dropColumn('tariff', 'superprepayment_loyal_price');
        $this->dropColumn('tariff', 'superprepayment_reservation_cost');

    }
}
