<?php
use app\assets\ChatWidgetAsset;
use yii\widgets\MaskedInput;

ChatWidgetAsset::register($this);

?>
<div class="chat-widget <?= (!in_array($userRole->alias, ['root', 'admin']) ? 'half-chat' : '') ?>">
    <div class="name-section">
        <div id="capt"><p class="chat-name">О-чат</p><span class="chat-close"><i class="glyphicon glyphicon-remove"></i></span></div>
    </div>
    <div id="messages-list-block">
        <?php

        //echo "messages_groups:<pre>"; print_r($messages_groups); echo "</pre>";

        $n = 1;
        foreach ($messages_groups as $dialog_id => $aMessages) {

            echo '<div class="group-messages" dialog-id="'.$dialog_id.'" dialog-num="'.$n.'">';
            foreach ($aMessages as $key => $message) { ?>
                <a class="message" href="">
                    <?php if($key == 0) { ?>
                        <span class="first-fio"><?= $n ?>. <?= $message->user->fio ?></span>: <span class="first-message"><?= $message->message ?>
                    <?php }else { ?>
                        <span class="n-fio"><?= $message->user->fio ?></span>: <span class="n-message"><?= $message->message ?></span>
                    <?php } ?>
                </a>
            <?php
            }
            echo '</div>';
            if($n < count($messages_groups)) {
                echo '<hr />';
            }
            $n++;
        }

        /*
        $n = 1;
        foreach($messages as $message) { ?>
            <div class="message">
                <?= $n ?>. <?= $message->user->fio ?>: <?= $message->message ?>
                <?php if(in_array($userRole->alias, ['root', 'admin', 'editor'])) { ?>
                    <div id="message-send-form-block" dialog-id="<?= $message->dialog_id ?>">
                        <div id="fields-block">
                            <div class="chat-message-answer" contenteditable="true"></div>
                            <input class="btn-send-message-answer btn-xml btn-default" type="button" value="Ответить">
                        </div>
                    </div>
                <?php } ?>
            </div>
            <?php if($n < count($messages)) {?>
                <hr />
            <?php } ?>
        <?php
        $n++;
        }*/ ?>
    </div>
    <?php if(in_array($userRole->alias, ['root', 'admin'])) { ?>
        <div id="admin-message-send-form-block">
            <div id="buttons-block">
                <button class="button" id="button-b">b</button><button class="button" id="button-i">i</button><button class="button" id="button-u">u</button><input id="button-color" type="color" value="#FF0000" name="color" /><div id="to-begin"><input name="to-begining" type="checkbox" /><span> В начало</span></div>
            </div>
            <div id="fields-block">
                <div id="chat-message" contenteditable="true"></div>
                <div id="chat-bottom-block-outer">
                    <div id="chat-bottom-block-inner">
                        <span id="lifetime-title">Вр.ж:</span>
                        <?php
                        echo MaskedInput::widget([
                            'name' => 'lifetime',
                            'mask' => '99:99',
                            'options' => [
                                'id' => 'lifetime'
                            ],
                        ]);
                        ?>
                        <input class="btn-send-message btn btn-success" type="button" value="Отправить">
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
</div>