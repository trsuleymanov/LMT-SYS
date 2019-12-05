<?php

namespace app\modules\admin\controllers;

use Yii;
use app\models\Point;
use app\models\PointSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PointController implements the CRUD actions for Point model.
 */
class PointController extends Controller
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
     * Ajax-создание точки остановки (в модальном окне)
     */
    public function actionAjaxCreate($city_id)
    {
        $model = new Point();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->response->format = 'json';
            return [
                'city_id' => $model->city_id
            ];

        }else {

            $model->city_id = $city_id;
            $model->active = 1;

            return $this->renderAjax('ajax_form.php', [
                'model' => $model,
            ]);
        }
    }

    /*
     * Ajax-редактирование точки остановки (в модальном окне)
     */
    public function actionAjaxUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->response->format = 'json';
            return [
                'city_id' => $model->city_id
            ];
        }else {
            return $this->renderAjax('ajax_form.php', [
                'model' => $model,
            ]);
        }
    }


    /**
     * Deletes an existing Point model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionAjaxDelete($id)
    {
        Yii::$app->response->format = 'json';

        return $this->findModel($id)->delete();
    }


    /**
     * Finds the Point model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Point the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Point::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
