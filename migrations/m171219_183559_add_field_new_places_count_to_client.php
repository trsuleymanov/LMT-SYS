<?php

use yii\db\Migration;

class m171219_183559_add_field_new_places_count_to_client extends Migration
{
    public function up()
    {
        $this->addColumn('client', 'sended_informer_beznal_orders_places_count', $this->smallInteger()->defaultValue(0)->after('sended_fixprice_orders_places_count')->comment('Количество отправленных мест в заказах где выбрана информаторская с безналичной оплатой'));
    }

    public function down()
    {
        $this->dropColumn('client', 'sended_informer_beznal_orders_places_count');
    }
}
