<?php

namespace app\commands;

use app\models\OperatorBeelineSubscription;
use app\models\SocketDemon;
use app\models\User;
use yii\console\Controller;
use Yii;
use yii\helpers\ArrayHelper;


class UserController extends Controller
{
    /*
     * Если у пользователей истекла сессия, то через сокеты посылается сигнал в браузеры чтобы те вылетели с сайта
     * команда: php yii user/logout-expired-sessions
     */
    public function actionLogoutExpiredSessions()
    {
        // ищем пользователей с истекшей сессией
        $users = User::find()
            ->where(['<', 'auth_seans_finish', time()])
            ->andWhere(['>', 'auth_seans_finish', 0])
            ->all();

        foreach($users as $user) {
            $user->logoutWithoutCookie();
        }

        if(count($users) > 0) {


            $aUsersIds = [];
            foreach($users as $user) {
                $aUsersIds[] = $user->id;
            }

            $operator_subscriptions = OperatorBeelineSubscription::find()
                ->where(['operator_id' => $aUsersIds])
                ->all();
            foreach($operator_subscriptions as $operator_subscription) {
                $operator_subscription->deleteFromAts();
                $operator_subscription->setStatus('OFFLINE'); // в АТС статус устанавливается неподписке, а СИПу
            }

            SocketDemon::sendOutBrowserMessageInstant('all_site_pages', [''], 'logout', [''], $aUsersIds);
        }
    }

}
