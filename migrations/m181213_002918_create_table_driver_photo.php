<?php

use yii\db\Migration;

/**
 * Class m181213_002918_create_table_driver_photo
 */
class m181213_002918_create_table_driver_photo extends Migration
{
    public function up()
    {
        $this->createTable('driver_photo', [ // причины отмены заказа
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->comment('Пользователь осуществивший выгрузку'),
            'time_loading_finish' => $this->integer()->comment('Время завершения выгрузки'),
            'photo_created_on_mobile' => $this->integer()->comment('Время создания скриншота на мобильном устройстве'),
            'photo_link' => $this->string(255)->comment('Ссылка на скриншот'),
        ]);
    }

    public function down()
    {
        $this->dropTable('driver_photo');
    }
}
