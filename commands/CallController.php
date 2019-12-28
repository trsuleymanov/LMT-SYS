<?php

namespace app\commands;

use app\models\Call;
use app\models\CallCase;
use app\models\CallEvent;
use app\models\Client;
use app\models\Setting;
use yii\console\Controller;
use Yii;
use yii\helpers\ArrayHelper;


class CallController extends Controller
{
    /*
     * команда: php yii call/close-missed-cases
     */
    public function actionCloseMissedCases()
    {
        //$setting = Setting::find()->where(['id' => 1])->one();
        // $setting->missed_calls_close_interval // Количество секунд до закрытия пропущенных обращений

        $missed_cases = CallCase::find()
            ->where(['case_type' => 'missed'])
            ->andWhere(['status' => 'not_completed'])
            ->andWhere(['close_time' => NULL])
            ->andWhere(['<', 'update_time', time() - Yii::$app->setting->missed_calls_close_interval])
            ->all();


        if(count($missed_cases) > 0) {
            foreach($missed_cases as $case) {
                $case->update_time = time();
                $case->close_time = time();
                $case->status = 'auto_completed';
                if(!$case->save(false)) {
                    return false;
                }
            }

            // отправляем сигнал в браузеры с новым количеством пропущенных звонков
            Call::sentToBrawsersMissedCallsCount();
        }

        return true;
    }


    /*
     * команда: php yii call/check-finished-calls
     */
    public function actionCheckFinishedCalls()
    {
        // отдельное закрытие косячных звонков
        $calls = Call::find()
            ->where(['ats_eok_time' => NULL])
            ->andWhere(['<', 't_create', time() - 300])
            ->all();
        if(count($calls) > 0) {
            foreach($calls as $call) {
                $call->setField('t_hungup', $call->t_create);
                $call->setField('ats_eok_time', $call->t_create.'000');
            }
        }


        // текущий скрипт запускается кроном каждую минуту, 2 секунды - это суммарное время 58-ми запусков метода _checkFinishedCalls()
        //for($i = 0; $i < 58; $i++) {
        for($i = 0; $i < 28; $i++) {
            $this->_checkFinishedCalls();
            sleep(2);
        }
    }


    private function _checkFinishedCalls()
    {
        // нахожу все звонки у которых ats_eok_time 0 или NULL, но есть t_hungup, и t_hungup меньше или равно текущему времени

        // этим звонкам устанавливаю ats_eok_time=1

        // для найденных звонков отправляю сообщения в браузер:
        // - о изменении количества входящих звонков
        // - о изменении количества пропущенных звонков
        // - для отдельных звонков сообщения о завершении звонка (такого типа события не существует)
        //$start = microtime(true);

        $calls = Call::find()
            ->where(['<', 't_hungup', time() - 1])
            ->andWhere(['>', 't_hungup', 0])
            ->andWhere([
                'OR',
                ['ats_eok_time' => 0],
                ['ats_eok_time' => NULL],
            ])
            ->all();

        //echo "найдено ".count($calls)." звонков \n";

        if(count($calls) > 0) {

            foreach($calls as $call) {
                $call->ats_eok_time = $call->ats_start_time + ($call->t_hungup - $call->t_create)*1000;
                $call->setField('ats_eok_time', $call->ats_eok_time);

                // создаем/изменяем обращения в момент завершения звонка
                $call->createUpdateCase(true);



                $aFields = [
                    'call_id' => $call->id,
                    'ats_callId' => $call->ext_tracking_id,
                    'ats_subscriptionId' => '',
                    'event_name' => 'Автоматическая отметка об окончании звонка в CRM (произошла так как из АТС не пришло событие завершения звонка)',
                    'ats_eventID' => '',
                    'crm_event_time' => $call->t_hungup,
                    'ats_event_time' => '',
                    'call_type' => $call->call_direction,
                    'sip' => '',
                    'client_phone' => $call->operand,
                ];
                CallEvent::saveLog($aFields);
            }


            // обновляем в браузерах всех пользователей количество входящих звонков
            Call::sentToBrawsersIncomingCallsCount();
            // обновляем в браузерах всех пользователей количество пропущенных звонков
            //Call::sentToBrawsersMissedCallsCount();
        }
    }

}
