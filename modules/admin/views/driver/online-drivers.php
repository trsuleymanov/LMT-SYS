<?php

use app\models\Driver;
use app\models\User;
use app\models\UserRole;
use yii\helpers\ArrayHelper;


$this->title = 'Водители онлайн';


$this->registerJsFile('https://api-maps.yandex.ru/2.1/?lang=ru_RU&apikey=5c7acdc8-48c9-43d9-9f44-2e6b9e178101', ['depends' => 'app\assets\AdminAsset']);
$this->registerJsFile('js/admin/online-drivers.js', ['depends'=>'app\assets\AdminAsset']);

// нужно получить всех пользователей у которых lat_long_ping_at больше чем (сейчас минус 600 секунд) и вывести
// их в виде меток на карте
//$user_roles = UserRole::find()->where(['alias' => 'driver'])->all();
//$users = User::find()
//    ->where(['>=', 'lat_long_ping_at', time() - 600])
//    ->andWhere(['role_id' => ArrayHelper::map($user_roles, 'id', 'id')])
//    ->all();


$arJsonUsers = [];
foreach($users as $user) {
    $arJsonUsers[] =
    '{
        lastname: "'.$user['lastname'].'",
        firstname: "'.$user['firstname'].'",
        lat: '.$user['lat'].',
        long: '.$user['long'].',
        phone: "'.$user['phone'].'",
        transport_car_reg: "'.$user['transport_car_reg'].'",
        transport_sh_model: "'.$user['transport_sh_model'].'",
        driver_fio: "'.$user['driver_fio'].'",
        direction_sh_name: "'.$user['direction_sh_name'].'",
        trip_id: "'.$user['trip_id'].'",
        trip_name: "'.$user['trip_name'].'",
        trip_date: "'.$user['trip_date'].'"
    }';
}
$json_users = implode(',', $arJsonUsers);

$js = <<<JS
    var users = [
        {$json_users}
    ];
JS;
$this->registerJs($js, yii\web\View::POS_HEAD);

?>
<table>
    <tr>
        <td>
            <div id="YMapsID" style="width:800px; height:600px;"></div>
        </td>
        <td style="vertical-align: top;">
            <div id="active-drivers-list" style="display: inline-block; margin-left: 20px;">
                <?= $this->render('_online-drivers-list', [
                    'users' => $users
                ]) ?>
            </div>
        </td>
    </tr>
</table>

