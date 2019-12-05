<?php

namespace app\modules\serverapi\actions\client;

use app\models\Client;
use Yii;
use app\models\DriverLoginForm;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;


class SetSyncToClientsAction extends \yii\rest\Action
{
    public $modelClass = '';

    /**
     * Установка клиентам даты синхронизации
     *
     * запрос: curl -i -H "Accept:application/json" -H "Content-Type:application/json" -XPOST http://tobus-yii2.ru/serverapi/client/set-sync-to-clients?ids=1,2,3,7
     * запрос с кодом доступа: curl -i -H "Authorization: SecretKey lsaeu5jERTffd_7" "Accept:application/json" -H "Content-Type:application/json" -XPOST http://tobus-yii2.ru/serverapi/client/set-sync-to-clients?ids=1,2,3,7
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

        $clients = Client::find()->where(['id' => $aClearIds])->all();
        if(count($clients) == 0) {
            throw new ForbiddenHttpException('Клиенты не найдены');
        }

        $sql = 'UPDATE `'.Client::tableName().'` SET sync_date = '.time().' WHERE id IN ('.implode(',', ArrayHelper::map($clients, 'id', 'id')).')';
        Yii::$app->db->createCommand($sql)->execute();


        return [
            'success' => true,
            'ids' => $ids
        ];

    }
}
