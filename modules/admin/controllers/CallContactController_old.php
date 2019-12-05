<?php

namespace app\modules\admin\controllers;

use Yii;
use app\models\CallContact;
use app\models\CallContactSearch;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CallContactController implements the CRUD actions for CallContact model.
 */
class CallContactController extends Controller
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
        $searchModel = new CallContactSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    public function actionAjaxGetContactCallsBlock($contact_id) {

        Yii::$app->response->format = 'json';

        $contact = CallContact::find()->where(['id' => $contact_id])->one();
        if($contact == null) {
            throw new ForbiddenHttpException('Контакт не найден');
        }

        $calls = $contact->calls;

        return $this->renderPartial('contact-calls-block.php', [
            'contact' => $contact,
            'calls' => $calls,
        ]);
    }


    protected function findModel($id)
    {
        if (($model = CallContact::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
