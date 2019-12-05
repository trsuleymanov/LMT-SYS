<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Transport;
use app\models\Driver;

/* @var $this yii\web\View */
/* @var $model app\models\TripTransport */
/* @var $form ActiveForm */
?>
<div class="TripTransport">

    <?php $form = ActiveForm::begin(); ?>

    <?php
    
	echo '<div style="display:inline-block;">';
    
	// получаем всех авторов
	$transports = Transport::find()->where(['active' => 1])->all();
	// формируем массив, с ключем равным полю 'id' и значением равным полю 'name' 
	//$items = ArrayHelper::map($transports,'id','model');
	
	$items = array();
	
	foreach($transports as $transport){
		$name = $transport->model.' '.$transport->sh_model.' '.$transport->car_reg.' ('.$transport->places_count.' мест)';
		$id = $transport->id;
		$items[$id] = $name;
	}
	
	
	$params = [
		'prompt' => 'Выберите машину',
		'style' => 'width:200px;'
	];
	echo $form->field($model, 'transport_id')->dropDownList($items,$params);
	
	echo '</div>';
	
    ?>	
    
    <?php
    
	echo '<div style="display:inline-block;">';    
    
	// получаем всех авторов
	$drivers = Driver::find()->all();
	// формируем массив, с ключем равным полю 'id' и значением равным полю 'name' 
	//$items = ArrayHelper::map($transports,'id','model');
	
	$items = array();
	
	foreach($drivers as $driver){
		$name = $driver->fio;
		$id = $driver->id;
		$items[$id] = $name;
	}
	
	
	$params = [
		'prompt' => 'Выберите водителя',
		'style' => 'width:200px;'
	];
	echo $form->field($model, 'driver_id')->dropDownList($items,$params);
	
	echo '</div>';
    ?>	
         
        
       
    
        <div class="form-group">
		<button class="btn btn-primary button-close" data-dismiss="modal" style="border:none;">Закрыть</button>
            <?= Html::submitButton('Добавить автомобиль к рейсу', ['class' => 'btn btn-primary button-submit', 'style'=>'border:none;']) ?>
        </div>
    <?php ActiveForm::end(); ?>

</div><!-- TripTransport -->
