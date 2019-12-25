<?php

namespace app\models;
use Yii;


/**
 * Класс для работы демона /commands/socket-demon.php и для работы с массивом объектов SocketPage
 */
class SocketDemon {

    public $socket_pages = [];  // массив объектов SocketPage
    public $socket_devices = []; // массив объектов SocketDevice

    //public function addConnection($connection, $user_id, $url, $url_params) {
    public function addBrowserConnection($connection, $browser_url, $user_code) {

        $user_id = self::getUserIdByDemonCode($user_code);
        //$user = User::find()->where(['id' => $user_id])->one(); // не работает и подключение возможносей yii порождает кучу проблем
        $user_data = self::getUserDataFromDB($user_id);
        if(self::getUserDemonCode($user_data['password_hash'], $user_data['id']) != $user_code) {
            return false;   // код пользователя не верен
        }

        $page_url = SocketPage::getPageUrlByBrowserUrl($browser_url, $_SERVER['HTTP_ORIGIN']);
        //echo "browser=".$browser_url." \n";
        if (isset($this->socket_pages[$page_url])) {
            $socket_page = $this->socket_pages[$page_url];
            return $socket_page->addConnection($user_id, $connection);
        }else {
            $this->socket_pages[$page_url] = new SocketPage($browser_url, $_SERVER['HTTP_ORIGIN'], $user_id, $connection);
            return true;
        }
    }

    public function addDeviceConnection($connection, $device_name, $device_code) {

        $this->socket_devices[$device_code] = new SocketDevice($connection, $device_name, $device_code);

        return true;
    }


    public function closeConnection($connection) {

        foreach($this->socket_devices as $key => $socket_device) {
            if($socket_device->connection == $connection) {
                // echo "удаляем устройство из списка соединений\n";
                unset($this->socket_devices[$key]);

                return true;
            }
        }


        list($socket_page, $user_id) = self::searchPageUser($this->socket_pages, $connection);
        if ($socket_page == null || $user_id == 0) {
            return false; //echo "socket_page или user_id не найдены\n";
        } else {
            $socket_page->removeConnection($user_id, $connection);
            if (count($socket_page->users_connections) == 0) {
                unset($this->socket_pages[$socket_page->page_url]);
                //echo "страница удалена\n";
            }

            return true;
        }

    }


    /*
     * Если в массиве сообщений есть идентичные сообщения (одна и та же страница и одно и то же сообщение), то такие
     * сообщения схлопываются до одного
     */
    public static function optimizeBrowserMessages($aMessages) {

        $aPagesMessages = [];
        // схлопываем одинаковое
        foreach($aMessages as $aMessage) {
            $page_url = SocketPage::getPageUrlByPageParams($aMessage['page_code'], $aMessage['url_params']);
            $aPagesMessages[$page_url.$aMessage['command']] = $aMessage;
        }

        $aMessages = [];
        foreach($aPagesMessages as $aPageMessage) {
            $aMessages[] = $aPageMessage;
        }

        return $aMessages;
    }

    /*
     * Действующему демону передается сообщение от сервера для рассылки клиентам данного сообщения
     */
    public static function sendOutBrowserMessage($page_code, $url_params, $command, $data = '', $with_buffer = true, $usersIds = []) {

        // сюда за 1 секунду может поступить несколько одинаковых запросов

        if(!$with_buffer) {
            self::sendOutBrowserMessageInstant($page_code, $url_params, $command, $data, $usersIds);
        }else {
            // сохраняем в буфер, а когда в yii-системе произойдет событие yii\web\Controller::EVENT_AFTER_ACTION,
            // то накопленные в буфере событие отправяться в демона (одинаковые события схлопнуться)
            Yii::$app->params['socket_messages'][] = [
                'page_code' => $page_code,
                'url_params' => $url_params,
                'command' => $command,
                'data' => $data,
                'usersIds' => $usersIds
            ];
        }
    }

