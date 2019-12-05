<?php

use yii\db\Migration;

/**
 * Class m181213_190154_add_field_photo_server_url_to_settings
 */
class m181213_190154_add_field_photo_server_url_to_settings extends Migration
{
    public function up()
    {
        $this->addColumn('setting', 'photo_server_url', $this->string(100)->comment('Url фото сервера')->after('access_to_client_info_main_page'));
    }

    public function down()
    {
        $this->dropColumn('setting', 'photo_server_url');
    }
}
