<?php

use yii\db\Migration;

/**
 * Class m180314_215403_add_penalty_fields_to_order
 */
class m180314_215403_add_penalty_fields_to_order extends Migration
{
    public function up()
    {
        $this->addColumn('order', 'penalty_comment', $this->string(255)->after('has_penalty')->comment('Комментарий к штафу'));
        $this->addColumn('order', 'penalty_time', $this->integer()->after('penalty_comment')->comment('Дата-время штрафования'));
        $this->addColumn('order', 'penalty_author_id', $this->integer()->after('penalty_time')->comment('Пользователь оштрафовавший клиента'));
    }

    public function down()
    {
        $this->dropColumn('order', 'penalty_comment');
        $this->dropColumn('order', 'penalty_time');
        $this->dropColumn('order', 'penalty_author_id');
    }
}
