<?php
namespace app\modules\serverapi\actions\yandexpoint;

use app\models\YandexPoint;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;


class SetSyncToYandexPointsAction extends \yii\rest\Action
{
    public $modelClass = '';

    /**
     * Установка яндекс-точкам даты синхронизации
     *
     * запрос: curl -i -H "Accept:application/json" -H "Content-Type:application/json" -XPOST http://tobus-yii2.ru/serverapi/yandex-point/set-sync-to-yandex-points?ids=1,2,3,7
     * запрос с кодом доступа: curl -i -H "Authorization: SecretKey lsaeu5jERTffd_7" "Accept:application/json" -H "Content-Type:application/json" -XPOST http://tobus-yii2.ru/serverapi/yandex-point/set-sync-to-yandex-points?ids=1,2,3,7
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

        $yandex_points = YandexPoint::find()->where(['id' => $aClearIds])->all();
        if(count($yandex_points) == 0) {
            throw new ForbiddenHttpException('Точки не найдены');
        }

        $sql = 'UPDATE `'.YandexPoint::tableName().'` SET sync_date = '.time().' WHERE id IN ('.implode(',', ArrayHelper::map($yandex_points, 'id', 'id')).')';
        Yii::$app->db->createCommand($sql)->execute();


        return [
            'success' => true,
            'ids' => $ids
        ];
    }
}