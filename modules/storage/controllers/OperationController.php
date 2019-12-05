<?php

namespace app\modules\storage\controllers;


use app\models\StorageOperation;
use Yii;
use yii\base\ErrorException;
use yii\web\Controller;
use app\models\StorageOperationSearch;
use yii\web\ForbiddenHttpException;


class OperationController extends Controller
{
    public function actionIndex()
    {
        $searchModel = new StorageOperationSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    public function actionDelete($id) {

        $operation = StorageOperation::find()->where(['id' => $id])->one();
        if($operation == null) {
            throw new ForbiddenHttpException('Операция не найдена');
        }

        $storage_detail = $operation->storageDetail;
        if($storage_detail == null) {
            throw new ErrorException('Не найдена деталь на складе связанная с операцией');
        }

        $operation_type = $operation->storageOperationType;
        if($operation_type->operation_type == 0) { // операция расхода
            $storage_detail->remainder += $operation->count;
        }else { // операция прихода
            $storage_detail->remainder -= $operation->count;
        }
        if(!$storage_detail->save(false)) {
            throw new ErrorException('Не удалось сохранить изменения в детали на складе');
        }

        $operation->delete();

        return $this->redirect(['index']);
    }

}