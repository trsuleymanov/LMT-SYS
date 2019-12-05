<?php

namespace app\components;

use Yii;


/**
 * Вспомогательные функции
 */
class Helper
{
    public static $arWeekDays = ['пн', 'вт', 'ср', 'чт', 'пт', 'сб', 'вс'];
    public static $awMonths = [
        'января', 'февраля', 'марта', 'апреля', 'мая', 'июня',
        'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'
    ];

    public static function getMainDate($unixtime, $type = 1)
    {
        if($type == 1) {
            return self::getWeekDay($unixtime)
            . date(', d ', $unixtime)
            . self::$awMonths[date('n', $unixtime) - 1]
            . date(', H:i', $unixtime);
        }elseif($type == 2) {
            $day_name = '';
            if(date('d.m.Y', $unixtime) == date('d.m.Y')) {
                $day_name = 'сегодня - ';
            }elseif(date('d.m.Y', $unixtime) == date('d.m.Y', time() + 86400)) {
                $day_name = 'завтра - ';
            }elseif(date('d.m.Y', $unixtime) == date('d.m.Y', time() - 86400)) {
                $day_name = 'вчера - ';
            }

            return $day_name
            . self::getWeekDay($unixtime)
            . date(', d ', $unixtime)
            . self::$awMonths[date('n', $unixtime) - 1]
            . date(' Y', $unixtime).' года';

        }elseif($type == 3) {
            $day_name = '';
            if(date('d.m.Y', $unixtime) == date('d.m.Y')) {
                $day_name = 'сегодня - ';
            }elseif(date('d.m.Y', $unixtime) == date('d.m.Y', time() + 86400)) {
                $day_name = 'завтра - ';
            }elseif(date('d.m.Y', $unixtime) == date('d.m.Y', time() - 86400)) {
                $day_name = 'вчера - ';
            }

            return $day_name
            . self::getWeekDay($unixtime)
            . date(', d ', $unixtime)
            . self::$awMonths[date('n', $unixtime) - 1];
        }
    }

    /*
     * Функция возвращает дату в формате: чт, 20 апреля, 19:00 или формат: чт, 20 апреля 2017 года
     *
     * @return string
     */

    public static function getWeekDay($unixtime)
    {
        return self::$arWeekDays[date('N', $unixtime) - 1];
    }

    /*
     * Функция возвращает код выбранного дня
     */
    public static function getUnixtimeByDateCode($date_code)
    {
        if($date_code == 'today') {
            return time();
        }elseif($date_code == 'tomorrow') {
            return time() + 86400;
        }else {
            return '';
        }
    }

    /*
     * Функция преобразует код дня в unixtime
     */

    public static function getMainClass($date = '')
    {
        if(empty($date)) {
            $date = date('d.m.Y');
        }
        $day_code = self::getDayCode($date);

        return Helper::getClassByDayCode($day_code);
    }

    /*
     * Функция возвращает имя класса в зависимости от кода даты
     *
     * @return string
     */

    public static function getDayCode($date = '') {
        if($date == date('d.m.Y')) {
            return 'today';
        }elseif($date == date('d.m.Y', (time() + 86400))) {
            return 'tomorrow';
        }else {
            return 'another-day';
        }
    }

    /*
     * Функция возвращает имя класса в зависимости от установленной даты
     *
     * @return string
     */

    public static function getClassByDayCode($day_code)
    {
        $aDayClass = [
            'today' => 'orange-day',
            'tomorrow' => 'purple-day',
            'another-day' => 'blue-day'
        ];
        return $aDayClass[$day_code];
    }

    public static function convertHoursMinutesToSeconds($hi_string)
    {
        if(strpos($hi_string, ':') !== false) {
            $hours_minutes = explode(':', $hi_string);
            return 3600*intval($hours_minutes[0]) + 60*intval($hours_minutes[1]);
        }else {
            return 0;
        }
    }

    public static function getRandomColor() {

        $aColors = [
            '#9900FF', '#abdcb9', '#ff9ba0', '#fff799', '#ff88f4', '#ffe321','#D3FFE2', '#6affc6',
        ];

        return $aColors[rand(0, 7)];
    }


    public static function getNumberString($number, $string1, $string2, $string3) {

        // 1,21,31,.. - $string1
        // 2,3,4,22,23,24,... - $string2
        // 0,5-20,25,..,100 - $string3

        $number = abs($number);
        if($number > 99) {
            $sNumber = (string)$number;
            $sNumber = substr($sNumber, strlen($sNumber) - 2, strlen($sNumber));
            $number = intval($sNumber);
        }

        if($number >= 10) {

            if($number > 20) {
                $sNumber = (string)$number;

                $secondNum = intval($sNumber[1]);
                if($secondNum == 1) {
                    return $string1;
                }elseif(in_array($secondNum, [2,3,4])) {
                    return $string2;
                }else { // ..0, ..5, ..6, ..7, ..8, ..9
                    return $string3;
                }

            }else {// 11-20
                return $string3;
            }

        }elseif(in_array($number, [0,5,6,7,8,9])) {
            return $string3;
        }elseif($number == 1) {
            return $string1;
        }elseif(in_array($number, [2,3,4])) {
            return $string2;
        }

        return '';
    }
}
