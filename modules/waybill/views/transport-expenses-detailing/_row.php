<?php
use yii\helpers\Html;

?>

<tr detailing-id="<?= $detailing->id ?>">


    <td>
        <?= $num ?>
        <?php
        //echo $form->field($detailing, 'id')->hiddenInput()->label(false);
        echo Html::activeHiddenInput($detailing, 'id', [
            'name' => 'TransportExpensesDetailing['.$key.'][id]',
        ]);
        ?>
    </td>
    <td>
        <?php
        echo $form->field($detailing, 'name', [
            'errorOptions' => ['style' => 'display:none;'],
            'options' => []
        ])->textInput([
            'maxlength' => true,
            'name' => 'TransportExpensesDetailing['.$key.'][name]',
            //'style' => 'width: 45px;'
        ])->label(false);
        ?>
    </td>
    <td>
        <?php
        if($detailing->price == 0) {
            $detailing->price = '';
        }
        echo $form->field($detailing, 'price', [
            'errorOptions' => ['style' => 'display:none;']
        ])->textInput([
            'maxlength' => true,
            'id' => 'transportexpensesdetailing-price_'.$key,
            'class' => 'form-control price',
            'name' => 'TransportExpensesDetailing['.$key.'][price]',
            //'style' => 'width: 60px;'
        ])->label(false);
        ?>
    </td>
    <td>
        <a class="btn btn-danger delete-transport-expenses-detailing" href="#">
            <i class="glyphicon glyphicon-remove"></i>
        </a>
    </td>
</tr>
