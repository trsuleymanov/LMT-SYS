<?php

namespace app\modules\serverapi\actions\order;


use Yii;
use app\models\Order;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;


class SetPushAcceptAction extends \yii\rest\Action
{
    public $modelClass = '';

    /**
     * Установка заказу времени нажатия "Согласен" в пуше
     *
     * запрос: curl -i -H "Accept:application/json" -H "Content-Type:application/json" -XPOST http://tobus-yii2.ru/serverapi/order/set-push-accept?id=123&accept_time=12345678
     * запрос с кодом доступа: curl -i -H "Authorization: SecretKey lsaeu5jERTffd_7" "Accept:application/json" -H "Content-Type:application/json" -XPOST http://tobus-yii2.ru/serverapi/order/set-push-accept?id=123&accept_time=12345678
     */
    public function run($clientext_id, $accept_time)
    {
        $order = Order::find()->where(['external_id' => $clientext_id])->one();
        if($order == null) {
            throw new ForbiddenHttpException('Заказ не найден');
        }

        $order->setField('push_confirm_time', $accept_time);

        return;
    }
}