<?php

namespace app\modules\admin\controllers;

use app\models\Client;
use app\models\Order;
use app\models\OrderStatus;
use Yii;
use app\models\Loyality;
use app\models\LoyalitySearch;
use yii\base\ErrorException;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * LoyalityController implements the CRUD actions for Loyality model.
 */
class LoyalityController extends Controller
{
    /**
     * {@inheritdoc}
     */
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
//        ini_set('memory_limit', '-1');
//        set_time_limit(0);

        $searchModel = new LoyalitySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    public function actionAjaxRewrite() {

        ini_set('memory_limit', '-1');
        set_time_limit(0);

        Yii::$app->response->format = 'json';

        $date_from = Yii::$app->request->post('date_from');
        $date_to = Yii::$app->request->post('date_to');

        if(empty($date_to)) {
            $date_to = date('d.m.Y', time() - 86400);
        }
        $unixtime_date_to = strtotime($date_to);
        $unixtime_date_from = !empty($date_from) ? strtotime($date_from) : 0;

        Yii::$app->db->createCommand()->truncateTable(Loyality::tableName())->execute();

        $clients_count = Client::find()->count();
        for($i = 0; $i <= $clients_count / 1000; $i++) {
            Loyality::rewriteLoyality($i, 1000, $unixtime_date_from, $unixtime_date_to);
            //break;
        }

        return [
            'success' => true
        ];
    }


    public function actionRecount() {

        return $this->render('recount');
    }

    public function actionAjaxGetLastClientId() {

        Yii::$app->response->format = 'json';

        $last_client = Client::find()->limit(1)->orderBy(['id' => SORT_DESC])->one();

        return [
            'success' => true,
            //'clients_count' => Client::find()->count()
            'last_client_id' => $last_client->id,
        ];

    }

    public function actionAjaxRewriteClientsCounters($limit, $client_id_from) {

//        ini_set('memory_limit', '-1');
//        set_time_limit(0);
//
//        Yii::$app->response->format = 'json';
//
//        $step_count = 100;
//        //$start = microtime(true);
//        $clients_count = Client::find()->count();
//        for($i = 0; $i <= $clients_count / $step_count; $i++) {
//            Loyality::rewriteClientsCounters($i, $step_count);
//            //break;
//        }
//        //echo 'Время выполнения скрипта: '.round(microtime(true) - $start, 4).' сек.';
//
//        return [
//            'success' => true
//        ];

        ini_set('memory_limit', '-1');
        set_time_limit(0);

        Yii::$app->response->format = 'json';

        //$start = microtime(true);
        $current_step_last_client_id = Loyality::rewriteClientsCounters($limit, $client_id_from);


        return [
            'success' => true,
            'current_step_last_client_id' => $current_step_last_client_id,
            //'time' => round(microtime(true) - $start, 4)
        ];
    }
}
