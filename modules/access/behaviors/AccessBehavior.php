<?php

namespace app\modules\access\behaviors;

use app\models\Access;
use app\models\AccessPlaces;
use app\models\SocketDemon;
use app\models\UserRole;
use Yii;
use yii\behaviors\AttributeBehavior;
use yii\di\Instance;
use yii\base\Module;
use app\models\User;
use yii\web\ForbiddenHttpException;
use yii\web\Cookie;
use yii\helpers\Json;

/**
 * Глобальное поведение проверки прав доступа.
 *
 * Class AccessBehavior
 * @package backend\modules\access\behaviors
 */
class AccessBehavior extends AttributeBehavior
{
    public $login_url = '/site/login';

    /**
     * @return array
     */
    public function events()
    {
        return [Module::EVENT_BEFORE_ACTION => 'interception'];
    }


    public function interception($event)
    {
        if(
            //Yii::$app->request->url == $this->login_url
            strpos(Yii::$app->request->url, '/site/login') !== false
             || Yii::$app->request->url == '/user/ajax-get-usernames'
            || Yii::$app->request->url == '/site/logout'
            || Yii::$app->request->url == '/site/ajax-get-chat'
            || Yii::$app->request->url == '/site/get-ajax-time'
            || strpos(Yii::$app->request->url, '/site/ajax-get-directions-trips-block') !== false
            || strpos(Yii::$app->request->url, '/client-ext/ajax-get-clientext-block') !== false
            || strpos(Yii::$app->request->url, '/api/') !== false
            || strpos(Yii::$app->request->url, '/debug/') !== false
            || strpos(Yii::$app->request->url, '/serverapi/') !== false
            //|| strpos(Yii::$app->request->url, '/megafon/') !== false
            || strpos(Yii::$app->request->url, '/beeline/') !== false
            || strpos(Yii::$app->request->url, 'get-call-window') !== false
        ) {
            return true;
        }

//        if(strpos(Yii::$app->request->url, 'get-call-window') !== false) {
//
//            $user = Yii::$app->user->identity;
//            if($user != null) {
//                $user_demon_code = SocketDemon::getUserDemonCode($user->password_hash, $user->id);
//
//                if(!empty($user->socket_ip_id)) {
//                    $socket_url = 'ws://'.$user->socketIp->ip;
//                }else {
//                    $socket_url = Yii::$app->params['browserDemonUrl'];
//                }
//
//                Yii::$app->view->registerJs(
//                    "var user = " . Json::encode($user_demon_code) . ";
//                     var socket_url=" . Json::encode($socket_url) . ";",
//                    \yii\web\View::POS_HEAD);
//            }
//
//            return true;
//        }

        $user = Yii::$app->user->identity;
        if($user != null) {
            $user_demon_code = SocketDemon::getUserDemonCode($user->password_hash, $user->id);

            if(!empty($user->socket_ip_id)) {
                $socket_url = 'ws://'.$user->socketIp->ip;
            }else {
                $socket_url = Yii::$app->params['browserDemonUrl'];
            }

            Yii::$app->view->registerJs(
                "var user = " . Json::encode($user_demon_code) . ";
                 var socket_url=" . Json::encode($socket_url) . ";",
                \yii\web\View::POS_HEAD);
        }

        if(empty(User::getCookieId())) {
            //exit('нет user_id в куках');
            if(!empty(Yii::$app->user->id)) {
                //$user = User::find()->where(['id' => Yii::$app->user->id])->one();
                $user = Yii::$app->user->identity;
                if($user != null) {
                    $user->logoutByCookie(true);
                }
            }

            $login_url = $this->login_url;
            if(count($_GET) > 0) {
                $login_url .= '?'.array_keys($_GET)[0];
            }
            Yii::$app->response->redirect($login_url)->send();
            exit(); // Exit нужно производить потому что ->redirect сразу не срабатывает, а вначале выводиться html

        }else {

            //$user = \app\models\User::findOne(User::getCookieId());
            $user = Yii::$app->user->identity;
            if ($user == null) {
                User::miniLogoutByCookie();
                throw new ForbiddenHttpException('Пользователь не найден');
            }

            if($user->blocked == 1) {
                $user->logoutByCookie(true);
                throw new ForbiddenHttpException('Пользователь заблокирован');
            }

            if(empty(Yii::$app->session->get('role_alias'))) {
                $user->updateLoginByCookie(); // обновили в куках время сеанса
            }

            if(User::getCookieAuthKey() != $user->auth_key) {

                //exit('auth_key не верный');
                $user->logoutByCookie(true);// Auth ключь неверный. Выходим...
                $login_url = $this->login_url;
                if(count($_GET) > 0) {
                    $login_url .= '?'.array_keys($_GET)[0];
                }
                Yii::$app->response->redirect($login_url)->send();
                exit();

            }elseif($user->auth_seans_finish < time()) {
                //exit('истекло время сессии');
                $user->logoutByCookie(true); // !
                $login_url = $this->login_url;
                if(count($_GET) > 0) {
                    $login_url .= '?'.array_keys($_GET)[0];
                }
                Yii::$app->response->redirect($login_url)->send(); // Закончилось время сеанса. Выходим...
                exit();

            }else {
                $user->updateLoginByCookie(); // обновили в куках время сеанса

                //echo "url=".Yii::$app->request->url; exit;

                //$access_modules = AccessPlaces::find()->where(['page_url' => ''])->all();
                //echo "count=".count($access_modules)."<br />"; exit;

                $access_places = AccessPlaces::find()->all();
                $access_modules = [];
                $aAccessPages = [];
                //$aAccessPagePlaces = [];
                foreach ($access_places as $access_place) {
                    if(empty($access_place->page_url)) { // доступ к модулю
                        $access_modules[] = $access_place;
                    }else {
                        if(empty($access_place->page_part)) { // доступ к странице
                            $aAccessPages[$access_place->module][] = $access_place;
                        }else { // доступ к области на странице

                        }
                    }
                }

                foreach ($access_modules as $access_module) {
                    if(Yii::$app->controller->module->id == $access_module->module) {
                        $access = Access::find()->where(['id_access_places' => $access_module->id])->andWhere(['user_role_id' => Yii::$app->session->get('role_id')])->one();
                        if ($access == null || $access->access == false) {
                            throw new ForbiddenHttpException('Доступ запрещен');
                        }else {

                            //$current_controller = Yii::$app->controller->id;
//                            echo "current_controller=$current_controller";
//                            //$current_route = $this->context->route;
//                            //echo "current_route=$current_route";
//                            exit;

                            if(isset($aAccessPages[$access_module->module])) {
                                //echo "module=".$access_module->module; exit;
                                foreach ($aAccessPages[$access_module->module] as $access_page) {
                                    // echo "".$access_page->page_url; exit;

                                    if(strpos(Yii::$app->request->url, $access_page->page_url) !== false) {
                                        if($access_page->page_url == '/' && Yii::$app->controller->id == 'site') {
                                            $access = Access::find()->where(['id_access_places' => $access_page->id])->andWhere(['user_role_id' => Yii::$app->session->get('role_id')])->one();
                                            if ($access == null || $access->access == false) {
                                                //echo "url=".Yii::$app->request->url."<br />";
                                                //echo "page_url=".$access_page->page_url."<br />"; exit;
                                                throw new ForbiddenHttpException('Доступ запрещен к текущей странице');
                                            }
                                            break;
                                        }elseif($access_page->page_url != '/') {
                                            $access = Access::find()->where(['id_access_places' => $access_page->id])->andWhere(['user_role_id' => Yii::$app->session->get('role_id')])->one();
                                            if ($access == null || $access->access == false) {
                                                //echo "url=".Yii::$app->request->url."<br />";
                                                //echo "page_url=".$access_page->page_url."<br />"; exit;
                                                throw new ForbiddenHttpException('Доступ запрещен к текущей странице');
                                            }
                                            break;
                                        }
                                    }





//                                    if (strpos(Yii::$app->request->url, $access_page->page_url) !== false) {
//                                        $access = Access::find()->where(['id_access_places' => $access_page->id])->andWhere(['user_role_id' => Yii::$app->session->get('role_id')])->one();
//                                        if ($access == null || $access->access == false) {
//                                            echo "url=".Yii::$app->request->url."<br />";
//                                            echo "page_url=".$access_page->page_url."<br />"; exit;
//                                            throw new ForbiddenHttpException('Доступ запрещен к текущей странице');
//                                        }
//                                        break;
//                                    }
                                }
                            }
                        }

                        break;
                    }
                }


                /*
                //if (strpos(Yii::$app->request->url, '/admin') !== false) {
                if (Yii::$app->controller->module->id == 'admin') {

//                    if(!in_array(Yii::$app->session->get('role_alias'), ['root', 'admin', 'editor', 'manager', 'graph_operator', 'warehouse_turnover'])) {
//                        throw new ForbiddenHttpException('Доступ запрещен');
//                    }

                    $access_plase = AccessPlaces::find()->where(['module' => 'admin'])->andWhere(['page_url' => ''])->one();
                    $access = Access::find()->where(['id_access_places' => $access_plase->id])->andWhere(['user_role_id' => Yii::$app->session->get('role_id')])->one();
                    if ($access->access == false) {
                        throw new ForbiddenHttpException('Доступ запрещен');
                    }

                    //

                //} elseif (strpos(Yii::$app->request->url, '/waybill') !== false) {
                }elseif (Yii::$app->controller->module->id == 'waybill') {

//                    if(!in_array(Yii::$app->session->get('role_alias'), ['root', 'admin', 'graph_operator', 'warehouse_turnover'])) {
//                        throw new ForbiddenHttpException('Доступ запрещен');
//                    }

                    $access_plases = AccessPlaces::find()->where(['module' => 'waybill'])->andWhere(['page_url' => ''])->one();
                    $access = Access::find()->where(['id_access_places' => $access_plases->id])->andWhere(['user_role_id' => Yii::$app->session->get('role_id')])->one();
                    if ($access->access == false) {
                        throw new ForbiddenHttpException('Доступ запрещен');
                    }

                    //}elseif(strpos(Yii::$app->request->url, '/storage') !== false) {
                }elseif (Yii::$app->controller->module->id == 'storage') {

//                    if(!in_array(Yii::$app->session->get('role_alias'), ['root', 'admin', 'warehouse_turnover'])) {
//                        throw new ForbiddenHttpException('Доступ запрещен');
//                    }

                    $access_plases = AccessPlaces::find()->where(['module' => 'storage'])->andWhere(['page_url' => ''])->one();
                    $access = Access::find()->where(['id_access_places' => $access_plases->id])->andWhere(['user_role_id' => Yii::$app->session->get('role_id')])->one();
                    if ($access->access == false) {
                        throw new ForbiddenHttpException('Доступ запрещен');
                    }

                //if(strpos(Yii::$app->request->url, '/') !== false) {
                }elseif(Yii::$app->controller->module->id == 'site') {

                    $access_plase = AccessPlaces::find()->where(['module' => 'site'])->andWhere(['page_url' => ''])->one();
                    $access = Access::find()->where(['id_access_places' => $access_plase->id])->andWhere(['user_role_id' => Yii::$app->session->get('role_id')])->one();
                    if($access->access == false) {
                        throw new ForbiddenHttpException('Доступ запрещен');
                    }
                }*/


                return true;
            }
        }
    }
}
