<?php

namespace app\modules\admin\controllers;

use app\models\Call;
use app\models\Client;
use app\models\CreateTestOrders;
use app\models\DayReportTripTransport;
use app\models\DispatcherAccounting;
use app\models\Order;
use app\models\OrderReport;
use app\models\OrderStatus;
use app\models\SecondTripTransport;
use app\models\Trip;
use app\models\TripTransport;
use Yii;
use app\models\Point;
use app\models\PointSearch;
use yii\base\ErrorException;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\OrderSearch;

/**
 * Отчеты
 */
class RescueController extends Controller
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
     * Отчет дня
     */
    public function actionDayPrint()
    {
//        if(!in_array(Yii::$app->session->get('role_alias'), ['root', 'admin'])) {
//            throw new ForbiddenHttpException('Доступ запрещен');
//        }

        $query_params = Yii::$app->request->queryParams;
        if(!isset($query_params['OrderSearch']['date']) || empty($query_params['OrderSearch']['date'])) {
            $query_params['OrderSearch']['date'] = date('d.m.Y');
        }

        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->searchDayPrint($query_params);

        return $this->render('day-print', [
            'unixdate' => strtotime($query_params['OrderSearch']['date']),
            //'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreateTestOrders() {

        $model = new CreateTestOrders();
        $model->date = strtotime(date('d.m.Y', time()));

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->generateOrders()) {

            Yii::$app->session->setFlash('successResult', 'Заказы созданы');
            return $this->refresh();

        } else {
//            echo "не удалось создать заказы:";
//            echo "<pre>"; print_r($model); echo "</pre>";
            return $this->render('create-test-orders', [
                'model' => $model,
            ]);
        }

    }

    public function actionActions() {

        return $this->render('actions');
    }

    public function actionAjaxDumpDatabase() {

        Yii::$app->response->format = 'json';

        $db_name = substr(Yii::$app->db->dsn, strpos(Yii::$app->db->dsn, 'dbname=') + 7);
        $dir_path = '/var/www/tobus-yii2/backups/';
        if(!file_exists($dir_path)) {
            if (!mkdir($dir_path, 0777, true)) {
                throw new ErrorException('Не удалось создать на сервере директорию '.$dir_path);
            }
        }

        $file_name = $db_name.'_'.mb_strtolower(date('dMY'), 'UTF-8').'.sql';
        $command = 'mysqldump -u'.Yii::$app->db->username.' -p'.Yii::$app->db->password.' '.$db_name.' > '.$dir_path.$file_name;
        exec($command, $output);
        //exit($dir_path.$file_name);
        if(!chmod($dir_path.$file_name, 0777)) {
            throw new ErrorException('Не удалось установить доступы для файла');
        }

        return [
            'success' => true,
            'file_href' => 'http://'.$_SERVER['HTTP_HOST'].'/admin/rescue/download-backup?filename='.$file_name
        ];
    }

    // https://adw0rd.com/2009/06/07/mysqldump-and-cheat-sheet/
    // пример: mysqldump -uroot -p123456 tobus_yii2_server   --tables order_status passenger > /var/www/tobus-yii2/backups/test.sql
    public function actionAjaxDumpStorage() {

        Yii::$app->response->format = 'json';

        $db_name = substr(Yii::$app->db->dsn, strpos(Yii::$app->db->dsn, 'dbname=') + 7);
        $dir_path = '/var/www/tobus-yii2/backups/';
        if(!file_exists($dir_path)) {
            if (!mkdir($dir_path, 0777, true)) {
                throw new ErrorException('Не удалось создать на сервере директорию '.$dir_path);
            }
        }

        $aTables = [
            'detail_measurement_value',
            'driver',
            'nomenclature_detail',
            'storage',
            'storage_detail',
            'storage_operation',
            'storage_operation_type',
            'transport',
            'transport_detail_origin',
            'transport_detail_state',
            'transport_model',
        ];

        $file_name = 'storage_tables_'.mb_strtolower(date('dMY'), 'UTF-8').'.sql';
        $command = 'mysqldump -u'.Yii::$app->db->username.' -p'.Yii::$app->db->password.' '.$db_name.
            ' --tables '.implode(' ', $aTables).' > '.$dir_path.$file_name;
        exec($command, $output);
        if(!chmod($dir_path.$file_name, 0777)) {
            throw new ErrorException('Не удалось установить доступы для файла');
        }

        return [
            'success' => true,
            'file_href' => 'http://'.$_SERVER['HTTP_HOST'].'/admin/rescue/download-backup?filename='.$file_name
        ];
    }


    public function actionDownloadBackup($filename) {

        ini_set('max_execution_time', '600');
        header('Content-Type: application/x-force-download; name="'.$filename.'"');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        readfile('/var/www/tobus-yii2/backups/'.$filename);


//        $filename = '/var/www/tobus-yii2/backups/'.$filename;
//        $mimetype = 'application/octet-stream';
//
//        header($_SERVER["SERVER_PROTOCOL"] . ' 200 OK');
//        header('Content-Type: ' . $mimetype);
//        header('Last-Modified: ' . gmdate('r', filemtime($filename)));
//        header('ETag: ' . sprintf('%x-%x-%x', fileinode($filename), filesize($filename), filemtime($filename)));
//        // Размер файла
//        header('Content-Length: ' . (filesize($filename)));
//        header('Connection: close');
//        // Имя файла, как он будет сохранен в браузере или в программе закачки.
//        // Без этого заголовка будет использоваться базовое имя скрипта PHP.
//        // Но этот заголовок не нужен, если вы используете mod_rewrite для
//        // перенаправления запросов к серверу на PHP-скрипт
//        header('Content-Disposition: attachment; filename="' . basename($filename) . '";');
//
//        // Отдаем содержимое файла
//        echo file_get_contents($filename);
    }

    public function actionChangeDateTripsOrders() {

        if(Yii::$app->request->isAjax) {
            Yii::$app->response->format = 'json';

            $date = Yii::$app->request->post('date');

            // дата самого раннего заказа
            $first_order = Order::find()->orderBy(['date' => SORT_ASC])->one();
            if($first_order == null) {
                throw new ForbiddenHttpException('Самый ранний заказ не найден');
            }
            $time_change = strtotime($date) - $first_order->date;
            if($time_change <= 0) {
                throw new ForbiddenHttpException('Дата может быть смещена только в будущее');
            }

            // 0. Изначально после заливки новой базы должны быть выполнены все миграции
            // 1. Сбросим счетчики у клиентов
            $sql = 'UPDATE `'.Client::tableName().'`
                    SET sended_orders_places_count=0, sended_prize_trip_count=0,
                        sended_fixprice_orders_places_count=0, canceled_orders_places_count=0,
                        sended_is_not_places_order_count=0, penalty=0';
            Yii::$app->db->createCommand($sql)->execute();

            // 2.1. таблица текущего дня должна быть очищена
            $sql = 'TRUNCATE `'.DayReportTripTransport::tableName().'`';
            Yii::$app->db->createCommand($sql)->execute();

            // 2.2. заодно очистим действия оператора
            $sql = 'TRUNCATE `'.DispatcherAccounting::tableName().'`';
            Yii::$app->db->createCommand($sql)->execute();

            // 2.3. заодно очистим отчет
            $sql = 'TRUNCATE `'.OrderReport::tableName().'`';
            Yii::$app->db->createCommand($sql)->execute();


            // 3.1. Даты заказов смещаем на time
//            $sql = '
//                UPDATE `'.Order::tableName().'`
//                SET
//                    cancellation_click_time = cancellation_click_time + '.$time_change.',
//                    `date` = date + '.$time_change.',
//                    time_sat = time_sat + '.$time_change.',
//                    time_confirm = time_confirm + '.$time_change.',
//                    time_vpz = time_vpz + '.$time_change.',
//                    first_writedown_click_time = first_writedown_click_time + '.$time_change.',
//                    first_confirm_click_time = first_confirm_click_time + '.$time_change;
            $sql = '
                UPDATE `'.Order::tableName().'`
                SET
                    cancellation_click_time = cancellation_click_time + '.$time_change.',
                    `date` = date + '.$time_change.',
                    time_sat = time_sat + '.$time_change.',
                    time_confirm = time_confirm + '.$time_change.',
                    first_writedown_click_time = first_writedown_click_time + '.$time_change.',
                    first_confirm_click_time = first_confirm_click_time + '.$time_change;
            Yii::$app->db->createCommand($sql)->execute();

            // 3.2. Статусы отправленных заказов меняем на созданные заказы и установим КЗМ отправленным заказам
            $sent_order_status = OrderStatus::getByCode('sent');
            $created_order_status = OrderStatus::getByCode('created');
            $sql = 'UPDATE `'.Order::tableName().'`
                    SET status_id='.$created_order_status->id.', confirm_selected_transport = 1'
                    .' WHERE status_id='.$sent_order_status->id;
            Yii::$app->db->createCommand($sql)->execute();


//            ----------
//            расписания schedule не буду трогать...
//            даты запуска тарифов tariff не трогаю...
//            ----------

            // 4. в таблице вторых транспортов second_trip_transport тоже нужно сместить даты.
            $sql = 'UPDATE `'.SecondTripTransport::tableName().'` SET `date` = `date` + '.$time_change;
            Yii::$app->db->createCommand($sql)->execute();

            // 5. рейсы делаю не отправленными и сдвигаю даты
            $sql = 'UPDATE `'.Trip::tableName().'` SET `date`=`date` + '.$time_change.', date_sended = NULL, sended_user_id = NULL';
            Yii::$app->db->createCommand($sql)->execute();


            // 6. все т/с должны стать не отправленными
            $sql = 'UPDATE `'.TripTransport::tableName().'` SET status_id=0, date_sended=NULL,
                    sender_id=NULL, set_date_time = set_date_time + '.$time_change.',
                    confirmed_date_time = confirmed_date_time + '.$time_change;
            Yii::$app->db->createCommand($sql)->execute();

            return [
                'success' => true
            ];

        }else {
            return $this->render('change-date-trips-orders-form');
        }
    }

    public function actionMoveRecords() {
        return $this->render('move-records');
    }

    public function actionAjaxMoveRecords() {

        Yii::$app->response->format = 'json';

        $ftp_server = Yii::$app->request->post('ftp_server'); // 185.148.219.40
        $ftp_login = Yii::$app->request->post('ftp_login'); // dahua
        $ftp_password = Yii::$app->request->post('ftp_password'); // EZeKNTwD
        $ftp_dir_path = Yii::$app->request->post('ftp_path'); // /BEEREC
        $beeline_token_api = Yii::$app->request->post('beeline_token_api'); // cda7719f-1dcc-46f6-937a-23f1d46dcf75


        // получаем первых 100 id записей
        $aRecordsId = Call::getRecordList($beeline_token_api);

        // для первых 10 записей скачиваем файлы записей и сохраняем здесь на сервере
        $aFilesNames = [];
        $current_server_files_dir_path = '/var/www/tobus-yii2/web/records/';
        for($i = 0; $i < 10; $i++) {
            $record_id = $aRecordsId[$i];
            $record_file_content = Call::getDownloadRecord($beeline_token_api, $record_id);

            $fp = fopen($current_server_files_dir_path.$record_id.'.mp3', "w");
            fwrite($fp, $record_file_content);
            fclose($fp);

            $aFilesNames[] = $record_id.'.mp3';
        }

        // перенос файлов по ftp на другой сервер
        Call::moveFilesToServer($ftp_server, $ftp_login, $ftp_password, $ftp_dir_path, $current_server_files_dir_path, $aFilesNames);

        // удаление файлов на текущем сервере
        foreach($aFilesNames as $file_name) {
            unlink($current_server_files_dir_path.$file_name);
        }

        return [
            'success' => true
        ];
    }
}
