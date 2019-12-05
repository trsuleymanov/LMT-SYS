<?php

namespace app\controllers;

use Yii;
use app\models\Transport;
use app\models\SecondTripTransport;
use yii\helpers\ArrayHelper;

class SecondTripTransportController extends \yii\web\Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }
    
     /*
     * Функция возвращает форму "Привязка транспортного средства к рейсу"
     */
    public function actionAjaxGetAddCarsForm($onDate)
    {
        Yii::$app->response->format = 'json';

		return [
			'success' => true,
            'html' => $this->renderAjax('add-cars-form', [
				'onDate' => $onDate,
                'second_trip_transports' => SecondTripTransport::find()->where(['date'=>strtotime($onDate)])->all(),
				'transport_list' => ArrayHelper::map(SecondTripTransport::getEmptyTransports($onDate), 'id', 'car_reg_places_count'),
			])
		];
    }
    
    /*
     * Функция возвращает незаполненную строку "транспорт-водитель" для формы "Привязка транспортного средства к рейсу"
     */
    public function actionAjaxGetAddCarTr($onDate)
    {
        Yii::$app->response->format = 'json';

        $second_trip_transport = new SecondTripTransport();
        $second_trip_transport->active = 1;

        return [
            'success' => true,
            'tr_html' => $this->renderPartial('_add-cars-form-row', [
                'onDate' => $onDate,
                'transport_list' => ArrayHelper::map([''=>'---'] + SecondTripTransport::getEmptyTransports($onDate), 'id', 'car_reg_places_count'),
                'second_trip_transport' => $second_trip_transport
            ])
        ];
    }


    /*
     * Функция возвращает список машин рейса для SelectWidget-элемента или элемента картика или т.п.
     */
    public function actionAjaxGetTransportsNames() {

        Yii::$app->response->format = 'json';

        $search = Yii::$app->getRequest()->post('search');
        $selected_transports_ids = Yii::$app->request->post('selected_transports_ids');

        if(count($selected_transports_ids) > 0) {
            $transports = Transport::find()
                ->where(['NOT IN', 'id', $selected_transports_ids])
                ->andWhere(['active' => 1])
                ->all();
        }else {
            $transports = Transport::find()
                ->where(['active' => 1])
                ->all();
        }


        $out['results'] = [];
        foreach($transports as $transport) {

            $text = $transport->car_reg_places_count;

            if($search != '') {
                if(strpos($text, $search) !== false) {

                    if(count($selected_transports_ids) > 0 && !in_array($transport->id, $selected_transports_ids)) {
                        $out['results'][] = [
                            'id' => $transport->id,
                            'text' => $text
                        ];
                    }else {
                        $out['results'][] = [
                            'id' => $transport->id,
                            'text' => $text
                        ];
                    }
                }
            }else {
                if(count($selected_transports_ids) > 0 && !in_array($transport->id, $selected_transports_ids)) {
                    $out['results'][] = [
                        'id' => $transport->id,
                        'text' => $text
                    ];
                }else {
                    $out['results'][] = [
                        'id' => $transport->id,
                        'text' => $text
                    ];
                }
            }
        }

        return $out;
    }

    /*
     * Функция сохраняет данных формы "Привязка транспортного средства к рейсу"
     */
    public function actionAjaxSaveCarsForm($onDate)
    {
        Yii::$app->response->format = 'json';

        if(SecondTripTransport::updatePostSecondTripTransports(Yii::$app->request->post('transport_ids'), Yii::$app->request->post('second_trip_transport_ids'), strtotime($onDate))) {
            return ['success' => true];
        }else {
            return ['success' => false];
        }
    }
}
