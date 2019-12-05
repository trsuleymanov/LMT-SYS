<?php

namespace app\modules\admin\controllers;

use app\models\User;
use app\models\UserRole;
use Yii;
use app\models\Driver;
use app\models\DriverSearch;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * DispatcherController implements the CRUD actions for Driver model.
 */
class OperatorController extends Controller
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
        return $this->render('index');
    }


    public function actionOnlineOperators() {

        $user_roles = UserRole::find()->where(['alias' => ['root', 'admin', 'editor', 'manager']])->all();

        $users = User::find()
            ->where(['>', 'auth_seans_finish', time()])
            ->andWhere(['role_id' => ArrayHelper::map($user_roles, 'id', 'id')])
            ->all();

        return $this->render('online-operators', [
            'users' => $users,
        ]);
    }

    public function actionGetOnlineOperators() {

        $user_roles = UserRole::find()->where(['alias' => ['root', 'admin', 'editor', 'manager']])->all();

        $users = User::find()
            ->where(['>', 'auth_seans_finish', time()])
            ->andWhere(['role_id' => ArrayHelper::map($user_roles, 'id', 'id')])
            ->all();

        return $this->renderPartial('_online-operators-list', [
            'users' => $users
        ]);
    }

    /**
     * Creates a new Driver model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
//    public function actionCreate()
//    {
//        $model = new Driver();
//
//        if ($model->load(Yii::$app->request->post()) && $model->save()) {
//            return $this->redirect(['index']);
//        } else {
//            return $this->render('create', [
//                'model' => $model,
//            ]);
//        }
//    }

    /**
     * Updates an existing Driver model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
//    public function actionUpdate($id)
//    {
//        $model = $this->findModel($id);
//
//        if ($model->load(Yii::$app->request->post()) && $model->save()) {
//            return $this->redirect(['index']);
//        } else {
//            return $this->render('update', [
//                'model' => $model,
//            ]);
//        }
//    }

    /**
     * Deletes an existing Driver model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
//    public function actionDelete($id)
//    {
//        $this->findModel($id)->delete();
//
//        return $this->redirect(['index']);
//    }

    /**
     * Finds the Driver model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Driver the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
//    protected function findModel($id)
//    {
//        if (($model = Driver::findOne($id)) !== null) {
//            return $model;
//        } else {
//            throw new NotFoundHttpException('The requested page does not exist.');
//        }
//    }
}
