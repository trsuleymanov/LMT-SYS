<?php
namespace app\modules\waybill\controllers;

use app\components\Helper;
use app\models\Driver;
use app\models\TransportExpenses;
use app\models\TransportExpensesDetailingSearch;
use app\models\TransportExpensesSearch;
use app\models\TransportExpensesSellerType;
use app\models\TransportExpensesTypes;
use app\models\TransportPaymentMethods;
use app\models\TransportWaybill;
use app\models\TransportWaybillSearch;
use app\modules\waybill\Waybill;
use ErrorException;
use Yii;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use app\models\LoginForm;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\ForbiddenHttpException;


class TransportWaybillController extends Controller
{
    public function actionCreate()
    {
        $model = new TransportWaybill();

        $model->scenario = 'create';
        if($model->load(Yii::$app->request->post()) && $model->save()) {

            /*
            $transport_expenses_seller_types = TransportExpensesSellerType::find()->all();
            $aTransportExpensesSellerTypes = ArrayHelper::map($transport_expenses_seller_types, 'name', 'id');

            $driver = $model->driver;


            $transport_expenses[0] = new TransportExpenses();
            $transport_expenses[0]->view_group = 'typical_expenses';
            $transport_expenses[0]->expenses_seller_type_id = $aTransportExpensesSellerTypes['АЗС'];// тип продавца
            //$transport_expenses[0]->count = 1;
            $transport_expenses[0]->payment_method_id = 1; // Из выручки (для групп таблиц: типовые и прочие) + платил - водитель + дата оплаты = дата документа
            $transport_expenses[0]->payment_date = $model->date_of_issue; // дата оплаты
            if($driver != null && $driver->user_id > 0) {
                $transport_expenses[0]->transport_expenses_paymenter_id = $driver->user_id;
            }

            $transport_expenses[1] = new TransportExpenses();
            $transport_expenses[1]->view_group = 'typical_expenses';
            $transport_expenses[1]->expenses_seller_type_id = $aTransportExpensesSellerTypes['Мойка'];
            //$transport_expenses[1]->count = 1;
            $transport_expenses[1]->payment_method_id = 1; // Из выручки
            $transport_expenses[1]->payment_date = $model->date_of_issue;
            if($driver != null && $driver->user_id > 0) {
                $transport_expenses[1]->transport_expenses_paymenter_id = $driver->user_id;
            }

            $transport_expenses[2] = new TransportExpenses();
            $transport_expenses[2]->view_group = 'typical_expenses';
            $transport_expenses[2]->expenses_seller_type_id = $aTransportExpensesSellerTypes['Стоянка'];
            //$transport_expenses[2]->count = 1;
            $transport_expenses[2]->payment_method_id = 1; // Из выручки
            $transport_expenses[2]->payment_date = $model->date_of_issue;
            if($driver != null && $driver->user_id > 0) {
                $transport_expenses[2]->transport_expenses_paymenter_id = $driver->user_id;
            }

            foreach($transport_expenses as $tr_expense) {
                $tr_expense->transport_waybill_id = $model->id;
                if(!$tr_expense->save(false)) {
                    throw new ErrorException('Не удалось создать пустой расход');
                }
            }*/

            $model->createTypicalExpenses();

            // а дальше редирект на update
            return $this->redirect(['update', 'id' => $model->id]);
        }


        return $this->render('create', [
            'model' => $model,
            //'transport_expenses' => $transport_expenses
        ]);
    }


