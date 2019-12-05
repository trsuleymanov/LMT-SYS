<?php

namespace app\commands;

use app\models\Client;
use app\models\Order;
use app\models\Trip;
use yii\base\ErrorException;
use yii\console\Controller;
use app\models\YandexPoint;
use app\models\City;
use yii\db\Query;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;


class ClientController extends Controller
{
    /*
     * Функция импортирует данные файла в таблицу client
     * команда: php yii client/import-file
     *
     * Что  есть в строке: Фамилия / мобильный в формате без "+7" / домашний в формате nnnnnn / количество совершенных поездок
     * Как обрабатывать строку при первом вхождении: Фамилия -> ФИО с большой буквы / мобильный в формате без "+7"
     * -> сделать так, чтобы представление в ПО было нормальным / домашний в формате nnnnnn -> +7-855-3nn-nnnn / количество совершенных поездок
     * Как выполнять слияние дубликатов:
     * первое вхождение в БД дает фамилию, мобильный номер, домашний номер. При повторных вхождениях
     * мобильного номера фамилия созраняется, значение количества совершенных поездок суммируется
     */
    public function actionImportFile()
    {
        // предварительная очистка таблицы yandex_point
//        $sql = 'TRUNCATE `'.YandexPoint::tableName().'`';
//        Yii::$app->db->createCommand($sql)->execute();


        $file = \Yii::$app->basePath.'/commands/resources/Клиенты_с_дублями.csv';
        $file_content = file_get_contents($file);
        $aRows = explode("\n", $file_content);

        //echo "aRows:<pre>"; print_r($aRows); echo "</pre>";

        $aClients = [];
        foreach($aRows as $aRow) {
            $aRowFields = explode(';', $aRow);

            $fio = $aRowFields[0];  // client.name
            $mobile = self::convertMobilePhone($aRowFields[1]);  // client.mobile_phone
            $home_phone = self::convertHomePhone($aRowFields[2]);  // client.home_phone
            $sended_orders_places_count = $aRowFields[3];

            if(isset($aClients[$mobile])) {
                $aClients[$mobile]['sended_orders_places_count'] += intval($sended_orders_places_count);
            }else {
                $aClients[$mobile] = [
                    'name' => mb_convert_case($fio, 2, 'UTF-8'),
                    'mobile_phone' => $mobile,
                    'home_phone' => $home_phone,
                    'sended_orders_places_count' => intval($sended_orders_places_count)
                ];
            }
        }

        // загружаю клиентов по одной записи - слишком медленно работает
//        foreach($aClients as $aClient) {
//            $client = new Client();
//            $client->name = $aClient['name'];
//            $client->mobile_phone = $aClient['mobile_phone'];
//            $client->home_phone = $aClient['home_phone'];
//            $client->sended_orders_places_count = $aClient['sended_orders_places_count'];
//
//            if(!$client->save(false)) {
//                throw new ErrorException('Не удалось создать клиента с мобильным: '.$aClient['mobile_phone']);
//            }
//        }

        // разбиваю по 100 строк
        $aStepsClients = [];
        $num = 0;
        foreach($aClients as $aClient) {
            $aStepsClients[$num][] = $aClient;
            if(count($aStepsClients[$num]) == 100) {
                $num++;
            }
        }

        // формирование mysql-запросов
        $n = 0;
        foreach($aStepsClients as $aStepClients) {

            $aSqlValues = [];
            foreach($aStepClients as $aClient) {
                $aSqlValues[] =
                    '('
                    .'"'.$aClient['name'].'", '
                    .'"'.$aClient['mobile_phone'].'", '
                    .'"'.$aClient['home_phone'].'", '
                    .$aClient['sended_orders_places_count']
                    .')';
            }
            $sql = 'INSERT INTO `'.Client::tableName().'` (`name`, `mobile_phone`, `home_phone`, `sended_orders_places_count`) VALUES '.implode(',', $aSqlValues);

            Yii::$app->db->createCommand($sql)->execute();


            echo ++$n."-ая сотня загружена\n";
        }
        echo "готово\n";
    }


