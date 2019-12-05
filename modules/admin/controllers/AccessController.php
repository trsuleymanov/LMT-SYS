<?php

namespace app\modules\admin\controllers;

use app\models\AccessPlaces;
use app\models\Client;
use app\models\UserRole;
use Yii;
use app\models\Access;
use app\models\AccessSearch;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AccessController implements the CRUD actions for Access model.
 */
class AccessController extends Controller
{
    /**
     * {@inheritdoc}
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


    public function actionIndex()
    {
        //$searchModel = new AccessSearch();
        //$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $access_places = AccessPlaces::find()->orderBy([
            'module' => SORT_ASC,
            'page_url' => SORT_ASC,
            'page_part' => SORT_ASC
        ])->all();
        $accesses = Access::find()->all();

        $roles = UserRole::find()->all();

        return $this->render('index', [
            //'searchModel' => $searchModel,
            //'dataProvider' => $dataProvider,
            'access_places' => $access_places,
            'roles' => $roles,
            'accesses' => $accesses
        ]);
    }


    /**
     * @param $role_id
     * @param $place_id
     * @param $access_value
     * @return array
     * @throws ForbiddenHttpException
     */
    public function actionAjaxSetAccess($role_id, $place_id, $access_value) {

        Yii::$app->response->format = 'json';

        $access = Access::find()
            ->where(['user_role_id' => $role_id])
            ->andWhere(['id_access_places' => $place_id])
            ->one();

        if($access == null) {
            $access = new Access();
            $access->id_access_places = $place_id;
            $access->user_role_id = $role_id;
        }

        $access->access = $access_value;
        if(!$access->save(false)) {
            throw new ForbiddenHttpException('Не удалось установить доступ');
        }

        // вложенным областям доступа устанавливаю подобный доступ
//        $access_place = AccessPlaces::find()->where(['id' => $access->id_access_places])->one();
//        if(empty($access_place->page_url)) { // это модуль
//
//        }elseif(empty($access_place->page_part)) { // это страница
//
//        }

        return [
            'success' => true,
            'access_value' => $access_value,
            'access' => $access
        ];
    }

    public function actionCreate()
    {
        $user_role = new UserRole();

        if ($user_role->load(Yii::$app->request->post()) && $user_role->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $user_role,
        ]);
    }

    protected function findModel($id)
    {
        if (($model = Access::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
