<?php

use app\models\User;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AdminAsset;
use app\assets\FontAwesomeAsset;
use yii\bootstrap\Modal;
use app\components\Helper;

AdminAsset::register($this);
FontAwesomeAsset::register($this);

$cookie = Yii::$app->getRequest()->getCookies();
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <?php $this->head() ?>
</head>
<body class="hold-transition skin-blue fixed sidebar-mini<?= $cookie->getValue('main-menu', 0) ? ' sidebar-collapse' : '' ?>">
<?php /* skin-black fixed sidebar-mini  pace-done */ ?>
<?php $this->beginBody() ?>


<div class="wrap">
    <!-- Main Header -->
    <header class="main-header">

        <!-- Logo -->
        <a href="/admin" class="logo">
            <span class="logo-lg"><b>Администратор</b></span>
        </a>

        <!-- Header Navbar -->
        <?php /*
        <nav class="navbar navbar-static-top" role="navigation">
            <!-- Sidebar toggle button-->
            <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                <span class="sr-only">Toggle navigation</span>
            </a>
            <!-- Navbar Right Menu -->
            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <!-- Messages: style can be found in dropdown.less-->
                    <li class="dropdown messages-menu">
                        <!-- Menu toggle button -->
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-envelope-o"></i>
                            <span class="label label-success">4</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="header">You have 4 messages</li>
                            <li>
                                <!-- inner menu: contains the messages -->
                                <ul class="menu">
                                    <li><!-- start message -->
                                        <a href="#">
                                            <div class="pull-left">
                                                <!-- User Image -->
                                                <img src="dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">
                                            </div>
                                            <!-- Message title and timestamp -->
                                            <h4>
                                                Support Team
                                                <small><i class="fa fa-clock-o"></i> 5 mins</small>
                                            </h4>
                                            <!-- The message -->
                                            <p>Why not buy a new awesome theme?</p>
                                        </a>
                                    </li>
                                    <!-- end message -->
                                </ul>
                                <!-- /.menu -->
                            </li>
                            <li class="footer"><a href="#">See All Messages</a></li>
                        </ul>
                    </li>
                    <!-- /.messages-menu -->

                    <!-- Notifications Menu -->
                    <li class="dropdown notifications-menu">
                        <!-- Menu toggle button -->
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-bell-o"></i>
                            <span class="label label-warning">10</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="header">You have 10 notifications</li>
                            <li>
                                <!-- Inner Menu: contains the notifications -->
                                <ul class="menu">
                                    <li><!-- start notification -->
                                        <a href="#">
                                            <i class="fa fa-users text-aqua"></i> 5 new members joined today
                                        </a>
                                    </li>
                                    <!-- end notification -->
                                </ul>
                            </li>
                            <li class="footer"><a href="#">View all</a></li>
                        </ul>
                    </li>
                    <!-- Tasks Menu -->
                    <li class="dropdown tasks-menu">
                        <!-- Menu Toggle Button -->
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-flag-o"></i>
                            <span class="label label-danger">9</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="header">You have 9 tasks</li>
                            <li>
                                <!-- Inner menu: contains the tasks -->
                                <ul class="menu">
                                    <li><!-- Task item -->
                                        <a href="#">
                                            <!-- Task title and progress text -->
                                            <h3>
                                                Design some buttons
                                                <small class="pull-right">20%</small>
                                            </h3>
                                            <!-- The progress bar -->
                                            <div class="progress xs">
                                                <!-- Change the css width attribute to simulate progress -->
                                                <div class="progress-bar progress-bar-aqua" style="width: 20%" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                                                    <span class="sr-only">20% Complete</span>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                    <!-- end task item -->
                                </ul>
                            </li>
                            <li class="footer">
                                <a href="#">View all tasks</a>
                            </li>
                        </ul>
                    </li>
                    <!-- User Account Menu -->
                    <li class="dropdown user user-menu">
                        <!-- Menu Toggle Button -->
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <!-- The user image in the navbar-->
                            <img src="dist/img/user2-160x160.jpg" class="user-image" alt="User Image">
                            <!-- hidden-xs hides the username on small devices so only the image appears. -->
                            <span class="hidden-xs">Alexander Pierce</span>
                        </a>
                        <ul class="dropdown-menu">
                            <!-- The user image in the menu -->
                            <li class="user-header">
                                <img src="dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">

                                <p>
                                    Alexander Pierce - Web Developer
                                    <small>Member since Nov. 2012</small>
                                </p>
                            </li>
                            <!-- Menu Body -->
                            <li class="user-body">
                                <div class="row">
                                    <div class="col-xs-4 text-center">
                                        <a href="#">Followers</a>
                                    </div>
                                    <div class="col-xs-4 text-center">
                                        <a href="#">Sales</a>
                                    </div>
                                    <div class="col-xs-4 text-center">
                                        <a href="#">Friends</a>
                                    </div>
                                </div>
                                <!-- /.row -->
                            </li>
                            <!-- Menu Footer-->
                            <li class="user-footer">
                                <div class="pull-left">
                                    <a href="#" class="btn btn-default btn-flat">Profile</a>
                                </div>
                                <div class="pull-right">
                                    <a href="#" class="btn btn-default btn-flat">Sign out</a>
                                </div>
                            </li>
                        </ul>
                    </li>
                    <!-- Control Sidebar Toggle Button -->
                    <li>
                        <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
                    </li>
                </ul>
            </div>
        </nav>
        */ ?>

        <nav class="navbar navbar-static-top" role="navigation">
            <!-- Sidebar toggle button-->
            <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                <span class="sr-only">Toggle navigation</span>
            </a>
            <!-- Navbar Right Menu -->
            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <li><a href="/site/logout">Выход (<?= Yii::$app->user->identity->username ?>) </a> </li>
                    <?php
                    if(Yii::$app->session->get('role_alias') == 'warehouse_turnover') {
                        $url = '/storage';
                    }else {
                        $url = '/';
                    }
                    ?>
                    <li><a href="<?= $url ?>" style="font-size: 24px; font-weight: 700;">LMT-SYS</a></li>
                    <li style="padding: 7px 0 7px 15px; font-size: 12px; text-align: left; width: 160px; color: #FFFFFF;">
                        Текущая дата:<br />
                        <span id="system-time"><?= Helper::getMainDate(time(), 1); ?></span>
                    </li>
                </ul>
            </div>
        </nav>

        <?php
        /*NavBar::begin([
            'brandLabel' => ' <span class="sr-only">Toggle navigation</span> В диспетчерскую',
            'brandUrl' => Yii::$app->homeUrl,
            'options' => [
                //'class' => 'navbar-inverse navbar-fixed-top'
                'class' => 'navbar navbar-static-top'
            ],
        ]);
        ?>
        <?php
        echo Nav::widget([
            'options' => [
                //'class' => 'navbar-nav navbar-right'
                'class' => 'navbar-custom-menu'
            ],
            'items' => [
                Yii::$app->user->isGuest ? (
                ['label' => 'Login', 'url' => ['/site/login']]
                ) : (
                    '<li>'
                    . Html::beginForm(['/site/logout'], 'post')
                    . Html::submitButton(
                        'Logout (' . Yii::$app->user->identity->username . ')',
                        ['class' => 'btn btn-link logout']
                    )
                    . Html::endForm()
                    . '</li>'
                )
            ],
        ]);
        NavBar::end();*/
        ?>

    </header>
    <!-- Left side column. contains the logo and sidebar -->
    <aside class="main-sidebar">

        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">

            <?php
            $current_module = Yii::$app->controller->module->id;
            $current_controller = Yii::$app->controller->id;
            $current_route = $this->context->route;

