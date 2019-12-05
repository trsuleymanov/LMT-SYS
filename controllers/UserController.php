<?php

namespace app\controllers;

use app\models\Street;
use app\models\User;
use Yii;
use yii\base\ErrorException;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Point;
use app\models\PointSearch;
use app\models\Direction;


class UserController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }


    /*
     * Функция возвращает результат поиска точек отправления для SelectWidget`а или скажем для kartik-элемента формы
     */
    public function actionAjaxGetUsers()
    {
        Yii::$app->response->format = 'json';

        $out['results'] = [];

        $is_get_username = boolval(Yii::$app->getRequest()->post('get-username'));

        $search = trim(Yii::$app->getRequest()->post('search'));
        if(strpos($search, ' ') !== false) {

            if($is_get_username) {
                $users_query = User::find()->where(['like', 'username', $search]);
            }else {
                $aSearch = explode(' ', $search);
                $users_query = User::find()->where(
                    [
                        'or',
                        ['like', 'lastname', $aSearch[0]],
                        ['like', 'firstname', $aSearch[0]],
                        ['like', 'lastname', $aSearch[1]],
                        ['like', 'firstname', $aSearch[1]],
                    ]);
            }
        }else {

            if($is_get_username) {
                $users_query = User::find()->where(['like', 'username', $search]);
            }else {
                $users_query = User::find()->where(
                    [
                        'or',
                        ['like', 'lastname', $search],
                        ['like', 'firstname', $search]
                    ]);
            }
        }

        if($is_get_username) {
            $users = $users_query->orderBy(['username' => SORT_ASC])->all();
        }else {
            $users = $users_query->orderBy(['CONCAT(lastname, " ", firstname)' => SORT_ASC])->all();
        }


        $out['results'] = [];
        foreach($users as $user) {
            $out['results'][] = [
                'id' => $user->id,
                'text' => ($is_get_username ? $user->username : $user->fullname),
            ];
        }

        return $out;
    }

    /*
     * Запрос не используется
     */
    public function actionAjaxGetUsernames()
    {
        Yii::$app->response->format = 'json';

        //$out['results'] = [];
        $out = [];

        $search = trim(Yii::$app->getRequest()->post('search'));
        $users_query = User::find()->where(['like', 'username', $search]);
        $users_query = $users_query->andWhere(['blocked' => 0]);
        $users = $users_query->all();

//        $out['results'] = [];
        foreach($users as $user) {
//            $out['results'][] = [
//                'id' => $user->username,
//                'text' => $user->username,
//            ];
            $out[$user->username] = $user->username;
        }

        return $out;

//        $sql = 'SELECT * FROM `user`';
//        $rs = Yii::$app->db->createCommand($sql)->queryAll();
//        $row_set = [];
//        foreach ($rs as $row)
//        {
//            $row_set[] = $row['username']; //build an array
//        }
//        echo json_encode($row_set);
    }


    public function actionAjaxGetUsernames2($field_key = 'id', $field_value = 'username') {

        Yii::$app->response->format = 'json';

        $search = Yii::$app->getRequest()->post('search');

        $drivers = User::find()
            ->andWhere(['like', 'username', $search])
            ->all();

        $out['results'] = [];
        foreach($drivers as $driver) {
            $out['results'][] = [
                'id' => $driver->$field_key,
                'text' => $driver->$field_value,
            ];
        }

        return $out;
    }


    public function actionTest() {

        echo 'id='.Yii::$app->user->id;
        //echo 'id='.Yii::$app->user->identity->id;
        //$user = Yii::$app->user->identity;

        //echo "user:<pre>"; print_r($user); echo "</pre>";
    }
}
