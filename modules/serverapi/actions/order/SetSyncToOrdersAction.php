<?php

namespace app\modules\serverapi\actions\order;


use Yii;
use app\models\Order;
use app\models\DriverLoginForm;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;


class SetSyncToOrdersAction extends \yii\rest\Action
{
    public $modelClass = '';

    /**
     * Установка заказам даты синхронизации
     *
     * запрос: curl -i -H "Accept:application/json" -H "Content-Type:application/json" -XPOST http://tobus-yii2.ru/serverapi/order/set-sync-to-orders?ids=1,2,3,7
     * запрос с кодом доступа: curl -i -H "Authorization: SecretKey lsaeu5jERTffd_7" "Accept:application/json" -H "Content-Type:application/json" -XPOST http://tobus-yii2.ru/serverapi/order/set-sync-to-orders?ids=31019
     */
    public function run($ids)
    {
        $aIds = explode(',', $ids);
        $aClearIds = [];
        foreach($aIds as $id) {
            $id = intval($id);
            if($id > 0) {
                $aClearIds[] = $id;
            }
        }

        $orders = Order::find()->where(['id' => $aClearIds])->all();
        if(count($orders) == 0) {
            throw new ForbiddenHttpException('Заказы не найдены');
        }

        $sql = 'UPDATE `'.Order::tableName().'` SET sync_date = '.time().' WHERE id IN ('.implode(',', ArrayHelper::map($orders, 'id', 'id')).')';
        Yii::$app->db->createCommand($sql)->execute();


        return [
            'success' => true,
            'ids' => $ids
        ];
    }
}