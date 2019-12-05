<?php

namespace app\models;

/**
 * сохранение сокет- мобильные сокет-соединения
 */
class SocketDevice {

    public $connection = null;
    public $device_name = '';
    public $device_code = '';

    public function __construct($connection, $device_name, $device_code){
        $this->connection = $connection;
        $this->device_name = $device_name;
        $this->device_code = $device_code;

        //echo "создано новое устройство device_name=".$this->device_name." device_code=".$this->device_code."\n";
    }

    public function sendMessage($message) {
        $this->connection->send($message);
        // echo "сообщение $message ушло устройству ".$this->device_name." \n";
    }
}