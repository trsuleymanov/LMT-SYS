<?php

namespace app\modules\serverapi\actions\setting;

use app\models\Client;
use app\models\Setting;


class GetNotSyncSettingAction extends \yii\rest\Action
{
    public $modelClass = '';

    /**
     * Возвращаются настройки, если они не синхронизированны
     *
     * запрос с кодом доступа: curl -i -H "Authorization: SecretKey lsaeu5jERTffd_7" "Accept:application/json" -H "Content-Type:application/json" -XPOST http://tobus-yii2.ru/serverapi/setting/get-not-sync-setting
     */
    public function run()
    {
        \Yii::$app->response->format = 'json';

        $setting = Setting::find()->where(['id' => 1])->andWhere(['sync_date' => NULL])->one();

        $aSetting = [];
        if($setting != null) {
            $aSetting = [
                'count_hours_before_trip_to_cancel_order' => $setting->count_hours_before_trip_to_cancel_order,
            ];

        }

        return $aSetting;
    }
}
