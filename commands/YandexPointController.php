<?php

namespace app\commands;

use app\models\Order;
use app\models\OrderStatus;
use app\models\Point;
use app\models\Street;
use app\models\Trip;
use app\models\TripTransport;
use yii\base\ErrorException;
use yii\console\Controller;
use app\models\YandexPoint;
use app\models\City;
use yii\db\Query;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;


class YandexPointController extends Controller
{
    /*
     * Экшен импортирует данные файла в таблицу yandex_point
     * команда: php yii yandex-point/import-file
     */
    public function actionImportFile()
    {
        // предварительная очистка таблицы yandex_point
//        $sql = 'TRUNCATE `'.YandexPoint::tableName().'`';
//        Yii::$app->db->createCommand($sql)->execute();


        $file = \Yii::$app->basePath.'/commands/resources/8 Марта 3-я Школа и еще 89.vcf';
        $file_content = file_get_contents($file);
        $aPoints = explode('BEGIN:VCARD', $file_content);

        $aPointsData = [];
        $city = City::find()->where(['name' => "Альметьевск"])->one();
        foreach($aPoints as $sPoint) {
            $start = mb_strpos($sPoint, 'FN:', 0, 'UTF-8') + 3;
            $name = mb_substr($sPoint, $start, NULL, 'UTF-8');
            $stop = mb_strpos($name, "\n", 0, 'UTF-8');
            $name = trim(mb_substr($name, 0, $stop, 'UTF-8'));

            $start = mb_strpos($sPoint, 'item1.URL;type=pref:', 0, 'UTF-8') + 20;
            $lat_long = mb_substr($sPoint, $start, NULL, 'UTF-8');
            $stop = mb_strpos($lat_long, "\n", 0, 'UTF-8');
            $lat_long = mb_substr($lat_long, 0, $stop, 'UTF-8');

            $start = mb_strpos($lat_long, 'pt=', 0, 'UTF-8') + 3;
            $lat_long = mb_substr($lat_long, $start, NULL, 'UTF-8');
            $start = mb_strpos($lat_long, "=", 0, 'UTF-8') + 1;
            $lat_long = mb_substr($lat_long, $start, NULL, 'UTF-8');

            $start = mb_strpos($lat_long, '\,', 0, 'UTF-8');
            $long = doubleval(mb_substr($lat_long, 0, $start, 'UTF-8'));
            $lat = doubleval(mb_substr($lat_long, $start + 2, NULL, 'UTF-8'));

            if(!empty($name)) {
                $aPointsData[] = [
                    'name' => $name,
                    'city_id' => $city->id,
                    'lat' => $lat,
                    'long' => $long,
                ];
            }

        }


        $aSqlValues = [];
        foreach($aPointsData as $aPointData) {
            $aSqlValues[] =
                '('
                    .'"'.$aPointData['name'].'", '
                    .$aPointData['city_id'].', '
                    .$aPointData['lat'].', '
                    .$aPointData['long']
                .')';
        }

        $sql = 'INSERT INTO `'.YandexPoint::tableName().'` (`name`, `city_id`, `lat`, `long`) VALUES '.implode(',', $aSqlValues);

        Yii::$app->db->createCommand($sql)->execute();

        echo "готово\n";
    }

