<?php

namespace app\modules\admin\controllers;

use Yii;
use app\models\OperatorBeelineSubscription;
use app\models\OperatorBeelineSubscriptionSearch;
use yii\base\ErrorException;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * OperatorBeelineSubscriptionController implements the CRUD actions for OperatorBeelineSubscription model.
 */
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


    public function actionIndex()
    {
        $searchModel = new OperatorBeelineSubscriptionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    public function actionCreate()
    {
        $model = new OperatorBeelineSubscription();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }


    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }


    public function actionDelete($id)
    {
        $operator_subscription = OperatorBeelineSubscription::find()->where(['id' => $id])->one();
        if(!$operator_subscription->deleteFromAts()) {
            throw new ForbiddenHttpException('Не удалось удалить подписку из базы в CRM');
        }

        $operator_subscription->delete();

        return $this->redirect(['index']);
    }


/*
    public function actionCreateAtsSubscription($operator_subscription_id) {

        Yii::$app->response->format = 'json';

        $operator_subscription = OperatorBeelineSubscription::find()->where(['id' => $operator_subscription_id])->one();
        if($operator_subscription == null) {
            throw new ForbiddenHttpException('Подписка оператора в CRM не найдена');
        }

        if($operator_subscription->createAtsSubscription()) {
            return [
                'success' => true,
                'expired_at' => date('d.m.Y H:i', $operator_subscription->expire_at),
            ];
        }else {
            return [
                'success' => false
            ];
        }
    }*/


    public function actionCheckAtsSubscription($operator_subscription_id) {

        // curl -X GET --header 'X-MPBX-API-AUTH-TOKEN: b3469183-f19a-46ce-9b44-19ace72e84c2' 'https://cloudpbx
        //.beeline.ru/apis/portal/subscription?subscriptionId=efc073ec-3a6f-4344-8684-40b79a779bf5'

        Yii::$app->response->format = 'json';

        $operator_subscription = OperatorBeelineSubscription::find()->where(['id' => $operator_subscription_id])->one();
        if($operator_subscription == null) {
            throw new ForbiddenHttpException('Подписка оператора в CRM не найдена');
        }

        if($operator_subscription->isExistInAts()) {
            return [
                'success' => true,
            ];
        }else {
            return [
                'success' => false
            ];
        }
    }


    public function actionDeleteSubscription($operator_subscription_id) {

        Yii::$app->response->format = 'json';

        $operator_subscription = OperatorBeelineSubscription::find()->where(['id' => $operator_subscription_id])->one();
        if($operator_subscription == null) {
            throw new ForbiddenHttpException('Подписка оператора в CRM не найдена');
        }

        if(!$operator_subscription->deleteFromAts()) {
            throw new ForbiddenHttpException('Не удалось удалить подписку из базы в CRM');
        }

        if(!$operator_subscription->setStatus('OFFLINE')) { // в АТС статус устанавливается неподписке, а СИПу
            throw new ForbiddenHttpException('Не удалось установить офлайн-статус');
        }


//        $operator_subscription->subscription_id = NULL;
//        $operator_subscription->expire_at = NULL;
//        if(!$operator_subscription->save(false)) {
//            throw new ErrorException('Не удалось удалить подписку');
//        }

        return [
            'success' => true
        ];

    }




    protected function findModel($id)
    {
        if (($model = OperatorBeelineSubscription::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
