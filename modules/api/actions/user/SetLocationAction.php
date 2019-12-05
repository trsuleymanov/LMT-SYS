<?php

namespace app\modules\api\actions\user;

use Yii;
use app\models\User;
use yii\web\ForbiddenHttpException;


class SetLocationAction extends \yii\rest\Action
{
    public $modelClass = '';


    public function run()
    {
        $user = User::find()->where(['id' => Yii::$app->user->id])->one();
        if($user == null) {
            throw new ForbiddenHttpException('Пользователь не найден');
        }

        $user->scenario = 'set_location';
        $user->load(Yii::$app->getRequest()->getBodyParams(), '');
        if (!$user->validate()) {
            return $user;
        }

        $user->lat_long_ping_at = time();
        $user->save();

        return; // + stasus 200 by default
    }
}