    /*
     * - пройти все заказы всех отправленных рейсов с целью перенести в yandex_point_from_name/yandex_point_to_name
     * адреса путем простой конкатенации строк (чтобы отображение этих рейсов не изменилось)
     *
     * пройти все заказы всех (неотправленных) рейсов после времени T1 до 31/12/2018, сверяя ID точек откуда и ID
     * точек куда с предоставленной мною таблицей. Если в этой таблице есть ID, то в поля забиваются уже
     * яндекс-точки с координатами. Если нет, то адрес (опять же простая конкатенация) переносится
     * в пожелания в формате "(addr: Улица, Точка)"
     *
     * команда: php yii yandex-point/update-orders
     */
    public function actionUpdateOrders() {

        //SELECT * FROM `point` WHERE name IN('Аэропорт', 'Кольцо', 'ЖД центральный', 'ЖД Восстание', 'Филармония', 'Роторная', 'Танковое кольцо', 'Деревня Универсиады', 'МКДЦ', 'Ипподромная', 'Казанская ярмарка', 'РКБ', 'Сокуры', 'Онкология', 'Ирбис Сокуры', 'Каргали', 'Чирпы', 'Ост.казан.ярмар.', 'Каипы', 'Каиты', 'Ленино', 'Именькова', 'Благодатное', 'Полянка') AND city_id=1
//            SELECT * FROM `yandex_point` WHERE name IN('Аэропорт', 'Кольцо', 'ЖД центральный', 'ЖД Восстание',
//            'Филармония Павлюхина', 'Роторная Павлюхина', 'Танковое кольцо', 'Деревня Универсиады', 'МКДЦ',
//            'Ипподромная Павлюхина', 'Казанская ярмарка', 'РКБ', 'Сокуры', 'Онкология', 'Ирбис Сокуры',
//            'Каргали', 'Чирпы', 'Каипы', 'Ленино', 'Именькова', 'Благодатная', 'Полянка') AND city_id=1

        $start = microtime(true);


        // таблица соответствий id старых точек и названий новых яндекс-точек
//        $aAllPointsYandexPointsRelations = [
//            1 => 'Казанский Аэропорт',
//            3 => 'Кольцо',
//            4 => 'ЖД Центральный',
//            5 => 'ЖД Восстания',
//            6 => 'Павлюхина Филармония',
//            7 => 'Павлюхина Роторная',
//            8 => 'Танковое кольцо',
//            9 => 'Деревня Универсиады',
//            10 => 'МКДЦ',
//            11 => 'Павлюхина Ипподром',
//            12 => 'Казанская ярмарка',
//            13 => 'РКБ',
//            63 => 'Сокуры',
//            71 => 'Онкология',
//            264 => 'Ирбис Сокуры',
//            282 => 'Каргали',
//            293 => 'Чирпы',
//            312 => 'Казанская ярмарка',
//            371 => 'Чирпы',
//            407 => 'Каипы',
//            415 => 'Каипы',
//            517 => 'Ленино',
//            543 => 'Именьково',
//            608 => 'Благодатная',
//            632 => 'Полянка',
//            2 => 'АВ - Автовокзал Альм.'
//        ];

        $aPointIdYandexPointRelations = [
            1 => 10,
            3 => 3,
            4 => 2,
            5 => 1,
            6 => 4,
            7 => 5,
            8 => 7,
            9 => 8,
            10 => 15,
            11 => 6,
            12 => 16,
            13 => 9,
            63 => 11,
            71 => 17,
            264 => 18,
            282 => 19,
            293 => 20,
            312 => 16,
            371 => 20,
            407 => 21,
            415 => 21,
            517 => 22,
            543 => 23,
            608 => 24,
            632 => 25,
            2 => 13
        ];

        $relation_yandex_points = YandexPoint::find()->where(['id' => $aPointIdYandexPointRelations])->all();
        $aRelationYandexPoints = ArrayHelper::index($relation_yandex_points, 'id');
        foreach($aPointIdYandexPointRelations as $old_point_id => $yandex_point_id) {
            $aPointIdYandexPointRelations[$old_point_id] = $aRelationYandexPoints[$yandex_point_id];
        }

//        foreach($aPointIdYandexPointRelations as $old_point_id => $yandex_point) {
//            echo "old_point_id=$old_point_id yandex_point_name=".$yandex_point->name."\n";
//        }
//        exit;



//        $aKazanPointsYandexPointsRelations = [
//            'Аэропорт' => 'Аэропорт',                       // +
//            'Кольцо' => 'Кольцо',                           // +
//            'ЖД центральный' => 'ЖД центральный',           // +
//            'ЖД Восстание' => 'ЖД Восстание',               // +
//            'Филармония' => 'Филармония Павлюхина',         // +
//            'Роторная' => 'Роторная Павлюхина',             // +
//            'Танковое кольцо' => 'Танковое кольцо',         // +
//            'Деревня Универсиады' => 'Деревня Универсиады', // +
//            'МКДЦ' => 'МКДЦ',                               // +
//            'Ипподромная' => 'Ипподромная Павлюхина',       // +
//            'Казанская ярмарка' => 'Казанская ярмарка',     // +
//            'РКБ' => 'РКБ',                                 // +
//            'Сокуры' => 'Сокуры',                           // !
//            'Онкология' => 'Онкология',                     // +
//            'Ирбис Сокуры' => 'Ирбис Сокуры',               // +
//            'Каргали' => 'Каргали',                         // +
//            'Чирпы' => 'Чирпы',                             // +
//            'Ост.казан.ярмар.' => 'Казанская ярмарка',      // +
//            'Каипы' => 'Каипы',                             // +
//            'Каиты' => 'Каипы',                             // +
//            'Ленино' => 'Ленино',                           // +
//            'Именькова' => 'Именькова',                     // +
//            'Благодатное' => 'Благодатная',                 // +
//            'Полянка' => 'Полянка',                         // +
//        ];
//
//        $aAlmetevskPointsYandexPointsRelations = [
//            'АВ - Автовокзал' => 'АВ - Автовокзал Альм.',
//        ];


        // 1. на основе имен соотвествий страх точек и яндекс-точек найдем реальное в базе данных соответствие:
        // point_id ~ yandex_point_id
//        $kazan_points = Point::find()
//            ->where(['name' => array_keys($aKazanPointsYandexPointsRelations)])
//            ->andWhere(['city_id' => 1])
//            ->all();
//        $aKazanPoints = ArrayHelper::index($kazan_points, 'name');

//        $almetevsk_points = Point::find()
//            ->where(['name' => array_keys($aAlmetevskPointsYandexPointsRelations)])
//            ->andWhere(['city_id' => 2])
//            ->all();
//        $aAlmetevskPoints = ArrayHelper::index($almetevsk_points, 'name');


//        $kazan_yandex_points = YandexPoint::find()
//            ->where(['name' => $aKazanPointsYandexPointsRelations])
//            ->andWhere(['city_id' => 1])
//            ->all();
//        $aKazanYandexPoints = ArrayHelper::index($kazan_yandex_points, 'name');

//        $almetevsk_yandex_points = YandexPoint::find()
//            ->where(['name' => $aAlmetevskPointsYandexPointsRelations])
//            ->andWhere(['city_id' => 2])
//            ->all();
//        $aAlmetevskYandexPoints = ArrayHelper::index($almetevsk_yandex_points, 'name');

//        $aPointIdYandexPointRelations = [];
//        foreach($aKazanPointsYandexPointsRelations as $point_name => $yandex_point_name) {
//            if(isset($aKazanPoints[$point_name]) && isset($aKazanYandexPoints[$yandex_point_name])) {
//                $aPointIdYandexPointRelations[$aKazanPoints[$point_name]->id] = $aKazanYandexPoints[$yandex_point_name];
//            }
//        }
//        foreach($aAlmetevskPointsYandexPointsRelations as $point_name => $yandex_point_name) {
//            if(isset($aAlmetevskPoints[$point_name]) && isset($aAlmetevskYandexPoints[$yandex_point_name])) {
//                $aPointIdYandexPointRelations[$aAlmetevskPoints[$point_name]->id] = $aAlmetevskYandexPoints[$yandex_point_name];
//            }
//        }
        //echo "$aPointIdYandexPointRelations:<pre>"; print_r($aPointIdYandexPointRelations); echo "</pre>";


        // все улицы
        $all_streets = Street::find()->all();
        $aAllStreetsNames = ArrayHelper::map($all_streets, 'id', 'name');
        // все точки
        $all_points = Point::find()->all();
        $aAllPointsNames = ArrayHelper::map($all_points, 'id', 'name');


        /*
        // 2. для всех новых заказов (привязанных к неотправленным рейсам):
        //    - ищу для старых точек откуда/куда соответствия яндекс-точек. Если соответствия найдены, то сохраняю в заказе яндекс-точки
        //      - если соответствие не найдено, то в поле comment записывается "Улица, точка"
        $not_sended_trips = Trip::find()->where(['date_sended' => NULL])->all();
        echo "count_not_sended_trip=".count($not_sended_trips)."\n";

        $not_sended_orders = Order::find()
            ->where(['trip_id' => ArrayHelper::map($not_sended_trips, 'id', 'id')])
            ->all();
        echo "count_not_sended_orders=".count($not_sended_orders)."\n";

        //$aAllNotSendedOrders = [];

        $aOrdersWithYandexPoints = [];
        $aOrdersWithComments = [];

        $orders_i = 0;
        foreach($not_sended_orders as $order) {

//            if($order->id != 39876) {
//                continue;
//            }else {
//                echo "обрабатываем заказ 39876 \n";
//            }


            if(
                !empty($order->street_id_from)
                && !empty($order->point_id_from)
                && isset($aAllStreetsNames[$order->street_id_from])
                && isset($aAllPointsNames[$order->point_id_from]))
            {
               // echo "имя точки=".$aAllPointsNames[$order->point_id_from]."\n";
                //echo "все точки:<pre>"; print_r($aPointIdYandexPointRelations); echo "</pre>";


                if(isset($aPointIdYandexPointRelations[$order->point_id_from])) {
                    //$aAllNotSendedOrders[$order->id]['yandex_point_from_name'] = $aPointIdYandexPointRelations[$aAllPointsNames[$order->point_id_from]]->name;
                    //$aAllNotSendedOrders[$order->id]['yandex_point_from_id'] = $aPointIdYandexPointRelations[$aAllPointsNames[$order->point_id_from]]->id;
                    //$aAllNotSendedOrders[$order->id]['yandex_point_from_lat'] = $aPointIdYandexPointRelations[$aAllPointsNames[$order->point_id_from]]->lat;
                    //$aAllNotSendedOrders[$order->id]['yandex_point_from_long'] = $aPointIdYandexPointRelations[$aAllPointsNames[$order->point_id_from]]->long;
                    $order->yandex_point_from_name = $aPointIdYandexPointRelations[$order->point_id_from]->name;
                    $order->yandex_point_from_id = $aPointIdYandexPointRelations[$order->point_id_from]->id;
                    $order->yandex_point_from_lat = $aPointIdYandexPointRelations[$order->point_id_from]->lat;
                    $order->yandex_point_from_long = $aPointIdYandexPointRelations[$order->point_id_from]->long;
                    $aOrdersWithYandexPoints[$order->id] = $order->id;
                }else {
                    //$aAllNotSendedOrders[$order->id]['comment'] .= 'откуда: '.$aAllStreetsNames[$order->street_id_from].', '.$aAllPointsNames[$order->point_id_from];
                    $order->comment = (!empty($order->comment) ? $order->comment.', ' : '').'откуда: '.$aAllStreetsNames[$order->street_id_from].', '.$aAllPointsNames[$order->point_id_from];
                    $aOrdersWithComments[$order->id] = $order->id;
                }
            }

            if(
                !empty($order->street_id_to)
                && !empty($order->point_id_to)
                && isset($aAllStreetsNames[$order->street_id_to])
                && isset($aAllPointsNames[$order->point_id_to]))
            {
                if(isset($aPointIdYandexPointRelations[$order->point_id_to])) {
                    //$aAllNotSendedOrders[$order->id]['yandex_point_to_name'] = $aPointIdYandexPointRelations[$aAllPointsNames[$order->point_id_to]]->name;
                    //$aAllNotSendedOrders[$order->id]['yandex_point_to_id'] = $aPointIdYandexPointRelations[$aAllPointsNames[$order->point_id_to]]->id;
                    //$aAllNotSendedOrders[$order->id]['yandex_point_to_lat'] = $aPointIdYandexPointRelations[$aAllPointsNames[$order->point_id_to]]->lat;
                    //$aAllNotSendedOrders[$order->id]['yandex_point_to_long'] = $aPointIdYandexPointRelations[$aAllPointsNames[$order->point_id_to]]->long;

                    $order->yandex_point_to_name = $aPointIdYandexPointRelations[$order->point_id_to]->name;
                    $order->yandex_point_to_id = $aPointIdYandexPointRelations[$order->point_id_to]->id;
                    $order->yandex_point_to_lat = $aPointIdYandexPointRelations[$order->point_id_to]->lat;
                    $order->yandex_point_to_long = $aPointIdYandexPointRelations[$order->point_id_to]->long;
                    $aOrdersWithYandexPoints[$order->id] = $order->id;

                }else {
                    //$aAllNotSendedOrders[$order->id]['comment'] .= 'куда: '.$aAllStreetsNames[$order->street_id_to].', '.$aAllPointsNames[$order->point_id_to];
                    $order->comment = (!empty($order->comment) ? $order->comment.', ' : '').'куда: '.$aAllStreetsNames[$order->street_id_to].', '.$aAllPointsNames[$order->point_id_to];
                    $aOrdersWithComments[$order->id] = $order->id;
                }
            }

            $order->save(false);
            $orders_i++;
        }


//        REPLACE INTO`table` VALUES (`id`,`col1`,`col2`) VALUES
//        (1,6,1),(2,2,3),(3,9,5),(4,16,8);

        echo "было переписано $orders_i заказов \n";
        echo 'Время выполнения скрипта: '.(microtime(true) - $start)."\n";

        //        $aSendedOrdersId = ArrayHelper::map($sended_orders, 'id', 'id');
//        echo "aSendedOrdersId:<pre>"; print_r($aSendedOrdersId); echo "</pre>";

        echo 'count_orders_with_yandex_points='.count($aOrdersWithYandexPoints)."\n";
        echo 'count_orders_with_comments='.count($aOrdersWithComments)."\n";

        echo "count_orders_with_yandex_points:<pre>"; print_r($aOrdersWithYandexPoints); echo "</pre>";
        echo "aOrdersWithComments:<pre>"; print_r($aOrdersWithComments); echo "</pre>";

        //exit;
/**/

        /**/
        // 3. всем старым заказам устанавливаю в полях yandex_point_from_name/yandex_point_to_name "Улица, Точка"
        // Старые заказа - это отправленные или отмененнные заказы привязанные к отправленным машинам

        // найдем все отправленные trip_transports - плохая идея, т.к. около 4-х тысяч старых заказов существует без привязки к машинам
        //$sended_trip_transports = TripTransport::find()->where(['IS NOT', 'date_sended', NULL])->all();
        //echo "count=".count($sended_trip_transports)."<br />"; // 3373

        // найдем все отправленные рейсы
        $sended_trips = Trip::find()->where(['IS NOT', 'date_sended', NULL])->all();
        //echo "count=".count($sended_trips)."<br />";// 3447

        $sended_orders = Order::find()
            ->where(['trip_id' => ArrayHelper::map($sended_trips, 'id', 'id')])
            //->limit(10000)
            //->offset(40000)
            ->all();
        //echo "sended_orders_count=".count($sended_orders)."<br />"; // 36571 из 36804


        $num = 0; $k = 0; // делим заказа например по 100 штук
        $aAllSendedOrders = [];
        foreach($sended_orders as $sended_order) {
            if(empty($sended_order->yandex_point_from_name)
                && !empty($sended_order->street_id_from)
                && !empty($sended_order->point_id_from)
                && isset($aAllStreetsNames[$sended_order->street_id_from])
                && isset($aAllPointsNames[$sended_order->point_id_from])
            ) {
                $aAllSendedOrders[$k][$sended_order->id]['yandex_point_from_name'] = $aAllStreetsNames[$sended_order->street_id_from].', '.$aAllPointsNames[$sended_order->point_id_from];
            }

            if(empty($sended_order->yandex_point_to_name)
                && !empty($sended_order->street_id_to)
                && !empty($sended_order->point_id_to)
                && isset($aAllStreetsNames[$sended_order->street_id_to])
                && isset($aAllPointsNames[$sended_order->point_id_to])
            ) {
                $aAllSendedOrders[$k][$sended_order->id]['yandex_point_to_name'] = $aAllStreetsNames[$sended_order->street_id_to].', '.$aAllPointsNames[$sended_order->point_id_to];
            }
            $num++;
            if($num == 100) {
                $num = 0;
                $k++;
            }
        }


        echo "count_all_orders~".(count($aAllSendedOrders)*100)."\n";
        //echo "count_0k_orders=".count($aAllSendedOrders[0])."\n";


        foreach($aAllSendedOrders as $k => $aOrders) {

            //$aSqls = [];
            foreach($aOrders as $order_id => $aOrder) {
                if(isset($aOrder['yandex_point_from_name']) && isset($aOrder['yandex_point_to_name'])) {
                    //$aSqls[] = 'UPDATE `'.Order::tableName().'` SET yandex_point_from_name = "'.$aOrder['yandex_point_from_name'].'", yandex_point_to_name="'.$aOrder['yandex_point_to_name'].'" WHERE id = '.$order_id.';';
                    $sql = 'UPDATE `'.Order::tableName().'` SET yandex_point_from_name = "'.$aOrder['yandex_point_from_name'].'", yandex_point_to_name="'.$aOrder['yandex_point_to_name'].'" WHERE id = '.$order_id.';';
                }elseif(isset($aOrder['yandex_point_from_name']) && !isset($aOrder['yandex_point_to_name'])) {
                    //$aSqls[] = 'UPDATE `'.Order::tableName().'` SET yandex_point_from_name = "'.$aOrder['yandex_point_from_name'].'" WHERE id = '.$order_id.';';
                    $sql = 'UPDATE `'.Order::tableName().'` SET yandex_point_from_name = "'.$aOrder['yandex_point_from_name'].'" WHERE id = '.$order_id.';';
                }elseif(!isset($aOrder['yandex_point_from_name']) && isset($aOrder['yandex_point_to_name'])) {
                    //$aSqls[] = 'UPDATE `'.Order::tableName().'` SET yandex_point_to_name="'.$aOrder['yandex_point_to_name'].'" WHERE id = '.$order_id.';';
                    $sql = 'UPDATE `'.Order::tableName().'` SET yandex_point_to_name="'.$aOrder['yandex_point_to_name'].'" WHERE id = '.$order_id.';';
                }
                Yii::$app->db->createCommand($sql)->execute();
            }
            //Yii::$app->db->createCommand(implode("\n", $aSqls))->execute();
            echo "записано порядка ".(($k + 1)*100)." заказов \n";
            echo 'Время выполнения скрипта: '.(microtime(true) - $start)."\n";
        }
        //$sql = 'UPDATE `'.self::tableName().'` SET '.$field_name.' = false WHERE id = '.$this->id;

        echo 'Время выполнения скрипта: '.(microtime(true) - $start)."\n";
/**/
//        $aSendedOrdersId = ArrayHelper::map($sended_orders, 'id', 'id');
//        echo "aSendedOrdersId:<pre>"; print_r($aSendedOrdersId); echo "</pre>";
    }


