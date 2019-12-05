<?php

use yii\db\Migration;

class m180918_155523_create_table_loyality extends Migration
{
    public function up()
    {
//        ID клиента,
//
//        Делиться по 3 поля:
//          отправлено заказов,
//          отправлено мест в заказах,
//          отменено заказов,
//          отменено мест в заказах,
//          мест в заказах по фикс.цене,
//          посылок,
//          поездок с б/н оплатой,
//          получено призовых поездок,
//          штрафов,
//
//        i1 - усредненный интервал между временем создания заказа и временем посадки в т/с в сек,
//        i2 - усредненный  интервал между временем создания заказа и временем отмены заказа в сек,
//        i3 - усредненный интервал между временем отмены заказа и временем первой точки рейса в сек,
//        i4 - усредненный интервал между временем создания заказа и первым нажатием кнопки записать,
//        i5 - усредненный интервал между временем создания заказа и временем отмены,
//
//        прошлое или настоящее или суммарное,
//        индикатор лояльности - 0.00



        $this->createTable('loyality', [
            'id' => $this->primaryKey(),
            //'code' => $this->string(17)->comment('Код устройства')->unique(),
            'client_id' => $this->integer()->comment('Клиент'),

            'past_sent_orders' => $this->integer()->comment('прошлое: отправлено заказов'),
            'past_sent_orders_places' => $this->integer()->comment('прошлое: отправлено мест в заказах'),
            'past_canceled_orders' => $this->integer()->comment('прошлое: отменено заказов'),
            'past_canceled_orders_places' => $this->integer()->comment('прошлое: отменено мест в заказах'),
            'past_fixed_price_orders_places' => $this->integer()->comment('прошлое: мест в заказах по фикс.цене'),
            'past_is_not_places' => $this->integer()->comment('прошлое: посылок'),
            'past_informer_beznal_orders_places' => $this->integer()->comment('прошлое: поездок с б/н оплатой'),
            'past_prize_trip_count' => $this->integer()->comment('прошлое: получено призовых поездок'),
            'past_penalty' => $this->integer()->comment('прошлое: штрафов'),
            'past_i1' => $this->double()->comment('прошлое: усредненный интервал между временем создания заказа и временем посадки в т/с в сек'),
            'past_i2' => $this->double()->comment('прошлое: усредненный  интервал между временем создания заказа и временем отмены заказа в сек'),
            'past_i3' => $this->double()->comment('прошлое: усредненный интервал между временем отмены заказа и временем первой точки рейса в сек'),
            'past_i4' => $this->double()->comment('прошлое: усредненный интервал между временем создания заказа и первым нажатием кнопки записать'),
            'past_i5' => $this->double()->comment('прошлое: усредненный интервал между временем создания заказа и временем отмены'),

            'present_sent_orders' => $this->integer()->comment('настоящее: отправлено заказов'),
            'present_sent_orders_places' => $this->integer()->comment('настоящее: отправлено мест в заказах'),
            'present_canceled_orders' => $this->integer()->comment('настоящее: отменено заказов'),
            'present_canceled_orders_places' => $this->integer()->comment('настоящее: отменено мест в заказах'),
            'present_fixed_price_orders_places' => $this->integer()->comment('настоящее: мест в заказах по фикс.цене'),
            'present_is_not_places' => $this->integer()->comment('настоящее: посылок'),
            'present_informer_beznal_orders_places' => $this->integer()->comment('настоящее: поездок с б/н оплатой'),
            'present_prize_trip_count' => $this->integer()->comment('настоящее: получено призовых поездок'),
            'present_penalty' => $this->integer()->comment('настоящее: штрафов'),
            'present_i1' => $this->double()->comment('настоящее: усредненный интервал между временем создания заказа и временем посадки в т/с в сек'),
            'present_i2' => $this->double()->comment('настоящее: усредненный  интервал между временем создания заказа и временем отмены заказа в сек'),
            'present_i3' => $this->double()->comment('настоящее: усредненный интервал между временем отмены заказа и временем первой точки рейса в сек'),
            'present_i4' => $this->double()->comment('настоящее: усредненный интервал между временем создания заказа и первым нажатием кнопки записать'),
            'present_i5' => $this->double()->comment('настоящее: усредненный интервал между временем создания заказа и временем отмены'),


            'total_sent_orders' => $this->integer()->comment('суммарное: отправлено заказов'),
            'total_sent_orders_places' => $this->integer()->comment('суммарное: отправлено мест в заказах'),
            'total_canceled_orders' => $this->integer()->comment('суммарное: отменено заказов'),
            'total_canceled_orders_places' => $this->integer()->comment('суммарное: отменено мест в заказах'),
            'total_fixed_price_orders_places' => $this->integer()->comment('суммарное: мест в заказах по фикс.цене'),
            'total_is_not_places' => $this->integer()->comment('суммарное: посылок'),
            'total_informer_beznal_orders_places' => $this->integer()->comment('суммарное: поездок с б/н оплатой'),
            'total_prize_trip_count' => $this->integer()->comment('суммарное: получено призовых поездок'),
            'total_penalty' => $this->integer()->comment('суммарное: штрафов'),
            'total_i1' => $this->double()->comment('суммарное: усредненный интервал между временем создания заказа и временем посадки в т/с в сек'),
            'total_i2' => $this->double()->comment('суммарное: усредненный  интервал между временем создания заказа и временем отмены заказа в сек'),
            'total_i3' => $this->double()->comment('суммарное: усредненный интервал между временем отмены заказа и временем первой точки рейса в сек'),
            'total_i4' => $this->double()->comment('суммарное: усредненный интервал между временем создания заказа и первым нажатием кнопки записать'),
            'total_i5' => $this->double()->comment('суммарное: усредненный интервал между временем создания заказа и временем отмены'),



            'loyalty_indicator' => $this->double()->defaultValue(0.00)->comment('индикатор лояльности')
        ]);
    }

    public function down()
    {
        $this->dropTable('loyality');
    }
}
