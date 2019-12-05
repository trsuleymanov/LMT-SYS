<?php
namespace app\widgets;

use app\models\Order;
use Yii;
use yii\base\Widget;
use app\models\User;
use app\models\UserRole;
use app\models\ClientExt;
use yii\web\ForbiddenHttpException;

/*
 * Окно слева с поступившими заявками (заявки в таблице client_ext)
 */
class ClientextWidget extends Widget
{
    public $is_open = false;

    public function run()
    {
        //$clientexts_count = ClientExt::find()->where(['status' => 'created'])->count();
        $clientexts_orders_count = Order::find()
            ->where(['status_id' => 0])
            ->andWhere(['>', 'client_server_ext_id', 0])
            ->count();
        return $this->render('@app/widgets/views/clientext/index', [
            'clientexts_orders_count' => $clientexts_orders_count
        ]);
//        if($clientexts_orders_count > 0) {
//            return $this->render('@app/widgets/views/clientext/index', [
//                //'clientexts_count' => $clientexts_count
//                'clientexts_orders_count' => $clientexts_orders_count
//            ]);
//        }else {
//            return '';
//        }
    }
}