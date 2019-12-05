<?php

use yii\db\Migration;

/**
 * Class m181129_214857_add_fields_to_client
 */
class m181129_214857_add_fields_to_client extends Migration
{
    public function up()
    {
//        Для настоящего:
//        - Число отправленных мест в текущем году - current_year_sended_places
//        - Число отправленных заказов в текущем году - current_year_sended_orders
//        - Число отмененных мест в текущем году - current_year_canceled_places
//        - Число отмененных заказов в текущем году - current_year_canceled_orders
//        - Надежность по местам в текущем году - current_year_places_reliability
//        - Надежность по заказам в текущем году - current_year_orders_reliability
//
//        - Число отправленных призовых поездок в текущем году - current_year_sended_prize_places
//        - Число штрафов в текущем году,          - current_year_penalty
//        - Число мест по фикс.цене отправленных в текущем году,    - current_year_sended_fixprice_places
//        - Число заказов по фикс.цене в текущем году,   - current_year_sended_fixprice_orders
//        - Число мест с безналичной оплатой в текущем году,  - current_year_sended_informer_beznal_places
//        - Число заказов с безналичной оплатой в текущем году, - current_year_sended_informer_beznal_orders
//        - Число посылок в текущем году.   - current_year_sended_isnotplaces_orders
//
//        Для прошлого:
//        - Число отправленных мест всего по прошлым периодам - past_years_sended_places
//        - Число отмененных мест всего по прошлым периодам - past_years_sended_orders
//        - Число отправленных заказов по прошлым периодам - past_years_canceled_places
//        - Число отмененных заказов по прошлым периодам - past_years_canceled_orders
//
//        - Количество отправленных призовых поездок по прошлым периодам, - past_years_sended_prize_places
//        - Количество штрафов по прошлым периодам,          - past_years_penalty
//        - Количество мест по фикс.цене по прошлым периодам,    - past_years_sended_fixprice_places
//        - Количество заказов по фикс.цене по прошлым периодам,   - past_years_sended_fixprice_orders
//        - Количество мест с безналичной оплатой по прошлым периодам,  - past_years_sended_informer_beznal_places
//        - Количество заказов с безналичной оплатой по прошлым периодам, - past_years_sended_informer_beznal_orders
//        - Количество посылок по прошлым периодам.   - past_years_sended_isnotplaces_orders


        // Настоящее
        $this->addColumn('client', 'current_year_sended_places', $this->smallInteger(2)->defaultValue(0)->comment('Число отправленных мест')->after('penalty'));
        $this->addColumn('client', 'current_year_sended_orders', $this->smallInteger(2)->defaultValue(0)->comment('Число отправленных заказов')->after('current_year_sended_places'));
        $this->addColumn('client', 'current_year_canceled_places', $this->smallInteger(2)->defaultValue(0)->comment('Число отмененных мест')->after('current_year_sended_orders'));
        $this->addColumn('client', 'current_year_canceled_orders', $this->smallInteger(2)->defaultValue(0)->comment('Число отмененных заказов')->after('current_year_canceled_places'));
        //$this->addColumn('client', 'current_year_places_reliability', $this->smallInteger(2)->defaultValue(0)->comment('Надежность по местам в текущем году')->after('current_year_canceled_orders'));
        $this->addColumn('client', 'current_year_places_reliability', $this->decimal(8, 2)->defaultValue(0)->comment('Надежность по местам в текущем году')->after('current_year_canceled_orders'));
        //$this->addColumn('client', 'current_year_orders_reliability', $this->smallInteger(2)->defaultValue(0)->comment('Надежность по заказам в текущем году')->after('current_year_places_reliability'));
        $this->addColumn('client', 'current_year_orders_reliability', $this->decimal(8, 2)->defaultValue(0)->comment('Надежность по заказам в текущем году')->after('current_year_places_reliability'));

        $this->addColumn('client', 'current_year_sended_prize_places', $this->smallInteger(2)->defaultValue(0)->comment('Число отправленных призовых поездок в текущем году')->after('current_year_orders_reliability'));
        $this->addColumn('client', 'current_year_penalty', $this->smallInteger(2)->defaultValue(0)->comment('Число штрафов в текущем году')->after('current_year_sended_prize_places'));
        $this->addColumn('client', 'current_year_sended_fixprice_places', $this->smallInteger(2)->defaultValue(0)->comment('Число мест по фикс.цене отправленных в текущем году')->after('current_year_penalty'));
        $this->addColumn('client', 'current_year_sended_fixprice_orders', $this->smallInteger(2)->defaultValue(0)->comment('Число заказов по фикс.цене в текущем году')->after('current_year_sended_fixprice_places'));
        $this->addColumn('client', 'current_year_sended_informer_beznal_places', $this->smallInteger(2)->defaultValue(0)->comment('Число мест с безналичной оплатой в текущем году')->after('current_year_sended_fixprice_orders'));
        $this->addColumn('client', 'current_year_sended_informer_beznal_orders', $this->smallInteger(2)->defaultValue(0)->comment('Число заказов с безналичной оплатой в текущем году')->after('current_year_sended_informer_beznal_places'));
        $this->addColumn('client', 'current_year_sended_isnotplaces_orders', $this->smallInteger(2)->defaultValue(0)->comment('Число посылок в текущем году')->after('current_year_sended_informer_beznal_orders'));

        // Прошлое
        $this->addColumn('client', 'past_years_sended_places', $this->smallInteger(2)->defaultValue(0)->comment('Число отправленных мест всего по прошлым периодам')->after('current_year_sended_isnotplaces_orders'));
        $this->addColumn('client', 'past_years_sended_orders', $this->smallInteger(2)->defaultValue(0)->comment('Число отмененных мест всего по прошлым периодам')->after('past_years_sended_places'));
        $this->addColumn('client', 'past_years_canceled_places', $this->smallInteger(2)->defaultValue(0)->comment('Число отправленных заказов по прошлым периодам')->after('past_years_sended_orders'));
        $this->addColumn('client', 'past_years_canceled_orders', $this->smallInteger(2)->defaultValue(0)->comment('Число отмененных заказов по прошлым периодам')->after('past_years_canceled_places'));
        $this->addColumn('client', 'past_years_sended_prize_places', $this->smallInteger(2)->defaultValue(0)->comment('Количество отправленных призовых поездок по прошлым периодам')->after('past_years_canceled_orders'));
        $this->addColumn('client', 'past_years_penalty', $this->smallInteger(2)->defaultValue(0)->comment('Количество штрафов по прошлым периодам')->after('past_years_sended_prize_places'));
        $this->addColumn('client', 'past_years_sended_fixprice_places', $this->smallInteger(2)->defaultValue(0)->comment('Количество мест по фикс.цене по прошлым периодам')->after('past_years_penalty'));

        $this->addColumn('client', 'past_years_sended_fixprice_orders', $this->smallInteger(2)->defaultValue(0)->comment('Количество заказов по фикс.цене по прошлым периодам')->after('past_years_sended_fixprice_places'));
        $this->addColumn('client', 'past_years_sended_informer_beznal_places', $this->smallInteger(2)->defaultValue(0)->comment('Количество мест с безналичной оплатой по прошлым периодам')->after('past_years_sended_fixprice_orders'));
        $this->addColumn('client', 'past_years_sended_informer_beznal_orders', $this->smallInteger(2)->defaultValue(0)->comment('Количество заказов с безналичной оплатой по прошлым периодам')->after('past_years_sended_informer_beznal_places'));
        $this->addColumn('client', 'past_years_sended_isnotplaces_orders', $this->smallInteger(2)->defaultValue(0)->comment('Количество посылок по прошлым периодам')->after('past_years_sended_informer_beznal_orders'));
    }

