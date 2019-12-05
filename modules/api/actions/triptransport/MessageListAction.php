<?php
namespace app\modules\api\actions\triptransport;

use app\models\User;
use Yii;
use app\models\DriverOperatorChat;
use yii\helpers\ArrayHelper;

/*
 * Экшен возвращает незакрытые переписку водителя-операторов на рейсо-машине
 */
class MessageListAction extends \yii\rest\Action
{
    public $modelClass = '';

    public function run()
    {
        $trip_transport_id = Yii::$app->getRequest()->getBodyParam('trip_transport_id');

        $driver_operator_chats = DriverOperatorChat::find()
            ->where(['trip_transport_id' => $trip_transport_id])
            ->andWhere(['driver_is_read_at' => 0])
            ->andWhere(['>', 'operator_id', 0])
            ->all();

        $aMessages = [];
        if(count($driver_operator_chats) > 0) {

            $users = User::find()->where(['id' => ArrayHelper::map($driver_operator_chats, 'operator_id', 'operator_id')])->all();
            $aUsers = ArrayHelper::index($users, 'id');
            foreach ($driver_operator_chats as $driver_operator_chat) {

                $operator = $aUsers[$driver_operator_chat->operator_id];
                $aMessages[] = [
                    'message_id' => $driver_operator_chat->id,
                    'message_from_driver' => $driver_operator_chat->message_from_driver,
                    'message' => $driver_operator_chat->answer_from_operator,
                    'user_fio' => $operator->firstname . ' ' . $operator->lastname,
                ];
            }
        }

        return $aMessages;
    }
}