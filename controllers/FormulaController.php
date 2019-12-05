<?php

namespace app\controllers;

use Yii;
use app\models\Formula;
use app\models\FormulaSearch;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * FormulaController implements the CRUD actions for Formula model.
 */
class FormulaController extends Controller
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

    public function actionAjaxGetCalculateForm()
    {
        Yii::$app->response->format = 'json';

        return $this->renderAjax('/formula/calculate-form');
    }

    public function actionAjaxCalculate()
    {
        Yii::$app->response->format = 'json';

        $formula_id = Yii::$app->request->post('formula_id');
        $argument = doubleval(Yii::$app->request->post('argument'));

        $formula = Formula::findOne($formula_id);
        if($formula == null) {
            throw new ForbiddenHttpException('Формула не найдена');
        }
        if($argument <= 0) {
            throw new ForbiddenHttpException('Аргумент должен быть больше нуля');
        }

        return $formula->getResult($argument);
    }
}
