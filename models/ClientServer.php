<?php

namespace app\models;

// Вспомогательная модель для отсылки данных на клиентский сервер и т.п.
use Yii;
use yii\base\ErrorException;

class ClientServer extends \yii\base\Model
{
    public static $secretKey = 'zLitjs_lUIthw908y'; // ключ доступа к serverapi клиенского сервера.


    // ClientServer::sendPush('+7-966-112-8006', 'Диспетчерская Альмобус', 'Будьте собраны и готовы в 04:00, подъедет машина 077 Синий Ютонг, без звонка не выходите')
    public static function sendPush($client_phone, $push_title, $push_text, $send_event = 'with_sync_clientext', $client_ext_id = '') {

//        curl -i -H "Authorization: SecretKey zLitjs_lUIthw908y"
//        -XPOST -H "Content-Type:application/json" -d "{\"phone\": \"+7-966-112-8006\",
//        \"title\":\"Диспетчерская Альмобус\",\"text\":\"Будьте собраны и готовы в 04:00,
//        подъедет машина 077 Синий Ютонг, без звонка не выходите\",
//        \"clientext_id\":\"2\"}" http://developer.almobus.ru/serverapi/user/send-push

        $request_1 = new \yii\httpclient\Client();

//        $data = [
//            'phone' => '+7-966-112-8006',
//            'title' => 'Диспетчерская Альмобус',
//            'text' => 'Будьте собраны и готовы в 04:00, подъедет машина 077 Синий Ютонг, без звонка не выходите'
//        ];

        $data = [
            'phone' => $client_phone,
            'title' => $push_title,
            'text' => $push_text,
            'send_event' => $send_event,
            'client_ext_id' =>  $client_ext_id
        ];

        $response = $request_1->createRequest()
            ->setMethod('post')
            ->setUrl(Yii::$app->params['clientServerUrl'].'push/send')
            ->setData($data)
            ->setHeaders(['Authorization' => 'SecretKey '.ClientServer::$secretKey])
            ->send();

        if ($response->statusCode == 200) {
            return true;
        }else {
            //echo "При отправке пуша от клиентского сервера пришел ответ ".$response->statusCode."\n";
            //exit;
            throw new ErrorException('При отправке пуша от клиентского сервера пришел ответ '.$response->statusCode);
        }
    }
}