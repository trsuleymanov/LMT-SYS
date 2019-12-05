<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\SecondTripTransport;

?>

<div class="add-cars-form">

    <?php $form = ActiveForm::begin([
        'id' => 'add-cars-form',
        'options' => [
            'onDate' => $onDate,
        ],
    ]); ?>


    <?php
    if(count($second_trip_transports) > 0)
    {
        foreach($second_trip_transports as $second_trip_transport) {
            $tr_list = SecondTripTransport::getEmptyTransports($second_trip_transport->date, $second_trip_transport->transport_id);
            $tr_list = ArrayHelper::map($tr_list, 'id', 'car_reg_places_count');

            echo $this->render('_add-cars-form-row', [
                'transport_list' => $tr_list,
                'second_trip_transport' => $second_trip_transport,
				'onDate' => $onDate
            ]);
        }
    }else {

        $second_trip_transport = new SecondTripTransport();
		$second_trip_transport->date = $onDate;
        $second_trip_transport->active = true;

        echo $this->render('_add-cars-form-row', [
            'transport_list' => ['' => '---'] + $transport_list,
            'second_trip_transport' => $second_trip_transport,
	    	'onDate' => $onDate
        ]);
    } ?>

    <div class="row">
        <div class="col-sm-5"><a id="add-second-transport-driver-row" onDate="<?=$onDate;?>" href="#">Добавить еще</a></div>
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
