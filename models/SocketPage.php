<?php

namespace app\models;


/**
 * Объект одной страницы для использования в сокет-демоне (/commands/socket-demon.php)
 * с дополнительными статичными функциями для работы с сокет-демоном
 */
class SocketPage {

//    public $url;
//    public $url_params = [];
//    public $full_url = [];

    public $browser_url; // http://tobus-yii2.ru/trip/trip-orders?trip_id=9894&sort=yandex_point_from_id
    public $page_url;    // /trip/trip-orders?trip_id=9894  или /  или равно $browser_url

    public $users_connections = [];

    public function __construct($browser_url, $host, $user_id, $connection)
    {
        //$this->url = $url;
        //$this->url_params = $url_params;
        //$this->full_url = self::getFullUrl($this->url, $this->url_params);

        $this->browser_url = $browser_url;
        $this->page_url = SocketPage::getPageUrlByBrowserUrl($browser_url, $host);

        $this->addConnection($user_id, $connection);
    }

    public function addConnection($user_id, $connection) {

        $this->users_connections[$user_id][] = $connection;

        return true;
    }

    public function removeConnection($user_id, $connection) {

        if(!isset($this->users_connections[$user_id])) {
            //echo "пользователя $user_id не существует - удаление невозможно \n";
            return false;
        }else {
            //unset($this->users[$user_id]);
            //echo "удален пользователь $user_id \n";

            $key = array_search($connection, $this->users_connections[$user_id]);
            unset($this->users_connections[$user_id][$key]);

            if(count($this->users_connections[$user_id]) == 0) {
                unset($this->users_connections[$user_id]);
            }

            return true;
        }
    }

    public function sendMessage($message, $users = []) {

        if(count($users) == 0) {
            foreach ($this->users_connections as $user_id => $userConnections) {
                foreach ($userConnections as $connection) {
                    // сюда за 1 секунду может поступить несколько одинаковых запросов
                    $connection->send($message);
                }
            }
        }else {
            foreach ($this->users_connections as $user_id => $userConnections) {
                if(in_array($user_id, $users)) {
                    foreach ($userConnections as $connection) {
                        // сюда за 1 секунду может поступить несколько одинаковых запросов
                        $connection->send($message);
                    }
                }
            }
        }
    }

//    public function sendMessageToOneUserPage($message, $users = []) {
//
//        if(count($users) == 0) {
//            foreach ($this->users_connections as $user_id => $userConnections) {
//                foreach ($userConnections as $connection) {
//                    // сюда за 1 секунду может поступить несколько одинаковых запросов
//                    $connection->send($message);
//                    break;
//                }
//            }
//        }else {
//            foreach ($this->users_connections as $user_id => $userConnections) {
//                if(in_array($user_id, $users)) {
//                    foreach ($userConnections as $connection) {
//                        // сюда за 1 секунду может поступить несколько одинаковых запросов
//                        $connection->send($message);
//                        break;
//                    }
//                }
//            }
//        }
//    }


    /*
     * Функция преобразует урл браузера в тот урл который нужен серверу для посылки в него сообщений
     * Пример: http://tobus-yii2.ru/trip/trip-orders?trip_id=9894&sort=yandex_point_from_id
     *          -> /trip/trip-orders?trip_id=9894
     *
     * Или: http://tobus-yii2.ru/?xz=123  -> /
     */
    public static function getPageUrlByBrowserUrl($browser_url, $host) {

        $browser_url = substr($browser_url, strlen($host));

        if(strpos($browser_url, '?') !== false) {
            $page = substr($browser_url, 0, strpos($browser_url, '?'));
        }else {
            $page = $browser_url;
        }

        switch($page) {
            case '/trip/trip-orders':
                $url_params = substr($browser_url, strpos($browser_url, '?') + 1);
                $aUrlPairsParams = explode('&', $url_params);
                $trip_id = 0;
                foreach($aUrlPairsParams as $pair) {
                    if(strpos($pair, 'trip_id=') !== false) {
                        $trip_id = intval(substr($pair, 8));
                    }
                }
                if($trip_id > 0) {
                    $page_url = '/trip/trip-orders?trip_id='.$trip_id;
                }else {
                    $page_url = $browser_url;
                }
                break;

            case '/':
                $url_params = substr($browser_url, strpos($browser_url, '?') + 1);
                $aUrlPairsParams = explode('&', $url_params);
                $date = '';
                foreach($aUrlPairsParams as $pair) {
                    if(strpos($pair, 'date=') !== false) {
                        $date = substr($date, 5);
                    }
                }
                if(!empty($date)) {
                    $page_url = '/?date='.$date;
                }else {
                    $page_url = $browser_url;
                }
                break;

            case '/trip/set-trips':
                $url_params = substr($browser_url, strpos($browser_url, '?') + 1);
                $aUrlPairsParams = explode('&', $url_params);
                $date = '';
                foreach($aUrlPairsParams as $pair) {
                    if(strpos($pair, 'date=') !== false) {
                        $date = substr($date, 5);
                    }
                }
                if(!empty($date)) {
                    $page_url = '/?date='.$date;
                }else {
                    $page_url = $browser_url;
                }
                break;

            default:
                $page_url = $page;
                break;
        }

        //echo "page_url=$page_url \n";
        return $page_url;
    }

    public static function getPageUrlByPageParams($page_url, $url_params) {

        ksort($url_params);
        $url_pairs = [];
        foreach($url_params as $param => $value) {
            $url_pairs[] = $param.(!empty($value) ? '='.$value : '');
        }


        return $page_url. (count($url_pairs) > 0 ? '?'.implode('&', $url_pairs) : '');
    }
}