<?php

namespace app\modules\serverapi\actions\setting;

use app\models\City;
use app\models\Client;
use app\models\Setting;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;

class SetSyncToCitiesAction extends \yii\rest\Action
{
    public $modelClass = '';

    /**
     * Установка даты синхронизации для городов
     *
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

        $cities = City::find()->where(['id' => $aClearIds])->all();
        if(count($cities) == 0) {
            throw new ForbiddenHttpException('Города не найдены');
        }

        $sql = 'UPDATE `'.City::tableName().'` SET sync_date = '.time().' WHERE id IN ('.implode(',', ArrayHelper::map($cities, 'id', 'id')).')';
        Yii::$app->db->createCommand($sql)->execute();


        return [
            'success' => true,
            'ids' => $ids
        ];

    }
}
