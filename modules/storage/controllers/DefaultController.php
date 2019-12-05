<?php
namespace app\modules\storage\controllers;

use Yii;
use yii\web\Controller;
use app\models\LoginForm;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\StorageDetailSearch;


class DefaultController extends Controller
{

    public function actionIndex()
    {
        $searchModel = new StorageDetailSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

}
