<?php

use yii\helpers\Html;
use yii\grid\GridView;

$this->title = 'Доступы';
$this->params['breadcrumbs'][] = $this->title;

$aAccesses = [];
foreach ($accesses as $access) {
    $aAccesses[$access->user_role_id][$access->id_access_places] = $access;
}
//echo "aAccesses:<pre>"; print_r($aAccesses); echo "</pre>";

$this->registerJsFile('js/admin/access.js', ['depends'=>'app\assets\AppAsset']);
?>
<div class="access-index">

    <style type="text/css">
        #access-table tr td {
            border: solid 1px #CCCCCC;
            padding: 1px 10px;
        }

        #access-table tr.access-place:hover td {
            cursor: pointer;
            background: #CCFFFF;
        }
    </style>

    <a href="/admin/user" target="_blank">Список пользователей</a><br />

    <table id="access-table">
        <tr>
            <td>Область доступа &nbsp;&nbsp;&nbsp;&nbsp;<?= Html::a('<i class="glyphicon glyphicon-plus"></i> Создать роль', ['create'], ['class' => 'btn btn-success']) ?></td>
            <?php foreach ($roles as $role) { ?>
                <td><?= $role->name ?></td>
            <?php } ?>
        </tr>
        <?php foreach ($access_places as $place) {

            ?>
            <tr class="access-place" <?= (empty($place->page_url) ? 'module="'.$place->module.'"' : 'parent-module="'.$place->module.'"') ?> module="<?= $place->module ?>">
                <td nowrap>
                    <?php
                    if(empty($place->page_url)) { // имя модуля
                        echo '<i class="toggle-module glyphicon glyphicon-minus-sign" is-open="true"></i> '.$place->name;
                    }else {
                        if(empty($place->page_part)) { // имя страницы
                            echo '&nbsp;&nbsp;&nbsp;&nbsp;'.$place->name;
                        }else { // имя какой-то части страницы
                            echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$place->name;
                        }
                    }
                    ?>
                </td>
                <?php foreach ($roles as $role) { ?>
                    <td>
                        <?php
                        // echo "role_id=".$role->id." place_id=".$place->id."<br />";
                        if(isset($aAccesses[$role->id]) && isset($aAccesses[$role->id][$place->id])) {
                            $access = $aAccesses[$role->id][$place->id];
                            echo '<input class="access-checkbox" '.($access->access == true ? 'checked' : '').' type="checkbox" role-id="'.$role->id.'" place-id="'.$place->id.'" />';
                        }else {
                            echo '<input class="access-checkbox" type="checkbox" role-id="'.$role->id.'" place-id="'.$place->id.'" />';
                        }
                        ?>
                    </td>
                <?php } ?>
            </tr>
        <?php } ?>
    </table>

</div>
