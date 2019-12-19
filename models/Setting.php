<?php

namespace app\models;

use Yii;
use yii\base\ErrorException;
use yii\web\ForbiddenHttpException;

/**
 * This is the model class for table "setting".
 *
 * @property integer $create_orders_yesterday
 */
class Setting extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'setting';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[
                'create_orders_yesterday',
                'show_short_clients_phones',
                'show_short_drivers_phones',
                'access_to_client_info_main_page',
                'use_mobile_app_by_default',
                'show_passenger_button_in_trip_orders_page'
            ], 'boolean'],
            [['photo_server_url', 'crm_url_for_beeline_ats',], 'string', 'max' => 100],
            [['missed_calls_close_interval', 'ya_point_p_AK', 'ya_point_p_KA', 'max_time_short_trip_AK',
                'max_time_short_trip_KA', 'min_talk_time_to_perform_request',
                'count_hours_before_trip_to_cancel_order', 'interval_to_close_trip', 'sync_date'], 'integer'],
            [['phone_to_confirm_user', 'loyalty_switch'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'create_orders_yesterday' => 'Разрешено создание заказов вчерашним днем',
            'show_short_clients_phones' => 'Отображать номера клиентов в коротком формате (для операторов)',
            'show_short_drivers_phones' => 'Отображать номера водителей в коротком формате (для операторов)',
            'access_to_client_info_main_page' => 'Открыт доступ к инф.о клиенте через меню поиска в главном окне (для операторов)',
            'photo_server_url' => 'Url фото сервера',
            'missed_calls_close_interval' => 'Количество секунд до закрытия пропущенных обращений',
            'crm_url_for_beeline_ats' => 'Ссылка для АТС биллайна на струницу в CRM принимающую сообщения от АТС',
            'min_talk_time_to_perform_request' => 'Минимальное время разговора при обработке электронной заявки',
            'ya_point_p_AK' => 'для АК: Минимальное количество точек на рейс, меньше которого точки рейса не учитываются',
            'ya_point_p_KA' => 'для КА: Минимальное количество точек на рейс, меньше которого точки рейса не учитываются',
            'max_time_short_trip_AK' => 'Максимальное время короткого сбора для АК',
            'max_time_short_trip_KA' => 'Максимальное время короткого сбора для КА',
            'count_hours_before_trip_to_cancel_order' => 'Количество часов до первой точки рейса, меньше которых запрещено отменять заказ',
            'interval_to_close_trip' => 'Количество минут после последней точки рейса для закрытия рейса',
            'use_mobile_app_by_default' => 'Использовать интерактивный режим отправки рейса по умолчанию',
            'show_passenger_button_in_trip_orders_page' => 'Показывать кнопку редактирования паспортных данных',
            'phone_to_confirm_user' => 'Телефон в АТС куда переадресуется звонок для подтверждения пользователя во время регистрации',
            'loyalty_switch' => 'Переключатель лояльности',
            'sync_date' => 'Время когда была синхронизация с клиентским сайтом',
        ];
    }


    public function beforeSave($insert)
    {
        $this->sync_date = null;

        return parent::beforeSave($insert);
    }


    public function setField($field_name, $field_value)
    {
        if(!empty($field_value)) {
            $field_value = htmlspecialchars($field_value);
        }

        if($field_value === false) {
            $sql = 'UPDATE `'.self::tableName().'` SET '.$field_name.' = false WHERE id = '.$this->id;
        }elseif(empty($field_value)) {
            $sql = 'UPDATE `'.self::tableName().'` SET '.$field_name.' = NULL WHERE id = '.$this->id;
        }else {
            $sql = 'UPDATE `'.self::tableName().'` SET '.$field_name.' = "'.$field_value.'" WHERE id = '.$this->id;
        }

        $res = Yii::$app->db->createCommand($sql)->execute();
        return $res;
    }

    /*
     * Отображение телефона с учетом глобальных настроек
     */
    public static function changeShowingPhone($phone, $setting_field, $replacementSymbol = '') {

        $setting = Setting::find()->where(['id' => 1])->one();

        $sapi = php_sapi_name();
        if ($sapi=='cli') { // это консольный запуск
            return $phone;
        }

        switch ($setting_field) {
            case 'show_short_clients_phones':
                if($setting->show_short_clients_phones == true && !in_array(Yii::$app->session->get('role_alias'), ['root', 'admin'])) {
                    // +7-111-111-1111 -> +7-111-..-1111 - старый вариант
                    // +7-111-111-1111 -> +7-.....-1111 - новый вариант
                    // +7-111-111-1111 -> +7-xxx-xxx-1111 - посимвольная замена
                    if(empty($replacementSymbol)) {
                        $phone = substr($phone, 0, 2) . ' .......' . substr($phone, 11);
                    }else {
                        $phone = substr($phone, 0, 2).$replacementSymbol.$replacementSymbol.$replacementSymbol.'-'.$replacementSymbol.$replacementSymbol.$replacementSymbol.'-'.substr($phone, 11);
                    }
                }
                break;

            case 'show_short_drivers_phones':
                if($setting->show_short_drivers_phones == true && !in_array(Yii::$app->session->get('role_alias'), ['root', 'admin'])) {
                    // +7-111-111-1111 -> +7-111-..-1111
                    // +7-111-111-1111 -> +7-.....-1111 - новый вариант
                    if(empty($replacementSymbol)) {
                        $phone = substr($phone, 0, 2).' .......'.substr($phone, 11);
                    }else {
                        $phone = substr($phone, 0, 2).$replacementSymbol.$replacementSymbol.$replacementSymbol.'-'.$replacementSymbol.$replacementSymbol.$replacementSymbol.'-'.substr($phone, 11);
                    }
                }
                break;

            default:
                throw new ErrorException('Неизвестная настройка');
                break;
        }

        return $phone;
    }
}
