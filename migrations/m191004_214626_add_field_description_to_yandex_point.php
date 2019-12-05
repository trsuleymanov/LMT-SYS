<?php

use yii\db\Migration;

/**
 * Class m191004_214626_add_field_description_to_yandex_point
 */
class m191004_214626_add_field_description_to_yandex_point extends Migration
{
    public function up()
    {
        $this->addColumn('yandex_point', 'description', $this->string(255)->comment('Описание')->after('alias'));
    }

    public function down()
    {
        $this->dropColumn('yandex_point', 'description');
    }
}
