<?php

use app\models\Access;
use app\models\OperatorBeelineSubscription;
use app\models\Setting;
use yii\helpers\Html;
use app\components\Helper;
use app\models\Direction;
use app\models\Trip;
use app\models\ScheduleTrip;
use yii\helpers\Url;
use yii\widgets\MaskedInput;

$this->registerJsFile('js/site/create-order-modal.js', ['depends'=>'app\assets\AppAsset']);
$this->registerJsFile('js/site/index.js', ['depends'=>'app\assets\AppAsset']);
$this->registerJsFile('https://api-maps.yandex.ru/2.1/?lang=ru_RU&apikey=5c7acdc8-48c9-43d9-9f44-2e6b9e178101', ['depends' => 'app\assets\AdminAsset']);

$user = Yii::$app->user->identity;

$setting = Setting::find()->where(['id' => 1])->one();

//echo "aDirections:<pre>"; print_r($aDirections); echo "</pre>";
?>
<div id="main-page" class="row" test="134">
    <!--<div class="col-tobus-left">&nbsp;</div>-->

    <div id="directions-trips-block"  class="col-tobus-center__">
        <?php
        echo $this->render('/site/directions-trips-block', [
            'aDirections' => $aDirections,
            'view' => 'trip_list'
        ]); ?>
    </div>

    <?php /*
    <div class="col-tobus-right-1">&nbsp;</div>
    */ ?>

    <div id="main-buttons-block" class="col-tobus-right-2-3__">

        <br />
        <div id="account-block">
            <span>Имя пользователя.: <b><?= ($user != null ? $user->fullname : '');?></b></span> <a class="user-logout" href="/site/logout" title="Выход"><i class="glyphicon glyphicon-remove-sign"></i></a><br/>
            <span class="user_role">Группа: <b><?= ($user != null && $user->userRole ? $user->userRole->name : ''); ?></b></span><br/>
            <span>Время входа: <b><?= ($user != null && $user->last_login_date > 0 ? date('Y.m.d H:i:s', ($user->last_login_date)) : '');?></b></span><br/>

            <?php // if(in_array(Yii::$app->session->get('role_alias'), ['root', 'admin', 'warehouse_turnover'])) { ?>
            <?php if (Access::hasUserAccess('storage', 'module')) { ?>
                <a href="/storage">Перейти на склад</a>
            <?php } ?>
            <?php if (Access::hasUserAccess('waybill', 'module')) { ?>
            <?php // if(in_array(Yii::$app->session->get('role_alias'), ['root', 'admin', 'graph_operator', 'warehouse_turnover'])) { ?>
                <br /><a href="/waybill/transport-waybill/list">К путевым листам</a>
            <?php } ?>
            <hr/>

            <?php
            if (Access::hasUserAccess('call_block', 'page_part')) {

                $current_operator_subscription = null;
                if ($user != null) {
                    $current_operator_subscription = OperatorBeelineSubscription::find()->where(['operator_id' => $user->id])->one();
                }


                if ($current_operator_subscription == null) {

                    // список свободных операторов
                    $all_subscriptions = OperatorBeelineSubscription::find()
                        //->where(['operator_id' => NULL])
                        ->orderBy(['minutes' => SORT_DESC])
                        ->all();
                    $aSubscription = [];
                    foreach ($all_subscriptions as $subscription) {
                        $aSubscription[$subscription->id] = $subscription->minutes . ' мин - ' . $subscription->name . ($subscription->operator_id > 0 ? ' - <i class="occupied-subscription">Занят ' . $subscription->operator_id . '</i>' : '');
                    }

                    //echo Html::dropDownList('operator_subscription_id', 0, $aSubscription, ['class' => 'form-control']);
                    ?>

                    <div id="subscriptions-list" class="btn-group">
                        <button type="button" data-toggle="dropdown" class="btn btn-default dropdown-toggle">Выберите
                            номер <span class="caret"></span></button>
                        <ul class="dropdown-menu">
                            <?php foreach ($aSubscription as $id => $name) { ?>
                                <li sub-id="<?= $id ?>"><?= $name ?></li>
                            <?php } ?>
                        </ul>
                    </div>

                    <br/>
                    <?= Html::a('Подключиться к АТС', ['#'], ['class' => 'btn btn-default btn-create-subscription']); ?>

                <?php } else {

                    ?>
                    <div id="subscriptions-list" class="btn-group">
                        <button type="button" data-toggle="dropdown"
                                class="btn btn-default dropdown-toggle disabled"><?= ($current_operator_subscription->minutes . ' мин - ' . $current_operator_subscription->name) ?>
                            <span class="caret"></span></button>
                    </div>
                    <br/>
                    <?php
                    if ($current_operator_subscription->status == 'ONLINE') {
                        echo Html::a('Онлайн', ['#'], ['class' => 'btn btn-default btn-operator-online']);
                    } elseif ($current_operator_subscription->status == 'OFFLINE') {
                        echo Html::a('Отключиться от АТС', ['#'], ['class' => 'btn btn-default btn-delete-subscription']);
                        echo '<br />';
                        echo Html::a('Оффлайн', ['#'], ['class' => 'btn btn-danger btn-operator-offline blink']);
                    }
                }
                echo '<hr />';
            }
            ?>

        </div>

        <?php if (Access::hasUserAccess('create_order_block', 'page_part')) { ?>
            <?php //if(!in_array(Yii::$app->session->get('role_alias'), ['graph_operator', 'warehouse_turnover'])) { ?>
            <?= Html::a('Запись на сегодня', ['#'], ['id' => 'new-order-today', 'class' => 'btn btn-default create-order', 'style' => 'margin-top: 0;']); ?>
            <br />
        <?php //} ?>

        <?php //if(!in_array(Yii::$app->session->get('role_alias'), ['graph_operator', 'warehouse_turnover'])) { ?>
            <?= Html::a('Записать на завтра', ['#'], ['id' => 'new-order-tomorrow', 'class' => 'btn btn-default create-order']); ?>
            <br />
        <?php //} ?>

        <?php //if(!in_array(Yii::$app->session->get('role_alias'), ['graph_operator', 'warehouse_turnover'])) { ?>
            <?= Html::a('Записать на другой день', ['#'], ['id' => 'new-order-another-day', 'class' => 'btn btn-default create-order']); ?>
            <hr />
        <?php } ?>

	
	<?php
		if(isset($_GET["date"])){
			$date = $_GET["date"];
		} else {
			$date = date('d.m.Y');
		}
	?>

        <?php // if(!in_array(Yii::$app->session->get('role_alias'), ['graph_operator', 'warehouse_turnover'])) { ?>
        <?php if(Access::hasUserAccess('set-trips', 'page_url')) { ?>
            <?= Html::a('Расстановка', ['/trip/set-trips?date='.$date], ['id' => 'edit-trip', 'class' => 'btn btn-default']); ?>
            <br />
        <?php } ?>

        <?php // if(!in_array(Yii::$app->session->get('role_alias'), ['graph_operator', 'warehouse_turnover'])) { ?>
        <?php if(Access::hasUserAccess('day_report', 'page_part')) { ?>
            <?= Html::a('Отчет отображаемого дня', ['#'], ['id' => 'day-report', 'class' => 'btn btn-default', 'prev-date' => date("d.m.Y", strtotime($date) - 86400), 'next-date' => date("d.m.Y", strtotime($date) + 86400) ]); ?>
            <br />
        <?php } ?>
        <?php //if(!in_array(Yii::$app->session->get('role_alias'), ['graph_operator', 'warehouse_turnover'])) { ?>
        <?php if(Access::hasUserAccess('calculate_with_formula', 'page_part')) { ?>
            <?= Html::a('Расчеты', ['#'], ['id' => 'calculations', 'class' => 'btn btn-success']); ?>
            <br />
        <?php } ?>

        <?php if(Access::hasUserAccess('ejsv')) { ?>
            <?= Html::a('ЭЖСВ', ['#'], ['id' => 'ejsv', 'class' => 'btn btn-success']); ?>
        <?php } ?>
        <hr />

        <?php //if(!in_array(Yii::$app->session->get('role_alias'), ['graph_operator', 'manager', ])) { ?>
        <?php if(Access::hasUserAccess('admin', 'module')) { ?>
            <?= Html::a('Панель администратора', ['/admin/'], ['id' => 'admin-panel', 'class' => 'btn btn-default']); ?>
            <br />
        <?php } ?>

        <?php
//        if(
//            !in_array(Yii::$app->session->get('role_alias'), ['graph_operator', 'warehouse_turnover'])
//            && ($setting->access_to_client_info_main_page == true || in_array(Yii::$app->session->get('role_alias'), ['root', 'admin']))
//        ) {
        if($setting->access_to_client_info_main_page == true && Access::hasUserAccess('search_client_by_phone', 'page_part')) { ?>

            <?= MaskedInput::widget([
                'name' => 'client-search',
                'mask' => '+7-999-999-9999',
                'options' => [
                    'id' => 'client-search',
                    'placeholder' => 'Поиск пассажира по номеру'
                ]
            ]); ?>
            <br />
        <?php } ?>

        <?php
        //if(in_array(Yii::$app->session->get('role_alias'), ['root', 'admin'])) {
        if(Access::hasUserAccess('admin_button_open_chat', 'page_part')) { ?>
            <?= Html::a('Оперативный чат', ['#'], ['id' => 'open-chat', 'class' => 'btn btn-success']); ?>
        <?php } ?>
    </div>

</div>