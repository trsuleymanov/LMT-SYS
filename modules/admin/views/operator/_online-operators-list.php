<?php
use yii\helpers\Url;

$i = 1;
foreach($users as $user) { ?>
    <div class="user"><?= $i++ ?>&nbsp;&nbsp;&nbsp;<a href="<?= Url::to(['/admin/user/update', 'id' => $user->id]) ?>"><?= $user->username ?></a></div>
<?php } ?>