<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\Transport;


class TransportController extends Controller
{
    /**
     * @inheritdoc
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


    /*
     * Функция возвращает список водителей рейса для SelectWidget-элемента в форме "Добавить транспорт к рейсу"
     */
    public function actionEfficiencyList($date = '') {

        $this->layout = 'ajax_layout';

        if(empty($date)) {
            $date = date('d.m.Y');
        }

        $aTransportsEfficiencyData = Transport::getEfficiencyData($date);

        $aDates = Transport::getEfficiencyDates($date);

        return $this->render('efficiency-list', [
            'aTransportsEfficiencyData' => $aTransportsEfficiencyData,
            'aDates' => $aDates
        ]);
    }


    public function actionAjaxGetActiveTransports() {

        Yii::$app->response->format = 'json';

        $search = Yii::$app->getRequest()->post('search');

        $transports = Transport::find()
            ->where(['active' => 1])
            ->andWhere(['like', 'CONCAT(`transport`.model, " (", `transport`.car_reg, ")")', $search])
            ->all();

        $out['results'] = [];
        foreach($transports as $transport) {
            $out['results'][] = [
                'id' => $transport->id,
                'text' => $transport->name2,
            ];
        }

        return $out;
    }
}
