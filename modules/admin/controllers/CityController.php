<?php

namespace app\modules\admin\controllers;

use Yii;
use app\models\City;
use app\models\CitySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\base\ErrorException;
use yii\web\ForbiddenHttpException;
use yii\filters\VerbFilter;
use app\models\Point;
use app\models\PointSearch;
use app\models\Street;
use app\models\StreetSearch;
use app\models\YandexPoint;
use app\models\YandexPointSearch;

/**
 * CityController implements the CRUD actions for City model.
 */
class CityController extends Controller
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
     * Lists all City models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CitySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * Creates a new City model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new City();
        if (Yii::$app->request->isAjax)
        {
            // данные из таблицы Points
            return $this->render('create', [
                'model' => $model,
                'yandexPointSearchModel' => [],
                'yandexPointDataProvider' => [],
                'pointSearchModel' => [],
                'pointDataProvider' => [],
                'streetSearchModel' => [],
                'streetDataProvider' => [],
            ]);

        }else {
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                return $this->redirect(['update', 'id' => $model->id]);
            } else {
                return $this->render('create', [
                    'model' => $model,
                    'yandexPointSearchModel' => [],
                    'yandexPointDataProvider' => [],
                    'pointSearchModel' => [],
                    'pointDataProvider' => [],
                    'streetSearchModel' => [],
                    'streetDataProvider' => [],
                ]);
            }
        }
    }

    /**
     * Updates an existing City model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);


        if (Yii::$app->request->isAjax)
        {
            $pointSearchModel = new PointSearch();
            $queryParams = Yii::$app->request->queryParams;

            $queryParams['PointSearch']['city_id'] = $id;
            $queryParams['StreetSearch']['city_id'] = $id;
            $queryParams['YandexPointSearch']['city_id'] = $id;

            $pointDataProvider = $pointSearchModel->search($queryParams);

            $streetSearchModel = new StreetSearch();
            $streetDataProvider = $streetSearchModel->search($queryParams);

            $yandexPointSearchModel = new YandexPointSearch();
            $yandexPointDataProvider = $yandexPointSearchModel->search($queryParams);

            return $this->render('update', [
                'model' => $model,
                'yandexPointSearchModel' => $yandexPointSearchModel,
                'yandexPointDataProvider' => $yandexPointDataProvider,
                'pointSearchModel' => $pointSearchModel,
                'pointDataProvider' => $pointDataProvider,
                'streetSearchModel' => $streetSearchModel,
                'streetDataProvider' => $streetDataProvider,
            ]);

        }else {

            $pointSearchModel = new PointSearch();
            $queryParams = Yii::$app->request->queryParams;
            $queryParams['PointSearch']['city_id'] = $id;
            $queryParams['StreetSearch']['city_id'] = $id;
            $queryParams['YandexPointSearch']['city_id'] = $id;
            $pointDataProvider = $pointSearchModel->search($queryParams);

            $streetSearchModel = new StreetSearch();
            $streetDataProvider = $streetSearchModel->search($queryParams);

            $yandexPointSearchModel = new YandexPointSearch();
            $yandexPointDataProvider = $yandexPointSearchModel->search($queryParams);

            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                return $this->redirect(['index']);
            } else {
                return $this->render('update', [
                    'model' => $model,
                    'yandexPointSearchModel' => $yandexPointSearchModel,
                    'yandexPointDataProvider' => $yandexPointDataProvider,
                    'pointSearchModel' => $pointSearchModel,
                    'pointDataProvider' => $pointDataProvider,
                    'streetSearchModel' => $streetSearchModel,
                    'streetDataProvider' => $streetDataProvider,
                ]);
            }
        }
    }

    /**
     * Finds the City model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return City the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = City::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /*
     * Функция возвращает данные по городу и яндекс-точкам города для карты
     */

    /**
     * Deletes an existing City model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionAjaxDelete($id)
    {
        // если у города есть точки остановки, то запрещаем удалять
        $point = Point::find()->where(['city_id' => $id])->one();
        if($point != null) {
            Yii::$app->response->format = 'json';
            throw new ForbiddenHttpException('Нельзя удалить город, так как у него есть ориентиры (удалите вначале все ориентиры города)');
        }

        $this->findModel($id)->delete();
    }

    public function actionAjaxGetCityYandexPointsData($city_id) {

        Yii::$app->response->format = 'json';

        $city = City::find()->where(['id' => $city_id])->one();
        if($city == null) {
            throw new ForbiddenHttpException('Город не найден');
        }

        return [
            'city' => $city,
            'yandex_points' => $city->yandexPoints
        ];
    }
}
