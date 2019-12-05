<?php
namespace app\modules\api\actions\workingshift;

use app\models\WorkingShiftUnlockingTime;
use Yii;
use yii\base\ErrorException;
use yii\web\ForbiddenHttpException;
use app\models\WorkingShift;



class SetStatusAction extends \yii\rest\Action
{
    public $modelClass = '';


    /**
     * @throws ForbiddenHttpException
     * @throws ErrorException
     */
    public function run()
    {
        // принимаем: user_id, shift_type, status
        $user_id = Yii::$app->request->post('user_id');
        $shift_type = Yii::$app->request->post('shift_type');
        $status = Yii::$app->request->post('status');

        if(empty($user_id)) {
            throw new ForbiddenHttpException('Отствует пользователь в переданных данных');
        }
        if(empty($shift_type)) {
            throw new ForbiddenHttpException('Отствует смена в переданных данных');
        }
        if(!in_array($status, ['start', 'unlock', 'finish'])) {
            throw new ForbiddenHttpException('Неправильный статус в переданных данных');
        }

        if($status == 'start') {

            $working_shift = new WorkingShift();
            $working_shift->user_id = $user_id;
            $working_shift->shift_type = $shift_type;
            $working_shift->start_time = time();
            if(!$working_shift->save(false)) {
                throw new ErrorException('Не удалось сохранить пинг от приложения');
            }

        }elseif($status == 'finish') {

            $working_shift = WorkingShift::find()
                ->where(['user_id' => $user_id])
                ->andWhere(['finish_time' => NULL])
                ->orderBy(['id' => 'DESC'])
                ->one();
            if($working_shift == null) {
                throw new ForbiddenHttpException('Не найден пинг с начатой сменой');
            }
            $working_shift->setField('finish_time', time());

        }elseif($status == 'unlock') {

            $working_shift = WorkingShift::find()
                ->where(['user_id' => $user_id])
                ->andWhere(['finish_time' => NULL])
                ->orderBy(['id' => 'DESC'])
                ->one();
            if($working_shift == null) {
                throw new ForbiddenHttpException('Не найден пинг с начатой сменой');
            }

            $working_shift_unlocking_time = new WorkingShiftUnlockingTime();
            $working_shift_unlocking_time->working_shift_id = $working_shift->id;
            if(!$working_shift_unlocking_time->save(false)) {
                throw new ErrorException('Не удалось сохранить пинг от приложения');
            }
        }

        return;
    }
}