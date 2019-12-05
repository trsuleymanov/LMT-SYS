<?php
namespace app\modules\api\actions\workingshift;

use yii\base\ErrorException;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;
use app\models\WorkingShift;
use app\models\User;
use app\models\UserRole;


class GetDataAction extends \yii\rest\Action
{
    public $modelClass = '';


    /**
     * @throws ForbiddenHttpException
     * @throws ErrorException
     */
    public function run()
    {
        $roles = UserRole::find()->where(['controlled' => true])->all();
        if(count($roles) == 0) {
            throw new ErrorException('Контролируемые роли пользователей не нейдены');
        }

        $users = User::find()->where(['role_id' => ArrayHelper::map($roles, 'id', 'id')])->all();
        if(count($users) == 0) {
            throw new ErrorException('Контролируемые пользователи не нейдены');
        }
        $aUsers = [];
        foreach ($users as $user) {
            $aUsers[] = [
                'id' => $user->id,
                'username' => $user->username
            ];
        }

        $aShiftTypes = [];
        foreach (WorkingShift::getShiftTypes() as $key => $value) {
            $aShiftTypes[] = [
                'key' => $key,
                'value' => $value
            ];
        }

        return [
            'shift_types' => $aShiftTypes,
            'users' => $aUsers
        ];
    }
}