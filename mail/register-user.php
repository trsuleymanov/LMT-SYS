<?php
/**
 * Шаблон письма для созданного пользователя
 *
 */

use yii\helpers\Html;
use yii\helpers\Url;
?>

<h1>Уважаемый <?= Html::encode($user->lastname) ?> <?= Html::encode($user->firstname) ?></h1>
<p>Вы зарегистрированы на сайте «<?= \Yii::$app->params['brandName']?>».</p>

<p>Логин: <?= Html::encode($user->username) ?></p>
<p>Пароль: <?= Html::encode($user->password) ?></p>