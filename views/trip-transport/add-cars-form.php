<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\TripTransport;

?>

<div class="add-cars-form">

    <?php $form = ActiveForm::begin([
        'id' => 'add-cars-form',
        'options' => [
            'trip-id' => $trip->id,
        ],
    ]); ?>


    <?php
    if(count($trip_transports) > 0)
    {
        $num = 0;
        foreach($trip_transports as $trip_transport) {
            echo $this->render('_add-cars-form-row', [
                'trip' => $trip,
                'trip_transport' => $trip_transport,
                'num' => ++$num
            ]);
        }
    }else {

        $trip_transport = new TripTransport();

        echo $this->render('_add-cars-form-row', [
            'trip' => $trip,
            'trip_transport' => $trip_transport
        ]);
    } ?>

    <div class="row">
        <div class="col-sm-5"><a id="add-transport-driver-row" trip_id="<?=$trip->id;?>" href="#">Добавить еще</a></div>
    </div>

    <hr />
    <div class="row">
        <div class="col-sm-2">
            <div class="form-group">
                <?= Html::button('Отмена', ['class' => 'btn btn-default button-close', 'data-dismiss' => 'modal', 'aria-hidden' => 'true']) ?>
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group">

                <?= Html::button('Применить', ['id' => 'add-cars-form-submit', 'class' => 'btn btn-success button-submit', ]) ?>

            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