    public function down()
    {
        // Настоящее
        $this->dropColumn('client', 'current_year_sended_places');
        $this->dropColumn('client', 'current_year_sended_orders');
        $this->dropColumn('client', 'current_year_canceled_places');
        $this->dropColumn('client', 'current_year_canceled_orders');
        $this->dropColumn('client', 'current_year_places_reliability');
        $this->dropColumn('client', 'current_year_orders_reliability');

        $this->dropColumn('client', 'current_year_sended_prize_places');
        $this->dropColumn('client', 'current_year_penalty');
        $this->dropColumn('client', 'current_year_sended_fixprice_places');
        $this->dropColumn('client', 'current_year_sended_fixprice_orders');
        $this->dropColumn('client', 'current_year_sended_informer_beznal_places');
        $this->dropColumn('client', 'current_year_sended_informer_beznal_orders');
        $this->dropColumn('client', 'current_year_sended_isnotplaces_orders');

        // Прошлое
        $this->dropColumn('client', 'past_years_sended_places');
        $this->dropColumn('client', 'past_years_sended_orders');
        $this->dropColumn('client', 'past_years_canceled_places');
        $this->dropColumn('client', 'past_years_canceled_orders');
        $this->dropColumn('client', 'past_years_sended_prize_places');
        $this->dropColumn('client', 'past_years_penalty');
        $this->dropColumn('client', 'past_years_sended_fixprice_places');

        $this->dropColumn('client', 'past_years_sended_fixprice_orders');
        $this->dropColumn('client', 'past_years_sended_informer_beznal_places');
        $this->dropColumn('client', 'past_years_sended_informer_beznal_orders');
        $this->dropColumn('client', 'past_years_sended_isnotplaces_orders');
    }
}
