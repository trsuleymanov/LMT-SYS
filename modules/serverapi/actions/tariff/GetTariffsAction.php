<?php

namespace app\modules\serverapi\actions\tariff;



use app\models\Tariff;

class GetTariffsAction extends \yii\rest\Action
{
    public $modelClass = '';

    /**
     * Возвращается список не синхронизированных (новых или измененных) яндекс-точек
     *
     * запрос с кодом доступа:
     * curl -i -H "Authorization: SecretKey lsaeu5jERTffd_7" "Accept:application/json" -H "Content-Type:application/json" -XPOST "http://tobus-yii2.ru/serverapi/tariff/get-tariffs"
     */
    public function run()
    {
        \Yii::$app->response->format = 'json';

        $tariffs = Tariff::find()->where(['sync_date' => NULL])->all();

        return [
            'tariffs' => $tariffs
        ];
    }
}
