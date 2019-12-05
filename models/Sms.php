<?php
namespace app\models;

use yii\web\ForbiddenHttpException;

class Sms extends \yii\base\Model
{
    public static $api_key = 'F5FD212B-8625-8B65-62CE-D09E91BDCC22';

    public function actionSend($to, $text) {

        $request_1 = new \yii\httpclient\Client(); // это клиент запроса, а не Клиент-человек

        // https://sms.ru/sms/send?api_id=F5FD212B-8625-8B65-62CE-D09E91BDCC22&to=79661128005&msg=тест&json=1
        $response = $request_1->createRequest()
            ->setMethod('get')
            ->setUrl('https://sms.ru/sms/send?api_id='.self::$api_key.'&to='.$to.'&msg='.urlencode($text).'&json=1')
            ->send();
        if ($response->statusCode == 200) {
            return true;
        }else {
            throw new ForbiddenHttpException('Пришел ответ на запрос со статусом '.$response->statusCode);
        }
    }
}