//            echo "current_module=$current_module <br />";
//            echo "current_controller=$current_controller <br />";
//            echo "current_route=$current_route <br />";
            ?>

            <!-- Sidebar Menu -->
            <ul class="sidebar-menu">
                <?php if(in_array(Yii::$app->session->get('role_alias'), ['root', 'admin'])) { ?>
                    <li class="treeview <?= (in_array($current_controller, ['city', 'direction', ]) || in_array($current_route, ['admin/setting/vrpt', 'admin/yandex-point/statistics'])) ? 'active' : '' ?>">
                        <a href="#">
                            <i class="fa fa-exchange"></i> <span>Маршруты и точки</span>
                            <span class="pull-right-container">
                              <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li<?= $current_controller == 'city' ? ' class="active"' : '' ?>>
                                <?= Html::a('<i class="glyphicon glyphicon-map-marker"></i> <span>Города</span>', '/admin/city'); ?>
                            </li>
                            <li<?= $current_controller == 'direction' ? ' class="active"' : '' ?>>
                                <?= Html::a('<i class="glyphicon glyphicon-road"></i> <span>Направления</span>', '/admin/direction'); ?>
                            </li>
                            <li<?= $current_route == 'admin/setting/vrpt' ? ' class="active"' : '' ?>>
                                <?= Html::a('<i class="glyphicon glyphicon-cog"></i> <span>А-ВРПТ</span>', '/admin/setting/vrpt'); ?>
                            </li>
                            <li<?= $current_route == 'admin/yandex-point/statistics' ? ' class="active"' : '' ?>>
                                <?= Html::a('<i class="glyphicon glyphicon-map-marker"></i> <span>Статистика точек</span>', '/admin/yandex-point/statistics'); ?>
                            </li>
                        </ul>
                    </li>
                <?php } ?>

                <?php if(in_array(Yii::$app->session->get('role_alias'), ['root', 'admin'])) { ?>
                    <li<?= ($current_module == 'admin' && ($current_controller == 'user' || $current_route == 'admin/access/index') ? ' class="active"' : '') ?>>
                        <a href="#">
                            <i class="glyphicon glyphicon-user"></i> <span>Пользователи</span>
                            <span class="pull-right-container">
                              <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li<?= $current_controller == 'user' && $current_route == 'admin/user/index' ? ' class="active"' : '' ?>>
                                <?= Html::a('<i class="fa fa-meh-o"></i> <span>Пользователи</span>', '/admin/user'); ?>
                            </li>
                            <li<?= $current_route == 'admin/access/index' ? ' class="active"' : '' ?>>
                                <?= Html::a('<i class="glyphicon glyphicon-cog"></i> <span>Управление доступами</span>', '/admin/access/index'); ?>
                            </li>
                        </ul>
                    </li>
                <?php } ?>

                <?php if(in_array(Yii::$app->session->get('role_alias'), ['root', 'admin'])) { ?>
                    <li<?= ($current_module == 'admin' && in_array($current_controller, ['operator', 'operator-accounting', 'trip-operation', 'working-shift'])? ' class="active"' : '') ?>>
                        <a href="#">
                            <i class="glyphicon glyphicon-user"></i> <span>Операторы</span>
                            <span class="pull-right-container">
                              <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li<?= $current_controller == 'operator' && $current_route == 'admin/operator/online-operators' ? ' class="active"' : '' ?>>
                                <?= Html::a('<i class="fa fa-lightbulb-o"></i> <span>Операторы онлайн</span>', '/admin/operator/online-operators'); ?>
                            </li>
                            <li<?= $current_controller == 'operator-accounting' ? ' class="active"' : '' ?>>
                                <?= Html::a('<i class="fa fa-fax"></i> <span>Учет работы оператора</span>', '/admin/operator-accounting/index?sort=-created_at'); ?>
                            </li>
                            <li<?= $current_controller == 'trip-operation' ? ' class="active"' : '' ?>>
                                <?= Html::a('<i class="fa fa-list-alt"></i> <span>Действия с рейсами</span>', '/admin/trip-operation'); ?>
                            </li>
                            <li<?= $current_controller == 'working-shift' ? ' class="active"' : '' ?>>
                                <?= Html::a('<i class="fa fa-mobile"></i> <span>Контроль блокировки телефона</span>', '/admin/working-shift'); ?>
                            </li>
                        </ul>
                    </li>
                <?php } ?>



                <?php if(in_array(Yii::$app->session->get('role_alias'), ['root', 'admin', 'editor', 'graph_operator'])) { ?>
                    <li class="treeview <?= ($current_module == 'admin' && in_array($current_controller, ['transport', 'driver', 'driver-accounting', 'driver-photo', 'magic-device-code'])) ? 'active' : '' ?>">
                        <a href="#">
                            <i class="fa fa-truck"></i> <span>Транспорт и водители</span>
                            <span class="pull-right-container">
                              <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <?php if(in_array(Yii::$app->session->get('role_alias'), ['root', 'admin', 'editor'])) { ?>
                                <li<?= $current_controller == 'transport' ? ' class="active"' : '' ?>>
                                    <?= Html::a('<i class="fa fa-bus"></i> <span>Машины</span>', '/admin/transport'); ?>
                                </li>
                            <?php } ?>
                            <li<?= $current_controller == 'driver' && $current_route == 'admin/driver/index' ? ' class="active"' : '' ?>>
                                <?= Html::a('<i class="fa fa-wheelchair-alt"></i> <span>Водители</span>', '/admin/driver'); ?>
                            </li>
                            <?php if(in_array(Yii::$app->session->get('role_alias'), ['root', 'admin', 'graph_operator'])) { ?>
                                <li<?= $current_controller == 'driver-accounting' ? ' class="active"' : '' ?>>
                                    <?= Html::a('<i class="fa fa-list-alt"></i> <span>Учет работы водителя</span>', '/admin/driver-accounting?sort=-trip_id'); ?>
                                </li>
                                <li<?= $current_controller == 'driver-photo' ? ' class="active"' : '' ?>>
                                    <?= Html::a('<i class="fa fa-list-alt"></i> <span>Контроль АЗС</span>', '/admin/driver-photo'); ?>
                                </li>
                                <li<?= $current_controller == 'driver' && $current_route == 'admin/driver/online-drivers' ? ' class="active"' : '' ?>>
                                    <?= Html::a('<i class="fa fa-lightbulb-o"></i> <span>Водители онлайн</span>', '/admin/driver/online-drivers'); ?>
                                </li>
                            <?php } ?>
                            <?php if(in_array(Yii::$app->session->get('role_alias'), ['root', 'admin',])) { ?>
                                <li<?= $current_controller == 'magic-device-code' ? ' class="active"' : '' ?>>
                                    <?= Html::a('<i class="fa fa-mobile"></i> <span>Магические устройства</span>', '/admin/magic-device-code'); ?>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php } ?>

                <?php if(in_array(Yii::$app->session->get('role_alias'), ['root', 'admin'])) { ?>
                    <li class="treeview <?= (in_array($current_controller, ['operator-subscription', 'call-case', ]) || in_array($current_route, ['admin/call/index', 'admin/setting/call-auth']) ? 'active' : '') ?>">
                        <a href="#">
                            <i class="glyphicon glyphicon-headphones"></i> <span>Call-центр</span>
                            <span class="pull-right-container">
                              <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li<?= $current_controller == 'operator-subscription' ? ' class="active"' : '' ?>>
                                <?= Html::a('<i class="fa fa-reddit-alien"></i> <span>Агенты АТС</span>', '/admin/operator-subscription'); ?>
                            </li>
                            <li<?= $current_route == 'admin/call-case/index' ? ' class="active"' : '' ?>>
                                <?= Html::a('<i class="glyphicon glyphicon-headphones"></i> <span>История обращений</span>', '/admin/call-case/index?sort=-id'); ?>
                            </li>
                            <li<?= $current_route == 'admin/call/index' ? ' class="active"' : '' ?>>
                                <?= Html::a('<i class="glyphicon glyphicon-headphones"></i> <span>История звонков</span>', '/admin/call/index?sort=-id'); ?>
                            </li>
                            <li<?= $current_route == 'admin/setting/call-auth' ? ' class="active"' : '' ?>>
                                <?= Html::a('<i class="glyphicon glyphicon-headphones"></i> <span>CALL-авторизация</span>', '/admin/setting/call-auth'); ?>
                            </li>
                        </ul>
                    </li>
                <?php } ?>


                <?php
                if(in_array(Yii::$app->session->get('role_alias'), ['root', 'admin'])) { ?>
                    <li<?= ($current_module == 'admin' && (in_array($current_route, ['admin/setting/another'])) ? ' class="active"' : '') ?>>
                        <a href="#">
                            <i class="glyphicon glyphicon-cog"></i> <span>Настройки</span>
                            <span class="pull-right-container">
                              <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li<?= $current_route == 'admin/setting/another' ? ' class="active"' : '' ?>>
                                <?= Html::a('<i class="glyphicon glyphicon-th-list"></i> <span>Другие настройки</span>', '/admin/setting/another'); ?>
                            </li>
                        </ul>
                    </li>
                <?php } ?>

                <?php if(in_array(Yii::$app->session->get('role_alias'), ['root', 'admin'])) { ?>
                    <li class="treeview <?= ($current_module == 'admin' && in_array($current_controller, ['client', 'order', 'loyality'])) ? 'active' : '' ?>">
                        <a href="#">
                            <i class="fa fa-group"></i> <span>Пассажиры</span>
                            <span class="pull-right-container">
                              <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li<?= $current_controller == 'client' ? ' class="active"' : '' ?>>
                                <?= Html::a('<i class="fa fa-meh-o"></i> <span>Клиенты</span>', '/admin/client'); ?>
                            </li>
                            <li<?= $current_route == 'admin/order/index' ? ' class="active"' : '' ?>>
                                <?= Html::a('<i class="fa fa-tasks"></i> <span>Заказы</span>', '/admin/order'); ?>
                            </li>
                            <li<?= $current_route == 'admin/order/abnormal-order-list' ? ' class="active"' : '' ?>>
                                <?= Html::a('<i class="fa fa-bug"></i> <span>Аномальные заказы</span>', '/admin/order/abnormal-order-list'); ?>
                            </li>
                            <li<?= $current_route == 'admin/order/electronic-request-list' ? ' class="active"' : '' ?>>
                                <?= Html::a('<i class="glyphicon glyphicon-log-in"></i> <span>Электронные заявки</span>', '/admin/order/electronic-request-list'); ?>
                            </li>
                            <li<?= $current_route == 'admin/loyality/index' ? ' class="active"' : '' ?>>
                                <?= Html::a('<i class="glyphicon glyphicon-thumbs-up"></i> <span>Лояльность</span>', '/admin/loyality/index'); ?>
                            </li>
                            <li<?= $current_route == 'admin/loyality/recount' ? ' class="active"' : '' ?>>
                                <?= Html::a('<i class="glyphicon glyphicon-asterisk"></i> <span>Пересчет</span>', '/admin/loyality/recount'); ?>
                            </li>
                        </ul>
                    </li>
                <?php } ?>

                <?php if(in_array(Yii::$app->session->get('role_alias'), ['root', 'admin'])) { ?>

                    <li class="treeview <?= ($current_module == 'admin' && in_array($current_controller, ['informer-office', 'advertising-source', 'advertising-source-report']) ? 'active' : '') ?>">
                        <a href="#">
                            <i class="fa fa-group"></i> <span>Источники</span>
                            <span class="pull-right-container">
                              <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li<?= $current_controller == 'informer-office' && $current_route == 'admin/informer-office/index' ? ' class="active"' : '' ?>>
                                <?= Html::a('<i class="fa fa-meh-o"></i> <span>Источники заказов</span>', '/admin/informer-office'); ?>
                            </li>
                            <li<?= $current_controller == 'informer-office' && $current_route == 'admin/informer-office/report' ? ' class="active"' : '' ?>>
                                <?= Html::a('<i class="fa fa-tasks"></i> <span>Активность ИЗ</span>', '/admin/informer-office/report'); ?>
                            </li>
                            <li<?= $current_controller == 'advertising-source' ? ' class="active"' : '' ?>>
                                <?= Html::a('<i class="glyphicon glyphicon-random"></i> <span>Рекламные источники</span>', '/admin/advertising-source'); ?>
                            </li>
                            <li<?= $current_controller == 'advertising-source-report' ? ' class="active"' : '' ?>>
                                <?= Html::a('<i class="fa fa-tasks"></i> <span>Активность РИ</span>', '/admin/advertising-source-report'); ?>
                            </li>
                        </ul>
                    </li>

                <?php } ?>


                <?php if(in_array(Yii::$app->session->get('role_alias'), ['root', 'admin'])) { ?>
                    <li<?= (in_array($current_controller, ['tariff', 'cashback-setting', 'formula', 'day-report-transport-circle', 'do-tariff', 'transport-waybill-report', 'notaccountability-transport-report', 'yandex-point-category']) || in_array($current_route, ['admin/setting/loyalty-switch'])  ? ' class="active"' : '') ?>>
                        <a href="#">
                            <i class="glyphicon glyphicon-piggy-bank"></i> <span>Финансы</span>
                            <span class="pull-right-container">
                              <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li<?= $current_controller == 'tariff' ? ' class="active"' : '' ?>>
                                <?= Html::a('<i class="glyphicon glyphicon-piggy-bank"></i> <span>Тарифы</span>', '/admin/tariff'); ?>
                            </li>
                            <li<?= $current_route == 'admin/formula/index' ? ' class="active"' : '' ?>>
                                <?= Html::a('<i class="glyphicon glyphicon-list-alt"></i> <span>Формулы</span>', '/admin/formula'); ?>
                            </li>
                            <li<?= $current_route == 'admin/day-report-transport-circle/index' ? ' class="active"' : '' ?>>
                                <?= Html::a('<i class="glyphicon glyphicon-list-alt"></i> <span>Выгрузка отчетов дня</span>', '/admin/day-report-transport-circle'); ?>
                            </li>
                            <li<?= $current_controller == 'do-tariff' ? ' class="active"' : '' ?>>
                                <?= Html::a('<i class="glyphicon glyphicon-list-alt"></i> <span>Признаки формирования цены</span>', '/admin/do-tariff/index'); ?>
                            </li>
                            <li<?= $current_controller == 'transport-waybill-report' ? ' class="active"' : '' ?>>
                                <?= Html::a('<i class="fa fa-truck"></i> <span>Сдача выручки</span>', '/admin/transport-waybill-report/index'); ?>
                            </li>
                            <li<?= $current_controller == 'notaccountability-transport-report' ? ' class="active"' : '' ?>>
                                <?= Html::a('<i class="fa fa-truck"></i> <span>Сдача процентов</span>', '/admin/notaccountability-transport-report/index'); ?>
                            </li>
                            <li<?= $current_route == 'admin/setting/loyalty-switch' ? ' class="active"' : '' ?>>
                                <?= Html::a('<i class="glyphicon glyphicon-cog"></i> <span>Переключатель лояльности</span>', '/admin/setting/loyalty-switch/'); ?>
                            </li>
                            <li<?= $current_controller == 'cashback-setting' ? ' class="active"' : '' ?>>
                                <?= Html::a('<i class="glyphicon glyphicon-piggy-bank"></i> <span>Кэш-бэки</span>', '/admin/cashback-setting'); ?>
                            </li>
                            <li<?= $current_controller == 'yandex-point-category' ? ' class="active"' : '' ?>>
                                <?= Html::a('<i class="glyphicon glyphicon-piggy-bank"></i> <span>Категории для яндекс-точек</span>', '/admin/yandex-point-category'); ?>
                            </li>
                        </ul>
                    </li>
                <?php } ?>

                <?php
                if(in_array(Yii::$app->session->get('role_alias'), ['root', 'admin'])) { ?>
                    <li<?= ($current_module == 'admin' && (in_array($current_route, ['admin/setting/create-orders-yesterday', 'admin/rescue/actions', 'admin/setting/display', 'admin/setting/photo-server-url']) || in_array($current_controller, ['socket-ip',])) ? ' class="active"' : '') ?>>
                        <a href="#">
                            <i class="glyphicon glyphicon-cog"></i> <span>Глобальные настройки</span>
                            <span class="pull-right-container">
                              <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <?php /*
                            <li<?= $current_route == 'admin/setting/create-orders-yesterday' ? ' class="active"' : '' ?>>
                                <?= Html::a('<i class="glyphicon glyphicon-calendar"></i> <span>Создание заказов вчерашним днем</span>', '/admin/setting/create-orders-yesterday'); ?>
                            </li>
                            <?php if(in_array(Yii::$app->session->get('role_alias'), ['root'])) { ?>
                                <li<?= $current_route == 'admin/setting/display' ? ' class="active"' : '' ?>>
                                    <?= Html::a('<i class="glyphicon glyphicon-sunglasses"></i> <span>Отображение</span>', '/admin/setting/display'); ?>
                                </li>
                            <?php } */ ?>

                            <?php if(in_array(Yii::$app->session->get('role_alias'), ['root'])) { ?>
                            <li<?= $current_route == 'admin/setting/display' ? ' class="active"' : '' ?>>
                                <?= Html::a('<i class="glyphicon glyphicon-sunglasses"></i> <span>Поведение CRM</span>', '/admin/setting/display'); ?>
                            </li>
                            <?php } ?>

                            <li<?= $current_route == 'admin/rescue/actions' ? ' class="active"' : '' ?>>
                                <?= Html::a('<i class="glyphicon glyphicon-calendar"></i> <span>Действия</span>', '/admin/rescue/actions'); ?>
                            </li>
                            <?php if(in_array(Yii::$app->session->get('role_alias'), ['root', 'admin'])) { ?>
                                <li<?= $current_controller == 'socket-ip' ? ' class="active"' : '' ?>>
                                    <?= Html::a('<i class="glyphicon glyphicon-random"></i> <span>Настройка сокетов</span>', '/admin/socket-ip/index'); ?>
                                </li>
                            <?php } ?>
                            <?php if(in_array(Yii::$app->session->get('role_alias'), ['root', 'admin'])) { ?>
                                <li<?= $current_route == 'admin/setting/photo-server-url' ? ' class="active"' : '' ?>>
                                    <?= Html::a('<i class="glyphicon glyphicon-transfer"></i> <span>Ссылка на фото-сервер</span>', '/admin/setting/photo-server-url'); ?>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php } ?>

                <?php
                if(in_array(Yii::$app->session->get('role_alias'), ['root', 'admin'])) { ?>
                    <li<?= ($current_module == 'admin' && in_array($current_route, ['admin/rescue/day-print', 'admin/setting/index', 'admin/rescue/change-date-trips-orders', 'admin/rescue/create-test-orders']) ? ' class="active"' : '') ?>>
                        <a href="#">
                            <i class="glyphicon glyphicon-list-alt"></i> <span>Rescue</span>
                            <span class="pull-right-container">
                              <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li<?= $current_route == 'admin/rescue/day-print' ? ' class="active"' : '' ?>>
                                <?= Html::a('<i class="glyphicon glyphicon-calendar"></i> <span>Печать дня</span>', '/admin/rescue/day-print'); ?>
                            </li>
                            <li<?= $current_route == 'admin/rescue/change-date-trips-orders' ? ' class="active"' : '' ?>>
                                <?= Html::a('<i class="glyphicon glyphicon-calendar"></i> <span>Сдвиг времени заказов, рейсов и т.п.</span>', '/admin/rescue/change-date-trips-orders'); ?>
                            </li>
                            <li<?= $current_route == 'admin/rescue/create-test-orders' ? ' class="active"' : '' ?>>
                                <?= Html::a('<i class="glyphicon glyphicon-calendar"></i> <span>Создание тестовых заказов</span>', '/admin/rescue/create-test-orders'); ?>
                            </li>
                            <li<?= $current_route == 'admin/rescue/move-records' ? ' class="active"' : '' ?>>
                                <?= Html::a('<i class="glyphicon glyphicon-calendar"></i> <span>Выгрузка записей разговоров<br /> на другой сервер</span>', '/admin/rescue/move-records'); ?>
                            </li>
                        </ul>
                    </li>
                <?php } ?>

                <?php
                if(in_array(Yii::$app->session->get('role_alias'), ['root', 'admin', 'warehouse_turnover'])) { ?>
                    <li<?= ($current_module == 'admin' && in_array($current_controller, ['detail-name', 'nomenclature-detail', 'transport-model', 'transport-detail-state',
                        'transport-detail-origin', 'storage', 'storage-operation-type', 'detail-measurement-value']) ? ' class="active"' : '') ?>>
                        <a href="#">
                            <i class="glyphicon glyphicon-th"></i> <span>Склад</span>
                            <span class="pull-right-container">
                              <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <?php if(in_array(Yii::$app->session->get('role_alias'), ['root', 'admin'])) { ?>
                                <li<?= $current_route == 'admin/detail-name/index' ? ' class="active"' : '' ?>>
                                    <?= Html::a('<i class="glyphicon glyphicon-list"></i> <span>Наименования деталей</span>', '/admin/detail-name'); ?>
                                </li>
                            <?php } ?>
                            <?php if(in_array(Yii::$app->session->get('role_alias'), ['root', 'admin'])) { ?>
                                <li<?= $current_route == 'admin/nomenclature-detail/index' ? ' class="active"' : '' ?>>
                                    <?= Html::a('<i class="glyphicon glyphicon-list-alt"></i> <span>Номенклатура</span>', '/admin/nomenclature-detail'); ?>
                                </li>
                            <?php } ?>
                            <?php if(in_array(Yii::$app->session->get('role_alias'), ['root', 'admin', 'warehouse_turnover'])) { ?>
                                <li<?= $current_route == 'admin/transport-model/index' ? ' class="active"' : '' ?>>
                                    <?= Html::a('<i class="glyphicon glyphicon-send"></i> <span>Модели</span>', '/admin/transport-model'); ?>
                                </li>
                            <?php } ?>
                            <?php if(in_array(Yii::$app->session->get('role_alias'), ['root', 'admin'])) { ?>
                                <li<?= $current_route == 'admin/transport-detail-state/index' ? ' class="active"' : '' ?>>
                                    <?= Html::a('<i class="glyphicon glyphicon-wrench"></i> <span>Состояние запчасти</span>', '/admin/transport-detail-state'); ?>
                                </li>
                            <?php } ?>
                            <?php if(in_array(Yii::$app->session->get('role_alias'), ['root', 'admin'])) { ?>
                                <li<?= $current_route == 'admin/transport-detail-origin/index' ? ' class="active"' : '' ?>>
                                    <?= Html::a('<i class="glyphicon glyphicon-book"></i> <span>Происхождение</span>', '/admin/transport-detail-origin'); ?>
                                </li>
                            <?php } ?>
                            <?php if(in_array(Yii::$app->session->get('role_alias'), ['root', 'admin'])) { ?>
                                <li<?= $current_route == 'admin/storage/index' ? ' class="active"' : '' ?>>
                                    <?= Html::a('<i class="glyphicon glyphicon-th"></i> <span>Склады</span>', '/admin/storage'); ?>
                                </li>
                            <?php } ?>
                            <?php if(in_array(Yii::$app->session->get('role_alias'), ['root', 'admin'])) { ?>
                                <li<?= $current_route == 'admin/storage-operation-type/index' ? ' class="active"' : '' ?>>
                                    <?= Html::a('<i class="glyphicon glyphicon-tasks"></i> <span>Виды операций</span>', '/admin/storage-operation-type'); ?>
                                </li>
                            <?php } ?>
                            <?php if(in_array(Yii::$app->session->get('role_alias'), ['root', 'admin'])) { ?>
                                <li<?= $current_route == 'admin/detail-measurement-value/index' ? ' class="active"' : '' ?>>
                                    <?= Html::a('<i class="glyphicon glyphicon-asterisk"></i> <span>Единицы измерения</span>', '/admin/detail-measurement-value'); ?>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php } ?>


                <?php
                if(in_array(Yii::$app->session->get('role_alias'), ['root', 'admin', ])) { ?>
                    <li<?= ($current_module == 'admin' && in_array($current_controller, ['transport-expenses-doc-type', 'transport-expenses-types', 'transport-expenses-seller-type', 'transport-expenses-seller', 'transport-payment-methods', 'transport-waybill-trip-events']) ? ' class="active"' : '') ?>>
                        <a href="#">
                            <i class="glyphicon glyphicon-list-alt"></i> <span>Путевой лист</span>
                            <span class="pull-right-container">
                              <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <?php if(in_array(Yii::$app->session->get('role_alias'), ['root', 'admin'])) { ?>
                                <li<?= $current_controller == 'transport-expenses-doc-type' ? ' class="active"' : '' ?>>
                                    <?= Html::a('<i class="glyphicon glyphicon-object-align-left"></i> <span>Вид ДО</span>', '/admin/transport-expenses-doc-type'); ?>
                                </li>
                            <?php } ?>
                            <?php if(in_array(Yii::$app->session->get('role_alias'), ['root', 'admin'])) { ?>
                                <li<?= $current_controller == 'transport-expenses-types' ? ' class="active"' : '' ?>>
                                    <?= Html::a('<i class="glyphicon glyphicon-ruble"></i> <span>Документы основания</span>', '/admin/transport-expenses-types'); ?>
                                </li>
                            <?php } ?>
                            <?php if(in_array(Yii::$app->session->get('role_alias'), ['root', 'admin'])) { ?>
                                <li<?= $current_controller == 'transport-expenses-seller-type' ? ' class="active"' : '' ?>>
                                    <?= Html::a('<i class="glyphicon glyphicon-ruble"></i> <span>Тип продавцов (За что)</span>', '/admin/transport-expenses-seller-type'); ?>
                                </li>
                            <?php } ?>
                            <?php if(in_array(Yii::$app->session->get('role_alias'), ['root', 'admin'])) { ?>
                                <li<?= $current_controller == 'transport-expenses-seller' ? ' class="active"' : '' ?>>
                                    <?= Html::a('<i class="glyphicon glyphicon-ruble"></i> <span>Продавцы</span>', '/admin/transport-expenses-seller'); ?>
                                </li>
                            <?php } ?>
                            <?php if(in_array(Yii::$app->session->get('role_alias'), ['root', 'admin'])) { ?>
                                <li<?= $current_controller == 'transport-payment-methods' ? ' class="active"' : '' ?>>
                                    <?= Html::a('<i class="fa fa-exchange"></i> <span>Способы оплаты</span>', '/admin/transport-payment-methods'); ?>
                                </li>
                            <?php } ?>
                            <?php if(in_array(Yii::$app->session->get('role_alias'), ['root', 'admin'])) { ?>
                                <li<?= $current_controller == 'transport-waybill-trip-events' ? ' class="active"' : '' ?>>
                                    <?= Html::a('<i class="glyphicon glyphicon-bell"></i> <span>События</span>', '/admin/transport-waybill-trip-events'); ?>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php } ?>




            </ul>
            <!-- /.sidebar-menu -->
        </section>
        <!-- /.sidebar -->
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->

        <?php //if(in_array($current_controller, ['city', 'client'])) { ?>

            <?php if ($this->title) {?>
                <section class="content-header">
                    <h1 class="text-muted"><?= $this->title ?></h1>
                </section>
                <?= Breadcrumbs::widget([
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                    'homeLink' => ['label' => 'Администратор', 'url' => '/admin/'],
                    'encodeLabels' => false,
                    'options' => ['class' => 'breadcrumb breadcrumb-tobus'] // breadcrumb-lte
                ]); ?>
            <?php } ?>

        <?php /*}
        elseif($current_controller == 'direction') { ?>
            <section class="content-header">
                <h1 class="text-muted"><?= $this->title ?></h1>
                <?= Breadcrumbs::widget([
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                    'homeLink' => ['label' => 'Администратор', 'url' => '/admin/'],
                    'encodeLabels' => false,
                    'options' => ['class' => 'breadcrumb breadcrumb-lte'] // breadcrumb-lte
                ]); ?>
            </section>
        <?php }else { ?>


        <?php }*/ ?>

        <!-- Main content -->
        <section class="content">

            <!-- Your Page Content Here -->
            <?= $content ?>

        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

</div>

<?php $this->endBody() ?>

<?php
// Модальное окно для загрузки содержимого с помощью ajax
Modal::begin([
    'header' => '<h4 class="modal-title">Заполните форму</h4>',
    'id' => 'default-modal',
    'size' => 'modal-md',
]);
?>
<div id='modal-content'>Загружаю...</div>
<?php Modal::end(); ?>

</body>
</html>
<?php $this->endPage() ?>
