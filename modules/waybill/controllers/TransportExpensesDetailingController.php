<?php

namespace app\modules\waybill\controllers;

use app\models\TransportExpenses;
use app\models\TransportExpensesDetailing;
use app\models\TransportExpensesSeller;
use app\models\TransportWaybill;
use Yii;
use app\models\StorageOperation;
use app\models\StorageOperationSearch;
use yii\base\ErrorException;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\widgets\ActiveForm;

/**
 * Расшифровка расходов в Путевом листе
 */
class TransportExpensesDetailingController extends Controller
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


    public function actionAjaxGetForm($transport_expenses_id)
    {
        Yii::$app->response->format = 'json';

        $tr_expenses = TransportExpenses::find()->where(['id' => $transport_expenses_id])->one();
        if($tr_expenses == null) {
            throw new ForbiddenHttpException('Расход не найден');
        }
        $detailings = $tr_expenses->transportExpensesDetailings;

        // обработка пришедших данных формы
        $post = Yii::$app->request->post();
        if(isset($post['TransportExpensesDetailing'])) {

            //echo "post:<pre>"; print_r($post); echo "</pre>";
            $postDetailings = [];
            foreach($post['TransportExpensesDetailing'] as $postDetail) {
                $postDetailings[$postDetail['id']] = $postDetail;
            }

            $has_error = false;
            foreach($detailings as $detail) {
                $postDetail = $postDetailings[$detail->id];

                $detail->name = $postDetail['name'];
                $detail->name = mb_strtoupper(mb_substr($detail->name, 0, 1, 'UTF-8'), 'UTF-8') . mb_substr($detail->name, 1, NULL, 'UTF-8');
                $detail->price = $postDetail['price'];

                if (!$detail->validate()) {
                    $has_error = true;
                    //echo "errors:<pre>"; print_r($tr_detailing->getErrors()); echo "</pre>";
                }
            }

            if($has_error == false) {
                $summ_price = 0;
                foreach($detailings as $detail) {
                    if(!$detail->save()) {
                        throw new ErrorException('Не удалось сохранить расшифровку расхода');
                    }

                    $summ_price += $detail->price;
                }
                $tr_expenses->setField('price', $summ_price);

                return [
                    'success' => true,
                    'tr_expenses_id' => $tr_expenses->id,
                    'tr_expenses_price' => $summ_price
                ];
            }
        }


        $waybill = $tr_expenses->waybill;
        //$transport = $waybill->transport;

        return [
            'success' => true,
            //'title' => 'Заказ-наряд № '.$waybill->number.' от '.date('d.m.Y', $waybill->date_of_issue).' по т/с '.($transport != null ? $transport->sh_model.' '.$transport->car_reg : ''),
            'title' => $this->renderPartial('form-title.php', [
                'waybill' => $waybill,
                'tr_expenses' => $tr_expenses,
                'detailings' => $detailings
            ]),
            'html' => $this->renderAjax('form.php', [
                'tr_expenses' => $tr_expenses,
                'detailings' => $detailings
            ]),
        ];
    }

    // создание новой детализации и возврат строки с полями для редактирования детализации
    public function actionAjaxGetDetailingRow($transport_expenses_id) {

        Yii::$app->response->format = 'json';

        $block_type = Yii::$app->request->post('block_type'); // works / goods

        $tr_expense = TransportExpenses::find()->where(['id' => $transport_expenses_id])->one();
        if($tr_expense == null) {
            throw new ForbiddenHttpException('Расход не найден');
        }

        $form = ActiveForm::begin([
            'id' => 'transport-expenses-detailing-form',
        ]);

        $detailing = new TransportExpensesDetailing();
        $detailing->expense_id = $tr_expense->id;
        //$detailing->type = ($block_type == 'works' ? 'work' : 'spare_part');
        $detailing->type = $block_type;
        if(!$detailing->save(false)) {
            throw new ErrorException('Не удалось создать детализацию');
        }

        $all_detailings = $tr_expense->transportExpensesDetailings;
        $works_detailings = [];
        $goods_detailings = [];

        foreach($all_detailings as $detailing) {
            //if(in_array($detailing->type,  array_keys(TransportExpensesDetailing::getWorkTypes()))) {
            if($detailing->type == 'work_services') {
                $works_detailings[] = $detailing;
            }else {
                $goods_detailings[] = $detailing;
            }
        }
        return [
            'success' => true,
            'html' => $this->renderPartial('_row', [
                'form' => $form,
                'detailing' => $detailing,
                'num' => ($detailing->type == 'work_services' ? count($works_detailings) : count($goods_detailings)),
                'key' => count($all_detailings),
                //'aDetailingTypes' => ($block_type == 'works' ? TransportExpensesDetailing::getWorkTypes() : TransportExpensesDetailing::getGoodTypes())
            ])
        ];
    }

    public function actionAjaxDeleteDetailingRow($detailing_id) {

        Yii::$app->response->format = 'json';

        $detailing = TransportExpensesDetailing::find()->where(['id' => $detailing_id])->one();
        if($detailing == null) {
            throw new ForbiddenHttpException('Расшифровка не найдена');
        }

        $detailing->delete();

        return [
            'success' => true,
        ];
    }


    public function actionAjaxSaveExpensesField($transport_expenses_id) {

        Yii::$app->response->format = 'json';

        $tr_expenses = TransportExpenses::find()->where(['id' => $transport_expenses_id])->one();
        if($tr_expenses == null) {
            throw new ForbiddenHttpException('Расход не найден');
        }

        //$field_name, $field_value
        $field_name = Yii::$app->request->post('field_name'); // TransportWaybill[transport_id]
        $field_value = Yii::$app->request->post('field_value');

        $start_pos = strrpos($field_name, '[') + 1;
        $end_pos = strpos($field_name, ']', $start_pos);
        $field_name = substr($field_name, $start_pos, $end_pos - $start_pos);

        $tr_expenses->$field_name = $field_value;
        if(!$tr_expenses->validate()) {

            $aErrors = $tr_expenses->getErrors();
            if(isset($aErrors[$field_name])) {
                return [
                    'success' => false,
                    'field_name' => $field_name,
                    'errors' => $aErrors[$field_name]
                ];
            }
        }

        $tr_expenses->setField($field_name, $field_value);

        $preliminary_results_html = '';
        $accruals_html = '';
        $correct_html = '';
        $waybill = null;
        if(in_array($field_name, ['price', 'expenses_is_taken', 'expenses_type_id'])) {

            //$waybill = $tr_expenses->waybill;
            // получаем обновленную модель $waybill
            $waybill = TransportWaybill::find()->where(['id' => $tr_expenses->transport_waybill_id])->one();

            $waybill->updateResultFields(); // пересчитываем некоторые поля Путевого листа

            // обновленные значения из базы берем (могут отличаться форматы чисел с .00 или без)
            //$waybill = TransportWaybill::find()->where(['id' => $waybill->id])->one();

            $form = ActiveForm::begin([
                'id' => 'waybill-form',
                'options' => [
                    'transport-waybill-id' => $waybill->id,
                ],
            ]);
            $preliminary_results_html = $this->renderPartial('/transport-waybill/_preliminary_results_block.php', [
                'form' => $form,
                'model' => $waybill
            ]);
            $correct_html = $this->renderPartial('/transport-waybill/_correct_block.php', [
                'form' => $form,
                'model' => $waybill
            ]);
            $accruals_html = $this->renderPartial('/transport-waybill/_accruals_block.php', [
                'form' => $form,
                'model' => $waybill
            ]);
        }

        return [
            'success' => true,
            'field_name' => $field_name,
            'preliminary_results_html' => $preliminary_results_html,
            'correct_html' => $correct_html,
            'accruals_html' => $accruals_html,
        ];
    }


    public function actionAjaxGetSellersNames() {

        Yii::$app->response->format = 'json';

        $search = Yii::$app->getRequest()->post('search');
        $sellers = TransportExpensesSeller::find()->orderBy(['name' => SORT_ASC])->all();


        $out['results'] = [];
        foreach($sellers as $seller) {

            $text = $seller->name;

            if($search != '') {
                if(mb_stripos($text, $search, 0, 'UTF-8') !== false) {
                    $out['results'][] = [
                        'id' => $seller->id,
                        'text' => $text,
                    ];
                }
            }else {
                $out['results'][] = [
                    'id' => $seller->id,
                    'text' => $text,
                ];
            }
        }

        return $out;
    }


    public function actionAjaxAddNewSeller($new_name) {

        Yii::$app->response->format = 'json';

        $seller = new TransportExpensesSeller();
        $seller->name = mb_strtoupper(mb_substr($new_name, 0, 1, 'UTF-8'), 'UTF-8') . mb_substr($new_name, 1, NULL, 'UTF-8');

        if($seller->validate() && $seller->save()) {
            return [
                'success' => true,
                'seller_id' => $seller->id,
                'name' => $seller->name
            ];
        }else {
            return [
                'success' => false,
                'errors' => $seller->getErrors()
            ];
        }
    }
}
