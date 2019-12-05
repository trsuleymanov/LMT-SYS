<?php
use app\models\SocketDemon;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use app\models\OrderStatus;
use yii\helpers\Html;


// заново формируются здесь данные для работы сокетов в браузере, так как стандартное формирование данных для вызова
// этого окна из сокетов не будет работать
$user = \Yii::$app->user->identity;
if($user != null) {
    $user_demon_code = SocketDemon::getUserDemonCode($user->password_hash, $user->id);

    if(!empty($user->socket_ip_id) && $user->socketIp != null) {
        $socket_url = 'ws://'.$user->socketIp->ip;
    }else {
        $socket_url = \Yii::$app->params['browserDemonUrl'];
    }

    \Yii::$app->view->registerJs(
        "var user = " . Json::encode($user_demon_code) . ";
         var socket_url=" . Json::encode($socket_url) . ";",
        \yii\web\View::POS_HEAD);

    //$user_role_alias = $user->userRole->alias;

}else {
    \Yii::$app->view->registerJs(
        "var socket_url='не найден пользователь';",
        \yii\web\View::POS_HEAD);
}


//$user = Yii::$app->user->identity;
//$user_role_alias = $user->userRole->alias;
?>

<?php
$this->registerJsFile('js/site/create-order-modal.js', ['depends'=>'app\assets\AppAsset']);
$this->registerJsFile('js/site/index.js', ['depends'=>'app\assets\AppAsset']);
$this->registerJsFile('https://api-maps.yandex.ru/2.1/?lang=ru_RU&apikey=5c7acdc8-48c9-43d9-9f44-2e6b9e178101', ['depends' => 'app\assets\AdminAsset']);
?>

<div id="call-page">
    <?= $this->render('call-window-content', [
        'client_id' => $client_id,
        'call_id' => $call_id,
        'client_phone' => $client_phone,
        'client' => $client,
        'call_speaking_seconds' => $call_speaking_seconds,
        'start_speaking' => $start_speaking,

        'orderSearchModel' => $orderSearchModel,
        'orderDataProvider' => $orderDataProvider,
        'searchOrderByPhoneDataProvider' => $searchOrderByPhoneDataProvider,
    ]) ?>
</div>



