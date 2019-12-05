<div id="<?= $options['id'] ?>-pfw-popup-form" for="<?= $options['id'] ?>" class="pfw-popup-form <?= $popupPosition ?>" style="display:none;">
    <div class="pfw-arrow"></div>
    <div class="pfw-title">
        <button class="close" type="button">×</button>
        <?= $formTitle ?>
    </div>
    <div class="pfw-content">
        <div class="pfw-inner-content"><?= $formContent ?></div>
        <div class="pfw-content-buttons">
            <?php if($useDefaultAcceptBut == true) {
                ?><button class="btn btn-sm btn-success pfw-accept" type="button"><i class="glyphicon glyphicon-ok"></i></button><?php
            } ?>
            <?php if($useDefaultCancelBut == true) {
                ?><button class="btn btn-sm btn-danger pfw-cancel" type="button"><i class="glyphicon glyphicon-remove"></i></button><?php
            } ?>
        </div>
    </div>
</div>