    /*
     * Создание в таблице небольшого кол-ва клиентов
     *
     * команда: php yii client/add-clients
     */

    public static function convertMobilePhone($mobile) {

        if(empty($mobile)) {
            return '';
        }

        $mobile_1 = substr($mobile, 0, 3);
        $mobile_2 = substr($mobile, 3, 3);
        $mobile_3 = substr($mobile, 6);

        return '+7-'.$mobile_1.'-'.$mobile_2.'-'.$mobile_3;
    }

    public static function convertHomePhone($home_phone) {

        if(empty($home_phone)) {
            return '';
        }

        $home_phone_1 = substr($home_phone, 0, 3);
        $home_phone_2 = substr($home_phone, 3);

        return '+7-855-3'.$home_phone_1.'-'.$home_phone_2;
    }

    public function actionAddClients() {

        $aClients = [
            ['mobile_phone' => '9874204764', 'name' => 'Юсуленова', 'sended_prize_trip_count' => 1],
            ['mobile_phone' => '9874106122', 'name' => 'Кучмарев', 'sended_prize_trip_count' => 1],
            ['mobile_phone' => '9872712656', 'name' => 'Рыжикова', 'sended_prize_trip_count' => 1],
            ['mobile_phone' => '9871825842', 'name' => 'Макарова', 'sended_prize_trip_count' => 1],
            ['mobile_phone' => '9863202082', 'name' => 'Ахуньянова', 'sended_prize_trip_count' => 1],
            ['mobile_phone' => '9518976708', 'name' => 'Булатова', 'sended_prize_trip_count' => 1],
            ['mobile_phone' => '9393909939', 'name' => 'Волков', 'sended_prize_trip_count' => 1],
            ['mobile_phone' => '9276720814', 'name' => 'Хабибуллин', 'sended_prize_trip_count' => 1],
            ['mobile_phone' => '9274865345', 'name' => 'Фархутдинова', 'sended_prize_trip_count' => 2],
            ['mobile_phone' => '9274374371', 'name' => 'Повадырева', 'sended_prize_trip_count' => 1],
            ['mobile_phone' => '9274335688', 'name' => 'Белоусова', 'sended_prize_trip_count' => 2],
            ['mobile_phone' => '9199794071', 'name' => 'Абдулкагиров', 'sended_prize_trip_count' => 1],
            ['mobile_phone' => '9196927427', 'name' => 'Равилова', 'sended_prize_trip_count' => 1],
            ['mobile_phone' => '9196248914', 'name' => 'Китанова', 'sended_prize_trip_count' => 1],
            ['mobile_phone' => '9179633909', 'name' => 'Нестерова', 'sended_prize_trip_count' => 1],
            ['mobile_phone' => '9179340736', 'name' => 'Асылгараева', 'sended_prize_trip_count' => 1],
            ['mobile_phone' => '9179185323', 'name' => 'Султанова', 'sended_prize_trip_count' => 1],
            ['mobile_phone' => '9178789662', 'name' => 'Бозырева', 'sended_prize_trip_count' => 1],
            ['mobile_phone' => '9178527799', 'name' => 'Низамеева', 'sended_prize_trip_count' => 1],
            ['mobile_phone' => '9172921714', 'name' => 'Заляева', 'sended_prize_trip_count' => 1],
            ['mobile_phone' => '9172730821', 'name' => 'Губайдуллин', 'sended_prize_trip_count' => 1],
            ['mobile_phone' => '9172506623', 'name' => 'Гараева', 'sended_prize_trip_count' => 1],
            ['mobile_phone' => '9172342824', 'name' => 'Хазиахметова', 'sended_prize_trip_count' => 1],
            ['mobile_phone' => '9061205614', 'name' => 'Богданов', 'sended_prize_trip_count' => 1],
            ['mobile_phone' => '9053742025', 'name' => 'Яковлева', 'sended_prize_trip_count' => 2],
        ];

        foreach($aClients as $aClient) {
            $client = new Client();
            $client->name = $aClient['name'];
            $client->mobile_phone = self::convertMobilePhone($aClient['mobile_phone']);
            $client->sended_prize_trip_count = $aClient['sended_prize_trip_count'];
            if(!$client->save(false)) {
                echo "не удалось создать клиента с телефоном ".$client->mobile_phone."\n";
            }
        }

        echo "готово\n";
    }