    // функция вычисляет по истории заказов (+яндекс-точке, +рейсов) относительно время короткого сбора и длинного сбора и
    // записывает их в 2 поля яндекс-точек

    // команда: php yii yandex-point/write-time-to-get-together
    public function actionWriteTimeToGetTogether() {

        /*
        + читаю полный список всех яндекс-точек 295 штук
        + создаю 2 пустых массива Точки коротких рейсов и Точки длинных рейсов
        + читаю полный список всех заказов отправленных с ВРПТ, и оттуда извлекаю: точку отправки, id рейса, ВРПТ
        - по данным из списка заказов нахожу список рейсов и группирую рейсы в два массива: коротких и длинных.
        - список заказов группирую по яндекс-точкам отправки.
        + и потом для каждой яндекс-точки отправки группирую заказы по рейсам:  [id_точки_отправки][id_рейса] => массив заказов

        + запускаю большой перебор яндекс-точек. И для каждой яндекс-точки:
        - для каждого рейса смотрю:
            если это рейс по направление АК, то беру p_АК, и если кол-во заказов на рейсе >= p_АК,
            то для каждого заказа нахожу время между ВРПТ и последней базовой точкой рейса, и для каждой точки эту разницу добавляю в массив
            или точек коротких рейсов или точек длинных рейсов.

        - дальше перебираю отдельно массив точек коротких рейсов и перебираю массив точек длинных рейсов,
            и для каждой точки группирую по разнице времен заказы, т.е. должен получиться массив вида  [точка нижняя Мактами][10] => [1, 1, 1] 1,1,1 - это заказы
            - дальше ищу для каждого массива рейсов для каждой точки массив наиболее часто встречающейся разницы времен. И это время добавляю в массив результирующий:
            [точка нижняя Мактами][относительно время короткого сбора] = 10
            [точка нижняя Мактами][относительно время длинного сбора] = 40
        */

        $start = microtime(true);
        $p_AK = 6;
        $p_KA = 2;
        $max_time_short_trip_AK = 40*60;
        $max_time_short_trip_KA = 40*60;

        YandexPoint::recountTimeToGetTogether($p_AK, $p_KA, $max_time_short_trip_AK, $max_time_short_trip_KA);

        /*
        $pointsTimeToGetTogetherLong = []; // относительно время короткого сбора
        $pointsTimeToGetTogetherShort = []; // относительно время длинного сбора

        $p_AK = 6;
        $p_KA = 2;

//        $max_time_short_trip_AK = 40*60;
//        $max_time_short_trip_KA = 30*60;

        $start = microtime(true);

        $sent_order_status = OrderStatus::getByCode('sent');

        $yandex_points = YandexPoint::find()->all(); // 0.0391 сек.
        //$aYandexPoints = ArrayHelper::index($yandex_points, 'id');

        $orders = Order::find()
            ->where(['status_id' => $sent_order_status->id])
            ->andWhere(['>', 'time_confirm', 0])
            ->andWhere(['>', 'yandex_point_from_id', 0])
            //->andWhere(['>', 'trip_id', 0])
            ->all(); // 8.4438 сек.
        $trips = Trip::find()
            ->where(['id' => ArrayHelper::map($orders, 'trip_id', 'trip_id')])
            ->all(); // 0.3538 сек.
        $aTrips = ArrayHelper::index($trips, 'id');

        // делю рейсы на котороткие и длинные


        // для каждой яндекс-точки отправки группирую заказы по рейсам:  [id_точки_отправки][id_рейса] => массив заказов
        $aYandexPointsOrders = [];
        foreach($orders as $order) {
            $aYandexPointsOrders[$order->yandex_point_from_id][$order->trip_id][$order->id] = $order;
        }

        //echo "count_241=".count($aYandexPointsOrders[241])."\n";
        //echo "241:<pre>"; print_r($aYandexPointsOrders[241]); echo "</pre>";

        foreach($aYandexPointsOrders as $yandex_point_from_id => $aTripsOrders) {

            foreach($aTripsOrders as $trip_id => $aTripOrders) {

                $trip = $aTrips[$trip_id];
                if($trip->direction_id == 1) { // АК
                    //if(count($aTripOrders) >= $p_AK) {

                        // перебираю заказы для нахождения разницы между ВРПТ и конечной базовой точкой рейса
                        $aTripEnd = explode(':', $trip->end_time);
                        $trip_end_time_secs = 3600 * intval($aTripEnd[0]) + 60 * intval($aTripEnd[1]);

                        // определею этот рейс - длинный или короткий
                        $aTripStart = explode(':', $trip->start_time);
                        $trip_start_time_secs = 3600 * intval($aTripStart[0]) + 60 * intval($aTripStart[1]);

                        foreach($aTripOrders as $order_id => $order) {

                            // возможно $order->date - это не начало дня, а начало дня + какое-то время рейса?...
                            $time_to_get_together = $trip_end_time_secs - ($order->time_confirm - $order->date);

                            if($trip_end_time_secs - $trip_start_time_secs <= Trip::$max_time_short_trip_AK) { // короткий рейс
                                $pointsTimeToGetTogetherShort[$yandex_point_from_id][$time_to_get_together][$order_id] = 1;
                            }else { // длинный рейс
                                $pointsTimeToGetTogetherLong[$yandex_point_from_id][$time_to_get_together][$order_id] = 1;
                            }
                        }
                    //}

                }else { // КА
                    //if(count($aTripOrders) >= $p_KA) {

                        // перебираю заказы для нахождения разницы между ВРПТ и конечной базовой точкой рейса
                        $aTripEnd = explode(':', $trip->end_time);
                        $trip_end_time_secs = 3600 * intval($aTripEnd[0]) + 60 * intval($aTripEnd[1]);

                        // определею этот рейс - длинный или короткий
                        $aTripStart = explode(':', $trip->start_time);
                        $trip_start_time_secs = 3600 * intval($aTripStart[0]) + 60 * intval($aTripStart[1]);

                        foreach($aTripOrders as $order_id => $order) {

                            //echo "order:<pre>"; print_r($order); echo "</pre>"; exit;

                            // возможно $order->date - это не начало дня, а начало дня + какое-то время рейса?...
                            $time_to_get_together = $trip_end_time_secs - ($order->time_confirm - $order->date);
                            if($trip_end_time_secs - $trip_start_time_secs <= Trip::$max_time_short_trip_KA) { // короткий рейс
                                $pointsTimeToGetTogetherShort[$yandex_point_from_id][$time_to_get_together][$order_id] = 1;
                            }else { // длинный рейс
                                $pointsTimeToGetTogetherLong[$yandex_point_from_id][$time_to_get_together][$order_id] = 1;
                            }
                        }
                    //}
                }
            }
        }


        // ищу для каждой точки наиболее часто встречающиеся относительно время
        foreach($yandex_points as $yandex_point) {

            if(isset($pointsTimeToGetTogetherShort[$yandex_point->id])) {

                $max_count_point_orders = 0;
                $max_count_time_to_get_together = 0;
                foreach($pointsTimeToGetTogetherShort[$yandex_point->id] as $time_to_get_together => $pointOrders) {
                    if(count($pointOrders) > $max_count_point_orders) {
                        $max_count_point_orders = count($pointOrders);
                        $max_count_time_to_get_together = $time_to_get_together;
                    }
                }

                if($max_count_point_orders > 0) {
                    $yandex_point->time_to_get_together_short = $max_count_time_to_get_together;
                }

            }else {
                //echo "для точки ".$yandex_point->id." нет коротких рейсов\n";
            }

            if(isset($pointsTimeToGetTogetherLong[$yandex_point->id])) {

                $max_count_point_orders = 0;
                $max_count_time_to_get_together = 0;
                foreach($pointsTimeToGetTogetherLong[$yandex_point->id] as $time_to_get_together => $pointOrders) {
                    if(count($pointOrders) > $max_count_point_orders) {
                        $max_count_point_orders = count($pointOrders);
                        $max_count_time_to_get_together = $time_to_get_together;
                    }
                }

                if($max_count_point_orders > 0) {
                    $yandex_point->time_to_get_together_long = $max_count_time_to_get_together;
                }

            }else {
                //echo "для точки ".$yandex_point->id." нет длинных рейсов\n";
            }

            $yandex_point->sync_date = null;
            $yandex_point->scenario = 'set_time_to_get_together';
            if (!$yandex_point->save(false)) {
                throw new ErrorException('Не удалось сохранить яндекс-точку');
            }
        }*/



        echo 'Время выполнения скрипта: '.round(microtime(true) - $start, 4)." сек.\n";
        //echo "orders_count=".count($orders)."\n"; // orders_count=77783 - а с яндекс-точками 41308
    }
}
