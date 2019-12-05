<?php

namespace app\modules\admin\controllers;


use app\models\Driver;
use app\models\User;
use Yii;
use app\models\TransportWaybillSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;


class TransportWaybillReportController extends Controller
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

    /**
     * Lists all NotaccountabilityTransportReport models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TransportWaybillSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    public function actionAjaxGetUsers($field_key = 'id', $field_value = 'fio') {

        Yii::$app->response->format = 'json';

        $search = Yii::$app->getRequest()->post('search');

        // set_hand_over_b1_operator_id
        $drivers = User::find()
            ->andWhere(['like', 'fio', $search])
            ->all();

        $out['results'] = [];
        foreach($drivers as $driver) {
            $out['results'][] = [
                'id' => $driver->$field_key,
                'text' => $driver->$field_value,
            ];
        }

        return $out;
    }
}
