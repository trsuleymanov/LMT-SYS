<?php

namespace app\controllers;

use app\models\LogOrderPriceRecount;
use app\models\Setting;
use app\models\TripOperation;
use Yii;
use app\models\Order;
use app\models\OrderSearch;
use yii\base\ErrorException;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\filters\VerbFilter;
use app\models\Trip;
use app\models\Direction;
use app\models\TripTransport;
use app\models\TripTransportSearch;
use app\models\DispatcherAccounting;
use app\models\OrderStatus;

/**
 * Рейсы
 *
 * !чтение данных по рейсам не должно происходить напрямую через Trip::find(), а должно происходить
 *  только через функции модели Trip
 */
class TripController extends Controller
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
     * Список рейсов
     */
    public function actionAjaxIndex($date, $direction_id)
    {
        Yii::$app->response->format = 'json';

        $direction = Direction::findOne($direction_id);
        if($direction == null) {
            throw new ForbiddenHttpException('Не найдено направление');
        }

		$trips = Trip::getTripsQuery(strtotime($date), $direction->id)
			->andWhere(['date_sended' => NULL])
			->all();

		if(count($trips) == 0) {
			throw new ForbiddenHttpException('Рейсы не найдены');
		}

		return $trips;
    }


	public function actionTest() {

		$date = '04.03.2018';
		$direction_id = 2;

		$direction = Direction::findOne($direction_id);
		if($direction == null) {
			throw new ForbiddenHttpException('Не найдено направление');
		}

		$xz = Trip::getTripsQuery(strtotime($date), $direction->id)
			->andWhere(['date_sended' => NULL])
			->all();

		echo "xz:<pre>"; print_r($xz); echo "</pre>";
	}

    /*
     * Информация о рейсе (Заказы на рейсе + выбор машины для рейса)
     */
    public function actionTripOrders($trip_id) {

        $trip = Trip::findOne($trip_id);
        if($trip == null) {
            throw new ForbiddenHttpException('Рейс не найден');
        }
//		if(!in_array(Yii::$app->session->get('role_alias'), ['root', 'admin', 'editor', 'manager'])) {
//			throw new ForbiddenHttpException('Доступ запрещен к TripOrders');
//		}

        $orderSearchModel = new OrderSearch();
		//echo "queryParams:<pre>"; print_r(Yii::$app->request->queryParams); echo "</pre>";
        $orderDataProvider = $orderSearchModel->TripSearch(Yii::$app->request->queryParams, $trip->id);

		$transportSearchModel = new TripTransportSearch([]);
		$transportDataProvider = $transportSearchModel->search(Yii::$app->request->queryParams, $trip->id);

        return $this->render('trip-orders', [
            'trip' => $trip,
            'orderSearchModel' => $orderSearchModel,
            'orderDataProvider' => $orderDataProvider,
			'transportSearchModel' => $transportSearchModel,
			'transportDataProvider' => $transportDataProvider,
        ]);
    }

	/*
	 * Функция возвращает html страницы "Информация о рейсе"
	 */
	public function actionAjaxGetTripOrders($trip_id)
	{
		Yii::$app->response->format = 'json';

		$trip = Trip::findOne($trip_id);
		if($trip == null) {
			throw new ForbiddenHttpException('Рейс не найден');
		}

		Yii::$app->request->queryParams = Yii::$app->request->post('url_params');

		$orderSearchModel = new OrderSearch();
		$orderDataProvider = $orderSearchModel->TripSearch(Yii::$app->request->queryParams, $trip->id);

		$transportSearchModel = new TripTransportSearch();
		$transportDataProvider = $transportSearchModel->search(Yii::$app->request->queryParams, $trip->id);

		return [
			'success' => true,
			'html' => $this->renderAjax('_ajax-trip-orders-block', [
				'trip' => $trip,
				'orderSearchModel' => $orderSearchModel,
				'orderDataProvider' => $orderDataProvider,
				'transportSearchModel' => $transportSearchModel,
				'transportDataProvider' => $transportDataProvider,
			]),
		];
	}


    public function actionAddTrip($date, $is_reserv_trip = false){
	
		$this->layout = 'ajax_layout1';

		$model = new Trip();
		$model->date = strtotime($date);
        $model->is_reserv = $is_reserv_trip;
        //$model->is_reserv = ($is_reserv_trip == "true" ? true : false);

        $post = Yii::$app->request->post();
        if(count($post) > 0) {

            if ($model->load(Yii::$app->request->post()) && $model->save()) {

                $trip_operation = new TripOperation();
                $trip_operation->type = 'create';
                $trip_operation->comment = 'Создан '.($model->direction_id==1 ? 'АК ' : 'КА ').$model->name;
                if(!$trip_operation->save(false)) {
                    throw new ErrorException('Не удалось сохранить в историю операцию с рейсом');
                }

                echo 'ok';

            }else {
                Yii::$app->response->format = 'json';

                $aAllErrors = [];
                $aErrors = $model->getErrors();
                foreach ($aErrors as $field => $aFieldErrors) {
                    foreach ($aFieldErrors as $error) {
                        $aAllErrors[] = $error;
                    }
                }



                return [
                    'error' => implode(' ', $aAllErrors)
                ];

            }
        }else {
            return $this->render('trip-form', [
                'model' => $model,
            ]);
        }

//		if ($model->load(Yii::$app->request->post()) && $model->save()) {
//
//			$trip_operation = new TripOperation();
//			$trip_operation->type = 'create';
//			$trip_operation->comment = 'Создан '.($model->direction_id==1 ? 'АК ' : 'КА ').$model->name;
//			if(!$trip_operation->save(false)) {
//				throw new ErrorException('Не удалось сохранить в историю операцию с рейсом');
//			}
//
//			echo 'ok';
//		}else {
//
//			return $this->render('trip-form', [
//				'model' => $model,
//			]);
//		}
    }
    
    
    public function actionEditTrip($trip_id){

		$this->layout = 'ajax_layout1';

		$model = Trip::findOne($trip_id);
		$old_start_time = $model->start_time;
		$old_mid_time = $model->mid_time;
		$old_end_time = $model->end_time;
		$old_commercial = $model->commercial;
		$old_name = ($model->direction_id==1 ? 'АК ' : 'КА ').$model->name;

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			if($model->canUpdateOrders()
				&& (
					$old_start_time != $model->start_time || $old_mid_time != $model->mid_time
					|| $old_end_time != $model->end_time || boolval($old_commercial) != boolval($model->commercial)
				)
			) {
				$model->updateOrders();
			}


			$trip_operation = new TripOperation();
			$trip_operation->type = 'update';
			$trip_operation->comment = 'Изменен ';
			$new_name = ($model->direction_id==1 ? 'АК ' : 'КА ').$model->name;
			if($new_name != $old_name) {
				$trip_operation->comment .= $old_name.' -> '.$new_name."<br />";
			}
			if($old_commercial != $model->commercial) {
				$trip_operation->comment .= ($old_commercial==0?'не ком-й':'ком-й').' -> '.($model->commercial==0?'не ком-й':'ком-й')."<br />";
			}
			$aOldStartTime = explode(':', $old_start_time);
			$aNewStartTime = explode(':', $model->start_time);
			$trip_operation->delta = 3600*intval($aNewStartTime[0]) + 60*intval($aNewStartTime[1]) - 3600*intval($aOldStartTime[0]) - 60*intval($aOldStartTime[1]);
			if(!$trip_operation->save(false)) {
				throw new ErrorException('Не удалось сохранить в историю операцию с рейсом');
			}

			echo 'ok';
		}else {
			return $this->render('trip-form', [
				'model' => $model,
			]);
		}
    }


    public function actionAttachTransport($trip_id){
    
		$this->layout = 'ajax_layout1';

		$model = new TripTransport;

		$model->trip_id = $trip_id;

		//print_r(Yii::$app->request->post());

		if ($model->load(Yii::$app->request->post()) && $model->isNewRecord) {
			if ($model->validate()) {
				// form inputs are valid, do something here
				if($model->save()){
					if(Yii::$app->request->isAjax || Yii::$app->request->isPost){
						echo 'ok';
					}
				} else {
					if(Yii::$app->request->isAjax || Yii::$app->request->isPost){
						echo 'error';
					}
				}
				return;
			} else {
				/*
				if(Yii::$app->request->isAjax || Yii::$app->request->isPost){
					echo 'error';
				}
				return;
				*/
			   //echo 'error';
			}
		} else {
			//echo 'error';
		}


		return $this->render('TripTransport', [
			'model' => $model,
		]);
    }


	public function actionMergeTrips($trips_ids){

		Yii::$app->response->format = 'json';

		$trips = Trip::find()->where(['IN', 'id', explode(',', $trips_ids)])->all();
		if(!Trip::canMerge($trips)) {
			throw new ForbiddenHttpException('Объединение рейсов невозможно');
		}

		$model = new Trip();
		$model->date = $trips[0]->date;
		$model->direction_id = $trips[0]->direction_id;

		if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->save() && $model->mergeTrips($trips)) {
			//Yii::$app->response->format = 'json';

            // echo "post:<pre>"; print_r(Yii::$app->request->post()); echo "</pre>"; exit;

			$trip_operation = new TripOperation();
			$trip_operation->type = 'merge';
			$trip_operation->comment = 'Получен рейс '.($model->direction_id==1 ? 'АК ' : 'КА ').$model->name
				.' объединением: '.implode(', ', ArrayHelper::map($trips, 'name', 'name'));
			if(!$trip_operation->save(false)) {
				throw new ErrorException('Не удалось сохранить в историю операцию с рейсом');
			}

			return 'ok';

		} else {
			
			$model->name = 'ОБ(' . implode('; ', ArrayHelper::map($trips, 'id', 'name')) . ')';
			$model->start_time = $trips[0]->start_time;
			$model->mid_time = $trips[0]->mid_time;
			$model->end_time = $trips[0]->end_time;

			// если все рейсы $trips коммерческие, то результирующий рейс тоже коммерческий
            $model->commercial = true;
            foreach ($trips as $trip) {
                if($trip->commercial == false) {
                    $model->commercial = false;
                    break;
                }
            }

			return $this->renderPartial('mergeTrips', [
				'model' => $model,
				'trips' => $trips,
			]);
		}
	}


    public function actionSetTrips($date = null){

		$selected_unixdate = (!empty($date) ? strtotime($date) : strtotime(date('d.m.Y', time())));

//		if(!in_array(Yii::$app->session->get('role_alias'), ['root', 'admin', 'editor', 'manager'])) {
//			throw new ForbiddenHttpException('Доступ запрещен к SetTrips');
//		}

		return $this->render('setOfTrips', [
			'selected_unixdate' => $selected_unixdate,
			'user' => Yii::$app->user->identity,
			'aDirections' => Direction::getDirectionsTrips($selected_unixdate)
		]);
    }


    public function actionAjaxSettrips($date = null){

		$selected_unixdate = (!empty($date) ? strtotime($date) : strtotime(date('d.m.Y', time())));
	
//		return $this->renderPartial('_set-trip-directions', [
//			'aDirections' => Direction::getDirectionsTrips($selected_unixdate)
//		]);

		return $this->renderPartial('/site/directions-trips-block', [
			'aDirections' => Direction::getDirectionsTrips($selected_unixdate),
			'view' => 'set_trip_list'
		]);
    }
    

	/*
	 * Начало отправления рейса
	 */
	public function actionAjaxStartSendingReis($trip_id, $use_mobile_app = -1, $start = 0)
	{
		Yii::$app->response->format = 'json';

		$trip = Trip::findOne($trip_id);
		if($trip == null) {
			throw new ForbiddenHttpException('Рейс не найден');
		}


		if($start == "1") {

            //echo 'use_mobile_app='.$use_mobile_app;
            $trip->use_mobile_app = $use_mobile_app;

            if ($trip->startSending()) {
                DispatcherAccounting::createLog('trip_start_sending'); // логируем начало отправления рейса
            } else {
                throw new ForbiddenHttpException('Не удалось начать отправление рейса');
            }

            return [
                'success' => true
            ];

        }else {

		    //$setting = Setting::find()->where(['id' => 1])->one();
		    if(Yii::$app->setting->use_mobile_app_by_default == true) {

                $trip->use_mobile_app = true;

                if ($trip->startSending()) {
                    DispatcherAccounting::createLog('trip_start_sending'); // логируем начало отправления рейса
                } else {
                    throw new ForbiddenHttpException('Не удалось начать отправление рейса');
                }

                return [
                    'success' => true,
                    'status' => 'sended'
                ];

            }else {

                return [
                    'success' => true,
                    'status' => 'need_choice'
                ];
            }
        }
	}


	/*
	 * Отправка рейса (аналогично - "Закрытие рейса для редактирования")
	 * - больше не нужна отправка от 11.11.2019
	 */
	public function actionAjaxSendReis($trip_id)
	{
		Yii::$app->response->format = 'json';

		$trip = Trip::findOne($trip_id);
		if($trip == null) {
			throw new ForbiddenHttpException('Рейс не найден');
		}

		if($trip->send()) {
			//DispatcherAccounting::createLog('trip_send'); // логируем Отправку рейса
		}else {
			throw new ForbiddenHttpException('Не удалось отправить рейс');
		}

		return [
			'success' => true
		];
	}




	/*
	 * Пересчет цен в заказах
	 */
	public function actionAjaxRecountOrdersPrices($trip_id)
    {
        Yii::$app->response->format = 'json';

        $trip = Trip::findOne($trip_id);
        if($trip == null) {
            throw new ForbiddenHttpException('Рейс не найден');
        }

        $orders = $trip->orders;

        $update_page = false;
        $aOrdersLog = [];
        if(count($orders) > 0) {
            foreach ($orders as $order) {

                $is_changed = false;

                $price = $order->getCalculatePrice();
                if ($order->price != $price) {
                    $aOrdersLog[$order->id] = [
                        'order_id' => $order->id,
                        'old_price' => $order->price,
                        'new_price' => $price
                    ];
                    $order->setField('price', $price);
                    $is_changed = true;
                }

                $used_cash_back = $order->getCalculateUsedCashBack();
                if ($order->used_cash_back != $used_cash_back) {
                    $order->setField('used_cash_back', $used_cash_back);
                    $is_changed = true;
                    $aOrdersLog[$order->id] = [
                        'order_id' => $order->id,
                        'old_price' => $order->price,
                        'new_price' => $price
                    ];
                }

                $prizeTripCount = $order->prizeTripCount;
                if($order->prize_trip_count != $prizeTripCount) {
                    $order->setField('prize_trip_count', $prizeTripCount);
                    $is_changed = true;
                    $aOrdersLog[$order->id] = [
                        'order_id' => $order->id,
                        'old_price' => $order->price,
                        'new_price' => $price
                    ];
                }

                if($is_changed == true) {
                    $order->setField('sync_date', NULL);
                    $update_page = true;
                }
            }
        }

        if(count($aOrdersLog) > 0) {
            foreach ($aOrdersLog as $aOrderData) {

                $log = new LogOrderPriceRecount();
                $log->trip_id = $trip->id;
                $log->trip_link = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].'/trip/trip-orders?trip_id='.$trip->id;
                $log->order_id = $aOrderData['order_id'];
                $log->old_price = $aOrderData['old_price'];
                $log->new_price = $aOrderData['new_price'];
                if(!$log->save(false)) {
                    throw new ForbiddenHttpException('Не удалось сохранить лог для изменения цены заказа '.$aOrderData['order_id']);
                }
            }
        }


        return [
            'success' => true,
            'update_page' => $update_page
        ];
    }


	/*
	 * Превращение рейсов в коммерческие
	 */
	public function actionAjaxSetCommercialTrips() {

		Yii::$app->response->format = 'json';

		return Trip::setCommercialTrips(Yii::$app->request->post('trips'));
	}

	/*
	 * Отмена коммерческих рейсов
	 */
	public function actionAjaxUnsetCommercialTrips() {

		Yii::$app->response->format = 'json';

		return Trip::unsetCommercialTrips(Yii::$app->request->post('trips'));
	}


	public function actionAjaxCancelTripTransportsSended($trip_id, $password) {

		Yii::$app->response->format = 'json';

		$user = Yii::$app->user->identity;
		$user_role_alias = $user->userRole->alias;

		if($user_role_alias != 'root') {
			throw new ForbiddenHttpException('Отменять рейс может только root');
		}

		if(!$user->validatePassword($password)) {
			throw new ForbiddenHttpException('Пароль не правильный');
		}

		$trip = Trip::find()->where(['id' => $trip_id])->one();
		if($trip == null) {
			throw new ForbiddenHttpException('Рейс не найден');
		}

		if(!$trip->cancelSend()) {
			throw new ErrorException('Не удалось отменить отправку рейса');
		}

		$trip_transports = $trip->tripTransports;
		foreach($trip_transports as $trip_transport) {
			if(!$trip_transport->cancelSend()) {
				throw new ErrorException('Не удалось отменить отправку т/с id='.$trip_transport->id);
			}
		}


		return [
			'success' => true
		];
	}


	public function actionAjaxGetTripMapData($id) {

		Yii::$app->response->format = 'json';

		$trip = Trip::find()->where(['id' => $id])->one();
		if($trip == null) {
			throw new ForbiddenHttpException('Рейс не найден');
		}

		$direction = $trip->direction;
		$city = $direction->cityFrom;

		// точки посадки для каждого заказа на направлении
		$canceled_order_status = OrderStatus::getByCode('canceled');
		$orders = Order::find()
			->where(['trip_id' => $trip->id])
			->andWhere(['!=', 'status_id', $canceled_order_status->id])
			->all();

		$aYandexPointsFrom = [];
		foreach($orders as $order) {

			if(!empty($order->yandex_point_from_lat) && !empty($order->yandex_point_from_long) && !empty($order->yandex_point_from_name)) {
				$key = $order->yandex_point_from_lat.'_'.$order->yandex_point_from_long;
				if(!isset($aYandexPointsFrom[$key])) {
					$aYandexPointsFrom[$key] = [
						'id' => $order->yandex_point_from_id,
						'lat' => $order->yandex_point_from_lat,
						'long' => $order->yandex_point_from_long,
						'name' => $order->yandex_point_from_name,
					];
				}
				$aYandexPointsFrom[$key]['orders'][] = $order;

			}elseif($order->yandex_point_from_id > 0) {

				$yandex_point_from = $order->yandexPointFrom;
				$key = $yandex_point_from->lat.'_'.$yandex_point_from->long;
				if(!isset($aYandexPointsFrom[$key])) {
					$aYandexPointsFrom[$key] = [
						'id' => $order->yandex_point_from_id,
						'lat' => $yandex_point_from->lat,
						'long' => $yandex_point_from->long,
						'name' => $yandex_point_from->name,
					];
				}
				$aYandexPointsFrom[$key]['orders'][] = $order;
			}
		}

		return [
			'city' => $city,
			'yandex_points_from' => $aYandexPointsFrom
		];
	}

	// страница печати рейса
	public function actionPrint($id, $empty_rows_count = 10) {

		$trip = Trip::find()->where(['id' => $id])->one();
		if($trip == null) {
			throw new ForbiddenHttpException('Рейс не найден');
		}

		$queryParams = Yii::$app->request->queryParams;
		$queryParams['OrderSearch']['status_id'] = 1; // смотрим только новые заказы
		$queryParams['empty_rows_count'] = $empty_rows_count;

		$orderSearchModel = new OrderSearch();
		$orderDataProvider = $orderSearchModel->TripSearch($queryParams, $trip->id);

		$transportSearchModel = new TripTransportSearch();
		$transportDataProvider = $transportSearchModel->search(Yii::$app->request->queryParams, $trip->id);

		return $this->renderAjax('print', [
			'trip' => $trip,
			'orderSearchModel' => $orderSearchModel,
			'orderDataProvider' => $orderDataProvider,
			'transportSearchModel' => $transportSearchModel,
			'transportDataProvider' => $transportDataProvider,
		]);
	}


	public function actionAjaxGetTrips() {

		Yii::$app->response->format = 'json';

		$out['results'] = [];

		$search = Yii::$app->getRequest()->post('search');
		$date = Yii::$app->getRequest()->post('date');
		$direction_id = Yii::$app->getRequest()->post('direction_id');

		$trip_query = Trip::find()
			->where(['date' => strtotime($date)])
			->andWhere(['direction_id' => $direction_id]);

		if($search != '') {
			$trip_query->andWhere(['LIKE', 'name', $search]);
		}
		$trips = $trip_query->orderBy(['name' => SORT_ASC])->all();

		$out['results'] = [];
		foreach($trips as $trip) {
			$out['results'][] = [
				'id' => $trip->id,
				'text' => $trip->name,
			];
		}

		return $out;
	}
}
