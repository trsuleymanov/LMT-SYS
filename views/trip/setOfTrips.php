<?php

use app\models\Access;
use yii\helpers\Html;

$this->registerJsFile('js/site/create-order-modal.js', ['depends'=>'app\assets\AppAsset']);
$this->registerJsFile('js/site/setOfTrips.js', ['depends'=>'app\assets\AppAsset']);
$this->registerJsFile('js/site/modalAddTripTransport.js', ['depends'=>'app\assets\AppAsset']);

$this->title = 'Расстановка '.date('d.m.Y', $selected_unixdate);
$this->params['breadcrumbs'][] = $this->title;

//echo "aDirections:<pre>"; print_r($aDirections); echo "</pre>";
?>
<div id="set-of-trips-page" date="<?= date('d.m.Y', $selected_unixdate) ?>" class="row">

    <div id="directions-trips-block" class="col-tobus-center__">
        <?php
//        echo $this->render('_set-trip-directions', [
//            'aDirections' => $aDirections,
//        ]);

        echo $this->render('/site/directions-trips-block', [
            'aDirections' => $aDirections,
            'view' => 'set_trip_list'
        ]);
        ?>
    </div>

    <?php /*
    <div class="col-tobus-right-1">&nbsp;</div>
    */ ?>

    <div id="main-buttons-block" class="col-tobus-right-2-3__">

        <div id="account-block">
            <span>Имя пользователя: <b><?= ($user != null ? $user->fullname : '');?></b></span> <a class="user-logout" href="/site/logout" title="Выход"><i class="glyphicon glyphicon-remove-sign"></i></a><br/>
            <span class="user_role">Группа: <b><?= ($user != null && $user->userRole ? $user->userRole->name : ''); ?></b></span><br/>
            <span>Время входа: <b><?= ($user != null && $user->last_login_date > 0 ? date('Y.m.d H:i:s', ($user->last_login_date)) : '');?></b></span><br/>
            <hr/>
        </div>

        
		<button id="add-trip" class="btn btn-primary">Добавить рейс</button>
        <br />
		<button id="merge-trips" class="btn btn-primary">Объединить рейсы</button>
        <br />
		<button id="second-transport" class="btn btn-primary">Вторые рейсы</button>
        <br />

        <?php //if(in_array(Yii::$app->session->get('role_alias'), ['root', 'admin'])) { ?>
        <?php if(Access::hasUserAccess('block_set_commercial_trips', 'page_part')) { ?>
            <button id="set-commercial-trips" class="btn btn-danger">Установить коммерческие рейсы</button>
            <br />
            <button id="unset-commercial-trips" class="btn btn-warning">Отменить коммерческие рейсы</button>
            <br />
        <?php } ?>

        <button id="create-reserv-trip" class="btn btn-primary">Создать резервный рейс</button>
        <br />

        <?php //if(in_array(Yii::$app->session->get('role_alias'), ['root', 'admin'])) { ?>
        <?php if(Access::hasUserAccess('open_transport_efficiency', 'page_part')) { ?>
            <button id="open-transport-efficiency" class="btn btn-primary">График</button>
            <br />
        <?php } ?>


    </div>


</div>