    /*
     * Действующему демону передается сообщение от сервера для рассылки клиентам данного сообщения
     */
    public static function sendOutBrowserMessageInstant($page_code, $url_params, $command, $data, $usersIds = []) {

        // сюда за 1 секунду может поступить несколько одинаковых запросов

        //$localsocket = 'tcp://127.0.0.1:5555';
        global $params;


        // соединяемся с локальным tcp-сервером
        $instance = stream_socket_client($params['socketDemonInnerUrl']);

        //$full_url = SocketPage::getPageUrl($page_url, $url_params);
        if(in_array($page_code, ['all_pages', 'all_site_pages', 'all_admin_pages', 'all_storage_pages'])) {
            //$pages_urls = $this->getPagesByCode($page_code); // в статичной функции нельзя получить массив страницы хранящихся в объекте демона
            // поэтому через внутреннее соединение передаю демону код страницы вместо урла страницы
            fwrite($instance, json_encode([
                        'page_url' => $page_code,
                        'command' => $command,
                        'data' => $data,
                        'users' => $usersIds
                    ]
                ) . "\n");

        }elseif($page_code == 'new_page') {

            fwrite($instance, json_encode([
                        'page_url' => $page_code,
                        'command' => $command,
                        'data' => $data,
                        'users' => $usersIds
                    ]
                ) . "\n");

        }else {
            $page_url = SocketPage::getPageUrlByPageParams($page_code, $url_params);
            // отправляем сообщение
            fwrite($instance, json_encode([
                        'page_url' => $page_url,
                        'command' => $command,
                        'data' => $data,
                        'users' => $usersIds
                    ]
                )  . "\n");
        }
    }


    /*
    public static function sendOutDeviceMessageInstant($device_code, $message_type, $message, $user_fio = "", $message_id = 0) {

        //$socket_device = $this->socket_devices[$device_code];
        global $params;

        // соединяемся с локальным tcp-сервером
        $instance = stream_socket_client($params['socketDemonInnerUrl']);
        fwrite($instance, json_encode([
                'device_code' => $device_code,
                'message_type' => $message_type,
                'message' => $message,
                'user_fio' => $user_fio,
                'message_id' => $message_id
            ])  . "\n");
    }
    */

    /*
     * С сервера запросы уходят в демона
     */
    public static function sendOutDeviceMessageInstant($device_code, $data) {

        global $params;

        // соединяемся с локальным tcp-сервером
        $instance = stream_socket_client($params['socketDemonInnerUrl']);

        $data['device_code'] = $device_code;

        fwrite($instance, json_encode($data)  . "\n");
    }

    /*
     * С сокет-демона сообщения уходят в устройство
     */
    public function sendMessageToDevice($device_code, $data) {

        if(isset($this->socket_devices[$device_code])) {
            $socket_device = $this->socket_devices[$device_code];
            $socket_device->sendMessage(json_encode($data));
            echo "на устройство device_code=$device_code отправлено сообщение \n";

            echo "data:<pre>"; print_r($data); echo "</pre>";

        }else {
            echo "не существует устройства с кодом=$device_code \n";
        }
    }

    /*
    public function sendMessageToDevice($device_code, $message_type, $message, $user_fio = '', $message_id = 0) {

        if(isset($this->socket_devices[$device_code])) {

            $socket_device = $this->socket_devices[$device_code];

            $socket_message = json_encode([
                'type' => $message_type,
                'message' => $message,
                'user_fio' => $user_fio,
                'message_id' => $message_id
            ]);
            $socket_device->sendMessage($socket_message);
            echo "на устройство device_code=$device_code отправлено сообщение \n";

        }else {
            echo "не существует устройства с кодом=$device_code \n";
            // echo "socket_devices:<pre>"; print_r($this->socket_devices); echo "</pre>";
        }
    }*/


    /*
     * Действующий демон вызывает этот метод для передачи соединениям страницы сообщения
     */
    public function sendMessageToBrowserPage($page_code, $command, $data, $users) {

        $socket_message = json_encode([
            'command' => $command,
            'data' => $data,
            //'users' => $users
        ]);

        // сюда за 1 секунду может поступить несколько одинаковых запросов
        if(in_array($page_code, ['all_pages', 'all_site_pages', 'all_admin_pages', 'all_storage_pages'])) {

            $socket_pages = $this->getSocketPagesByCode($page_code);
            foreach ($socket_pages as $socket_page) {
                $socket_page->sendMessage($socket_message, $users);
            }

        }elseif($page_code == 'new_page') {

            // пока сделал чтобы сообщение передавалось во все вкладки браузера $users
            $socket_pages = $this->getSocketPagesByCode('all_pages');
            foreach ($socket_pages as $socket_page) {
                $socket_page->sendMessage($socket_message, $users);
            }

//            foreach ($socket_pages as $socket_page) {
//                $socket_page->sendMessageToOneUserPage($socket_message, $users);
//                break;
//            }


        }elseif(isset($this->socket_pages[$page_code])) {

            $socket_page = $this->socket_pages[$page_code];
            $socket_page->sendMessage($socket_message);

        }else {
            // это не ошибка, просто никто не открыл страницу с таким урлом в браузере
            echo "не существует страницы с page_url=$page_code \n";
        }
    }


