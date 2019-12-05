<?php

namespace app\commands;

use app\models\Client;
use app\models\LiteboxOperation;
use app\models\Order;
use app\models\Trip;
use yii\console\Controller;
use Yii;
use yii\helpers\ArrayHelper;


class LiteboxController extends Controller
{
    /*
     * Проверка и обновление данных по отправленным запросам в litebox-сервер
     * команда: php yii litebox/check-operations
     *
     */
    public function actionCheckOperations()
    {
        $i = 0;

        // пробуем по 10 штук брать
        $sell_operations = LiteboxOperation::find()->where(['sell_status' => 'wait'])->all();
        if(count($sell_operations) > 0) {
            foreach ($sell_operations as $operation) {
                $operation->checkSellStatusAndUpdate(true);
                $i++;
            }
        }

        // пробуем по 10 штук брать
        $refund_operations = LiteboxOperation::find()->where(['sell_refund_status' => 'wait'])->all();
        if(count($refund_operations) > 0) {
            foreach ($refund_operations as $operation) {
                $operation->checkSellRefundStatusAndUpdate(true);
                $i++;
            }
        }

        echo "готово. Обработано $i записей \n";
    }
}
