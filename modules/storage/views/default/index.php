<?php
use yii\helpers\Html;
use app\helpers\table\PageSizeHelper;
use yii\widgets\LinkPager;

//$this->registerJsFile('js/site/create-order-modal.js', ['depends'=>'app\assets\AppAsset']);
//$this->registerJsFile('js/site/index.js', ['depends'=>'app\assets\AppAsset']);
//$this->registerJsFile('https://api-maps.yandex.ru/2.1/?lang=ru_RU', ['depends' => 'app\assets\AdminAsset']);

$user = Yii::$app->user->identity;

$pagination = $dataProvider->getPagination();
$pagination->totalCount = $dataProvider->getTotalCount();
?>
<div id="storage-page">

    <table id="storage-menu-table">
        <tr>
            <td>
                <span>Имя пользователя:<br /><b><?= ($user != null ? $user->fullname : '');?></b></span> <a class="user-logout" href="/site/logout" title="Выход"><i class="glyphicon glyphicon-remove-sign"></i></a>
            </td>
            <td>
                <span class="user_role">Группа: <b><?= ($user != null && $user->userRole ? $user->userRole->name : ''); ?></b></span><br/>
                <span>Время входа: <b><?= ($user != null && $user->last_login_date > 0 ? date('Y.m.d H:i:s', ($user->last_login_date)) : '');?></b></span><br/>
            </td>
            <td>
                <?php if(!in_array(Yii::$app->session->get('role_alias'), ['graph_operator',])) { ?>
                    <?= Html::a('Приход', ['#'], ['id' => 'storage-operation-income', 'class' => 'btn btn-default']); ?>
                <?php } ?>
                <?php if(!in_array(Yii::$app->session->get('role_alias'), ['graph_operator', 'manager'])) { ?>
                    <?= Html::a('Адм. склада', ['/admin/'], ['id' => 'admin-panel', 'class' => 'btn btn-default']); ?>
                <?php } ?>
            </td>
            <td>
                <?php if(!in_array(Yii::$app->session->get('role_alias'), ['graph_operator'])) { ?>
                    <?= Html::a('Расход', ['#'], ['id' => 'storage-operation-expenditure', 'class' => 'btn btn-default']); ?>
                <?php } ?>
                <?php if(in_array(Yii::$app->session->get('role_alias'), ['root', 'admin', 'warehouse_turnover'])) { ?>
                    <a href="/" style="padding: 10px 15px; margin: 20px 0 0 0; display: inline-block;">Перейти в основное окно</a>
                <?php } ?>
            </td>
        </tr>
    </table>

    <div id="storage-grid" class="box box-default" style="margin-top: 20px; width: 110%;">
        <div class="box-header scroller with-border">
            <div class="pull-left">

            </div>

            <div class="pull-left">
                <?= LinkPager::widget([
                    'pagination' => $pagination,
                    'options' => [
                        'class' => 'pagination pagination-sm'
                    ]
                ]); ?>
            </div>
            <?= (new PageSizeHelper([20, 50, 100, 200, 500]))->getButtons() ?>
        </div>
        <div></div>

        <div class="box-body box-table">
            <?= $this->render('/default/storage-detail-grid', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
            ?>
        </div>
    </div>
</div>
