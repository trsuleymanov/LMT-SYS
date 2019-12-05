<?php

namespace app\modules\admin\controllers;

use app\models\User;
use app\models\UserRole;
use Yii;
use app\models\Driver;
use app\models\DriverSearch;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * DriverController implements the CRUD actions for Driver model.
 */
class DriverController extends Controller
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

    /**
     * Lists all Driver models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DriverSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Driver model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Driver();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Driver model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Driver model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }


    public function actionAjaxSetActive($id, $active) {

        Yii::$app->response->format = 'json';

        $driver = Driver::find()->where(['id' => $id])->one();
        if($driver == null) {
            throw new ForbiddenHttpException('Водитель не найден');
        }

        $active = ($active == "true" ? true : false);
        $driver->setField('active', $active);

        return [
            'success' => true
        ];
    }

    public function actionAjaxSetAccountability($id, $accountability) {

        Yii::$app->response->format = 'json';

        $driver = Driver::find()->where(['id' => $id])->one();
        if($driver == null) {
            throw new ForbiddenHttpException('Водитель не найден');
        }

        $accountability = ($accountability == "true" ? true : false);
        $driver->setField('accountability', $accountability);

        return [
            'success' => true
        ];
    }


    public function actionOnlineDrivers() {

        $users = Driver::getOnlineDriversData();

        return $this->render('online-drivers', [
            'users' => $users,
        ]);
    }

    public function actionGetOnlineDrivers() {

        $users = Driver::getOnlineDriversData();

        return $this->renderPartial('_online-drivers-list', [
            'users' => $users
        ]);
    }


    public function actionAjaxGetActiveDrivers() {

        Yii::$app->response->format = 'json';

        $search = Yii::$app->getRequest()->post('search');

        $drivers = Driver::find()
            ->where(['active' => 1])
            ->andWhere(['like', 'fio', $search])
            ->all();

        $out['results'] = [];
        foreach($drivers as $driver) {
            $out['results'][] = [
                'id' => $driver->id,
                'text' => $driver->fio,
            ];
        }

        return $out;
    }


    public function actionAjaxGetDrivers($field_key = 'id', $field_value = 'fio') {

        Yii::$app->response->format = 'json';

        $search = Yii::$app->getRequest()->post('search');

        $drivers = Driver::find()
            ->andWhere(['like', 'fio', $search])
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


    public function actionAjaxCreateUserLikeDriver($id) {

        Yii::$app->response->format = 'json';

        $driver = Driver::find()->where(['id' => $id])->one();
        if($driver == null) {
            throw new ForbiddenHttpException('Водитель не найден');
        }

        $user = User::find()->where(['username' => $driver->fio])->one();
        if($user != null) {
            throw new ForbiddenHttpException('Пользователь с таким же логином как у водителя уже существует');
        }

        $user = new User();
        $user->scenario = 'create_user_like_driver';
        $user->username = $driver->fio;
        $user_role = UserRole::find()->where(['alias' => 'driver'])->one();
        if($user_role == null) {
            throw new ForbiddenHttpException('Роль водителя не найдена');
        }
        $user->role_id = $user_role->id;

        $aFio = [];
        foreach(explode(' ', $user->username) as $text) { // Хайруллин Равиль Алмасович
            $text = trim($text);
            if($text != '') {
                $aFio[] = $text;
            }
        }
        $user->lastname = $aFio[0];
        if(isset($aFio[1])) {
            $user->firstname = $aFio[1];
        }

        if(!$user->save()) {
            return [
                'success' => false,
                'errors' => $user->getErrors()
            ];
        }else {
            return [
                'success' => true,
                'user' => $user
            ];
        }
    }


    /**
     * Finds the Driver model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Driver the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Driver::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
