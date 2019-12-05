<?php

use yii\db\Migration;
use app\models\Order;

class m170812_183232_add_field_time_vpz_to_order extends Migration
{
    public function up()
    {
        $this->addColumn('order', 'time_vpz', $this->integer()->after('time_confirm')->comment('ВПЗ - Время первичной записи - редактируемое время которое определяет приоритет внимания к заказу'));

        $sql = 'UPDATE `order` SET `time_vpz`=`created_at`';
        Yii::$app->db->createCommand($sql)->execute();
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('order', 'time_vpz');
    }
}
