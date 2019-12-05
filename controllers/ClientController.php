<?php

namespace app\controllers;

use app\models\AdvertisingSource;
use app\models\AdvertisingSourceReport;
use app\models\InformerOffice;
use Yii;
use app\models\Client;
use app\models\ClientSearch;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\base\ErrorException;
use yii\filters\VerbFilter;
use app\models\Direction;
use app\models\Order;
use app\models\OrderSearch;
use app\models\Street;
use app\models\Point;


/**
 * ClientController implements the CRUD actions for Client model.
 */
class ClientController extends Controller
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


    /**
     * Ajax поиск клиента по номеру телефона
     */
    public function actionAjaxGetClient($phone, $direction_id, $current_order_date, $current_order_id, $field_name = '')
    {
        Yii::$app->response->format = 'json';


        $client = Client::getClientByMobilePhone($phone);
        if($client == null) {
            $client = new Client();
        }


        list(
            $last_order,
            $yandexPointFrom,
            $yandex_point_from_id,
            $yandex_point_from_name,
            $yandex_point_from_lat,
            $yandex_point_from_long,

            $yandexPointTo,
            $yandex_point_to_id,
            $yandex_point_to_name,
            $yandex_point_to_lat,
            $yandex_point_to_long

            ) = $client->getLastOrderData($direction_id);

        $informer_office = null;
        $client_do_tariff = null;
        if($client != null && $client->do_tariff_id > 0) {
            $informer_office = InformerOffice::find()->where(['code' => 'individual_tariff'])->one();
            $client_do_tariff = $client->doTariff;
        }


        // echo "field_name=$field_name ";
        //echo "client:<pre>"; print_r($client); echo "</pre>";
        if($field_name == 'mobile_phone' && empty($client->id)) {

            $advertising_source_report = new AdvertisingSourceReport();
            $new_client_question_html = $this->renderAjax('advertising-source.php', [
                'model' => $advertising_source_report,
                //'client_id' => ($client != null ? $client->id : '')
                'phone' => $phone
            ]);
        }else {

            $new_client_question_html = '';
        }


        return [
            'success' => true,
            'order_id' => $last_order != null ? $last_order->id : null,
            'order' => $last_order,
            'client' => $client,
            'last_order' => $last_order,

            'yandexPointFrom' => $yandexPointFrom,
            'yandexPointTo' => $yandexPointTo,
            // эти поля должны/могут быть независимо от наличия объектов $yandexPointFrom/$yandexPointTo
            'yandex_point_from_id' => $yandex_point_from_id,
            'yandex_point_from_name' => $yandex_point_from_name,
            'yandex_point_from_lat' => $yandex_point_from_lat,
            'yandex_point_from_long' => $yandex_point_from_long,
            'yandex_point_to_id' => $yandex_point_to_id,
            'yandex_point_to_name' => $yandex_point_to_name,
            'yandex_point_to_lat' => $yandex_point_to_lat,
            'yandex_point_to_long' => $yandex_point_to_long,

            'message' => ($client != null ? $client->getNearestOrdersMsg($current_order_date, $current_order_id) : ''),
            'informer_office' => $informer_office,
            'client_do_tariff' => $client_do_tariff,

            'new_client_question_html' => $new_client_question_html
        ];
    }

    public function actionAjaxSaveAdvertisingSourceReport() {

        Yii::$app->response->format = 'json';

        $post = Yii::$app->request->post();

        if($post['AdvertisingSourceReport']['phone'] != '+') {
            $post['AdvertisingSourceReport']['phone'] = '+'.trim($post['AdvertisingSourceReport']['phone']);
        }

        $model = new AdvertisingSourceReport();
        if ($model->load($post) && $model->save()) {
            return [
                'success' => true,
            ];
        }else {
            return [
                'success' => false,
                'errors' => $model->getErrors()
            ];
        }
    }

    /*
     * Показ формы с данными клиента найденного по номеру телефона
     */
    public function actionAjaxGetClientForm($mobile_phone) {

        Yii::$app->response->format = 'json';

        $client = Client::getClientByMobilePhone($mobile_phone);
        if($client == null) {
            throw new ForbiddenHttpException('Клиента с таким номером телефона не существует');
        }

        return $this->renderAjax('client-data-form', [
            'client' => $client
        ]);
    }


    /*
     * Функция изменяем какой-либо поле модели order и возвращает ответ в элемент kartik\editable\Editable::widget
     */
    public function actionEditableClient($id)
    {
        Yii::$app->response->format = 'json';

        $client = $this->findModel($id);

        if (isset($_POST['hasEditable']))
        {
            if(isset($_POST['name']))
            {
                $client->name = Yii::$app->request->post('name');
                if(!empty($client->name)) { // устанавливаем значение

                    if($client->validate() == true) {
                        $client->setField('name', $client->name);
                        $client->setField('sync_date', null);

                        return ['output' => $client->name, 'message' => ''];
                    }else {
                        throw new ForbiddenHttpException(implode('. ', $client->getErrors('name')));
                    }

                }else { // очищаем значение
                    $client->setField('name', '');
                    $client->setField('sync_date', null);
                    return ['output' => '', 'message' => ''];
                }

            }elseif(isset($_POST['mobile_phone'])) {

                $client->mobile_phone = Yii::$app->request->post('mobile_phone');

                if($client->validate() == true) {
                    $client->setField('mobile_phone', $client->mobile_phone);
                    $client->setField('sync_date', null);
                    return ['output' => $client->mobile_phone, 'message' => ''];
                }else {
                    throw new ForbiddenHttpException(implode('. ', $client->getErrors('mobile_phone')));
                }


            }elseif(isset($_POST['home_phone'])) {

                $client->home_phone = Yii::$app->request->post('home_phone');
                if(!empty($client->home_phone)) { // устанавливаем значение

                    if($client->validate() == true) {
                        $client->setField('home_phone', $client->home_phone);
                        $client->setField('sync_date', null);
                        return ['output' => $client->home_phone, 'message' => ''];
                    }else {
                        throw new ForbiddenHttpException(implode('. ', $client->getErrors('home_phone')));
                    }

                }else { // очищаем значение
                    $client->setField('home_phone', '');
                    $client->setField('sync_date', null);
                    return ['output' => '', 'message' => ''];
                }

            }elseif(isset($_POST['alt_phone'])) {

                $client->alt_phone = Yii::$app->request->post('alt_phone');
                if(!empty($client->alt_phone)) { // устанавливаем значение

                    if($client->validate() == true) {
                        $client->setField('alt_phone', $client->alt_phone);
                        $client->setField('sync_date', null);
                        return ['output' => $client->alt_phone, 'message' => ''];
                    }else {
                        throw new ForbiddenHttpException(implode('. ', $client->getErrors('alt_phone')));
                    }

                }else { // очищаем значение
                    $client->setField('alt_phone', '');
                    $client->setField('sync_date', null);
                    return ['output' => '', 'message' => ''];
                }

            }else {
                return ['output' => '', 'message'=>'Неизвестное поле'];
            }


        }else {
            throw new ForbiddenHttpException('Формат запроса не верен');
        }
    }

    /**
     * Finds the Client model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Client the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Client::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionView($id)
    {
        $client = $this->findModel($id);

        $searchModelCurYear = new OrderSearch();
        $dataProviderCurYear = $searchModelCurYear->clientSearch($client->id, Yii::$app->request->queryParams, 'current_year');

        $searchModelPastYears = new OrderSearch();
        $dataProviderPastYears = $searchModelPastYears->clientSearch($client->id, Yii::$app->request->queryParams, 'past_years');

        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->clientSearch($client->id, Yii::$app->request->queryParams);



        return $this->render('view', [
            'client' => $client,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'searchModelCurYear' => $searchModelCurYear,
            'dataProviderCurYear' => $dataProviderCurYear,
            'searchModelPastYears' => $searchModelPastYears,
            'dataProviderPastYears' => $dataProviderPastYears,
        ]);
    }


//    public function actionAjaxSetPenalty($order_id) {
//
//        Yii::$app->response->format = 'json';
//
//        $order = Order::findOne($order_id);
//        if($order == null) {
//            throw new ForbiddenHttpException('Заказ не найден');
//        }
//
//        $client = $order->client;
//        if($client == null) {
//            throw new ForbiddenHttpException('Клиент не найден');
//        }
//
//        $order->setField('has_penalty', 1);
//
//        $client->penalty = $client->penalty + 1;
//        $client->setField('penalty', $client->penalty);
//
//        return [
//            'success' => true,
//        ];
//    }
}