    /*
     * В глобальном массиве сокет-страниц ищется объект сокет-страницы и пользователь соответствующие сокет-соединению
     */
    private static function searchPageUser($socket_pages, $connection) {

        $finded_socket_page = null;
        $finded_user_id = 0;

        foreach($socket_pages as $url => $socket_page) {
            foreach($socket_page->users_connections as $user_id => $userConnections) {
                foreach($userConnections as $key => $user_connection) {
                    if($user_connection == $connection) {
                        $finded_socket_page = $socket_page;
                        $finded_user_id = $user_id;
                        break 3;
                    }
                }
            }
        }

        return [$finded_socket_page, $finded_user_id];
    }

    public function getCountBrowserConnections() {

        $count_connections = 0;
        foreach($this->socket_pages as $socket_page) {
            foreach($socket_page->users_connections as $user_id => $connections) {
                $count_connections += count($connections);
            }
        }

        return $count_connections;
    }


    public static function getUserDemonCode($user_password_hash, $user_id) {
        return substr(md5($user_password_hash . $user_id), 0, 30).$user_id;
    }
    public static function getUserIdByDemonCode($code) {
        return intval(substr($code, 30));
    }

    public static function getUserDataFromDB($user_id) {

        global $db;
        $aDsn = explode(';', $db['dsn']);
        $db_hostname = str_replace('mysql:host=','', $aDsn[0]);
        $db_name = str_replace('dbname=','', $aDsn[1]);
        $link = mysqli_connect($db_hostname, $db['username'], $db['password'], $db_name);
        if (!$link) {
            echo "Ошибка: Невозможно установить соединение с MySQL." . PHP_EOL;
            echo "Код ошибки errno: " . mysqli_connect_errno() . PHP_EOL;
            echo "Текст ошибки error: " . mysqli_connect_error() . PHP_EOL;
            exit;
        }
        $sql = 'SELECT * FROM `user` WHERE id='.$user_id;
        $result = mysqli_query($link, $sql);
        $user_data = mysqli_fetch_assoc($result);
        //echo "<pre>"; print_r($row); echo "</pre>";

        mysqli_free_result($result);
        mysqli_close($link);

        return $user_data;
    }

    /*
     * При вызове из консоли при использовании $with_buffer=true - может не работать!
     */
    public static function updateMainPages($trip_id, $date, $with_buffer = true) {

        // обновление страницы Состав рейса
        SocketDemon::sendOutBrowserMessage(
            '/trip/trip-orders',
            ['trip_id' => $trip_id],
            'updateTripOrdersPage()',
            '',
            $with_buffer
        );

        // обновление Главной страницы
        SocketDemon::sendOutBrowserMessage(
            '/',
            ['date' => date("d.m.Y", $date)],
            'updateDirectionsTripBlock()',
            '',
            $with_buffer
        );
        if(date("d.m.Y", $date) == date("d.m.Y", time())) {
            SocketDemon::sendOutBrowserMessage(
                '/',
                [],
                'updateDirectionsTripBlock()',
                '',
                $with_buffer
            );
        }

        // обновление страницы Расстановка - /trip/set-trips?date=21.07.2018
        SocketDemon::sendOutBrowserMessage(
            '/trip/set-trips',
            ['date' => date("d.m.Y", $date)],
            'updateSetTripsPage()',
            '',
            $with_buffer
        );
    }

    public function getSocketPagesByCode($page_code) {

        $socket_pages = [];
        switch($page_code) {
            case 'all_pages':
                $socket_pages = $this->socket_pages;
                break;
            case 'all_site_pages':
                foreach($this->socket_pages as $socket_page) {
                    if(strpos($socket_page->page_url, '/admin') === false && strpos($socket_page->page_url, '/storage') === false) {
                        $socket_pages[] = $socket_page;
                    }
                }
                break;
            case 'all_admin_pages':
                foreach($this->socket_pages as $socket_page) {
                    if(strpos($socket_page->page_url, '/admin') !== false) {
                        $socket_pages[] = $socket_page;
                    }
                }
                break;
            case 'all_storage_pages':
                foreach($this->socket_pages as $socket_page) {
                    if(strpos($socket_page->page_url, '/storage') !== false) {
                        $socket_pages[] = $socket_page;
                    }
                }
                break;
            default:
                exit("Запрашивается несуществующий код демона \n");
                break;
        }

        return $socket_pages;
    }
}