    public function actionUpdate($id)
    {
        $model = TransportWaybill::find()->where(['id' => $id])->one();
        if($model == null) {
            throw new ForbiddenHttpException('Путевой лист не найден');
        }


        $transport_expenses = $model->transportExpenses;


        $post = Yii::$app->request->post();
        if(isset($post['TransportExpenses'])) {

            $has_error = false;
            foreach ($transport_expenses as $tr_expenses) { // взятые из базы расходы

                $tr_expenses_post = $post['TransportExpenses'][$tr_expenses->id];

                if(isset($tr_expenses_post['expenses_seller_type_id'])) {
                    $tr_expenses->expenses_seller_type_id = $tr_expenses_post['expenses_seller_type_id'];
                }


                if(isset($tr_expenses_post['expenses_type_id'])) {
                    $tr_expenses->expenses_type_id = $tr_expenses_post['expenses_type_id'];
                }
                if(isset($tr_expenses_post['expenses_is_taken'])) {
                    $tr_expenses->expenses_is_taken = $tr_expenses_post['expenses_is_taken'];
                }
                if(isset($tr_expenses_post['expenses_is_taken_comment'])) {
                    $tr_expenses->expenses_is_taken_comment = $tr_expenses_post['expenses_is_taken_comment'];
                }
                if(isset($tr_expenses_post['payment_method_id'])) {
                    $tr_expenses->payment_method_id = $tr_expenses_post['payment_method_id'];
                }
                if(isset($tr_expenses_post['payment_date'])) {
                    $tr_expenses->payment_date = $tr_expenses_post['payment_date'];
                }
                if(isset($tr_expenses_post['payment_comment'])) {
                    $tr_expenses->payment_comment = $tr_expenses_post['payment_comment'];
                }


                if(isset($tr_expenses_post['check_attached'])) {
                    $tr_expenses->check_attached = $tr_expenses_post['check_attached'];
                }
                if(isset($tr_expenses_post['price'])) {
                    $tr_expenses->price = $tr_expenses_post['price'];
                }
                if(isset($tr_expenses_post['count'])) {
                    $tr_expenses->count = $tr_expenses_post['count'];
                }
                if(isset($tr_expenses_post['points'])) {
                    $tr_expenses->points = $tr_expenses_post['points'];
                }



                if (!$tr_expenses->validate()) {
                    $has_error = true;
                    //echo "errors:<pre>"; print_r($tr_expenses->getErrors()); echo "</pre>";
                }
            }

            if ($model->load($post) && $model->validate() && !$has_error && $model->save()) {

                foreach ($transport_expenses as $key => $tr_expenses) {
                    $tr_expenses->transport_waybill_id = $model->id;
                    if(!$tr_expenses->save()) {
                        throw new ErrorException('Не удалось сохранить расход');
                    }
                }

                return $this->redirect(['list']);

            }else {
                //echo "has_error=$has_error <br />";
                //echo "transport_expenses:<pre>"; print_r($transport_expenses); echo "</pre>";
            }
        }


        return $this->render('update', [
            'model' => $model,
            'transport_expenses' => $transport_expenses
        ]);
    }


