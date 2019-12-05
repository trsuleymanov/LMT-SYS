<?php

namespace app\controllers;

use app\models\Street;
use Yii;
use yii\base\ErrorException;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Point;
use app\models\PointSearch;
use app\models\Direction;

/**
 * StreetController implements the CRUD actions for Point model.
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
     * Функция возвращает результат поиска точек отправления для SelectWidget`а или скажем для kartik-элемента формы
     */
    public function actionAjaxFormElemStreets($is_street_from)
    {
        Yii::$app->response->format = 'json';

        $out['results'] = [];

        $search = Yii::$app->getRequest()->post('search');
        $direction_id = intval(Yii::$app->getRequest()->post('direction_id'));

        $direction = Direction::findOne($direction_id);
        if($direction == null) {
            throw new ForbiddenHttpException('Необходимо выбрать направление');
        }

        if($is_street_from == 1) {
            $streets_query = Street::find()->where(['city_id' => $direction->city_from]);
        }else {
            $streets_query = Street::find()->where(['city_id' => $direction->city_to]);
        }

        if($search != '') {
            $streets_query->andWhere(['LIKE', 'name', $search]);
        }

        $streets = $streets_query->all();

        $out['results'] = [];
        foreach($streets as $street) {
            $out['results'][] = [
                'id' => $street->id,
                'text' => $street->name,
            ];
        }

        return $out;
    }
}
