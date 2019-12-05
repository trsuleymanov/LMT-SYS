<?php

namespace app\modules\admin\controllers;

use Yii;
use app\models\Street;
use app\models\StreetSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * StreetController implements the CRUD actions for Street model.
 */
class StreetController extends Controller
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
     * Ajax-создание улицы (в модальном окне)
     */
    public function actionAjaxCreate($city_id)
    {
        $model = new Street();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->response->format = 'json';
            return [
                'city_id' => $model->city_id
            ];

        }else {

            $model->city_id = $city_id;

            return $this->renderAjax('ajax_form.php', [
                'model' => $model,
            ]);
        }
    }

    /*
     * Ajax-редактирование улицы (в модальном окне)
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


    public function actionAjaxDelete($id)
    {
        Yii::$app->response->format = 'json';

        return $this->findModel($id)->delete();
    }

    /**
     * Finds the Street model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Street the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Street::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
