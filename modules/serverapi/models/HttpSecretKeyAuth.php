<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\modules\serverapi\models;
use app\models\User;
use yii\web\UnauthorizedHttpException;


class HttpSecretKeyAuth extends \yii\filters\auth\AuthMethod
{
    /**
     * @var string the HTTP authentication realm
     */
    public $realm = 'api';

    private $access_code = 'lsaeu5jERTffd_7';


    /**
     * @inheritdoc
     */
    public function authenticate($user, $request, $response)
    {
        $authHeader = $request->getHeaders()->get('Authorization');
        if ($authHeader !== null && preg_match('/^SecretKey\s+(.*?)$/', $authHeader, $matches)) {

            if($matches[1] == $this->access_code) {
                //return User::find()->orderBy(['id' => SORT_ASC])->one();
                return true;
            }else {
                throw new UnauthorizedHttpException('Неправильный код доступа');
                //$this->handleFailure($response);
            }
        }

        return null;
    }
}
