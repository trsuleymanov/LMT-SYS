<?php

/*
 * Глобальный демон управляющий всеми запросами от/в браузеры и от/на сервер с помощью канала
 *
 * https://habr.com/post/331462/
 *
 * Запуск: php commands/socket-demon.php start
 *
 * php commands/socket-demon.php start
 * php commands/socket-demon.php start -d -демонизировать скрипт
 * php commands/socket-demon.php status
 * php commands/socket-demon.php stop
 * php commands/socket-demon.php restart
 * php commands/socket-demon.php restart -d
 * php commands/socket-demon.php reload
 *
 *
 * Найти демонов: sudo netstat -tulpn | grep :19840
 * Убить процесс: kill 1234
 * Или:  kill -KILL 1234
 *
 * 127.0.0.1:5555 - для внутренного общения на сервере
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../models/SocketDemon.php';
require_once __DIR__ . '/../models/SocketPage.php';
require_once __DIR__ . '/../models/SocketDevice.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/DriverOperatorChat.php';
//require_once __DIR__ . '/../models/TripTransport.php';
//require_once __DIR__ . '/../models/Driver.php';

//require_once '/etc/ssl/certs/server.pem';
//require_once '/etc/ssl/private/server.key';


$db = require(__DIR__ . '/../config/db.php');
$params = require __DIR__ . '/../config/params.php';


use app\models\DriverOperatorChat;
//use app\models\TripTransport;
use app\models\User;
use Workerman\Worker;
use app\models\SocketDemon;


$socket_demon = new SocketDemon();
$buffer = [];  // очеред для сообщений от сервера на порт 5555 - очередь нужна чтобы удалять одинаковые сообщения
$json_message_parts = '';

// создаём ws-сервер, к которому будут подключаться все наши пользователи
//$ws_worker = new Worker("websocket://0.0.0.0:8000"); // 192.168.1.0:8900
//$ws_worker = new Worker("websocket://185.148.219.40:8900");
//$ws_worker = new Worker("websocket://192.168.1.0:8900");
//$ws_worker = new Worker("websocket://0.0.0.0:8900");
//$ws_worker = new Worker("websocket://127.0.0.1:80");
//$ws_worker = new Worker("websocket://192.168.1.37:80");
//$ws_worker = new Worker("websocket://0.0.0.0:19840");


// SSL context.
//$context = [
//    'ssl' => [
//        'local_cert'  => '/etc/ssl/certs/server.pem',
//        'local_pk'    => '/etc/ssl/private/server.key',
//        'verify_peer' => false,
//    ]
//];


$ws_worker = new Worker($params['socketDemonOutUrl']);
//$ws_worker = new Worker($params['socketDemonOutUrl'], $context);

// Enable SSL. WebSocket+SSL means that Secure WebSocket (wss://).
// The similar approaches for Https etc.
//$ws_worker->transport = 'ssl';

// создаём обработчик, который будет выполняться при запуске ws-сервера
$ws_worker->onWorkerStart = function() use (&$socket_demon, &$buffer, &$params, &$json_message_parts)
{
    // создаём локальный tcp-сервер, чтобы отправлять на него сообщения из кода нашего сайта
    //$inner_tcp_worker = new Worker("tcp://127.0.0.1:5555");
    $inner_tcp_worker = new Worker($params['socketDemonInnerUrl']);
    // создаём обработчик сообщений, который будет срабатывать когда на локальный tcp-сокет приходит сообщение

    // !!! сюда на один и тот же $connection за 1 секунду с одинаковыми данными могут поступить несколько запросов
    $inner_tcp_worker->onMessage = function($connection, $data) use (&$socket_demon, &$buffer, &$json_message_parts) {

        //echo "пришло сообщение в сокет-демона\n";

        if(strrpos($data, "\n") !== false) {
            //echo "есть символ переноса строки\n";

            if($json_message_parts != '') {
                $json_message_parts .= $data;
                $data = $json_message_parts;
                $json_message_parts = '';
            }

        }else {
            //echo "нет символа переноса строки\n";
            //$buffer = $data;
            $json_message_parts .= $data;
            return;
        }

        $data = json_decode($data);
        //echo "command=".$data->command."\n";
        //echo "выполнено json_decode\n";
        //echo "page_url=".$data->page_url."\n";

        //echo "onMessageFromServer\n";
        //echo $data->page_url . $data->command.$data->data->call_id.$data->data->event_name . time()."\n";
        //echo "data:<pre>"; print_r($data); echo "</pre>";
//        $temp_data = $data;
//        $xz='';
//        if(isset($temp_data->data)) {
//            $xz = $temp_data->data;
//            $temp_data->data = 'test';
//        }
//        echo "temp_data:<pre>"; print_r($temp_data); echo "</pre>";
//        if(isset($data->data)) {
//            $data->data = $xz;
//        }


        if(isset($data->device_code)) {

            //$socket_demon->sendMessageToDevice($data->device_code, $data->message_type, $data->message, $data->user_fio, $data->message_id);
            $socket_demon->sendMessageToDevice($data->device_code, $data);
            // echo "отправлено сообщение ".$data->message." на устройство ".$data->device_code."\n";

            // в некоторых случаях генерируются сервером несколько одинаковых сообщений для каждого клиента, вот такие
            // сообщения за 1 секунду для каждого $connection не запускаются больше 1 раза.
        }elseif(
            isset($data->page_url)
            && isset($data->command)
            && $data->command != 'updateCall'
            && $data->command != 'openCallWindow'
            && !isset($buffer[$data->page_url.$data->command.time()])
        ) {

            $buffer[$data->page_url . $data->command . time()] = 1;

            // отправляем сообщение всем соединениям на странице
            $socket_demon->sendMessageToBrowserPage($data->page_url, $data->command, $data->data, $data->users);

        }elseif(
            isset($data->page_url)
            && isset($data->command)
            && $data->command == 'updateCall'
            && !isset($buffer[$data->page_url.$data->command.$data->data->call_id.$data->data->event_name.implode(',', $data->users).time()])
        ) {

            $buffer[$data->page_url.$data->command.$data->data->call_id.$data->data->event_name.implode(',', $data->users).time()] = 1;

            //echo "jn";

            // отправляем сообщение всем соединениям на странице
            $socket_demon->sendMessageToBrowserPage($data->page_url, $data->command, $data->data, $data->users);

        }elseif(
            isset($data->page_url)
            && isset($data->command)
            && $data->command == 'openCallWindow'
        ) {
            // отправляем сообщение всем соединениям на странице
            //echo "дальше перенаправляем команду openCallWindow\n";
            //echo " data:<pre>"; print_r($data); echo "</pre>";
            $socket_demon->sendMessageToBrowserPage($data->page_url, $data->command, $data->data, $data->users);
        }

        if(count($buffer) > 1000) {
            $buffer = []; // очистка буфера
        }
    };
    $inner_tcp_worker->listen();
};


$ws_worker->onConnect = function($connection) use(&$socket_demon, &$db) {

    echo "onConnect\n";
    // echo "GET_0:<pre>"; print_r($_GET); echo "</pre>";// здесь get`а нету, а внутри onWebSocketConnect есть
    //echo "POST:<pre>"; print_r($_POST); echo "</pre>";
    //echo "REQUEST:<pre>"; print_r($_REQUEST); echo "</pre>";
    //echo "headers:<pre>"; print_r(apache_response_headers()); echo "</pre>";
    //echo "SERVER:<pre>"; print_r($_SERVER); echo "</pre>";
    //echo "apache_request_headers:<pre>"; print_r(apache_request_headers()); echo "</pre>";

    $connection->onWebSocketConnect = function($connection) use(&$socket_demon, &$db) {

        echo "onWebSocketConnect\n";

        //echo "connection:<pre>"; print_r($connection); echo "</pre>";
        // echo "GET:<pre>"; print_r($_GET); echo "</pre>";

        if(isset($_GET['user'])) {
            if ($socket_demon->addBrowserConnection($connection, $_GET['url'], $_GET['user'])) {
                echo "Добавлено новое соединение с браузером. \n";
//                 echo "Добавлено новое соединение. Всего: ".$socket_demon->getCountBrowserConnections()." \n";
//                 echo "страниц используется ".count($socket_demon->socket_pages)."\n";
//                $users = [];
//                foreach($socket_demon->socket_pages as $socket_page) {
//                    foreach($socket_page->users_connections as $user_id => $user_connections) {
//                        $users[$user_id] = $user_id;
//                    }
//                }
//                echo "пользователей всего ".count($users)."\n";

            } else {
                echo "Новое соединение с браузером не удалось добавить \n";
            }

        }elseif(isset($_GET['device_name']) && isset($_GET['device_code'])) {
            if($socket_demon->addDeviceConnection($connection, $_GET['device_name'], $_GET['device_code'])) {
                echo "Добавлено новое соединение с мобильным устройством device_code=".$_GET['device_code'].". \n";

                // вытолкнем в устройство все незакрытые сообщения для приложения водителей связанные с текущим рейсом

                //echo "GET:<pre>"; print_r($_GET); echo "</pre>";
                if(isset($_GET['trip_transport_id']) && !empty($_GET['trip_transport_id'])) {

                    $trip_transport_id = intval($_GET['trip_transport_id']);

                    $opt = [
                        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
                        //PDO::ATTR_EMULATE_PREPARES   => false,
                    ];
                    $pdo = new PDO($db['dsn'], $db['username'], $db['password'], $opt);


                    $aDriverOperatorChats = [];
                    $sql =
                        'SELECT * FROM `'.DriverOperatorChat::tableName().'`
                         WHERE trip_transport_id='.$trip_transport_id.'
                         AND answer_from_operator_at > 0
                         AND driver_is_read_at = 0';
                    $stmt = $pdo->query($sql);
                    if($stmt != false) {
                        while ($row = $stmt->fetch()) {
                            $aDriverOperatorChats[] = $row;
                        }
                    }


                    $aOperators = [];
                    if(count($aDriverOperatorChats) > 0) {

                        $aOperatorsIds = [];
                        foreach($aDriverOperatorChats as $aDriverOperatorChat) {
                            $aOperatorsIds[$aDriverOperatorChat['operator_id']] = $aDriverOperatorChat['operator_id'];
                        }
                        $sql =
                            'SELECT * FROM `user`
                             WHERE id IN('.implode(',', $aOperatorsIds).')';
                        $stmt = $pdo->query($sql);
                        if($stmt != false) {
                            while ($row = $stmt->fetch()) {
                                $aOperators[$row['id']] = $row;
                            }
                        }
                    }
                    //echo "\n aOperators count=".count($aOperators)."\n";
                    //echo "aOperators:<pre>"; print_r($aOperators); echo "</pre>";

                    // ищем магическое устаройство у водителя
                    $magic_code = '';
                    $sql =
                        'SELECT `magic_device_code`.code
                        FROM `trip_transport`
                        LEFT JOIN `driver` ON `driver`.id = `trip_transport`.driver_id
                        LEFT JOIN `magic_device_code` ON `magic_device_code`.id = `driver`.magic_device_code_id
                        WHERE `trip_transport`.id='.$trip_transport_id;
                    $stmt = $pdo->query($sql);
                    if($stmt != false) {
                        $row = $stmt->fetch();
                        if(isset($row['code'])) {
                            $magic_code = $row['code'];
                        }
                    }


                    if(count($aDriverOperatorChats) > 0 && count($aOperators) > 0) {
                        foreach ($aDriverOperatorChats as $aDriverOperatorChat) {

                            $aOperator = $aOperators[$aDriverOperatorChat['operator_id']];

                            echo "отправляем сообщение устройству " . $_GET['device_code'] . "\n";
                            //SocketDemon::sendOutDeviceMessageInstant($_GET['device_code'], 'message', $aDriverOperatorChat['answer_from_operator'], $user_fio, $aDriverOperatorChat['id']);
                            $aMesData = [
                                'message_type' => 'message',
                                'message' => $aDriverOperatorChat['answer_from_operator'],
                                'user_fio' => $aOperator['firstname'] . ' ' . $aOperator['lastname'],
                                'message_id' => $aDriverOperatorChat['id'],
                                'message_from_driver' => $aDriverOperatorChat['message_from_driver']
                            ];

                            //echo "aMesData:<pre>"; print_r($aMesData); echo "</pre>";

                            //SocketDemon::sendOutDeviceMessageInstant($_GET['device_code'], 'message', $aDriverOperatorChat['answer_from_operator'], $user_fio, $aDriverOperatorChat['id']);
                            SocketDemon::sendOutDeviceMessageInstant($_GET['device_code'], $aMesData);

                            if(!empty($magic_code) && $magic_code != $_GET['device_code']) {
                                echo "отправляем сообщение магическому устройству " . $magic_code . "\n";

                                //SocketDemon::sendOutDeviceMessageInstant($magic_code, 'message', $aDriverOperatorChat['answer_from_operator'], $user_fio, $aDriverOperatorChat['id']);
                                SocketDemon::sendOutDeviceMessageInstant($_GET['device_code'], $aMesData);
                            }
                        }
                    }
                }


            } else {
                echo "Новое соединение с мобильным устройством не удалось добавить \n";
            }
        }
    };
};

// сообщение от клиента (браузера/мобильного устройства)
$ws_worker->onMessage = function($connection, $data) use(&$socket_demon)
{
    echo "onMessage\n";
    echo "data:<pre>"; print_r($data); echo "</pre>";
    // device:TCL 4034D. device_code:354286079898265
    // Send hello $data
    //$connection->send('hello ' . $data);

    //if(isset($socket_demon->socket_devices[$connection])) {
//        $socket_device = $socket_demon->socket_devices[$connection];
        //$socket_device
    //}
};

$ws_worker->onClose = function($connection) use(&$socket_demon)
{
    //echo "onClose\n";

    if($socket_demon->closeConnection($connection)) {
        echo "Соединение закрыто\n";
    }else {
        echo "Не удалось закрыть соединение\n";
    }
};

// Run worker
Worker::runAll();