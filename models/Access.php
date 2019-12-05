<?php

namespace app\models;

use Yii;
use yii\base\ErrorException;

/**
 * This is the model class for table "access".
 *
 * @property int $id
 * @property int $id_access_places Область к которой устанавливается доступ
 * @property int $user_role_id Пользовательская роль
 * @property int $access Наличие доступа
 */
class Access extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'access';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_access_places', 'user_role_id', 'access'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_access_places' => 'Область к которой устанавливается доступ',
            'user_role_id' => 'Пользовательская роль',
            'access' => 'Наличие доступа',
        ];
    }

    public static function hasUserAccess($code, $type = 'page_part') {

        $access_place = null;
        if($type == 'page_part') {
            $access_place = AccessPlaces::find()->where(['page_part' => $code])->one();
            //echo "code=$code ";
            //echo "access_place:<pre>"; print_r($access_place); echo "</pre>";
        }elseif($type == 'page_url') {
            $access_place = AccessPlaces::find()->where(['page_url' => $code])->andWhere(['page_part' => ''])->one();
        }elseif($type == 'module') {
            $access_place = AccessPlaces::find()->where(['module' => $code])->andWhere(['page_url' => ''])->one();
        }

        //echo "role_id=".Yii::$app->session->get('role_id')."<br />";

        // по непонятным причинам получение данных из сессии иногда может не сработать, посему сделаю так:
        if(intval(Yii::$app->session->get('role_alias')) == 0) {

            $user = \Yii::$app->user->identity;
            if($user != null) {
                Yii::$app->session->set('role_alias', $user->userRole->alias);
                Yii::$app->session->set('role_id', $user->userRole->id);
            }
        }


        if(Yii::$app->session->get('role_id') > 0) {

            $access = Access::find()
                ->where(['id_access_places' => $access_place->id])
                ->andWhere(['user_role_id' => Yii::$app->session->get('role_id')])
                ->one();
            //echo "access:<pre>"; print_r($access); echo "</pre>";
            if ($access == null) {
                return false;
            } else {
                return $access->access;
            }

        }else {

            throw new ErrorException('Сессия пользователя потеряна, обновите страницу');
        }
    }
}
