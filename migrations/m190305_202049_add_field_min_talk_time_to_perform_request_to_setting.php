<?php

use yii\db\Migration;

/**
 * Class m190305_202049_add_field_min_talk_time_to_perform_request_to_setting
 */
class m190305_202049_add_field_min_talk_time_to_perform_request_to_setting extends Migration
{
    public function up()
    {
        $this->addColumn('setting', 'min_talk_time_to_perform_request', $this->integer()->comment('Минимальное время разговора при обработке электронной заявки, сек')->defaultValue(20)->after('crm_url_for_beeline_ats'));
    }

    public function down()
    {
        $this->dropColumn('setting', 'min_talk_time_to_perform_request');
    }
}
