<?php
namespace app\modules\serverapi\actions\tariff;

use app\models\Tariff;
use app\models\YandexPoint;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;


class SetSyncToTariffsAction extends \yii\rest\Action
{
    public $modelClass = '';

    /**
     * Установка тарифам даты синхронизации
     *
     * запрос с кодом доступа: curl -i -H "Authorization: SecretKey lsaeu5jERTffd_7" "Accept:application/json" -H "Content-Type:application/json" -XPOST http://tobus-yii2.ru/serverapi/tariff/set-sync-to-tariffs?ids=1,2,3,7
     */
    public function run($ids)
    {
        $aIds = explode(',', $ids);

        $tariffs = Tariff::find()->where(['id' => $aIds])->all();
        if(count($tariffs) == 0) {
            throw new ForbiddenHttpException('Тарифы не найдены');
        }

        $sql = 'UPDATE `'.Tariff::tableName().'` SET sync_date = '.time().' WHERE id IN ('.implode(',', ArrayHelper::map($tariffs, 'id', 'id')).')';
        Yii::$app->db->createCommand($sql)->execute();


        return [
            'success' => true,
            'ids' => $ids
        ];
    }
}