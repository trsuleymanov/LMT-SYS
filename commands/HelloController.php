<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use app\models\Call;
use app\models\Order;
use app\models\SocketDemon;
use app\widgets\IncomingOrdersWidget;
use yii\console\Controller;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class HelloController extends Controller
{
    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     */
    public function actionIndex($message = 'hello world')
    {
        echo $message . "\n";

        IncomingOrdersWidget::updateIncomingOrders();
    }

    // команда: php yii hello/test
    public function actionTest() {

        //echo "test\n";

        //print_r(\Yii::$aliases);
        //\Yii::$app->response->format = 'json';

        //echo \yii\console\Request::cookieValidationKey;

//        $call = Call::find()->where(['id' => 1])->one();
//        //echo $call->getCallWindowThroughController(1);
//        $call->sendToBrawserCallWindow();



        //echo $call->getGetCallWindow();

        //return \Yii::$app()->runAction("sample/my-action");

        //return \Yii::$app->runAction("http://tobus-yii2.ru/site/test2");
        //return \Yii::$app->runAction("/call/get-call-window?id=1");

        //\Yii::$app->controllerNamespace = 'app\controllers'; // change current controller
        //\Yii::$app->runAction('call/get-call-window'); // run the Action
        //\Yii::$app->runAction('site/test'); // run the Action

        /*
        // проблема с обработкой формата чего-то
        $request_1 = new \yii\httpclient\Client();

        $response = $request_1->createRequest()
            ->setMethod('get')
            ->setUrl('http://tobus-yii2.ru/site/test')
            //->setData(['name' => 'John Doe', 'email' => 'johndoe@domain.com'])
            //->setHeaders(['Authorization' => 'SecretKey '.ClientServer::$secretKey])
            ->send();

        if ($response->statusCode == 200) {
            $data = $response->data;
            echo $data;
        }
        */

        //$url = 'http://tobus-yii2.ru/site/test';
        //$url = 'http://tobus-yii2.ru/call/get-call-window?id=1';
        /*

        //$headers[] = 'X-MPBX-API-AUTH-TOKEN: b3469183-f19a-46ce-9b44-19ace72e84c2';
        $headers[] = 'Content-Type: application/json; charset=UTF-8';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);

        $aResult = json_decode($result, true);

        return $aResult;
        */


        //$main_page_content = file_get_contents($url);
        //echo $main_page_content;


        $order_id = 216175;
        $order = Order::find()->where(['id' => $order_id])->one();

        if($order->trip_id > 0) {
            //$trip = $order->trip;
            //SocketDemon::updateMainPages($trip->id, $trip->date);
            //echo "отработала SocketDemon::updateMainPages для рейса ".$trip->id."<br />";

            // обновление страницы Состав рейса
            SocketDemon::sendOutBrowserMessage(
                '/trip/trip-orders',
                ['trip_id' => $order->trip_id],
                'updateTripOrdersPage()',
                ''
            );

            echo "отработала sendOutBrowserMessage для рейса ".$order->trip_id."<br />";
        }
    }
}
