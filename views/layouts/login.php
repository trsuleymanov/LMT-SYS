<?php
use app\assets\LoginAsset;
use yii\helpers\Html;

LoginAsset::register($this);
//CheckboxAsset::register($this);

$this->beginPage();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php $this->head() ?>
</head>

<body>
    <?php $this->beginBody() ?>

    <div class="content">
        <?= $content ?>
    </div>

    <?php $this->endBody() ?>
</body>
</html>

<?php $this->endPage() ?>