    public function actionList() {

        $searchModel = new TransportWaybillSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('list', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    public function actionAjaxGetTransportExpensesRow($transport_waybill_id) {

        Yii::$app->response->format = 'json';

        $table_type = Yii::$app->request->post('table_type');

        $transport_waybill = TransportWaybill::find()->where(['id' => $transport_waybill_id])->one();
        if($transport_waybill == null) {
            throw new ForbiddenHttpException('Путевой лист не найден');
        }


        $transport_expenses_seller_type = TransportExpensesSellerType::find()->where(['name' => 'Введите название'])->one();

        $tr_expenses = new TransportExpenses();
        $tr_expenses->transport_waybill_id = $transport_waybill->id;
        if($table_type == 'other') {

            $tr_expenses->view_group = 'other_expenses';
            $tr_expenses->expenses_seller_type_id = $transport_expenses_seller_type->id;
            $tr_expenses->payment_method_id = 1; // Из выручки
            //$tr_expenses->count = 1;
            // если есть водитель, то пользователя водителя установить как плательщика
            if($transport_waybill->driver_id > 0) {

                $driver = Driver::find()->where(['id' => $transport_waybill->driver_id])->one();
                if($driver == null) {
                    throw new ForbiddenHttpException('Водитель не найден');
                }
                if(empty($driver->user_id)) {
                    throw new ForbiddenHttpException('У водителя нет связанного пользователя');
                }

                $tr_expenses->transport_expenses_paymenter_id = $driver->user_id;
            }

            $tr_expenses->payment_date = $transport_waybill->date_of_issue; // дата оплаты



        }elseif($table_type == 'incoming-payment-requests') {

            $tr_expenses->view_group = 'incoming_payment_requests';
            $tr_expenses->expenses_seller_type_id = $transport_expenses_seller_type->id;

        }
        //$tr_expenses->count = 1;
        if(!$tr_expenses->save(false)) {
            throw new ErrorException('Не удалось создать детализацию');
        }


        $form = ActiveForm::begin([
            'id' => 'waybill-form',
        ]);


        return [
            'success' => true,
            'html' => $this->renderPartial('transport_expenses_row', [
                'tr_expenses' => $tr_expenses,
                'form' => $form,
                'delete_row' => true,
                'num' => 0,
            ])
        ];
    }


    public function actionAjaxDeleteTransportExpensesRow($transport_expenses_id) {

        Yii::$app->response->format = 'json';

        $tr_expenses = TransportExpenses::find()->where(['id' => $transport_expenses_id])->one();
        if($tr_expenses == null) {
            throw new ForbiddenHttpException('Расшифровка не найдена');
        }

        $tr_expenses->delete();

        return [
            'success' => true,
        ];
    }


    public function actionAjaxSaveWaybillField($waybill_id) {

        Yii::$app->response->format = 'json';

        $waybill = TransportWaybill::find()->where(['id' => $waybill_id])->one();
        if($waybill == null) {
            throw new ForbiddenHttpException('Путевой лист не найден');
        }

        //$field_name, $field_value
        $field_name = Yii::$app->request->post('field_name'); // TransportWaybill[transport_id]
        $field_value = Yii::$app->request->post('field_value');


        $start_pos = strpos($field_name, '[') + 1;
        $end_pos = strpos($field_name, ']');
        $field_name = substr($field_name, $start_pos, $end_pos - $start_pos);


        if(in_array($field_name, ['hand_over_b1', 'hand_over_b2', 'hand_over_b1_data', 'hand_over_b2_data'])) {
            if(!in_array(Yii::$app->session->get('role_alias'), ['root', /*'admin'*/])) {
                throw new ForbiddenHttpException('Редактировать параметры B1/B2 может только Root');
            }
        }


        if($field_name == 'pre_trip_med_check' && $field_value != true) {
            $waybill->setField('pre_trip_med_check_time', '');
        }elseif($field_name == 'pre_trip_tech_check' && $field_value != true) {
            $waybill->setField('pre_trip_tech_check_time', '');
        }elseif($field_name == 'after_trip_med_check' && $field_value != true) {
            $waybill->setField('after_trip_med_check_time', '');
        }elseif($field_name == 'after_trip_tech_check' && $field_value != true) {
            $waybill->setField('after_trip_tech_check_time', '');
        }

        $waybill->$field_name = $field_value;
        if(!$waybill->validate()) {

            $aErrors = $waybill->getErrors();
            if(isset($aErrors[$field_name])) {
                return [
                    'success' => false,
                    'field_name' => $field_name,
                    'errors' => $aErrors[$field_name]
                ];
            }
        }

        $waybill->setField($field_name, $field_value);
        $waybill = TransportWaybill::find()->where(['id' => $waybill_id])->one();


        $preliminary_results_html = '';
        $accruals_html = '';
        $correct_html = '';
        if(in_array($field_name, ['date_of_issue', 'transport_id', 'trip_transport_start', 'trip_transport_end',
            'camera_eduction', 'camera_no_record', 'hand_over_b1', 'hand_over_b2',
            'accruals_to_issue_for_trip', 'accruals_given_to_hand', 'fines_gibdd', 'another_fines',
            //'pre_trip_med_check', 'pre_trip_tech_check', 'after_trip_med_check', 'after_trip_tech_check'
        ])) {

            $waybill->updateResultFields(); // пересчитываем некоторые поля Путевого листа

            // обновленные значения из базы берем (могут отличаться форматы чисел с .00 или без)
            $waybill = TransportWaybill::find()->where(['id' => $waybill->id])->one();

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


    public function actionAjaxGetSellersTypes() {

        Yii::$app->response->format = 'json';

        $search = Yii::$app->getRequest()->post('search');
        $seller_types = TransportExpensesSellerType::find()->all();


        $out['results'] = [];
        foreach($seller_types as $seller_type) {

            $text = $seller_type->name;

            if($search != '') {
                if(mb_stripos($text, $search, 0, 'UTF-8') !== false) {
                    $out['results'][] = [
                        'id' => $seller_type->id,
                        'text' => $text,
                    ];
                }
            }else {
                $out['results'][] = [
                    'id' => $seller_type->id,
                    'text' => $text,
                ];
            }
        }

        return $out;
    }


    public function actionAjaxAddNewSellerType($new_name) {

        Yii::$app->response->format = 'json';

        $seller_type = new TransportExpensesSellerType();
        $seller_type->name = mb_strtoupper(mb_substr($new_name, 0, 1, 'UTF-8'), 'UTF-8') . mb_substr($new_name, 1, NULL, 'UTF-8');

        if($seller_type->validate() && $seller_type->save()) {
            return [
                'success' => true,
                'seller_type_id' => $seller_type->id,
                'name' => $seller_type->name
            ];
        }else {
            return [
                'success' => false,
                'errors' => $seller_type->getErrors()
            ];
        }
    }


    public function actionExploitationData() {

        $searchModel = new TransportWaybillSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('exploitation-data', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionExpenses() {

        $searchModel = new TransportExpensesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'only_with_price');

        return $this->render('expenses', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionDetailing() {

        $searchModel = new TransportExpensesDetailingSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('detailing', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionAjaxUpdateExpenseField($expense_id, $field_name, $field_value) {

        Yii::$app->response->format = 'json';

        $tr_expense = TransportExpenses::find()->where(['id' => $expense_id])->one();
        if($tr_expense == null) {
            throw new ForbiddenHttpException('Расход не найден');
        }

        $tr_expense->setField($field_name, $field_value);

        $waybill = $tr_expense->waybill;
        $waybill->updateResultFields(); // пересчитываем некоторые поля Путевого листа

        return [
            'success' => true
        ];
    }

    public function actionAjaxUpdateExpensesFields() {

        Yii::$app->response->format = 'json';

        $form_data = Yii::$app->request->post('form_data');
//        0:  => {
//          ​​id: "8"
//          expenses_is_taken: false
//          payment_comment: "ыфв"
//          payment_date: "31.03.2019"
//          payment_method_id: "2"
//          transport_expenses_paymenter_id: "48"
//        }
        $aExpensesData = [];
        foreach($form_data as $expense_data) {
            //echo "<pre>"; print_r($waybill); echo "</pre>";
            $aExpensesData[$expense_data['id']] = $expense_data;
        }

        $expenses = TransportExpenses::find()->where(['id' => array_keys($aExpensesData)])->all();
        foreach($expenses as $expense) {
            foreach($aExpensesData[$expense->id] as $field => $value) {

                if($field == 'payment_date') {
                    $expense->$field = strtotime($value);
                }elseif($field == 'expenses_is_taken') {
                    $expense->$field = $value == 'true' ? true : false;
                }else {
                    $expense->$field = $value;
                }
            }
            if(!$expense->save(false)) {
                throw new ErrorException('Не удалось сохранить расход лист');
            }

            $waybill = $expense->waybill;
            if($waybill == null) {
                throw new ErrorException('У расхода не найден связанный путевой лист');
            }
            $waybill->updateResultFields();// пересчитываем некоторые поля Путевого листа
        }

        return [
            'success' => true
        ];
    }

    public function actionAjaxUpdateWaybillField($waybill_id, $field_name, $field_value) {

        Yii::$app->response->format = 'json';

        $waybill = TransportWaybill::find()->where(['id' => $waybill_id])->one();
        if($waybill == null) {
            throw new ForbiddenHttpException('Путевой лист не найден');
        }

        $waybill->setField($field_name, $field_value);

        return [
            'success' => true
        ];
    }

    public function actionAjaxUpdateWaybillFields() {

        Yii::$app->response->format = 'json';

        $form_data = Yii::$app->request->post('form_data');
        $aWaybillsData = [];
        foreach($form_data as $waybill_data) {
            //echo "<pre>"; print_r($waybill); echo "</pre>";
            $aWaybillsData[$waybill_data['id']] = $waybill_data;
        }

        $waybills = TransportWaybill::find()->where(['id' => array_keys($aWaybillsData)])->all();
        foreach($waybills as $waybill) {
            foreach($aWaybillsData[$waybill->id] as $field => $value) {
                $waybill->$field = $value;
            }
            if(!$waybill->save(false)) {
                throw new ErrorException('Не удалось сохранить путевой лист');
            }

            $waybill->updateResultFields();// пересчитываем некоторые поля Путевого листа
        }

        return [
            'success' => true
        ];
    }

    public function actionAjaxGetTovxrash() {

        Yii::$app->response->format = 'json';

        $search = Yii::$app->getRequest()->post('search');

        $out['results'] = [];

        $sql = '
            SELECT tr_ex.transport_waybill_id as id, CONCAT(tr_ex.`price`, "-", if(ex_types.id is not null, ex_types.name, "нет"), tr_ex.doc_number) as text
            FROM `transport_expenses` tr_ex
            LEFT JOIN `transport_expenses_types` ex_types ON ex_types.id = tr_ex.expenses_type_id
            WHERE tr_ex.view_group="incoming_payment_requests"
            AND CONCAT(tr_ex.`price`, "-", if(ex_types.id is not null, ex_types.name, "нет"), tr_ex.doc_number) LIKE "%'.$search.'%"
            LIMIT 20
        ';
        $results = Yii::$app->db->createCommand($sql)->queryAll();

        foreach($results as $result) {
            $out['results'][] = [
                'id' => $result['id'],
                'text' => $result['text'],
            ];
        }

        return $out;
    }

    public function actionAjaxGetMoveExpenseForm($expense_id) {

        Yii::$app->response->format = 'json';

        $expense = TransportExpenses::find()->where(['id' => $expense_id])->one();
        if($expense == null) {
            throw new ForbiddenHttpException('Расход не найден');
        }

        return [
            'success' => true,
            'html' => $this->renderAjax('move-expense-form.php', [
                'expense' => $expense
            ]),
        ];
    }

    public function actionAjaxSearchWaybills($date, $transport_id) {

        Yii::$app->response->format = 'json';

        if(empty($date)) {
            throw new ForbiddenHttpException('Необходимо выбрать дату');
        }
        if($transport_id < 1) {
            throw new ForbiddenHttpException('Необходимо выбрать транспорт');
        }


        $waybills = TransportWaybill::find()
            ->where(['date_of_issue' => strtotime($date)])
            ->andWhere(['transport_id' => $transport_id])
            ->all();

        $aWaybillList = [];
        foreach ($waybills as $waybill) {

            $title = '';
            if($waybill->date_of_issue > 0) {
                $title .= ' от '.date("d.m.Y", $waybill->date_of_issue).', '.Helper::getWeekDay($waybill->date_of_issue);
            }
            if($waybill->transport_id > 0 && $waybill->transport != null) {
                $title .= ' / '.$waybill->transport->sh_model.' '.$waybill->transport->car_reg;
            }
            if($waybill->driver_id > 0 && $waybill->driver != null) {
                $title .= ' / '.$waybill->driver->fio;
            }

            $aWaybillList[$waybill->id] = $title;
        }

//        $aWaybillList[123] = 'qqq';
//        $aWaybillList[555] = 'dfgdgd';

        return [
            'success' => true,
            'list' => $aWaybillList
        ];
    }

    public function actionAjaxMoveExpense($expense_id, $waybill_to_id) {

        Yii::$app->response->format = 'json';

        $expense = TransportExpenses::find()->where(['id' => $expense_id])->one();
        if($expense == null) {
            throw new ForbiddenHttpException('Расход не найден');
        }

        $waybill_to = TransportWaybill::find()->where(['id' => $waybill_to_id])->one();
        if($waybill_to == null) {
            throw new ForbiddenHttpException('Путевой лист (реципиент) не найден');
        }

        $waybill_from = $expense->waybill;
        if($waybill_from == null) {
            throw new ForbiddenHttpException('Путевой лист (донор) не найден');
        }

        $expense->setField('transport_waybill_id', $waybill_to->id);

        // пересчет показателей в путевом листе - реципиенте
        $waybill_from->updateResultFields();

        // пересчет показателей в путевом листе - доноре
        $waybill_to->updateResultFields();


        return [
            'success' => true
        ];
    }
}
