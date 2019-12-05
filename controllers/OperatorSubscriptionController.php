<?php

namespace app\controllers;

use app\models\OperatorBeelineSubscription;
use Yii;
use yii\base\ErrorException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\ForbiddenHttpException;


class OperatorSubscriptionController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }


    public function actionCreateAtsSubscription($subscription_id) {

        Yii::$app->response->format = 'json';

        // настаиваю на том, чтобы при нажатии кнопки "Подключиться к АТС" система сначала проверяла,
        // существует ли подписка, если да, то удаляла ее, выдавала алерт "Аномальная подписка удалена"
        // и создавала новую, срок подписки - 14 часов.
        // Если оператор выбрала занятый СИП, то нужно выдать обычный aлерт "Выбранный СИП занят".

        $operator_subscription = OperatorBeelineSubscription::find()->where(['id' => $subscription_id])->one();
        if ($operator_subscription == null) {
            throw new ForbiddenHttpException('Подписка оператора в CRM не найдена');
        }

        // проверка существующей подписки
        if(!empty($operator_subscription->subscription_id)) {
            throw new ForbiddenHttpException('Выбранный СИП занят');
        }

        $is_deleted_anomal_subscription = false;
        if($operator_subscription->isExistInAts()) {
            if($operator_subscription->deleteFromAts()) {
                //$aMessages[] = "Аномальная подписка удалена";
                $is_deleted_anomal_subscription = true;
            }else {
                throw new ErrorException('Не удалось удались аномальную подписку');
            }
        }


        if($operator_subscription->createAtsSubscription()) {
            return [
                'success' => true,
                'is_deleted_anomal_subscription' => $is_deleted_anomal_subscription,
                'expired_at' => date('d.m.Y H:i', $operator_subscription->expire_at),
            ];
        }else {
            return [
                'success' => false
            ];
        }
    }


    public function actionDeleteSubscription() {

        Yii::$app->response->format = 'json';

        $operator_subscription = OperatorBeelineSubscription::find()->where(['operator_id' => Yii::$app->user->id])->one();
        if($operator_subscription == null) {
            throw new ForbiddenHttpException('Подписка оператора в CRM не найдена');
        }

        if(!$operator_subscription->deleteFromAts()) {
            throw new ForbiddenHttpException('Не удалось удалить подписку из базы в CRM');
        }

        if(!$operator_subscription->setStatus('OFFLINE')) { // в АТС статус устанавливается неподписке, а СИПу
            throw new ForbiddenHttpException('Не удалось установить офлайн-статус');
        }


        return [
            'success' => true
        ];

    }

}
