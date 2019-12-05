<?php
use yii\helpers\Url;

$i = 1;

//echo "users:<pre>";print_r($users); echo "</pre>";

// 838 ПЖ, ФИО водителя, АК 7:30, дата рейса
foreach($users as $user) { ?>
    <div class="user">
        <?= $i++ ?>
        &nbsp;&nbsp;&nbsp;
        <label class="glyphicon glyphicon-map-marker online-driver-position" lat="<?= $user['lat'] ?>" long="<?= $user['long'] ?>"></label>&nbsp;
        <a href="<?= Url::to(['/admin/user/update', 'id' => $user['id']]) ?>">
            <?= $user['transport_car_reg']?> <?= $user['transport_sh_model']?>, <?= $user['driver_fio'] ?>, <?= $user['direction_sh_name'] ?> <?= $user['trip_name'] ?>, <?= $user['trip_date'] ?>
        </a>
    </div>
<?php } ?>