    public function actionImportPrizeFile() {

        $file = \Yii::$app->basePath.'/commands/resources/Призовые.csv';
        $file_content = file_get_contents($file);
        $aRows = explode("\n", $file_content);

        $aMobilePrizes = [];
        foreach($aRows as $sMobile) {

            $mobile_phone = self::convertMobilePhone(trim($sMobile));

            if(isset($aMobilePrizes[$mobile_phone])) {
                $aMobilePrizes[$mobile_phone]++;
            }else {
                $aMobilePrizes[$mobile_phone] = 1;
            }
        }


        $i = 0;
        foreach($aMobilePrizes as $mobile_phone => $prize_count) {

            $i++;
            $client = Client::getClientByMobilePhone($mobile_phone);
            if($client == null) {
                //throw new ForbiddenHttpException("Клиент с номером $mobile_phone не найден \n");
                echo "Клиент с номером $mobile_phone не найден \n";
            }else {
                $sql = 'UPDATE `client` SET sended_prize_trip_count = '.$prize_count.' WHERE id = '.$client->id;
                Yii::$app->db->createCommand($sql)->execute();
                //echo "клиент с id=".$client->id." получил аттрибут ".$prize_count."\n";
            }

            if(floor($i/100) == $i/100) {
                echo $i." штук загружено \n";
            }
        }

        echo "готово\n";
    }

    /*
     * Пересчет кэш-бэков клиентов по отправленным за последние сутки рейсам
     * команда: php yii client/recount-cashback
     */
    public function actionRecountCashback()
    {
        // по всем рейсам отправленным за последние сутки идет пересчет кэш-бэков клиентов
        // последние сутки - это с 01:00 прошлого дня по 01:00 текущего дня
        // начисляются кэш-бэки с заказов и списываются штрафные кэш-бэки за отмененные заказы, а вот
        // использованные кэш-бэки должны со счета списываться в момент оплаты за заказ.
        $end_unixdate = strtotime(date('d.m.Y').'01:00:00');
        $start_unixdate = $end_unixdate - 86400;


        $trips = Trip::find()
            ->where(['<', 'date_sended', $end_unixdate])
            ->andWhere(['>=', 'date_sended', $start_unixdate])
            ->all();


        $clients_recount_count = 0;
        if(count($trips) > 0) {
            $orders = Order::find()->where(['trip_id' => ArrayHelper::map($trips, 'id', 'id')])->all();
            $clients = Client::find()->where(['id' => ArrayHelper::map($orders, 'client_id', 'client_id')])->all();

            $aClients = ArrayHelper::index($clients, 'id');
            foreach($orders as $order) {
                if(empty($order->client_id)) {
                    continue;
                }

                $client = $aClients[$order->client_id];

                $client_modification_cashback = 0;
                if($order->accrual_cash_back > 0) { // начисленный кэш-бэк за заказ
                    $client_modification_cashback += $order->accrual_cash_back;
                }
//                if($order->used_cash_back > 0) {
//                    $client_modification_cashback -= $order->used_cash_back;
//                }
                if($order->penalty_cash_back > 0) { // штрафной кэш-бэк за отмену заказа
                    $client_modification_cashback -= $order->penalty_cash_back;
                }

                if($client_modification_cashback != 0) {
                    $client->cashback += $client_modification_cashback;
                    $client->setField('cashback', $client->cashback);
                    $client->setField('sync_date', null);
                    $clients_recount_count++;
                }
            }
        }

        echo "готово. Пересчитано $clients_recount_count клиентов.\n";
    }
}
