<?php

use yii\db\Migration;

/**
 * Class m190719_225904_add_fields_to_chat_message
 */
class m190719_225904_add_fields_to_chat_message extends Migration
{
    public function up()
    {
        $this->addColumn('chat_message', 'dialog_id', $this->integer()->comment('id диалога')->after('id'));
        $this->addColumn('chat_message', 'user_id', $this->integer()->comment('Пользователь')->after('dialog_id'));
    }

    public function down()
    {
        $this->dropColumn('chat_message', 'dialog_id');
        $this->dropColumn('chat_message', 'user_id');
    }
}
