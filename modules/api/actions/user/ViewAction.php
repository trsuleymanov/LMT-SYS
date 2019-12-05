<?php

namespace app\modules\api\actions\user;

use Yii;
use app\models\User;
use yii\web\ForbiddenHttpException;


class ViewAction extends \yii\rest\Action
{
    public $modelClass = '';

    /**
     * Отображение
     */
    public function run()
    {
        $user = User::find()->where(['id' => Yii::$app->user->id])->one();
        if($user == null) {
            throw new ForbiddenHttpException('Пользователь не найден');
        }

        return $user;
    }
}
