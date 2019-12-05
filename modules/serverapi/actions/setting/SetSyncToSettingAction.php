<?php

namespace app\modules\serverapi\actions\setting;

use app\models\Setting;

class SetSyncToSettingAction extends \yii\rest\Action
{
    public $modelClass = '';

    /**
     * Установка даты синхронизации для настроек
     *
     * запрос: curl -i -H "Accept:application/json" -H "Content-Type:application/json" -XPOST http://tobus-yii2.ru/serverapi/setting/set-sync-to-setting
     * запрос с кодом доступа: curl -i -H "Authorization: SecretKey lsaeu5jERTffd_7" "Accept:application/json" -H "Content-Type:application/json" -XPOST http://tobus-yii2.ru/serverapi/setting/set-sync-to-setting
     */
    public function run()
    {
        $setting = Setting::find()->where(['id' => 1])->andWhere(['sync_date' => NULL])->one();
        $setting->setField('sync_date', time());

        return [
            'success' => true,
        ];

    }
}
