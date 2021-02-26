<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\base\ErrorException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\User;
use app\models\Client;
use app\models\Order;
use app\models\Trip;
use app\models\TripTransport;
use app\models\DayReportTripTransport;

/**
 * Контроллер для отдельной страницы с кнопками не для общего пользования
 */
class ZerostateController extends Controller
{
    public function actionIndex()
    {
        $user = User::findOne(Yii::$app->user->id);
        $user_role = $user->userRole;
        if($user_role->alias != 'admin') {
            throw new ForbiddenHttpException('Доступ к странице запрещен');
        }

        return $this->render('index', [

        ]);
    }

    /*
     * Очистка таблицы заказов (order), а также обнуление в таблице клиентов:
     *  кол-ва заказов, призовых поездок и штрафов
     */
    public function actionAjaxClearOrder()
    {
        Yii::$app->response->format = 'json';

        $user = User::findOne(Yii::$app->user->id);
        $user_role = $user->userRole;
        if($user_role->alias != 'admin') {
            throw new ForbiddenHttpException('Доступ к странице запрещен');
        }

        $sql = 'UPDATE `'.Client::tableName().'` SET sended_orders_places_count=0, sended_prize_trip_count=0, penalty=0, current_year_penalty=0';
        Yii::$app->db->createCommand($sql)->execute();

        Yii::$app->db->createCommand()->truncateTable(Order::tableName())->execute();

        return true;
    }

    /*
     * Очистка таблицы рейсов (trip), а также очистка полей связанных данных:
     *      `order`.trip_id и `trip_transport`.trip_id
     */
    public function actionAjaxClearTrip()
    {
        Yii::$app->response->format = 'json';

        $user = User::findOne(Yii::$app->user->id);
        $user_role = $user->userRole;
        if($user_role->alias != 'admin') {
            throw new ForbiddenHttpException('Доступ к странице запрещен');
        }

        $sql = 'UPDATE `'.Order::tableName().'` SET trip_id=NULL';
        Yii::$app->db->createCommand($sql)->execute();

        $sql = 'UPDATE `'.TripTransport::tableName().'` SET trip_id=NULL';
        Yii::$app->db->createCommand($sql)->execute();

        Yii::$app->db->createCommand()->truncateTable(Trip::tableName())->execute();

        return true;
    }

    /*
     * Очистка таблицы машин на рейсе (trip_transport), а также очистка полей связанных данных:
     *  `order`.fact_trip_transport_id
     */
    public function actionAjaxClearTripTransport()
    {
        Yii::$app->response->format = 'json';

        $user = User::findOne(Yii::$app->user->id);
        $user_role = $user->userRole;
        if($user_role->alias != 'admin') {
            throw new ForbiddenHttpException('Доступ к странице запрещен');
        }

        $sql = 'UPDATE `'.Order::tableName().'` SET confirm_selected_transport=0, fact_trip_transport_id=NULL, time_sat=NULL';
        Yii::$app->db->createCommand($sql)->execute();

        Yii::$app->db->createCommand()->truncateTable(TripTransport::tableName())->execute();

        return true;
    }

    /*
     * Очистка таблицы отчета отображаемого дня (day_report_trip_transport)
     */
    public function actionAjaxClearDayReport()
    {
        Yii::$app->response->format = 'json';

        $user = User::findOne(Yii::$app->user->id);
        $user_role = $user->userRole;
        if($user_role->alias != 'admin') {
            throw new ForbiddenHttpException('Доступ к странице запрещен');
        }

        Yii::$app->db->createCommand()->truncateTable(DayReportTripTransport::tableName())->execute();

        return true;
    }

    /*
     * Очистка таблицы клиентов (client), а также связанных данных: `order`.client_id
     */
    public function actionAjaxClearClient()
    {
        Yii::$app->response->format = 'json';

        $user = User::findOne(Yii::$app->user->id);
        $user_role = $user->userRole;
        if($user_role->alias != 'admin') {
            throw new ForbiddenHttpException('Доступ к странице запрещен');
        }

        $sql = 'UPDATE `'.Order::tableName().'` SET client_id=0';
        Yii::$app->db->createCommand($sql)->execute();

        Yii::$app->db->createCommand()->truncateTable(Client::tableName())->execute();

        return true;
    }
}