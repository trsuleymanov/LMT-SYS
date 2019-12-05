<?php
use yii\helpers\Html;


$user = Yii::$app->user->identity;

?>
<table id="waybill-menu-table">
    <tr>
        <td>
            <span>Имя пользователя:<br /><b><?= ($user != null ? $user->fullname : '');?></b></span> <a class="user-logout" href="/site/logout" title="Выход"><i class="glyphicon glyphicon-remove-sign"></i></a>
        </td>
        <td>
            <span class="user_role">Группа: <b><?= ($user != null && $user->userRole ? $user->userRole->name : ''); ?></b></span><br/>
            <span>Время входа: <b><?= ($user != null && $user->last_login_date > 0 ? date('Y.m.d H:i:s', ($user->last_login_date)) : '');?></b></span><br/>
        </td>
        <td>
            <?php //if(!in_array(Yii::$app->session->get('role_alias'), ['graph_operator',])) { ?>
            <?= Html::a('Создание ПЛ', ['create'], ['id' => 'waybill-create', 'class' => 'btn btn-default']); ?>
            <?php //} ?>
        </td>
        <td>
            <?php //if(!in_array(Yii::$app->session->get('role_alias'), ['graph_operator'])) { ?>
            <?= Html::a('Эксплуатация', ['/waybill/transport-waybill/exploitation-data'], ['id' => 'exploitation-data', 'class' => 'btn btn-default']); ?>
            <?php //} ?>
        </td>
        <td>
            <?php //if(!in_array(Yii::$app->session->get('role_alias'), ['manager'])) { ?>
            <?= Html::a('Панель администратора', ['/admin/'], ['id' => 'admin-panel', 'class' => 'btn btn-default']); ?>
            <?php //} ?>
        </td>

    </tr>

    <tr>
        <td></td>
        <td></td>
        <td>
            <?php //if(in_array(Yii::$app->session->get('role_alias'), ['root', 'admin', ])) { ?>
            <?= Html::a('Расходы', ['/waybill/transport-waybill/expenses'], ['id' => 'waybill-expenses', 'class' => 'btn btn-default']); ?>
            <?php //} ?>
        </td>
        <td>
            <?php //if(in_array(Yii::$app->session->get('role_alias'), ['root', 'admin', ])) { ?>
            <?= Html::a('Детализация', ['/waybill/transport-waybill/detailing'], ['id' => 'waybill-detailing', 'class' => 'btn btn-default']); ?>
            <?php //} ?>
        </td>
        <td>
            <?php if(in_array(Yii::$app->session->get('role_alias'), ['root', 'admin', ])) { ?>
                <a href="/" style="padding: 10px 15px; margin: 20px 0 0 0; display: inline-block;">Перейти в основное окно</a>
            <?php } ?>
        </td>
    </tr>
</table>
