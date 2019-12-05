<?php

namespace app\modules\serverapi\actions\client;

use app\models\Client;
use Yii;
use app\models\DriverLoginForm;



class GetNotSyncClientsAction extends \yii\rest\Action
{
    public $modelClass = '';

    /**
     * Возвращается список клиентов не синхронизированных
     *
     * запрос: curl -i -H "Accept:application/json" -H "Content-Type:application/json" -XPOST http://tobus-yii2.ru/serverapi/client/get-not-sync-clients
     * запрос с кодом доступа: curl -i -H "Authorization: SecretKey lsaeu5jERTffd_7" "Accept:application/json" -H "Content-Type:application/json" -XPOST http://tobus-yii2.ru/serverapi/client/get-not-sync-clients
     */
    public function run()
    {
        // нужны поля клиента: id, name, mobile_phone, логин - нет такого, пароль - нет такого
        \Yii::$app->response->format = 'json';

        $clients = Client::find()
            ->where(['sync_date' => NULL])
            ->limit(50)
            ->all();

        $aClients = [];
        if(count($clients) > 0) {
            foreach($clients as $client) {
                $aClients[] = [
                    'id' => $client->id,
                    'email' => $client->email,
                    'name' => $client->name,
                    'mobile_phone' => $client->mobile_phone,
                    'cashback' => $client->cashback,
                    'current_year_sended_places' => $client->current_year_sended_places,
                    'current_year_sended_prize_places' => $client->current_year_sended_prize_places,
                    'current_year_penalty' => $client->current_year_penalty,
                ];
            }
        }

        return $aClients;
    }
}
