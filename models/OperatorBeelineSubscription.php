<?php

namespace app\models;

use Yii;
use yii\base\ErrorException;
use yii\web\ForbiddenHttpException;

/**
 * This is the model class for table "beeline_subscription".
 *
 * @property int $id
 * @property string $subscription_id Код подписки в АТС
 * @property string $mobile_ats_login Логин в АТС - поле targetId в АТС
 * @property int $expire_at Время когда истекает действие подписки
 */
class OperatorBeelineSubscription extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'operator_beeline_subscription';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['expire_at', 'operator_id', 'minutes'], 'integer'],
            [['subscription_id'], 'string', 'max' => 38],
            [['mobile_ats_login'], 'string', 'max' => 100],
            [['name'], 'string', 'max' => 50],
            ['operator_id', 'unique'],
            [['name', 'mobile_ats_login', 'minutes'], 'required']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'operator_id' => 'Агент',
            'status' => 'Статус',
            'minutes' => 'Количество доступный минут',
            'name' => 'Название аккаунта (отображается в форме входа)',
            'subscription_id' => 'Код подписки в АТС',
            'mobile_ats_login' => 'Логин в АТС (SIP)',
            'expire_at' => 'Время когда истекает действие подписки',
        ];
    }


    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'operator_id']);
    }


    // создание подписки в АТС
    public function createAtsSubscription() {

        $another_operator_subscription = OperatorBeelineSubscription::find()
            ->where(['mobile_ats_login' => $this->mobile_ats_login])
            ->andWhere(['!=', 'id', $this->id])
            ->andWhere(['IS NOT', 'subscription_id', NULL])
            ->one();
        if($another_operator_subscription != null) {
            throw new ForbiddenHttpException('Уже существует другая подписка с таким же SIP-логином');
        }

        // отправляем запрос в билайн API чтобы создать подписку
        //$next_year = intval(date("Y")) + 1;
        //$finish_date = "01.01.".$next_year;
        //$expires = strtotime($finish_date) - time() - 1; // 1 секунда - попытка компенсации времени прохода информации между 2-мя серверами

        $expires = 14*3600;

        $setting = Setting::find()->where(['id' => 1])->one();
        if($setting == null) {
            throw new ErrorException('Запись с настройками не найдена');
        }
        if(empty($setting->crm_url_for_beeline_ats)) {
            throw new ErrorException('Ссылка на струницу в CRM не найдена');
        }

        $something['pattern'] = $this->mobile_ats_login;
        $something['expires'] = $expires;
        //$something['subscriptionType'] = "BASIC_CALL";
        $something['subscriptionType'] = "ADVANCED_CALL";
        //$something['url'] = 'http://'.$_SERVER['HTTP_HOST'].'/beeline/default';
        $something['url'] = $setting->crm_url_for_beeline_ats;


        //$headers[] = 'X-MPBX-API-AUTH-TOKEN: b3469183-f19a-46ce-9b44-19ace72e84c2';
        $headers[] = 'X-MPBX-API-AUTH-TOKEN: '.Yii::$app->params['subscription_id'];
        $headers[] = 'Content-Type: application/json; charset=UTF-8';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://cloudpbx.beeline.ru/apis/portal/subscription');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($something));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);

        // ответ:  errorCode	BadRequest
        // либо ответ: subscriptionId	5b260736-7352-438d-95f3-8d8e9e7345fc        expires	3600

        $aResult = json_decode($result, true);
        //echo 'errorCode='.$aResult['errorCode']; exit;

        if(isset($aResult['subscriptionId'])) { // значит нет ошибки в ответе

            // обновляем данные подписки оператора
            $this->subscription_id = $aResult['subscriptionId'];
            $this->operator_id = Yii::$app->user->id;
            $this->expire_at = time() + $something['expires'];
            if(!$this->save(false)) {
                throw new ForbiddenHttpException('Не удалось сохранить данные подписки');
            }

            return true;
        }else {
            throw new ErrorException($aResult['description']);
            //return false;
        }
    }


    public function isExistInAts() {

        //$headers[] = 'X-MPBX-API-AUTH-TOKEN: b3469183-f19a-46ce-9b44-19ace72e84c2';
        $headers[] = 'X-MPBX-API-AUTH-TOKEN: '.Yii::$app->params['subscription_id'];
        $headers[] = 'Content-Type: application/json; charset=UTF-8';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://cloudpbx.beeline.ru/apis/portal/subscription?subscriptionId='.$this->subscription_id);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);

        $aResult = json_decode($result, true);

        if(isset($aResult['errorCode'])) {
            return false;
        }else {
            return true;
        }
    }


    public function deleteFromAts() {

        if($this->isExistInAts()) {

            //$headers[] = 'X-MPBX-API-AUTH-TOKEN: b3469183-f19a-46ce-9b44-19ace72e84c2';
            $headers[] = 'X-MPBX-API-AUTH-TOKEN: '.Yii::$app->params['subscription_id'];
            $headers[] = 'Content-Type: application/json; charset=UTF-8';

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://cloudpbx.beeline.ru/apis/portal/subscription?subscriptionId='.$this->subscription_id);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $result = curl_exec($ch);
            curl_close($ch);

            $aResult = json_decode($result, true);

            if (isset($aResult['errorCode'])) {
                return false;
            } else {

                $this->operator_id = NULL;
                $this->subscription_id = NULL;
                $this->expire_at = NULL;
                if(!$this->save(false)) {
                    throw new ErrorException('Не удалось удалить подписку');
                }

                return true;
            }

        }else {

            $this->operator_id = NULL;
            $this->subscription_id = NULL;
            $this->expire_at = NULL;
            if(!$this->save(false)) {
                throw new ErrorException('Не удалось удалить подписку');
            }

            return true;
        }
    }


    public function setStatus($status) {

        //$headers[] = 'X-MPBX-API-AUTH-TOKEN: b3469183-f19a-46ce-9b44-19ace72e84c2';
        $headers[] = 'X-MPBX-API-AUTH-TOKEN: '.Yii::$app->params['subscription_id'];
        $headers[] = 'Content-Type: application/json; charset=UTF-8';

        $ch = curl_init();
        $url = 'https://cloudpbx.beeline.ru/apis/portal/abonents/'.$this->mobile_ats_login.'/agent?status='.$status;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);


        $aResult = json_decode($result, true); // приходит пустой ответ
        if(isset($aResult['errorCode'])) {
            if(isset($aResult['description']) && !empty($aResult['description'])) {
                return $aResult['description'];
            }else {
                return $aResult['errorCode'];
            }
        }else {
            $this->setField('status', $status);
            return true;
        }
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
        return Yii::$app->db->createCommand($sql)->execute();
    }
}
