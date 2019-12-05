<?php

namespace app\modules\admin\controllers;

use Yii;
use app\models\Transport;
use app\models\TransportSearch;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TransportController implements the CRUD actions for Transport model.
 */
class TransportController extends Controller
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
     * Lists all Transport models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TransportSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Transport model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Transport();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Transport model.
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
     * Deletes an existing Transport model.
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

        $transport = Transport::find()->where(['id' => $id])->one();
        if($transport == null) {
            throw new ForbiddenHttpException('Водитель не найден');
        }

        $active = ($active == "true" ? true : false);
        $transport->setField('active', $active);

        return [
            'success' => true
        ];
    }

    public function actionAjaxSetAccountability($id, $accountability) {

        Yii::$app->response->format = 'json';

        $transport = Transport::find()->where(['id' => $id])->one();
        if($transport == null) {
            throw new ForbiddenHttpException('Водитель не найден');
        }

        $accountability = ($accountability == "true" ? true : false);
        $transport->setField('accountability', $accountability);

        return [
            'success' => true
        ];
    }

    public function actionAjaxGetActiveTransports($field_name = 'name2') {

        Yii::$app->response->format = 'json';

        $search = Yii::$app->getRequest()->post('search');

        $transports = Transport::find()
            ->where(['active' => 1])
            ->andWhere(['like', 'CONCAT(`transport`.model, " (", `transport`.car_reg, ")")', $search])
            ->all();

        $out['results'] = [];
        foreach($transports as $transport) {
            $out['results'][] = [
                'id' => $transport->id,
                'text' => $transport->$field_name,
            ];
        }

        return $out;
    }

    public function actionAjaxGetTransports($field_name = 'name2') {

        Yii::$app->response->format = 'json';

        $search = Yii::$app->getRequest()->post('search');

        $transports = Transport::find()
            //->where(['active' => 1])
            ->where(['like', 'CONCAT(`transport`.model, " (", `transport`.car_reg, ")")', $search])
            ->all();

        $out['results'] = [];
        foreach($transports as $transport) {
            $out['results'][] = [
                'id' => $transport->id,
                'text' => $transport->$field_name,
            ];
        }

        return $out;
    }


    /**
     * Finds the Transport model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Transport the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Transport::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
