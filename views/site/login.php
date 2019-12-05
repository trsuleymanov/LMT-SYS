<?php

use app\models\OperatorBeelineSubscription;
use app\models\UserRole;
use app\widgets\SelectWidget;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\models\User;
use yii\helpers\ArrayHelper;
use yii\jui\AutoComplete;
use yii\web\JsExpression;

$this->registerJsFile('js/site/login.js', ['depends'=>'app\assets\AppAsset']);

$this->title = 'Авторизация';

$user_roles = UserRole::find()->where(['alias' => ['root', 'admin', 'editor', 'manager', 'graph_operator', 'warehouse_turnover']])->all();
$users = User::find()->where(['blocked' => 0])->andWhere(['role_id' => ArrayHelper::map($user_roles, 'id', 'id')])->orderBy(['username' => SORT_ASC])->all();
?>

<div class="form-signin">

    <?php $form = ActiveForm::begin([
        'action' => '',
        'enableAjaxValidation' => false,
        'enableClientValidation' => false,
    ]); ?>

    <h2 class="form-signin-heading">Вход</h2>

    <p style="color: #a94442;"><?= Yii::$app->session->getFlash('error') ?></p>

    <?= $form->field($model, 'rememberMe')->label(false)->hiddenInput() ?>



    <?php
    $aUsernames = ArrayHelper::map($users, 'username', 'username');
    if(isset($aUsernames[$username])) {
        $model->username = $username;
    }

    echo $form->field($model, 'username')->widget(AutoComplete::classname(), [
        'clientOptions' => [
            'source' => new JsExpression("function(request, response) {
                $.ajax({
                    url: '/user/ajax-get-usernames',
                    data: {
                        search: request.term
                    },
                    type: 'post',
                    success: response,
                    error: function (data, textStatus, jqXHR) {
                        alert('error');
                    }
                });
            }"),
            'minLength'=>'2',
        ],
        'options' => [
            'placeholder' => 'Пользователь',
            'class' => 'input-block-level',
            'name' => 'LoginForm[username]',
        ]
    ])
    ->label(false);
    ?>

    <?= $form->field($model, 'password')
        ->passwordInput([
            'class'=>'input-block-level',
            'placeholder' => 'Пароль',
        ])
        ->label(false);
    ?>

    <?php
    /*
    // список свободных операторов
    $operator_subscriptions = OperatorBeelineSubscription::find()
        ->where(['operator_id' => NULL])
        ->orderBy(['minutes' => SORT_DESC])
        ->all();
    $aSubscription = [];
    foreach($operator_subscriptions as $operator_subscription) {
        $aSubscription[$operator_subscription->id] = $operator_subscription->name.' ('.$operator_subscription->minutes.' мин)';
    }

    echo $form->field($model, 'operator_subscription_id')
        ->dropDownList([0 => 'Нет телефона'] + $aSubscription)
        ->label(false);
    */
    ?>

    <div class="text-center">
         <?= Html::submitButton('Войти', ['class' => 'btn', 'name' => 'login-button',]) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
<a href="/" style="color: white;"><div style="width: 100%; height: 50px;">ПЕРЕХОД НА ГЛАВНУЮ</div></a>

