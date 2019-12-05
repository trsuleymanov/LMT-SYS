<?php
namespace app\widgets;

use Yii;
use yii\base\Widget;
use app\models\User;
use app\models\UserRole;
use app\models\ChatMessage;
use yii\web\ForbiddenHttpException;

/*
 * О-чат
 */
class ChatWidget extends Widget
{
    public $is_open = false;

    public function run()
    {
        //$user = User::findOne(Yii::$app->user->id);
        //$user = User::getUserByCookie();
        $user = Yii::$app->user->identity;
        if($user == null) {
            //throw new ForbiddenHttpException('У вас нет доступа к чату');
            return '';
        }
        $userRole = $user->userRole;

        $messages = ChatMessage::find()
            ->where(['>=', 'expiration_time', time()])
            ->orderBy(['to_the_begining' => SORT_DESC, 'created_at' => SORT_ASC])
            ->all();



        //echo "messages:<pre>"; print_r($messages); echo "</pre>";

        if(count($messages) == 0 && $this->is_open == false) {
            return '';
        }else {

            $messages_groups = [];
            foreach ($messages as $message) {
                $messages_groups[$message->dialog_id][] = $message;
            }

            return $this->render('chat-widget/index', [
                'userRole' => $userRole,
                //'messages' => $messages
                'messages_groups' => $messages_groups
            ]);
        }
    }
}