//PAGE_SWITCHER = 'SET_TRIPS';


function updateSetTripsPage() {

	var date = $('#set-of-trips-page').attr('date');

	$.ajax({
		url: '/trip/ajax-settrips?date=' + date,
		type: 'post',
		data: {},
		contentType: false,
		cache: false,
		processData: false,
		success: function (response) {

			var checked_merged_ids = [];
			$('.merged:checked').each(function(){
				checked_merged_ids.push($(this).val());
			});


			//var toInsert = $('.container').eq(1).find('.row').eq(0).find('.col-tobus-center').eq(0);
			//alert(toInsert.length);

			//toInsert.html(response);
			$('#directions-trips-block').html(response);


			$.each(checked_merged_ids, function(i,val){
				$('.merged[value="' + val + '"]').prop('checked',true);
			});
			bindEventsToTripsOfSetOfTrips();

		},
		error: function (data, textStatus, jqXHR) {
			if (textStatus == 'error' && data != undefined) {
				if (void 0 !== data.responseJSON) {
					if (data.responseJSON.message.length > 0) {
						alert(data.responseJSON.message);
					}
				} else {
					if (data.responseText.length > 0) {
						alert(data.responseText);
					}
				}

			}else {
				handlingAjaxError(data, textStatus, jqXHR);
			}
		}
	});
}



function bindEventsToTripsOfSetOfTrips()
{
	$('.trip_detail_link').filter(function( index ) {
		if(parseInt($(this).parent().attr('is-sended'))){
			return false;
		} else {
			return true;
		};
	}).unbind('click').click(function(e){

		e.preventDefault();
		var self = this;
		var editTripWindow = new modalWindow({
			url_from:'/trip/edit-trip',
			get_from:{
				trip_id:$(this).attr('trip-id')
			},
			title:'Редактирование рейса',
			actionType:'edit',
			needRemoveAfterAutomaticalClose: true,
			afterOpenAction:function(modalObj){

				$("#trip-start_time").mask("99:99");
				$("#trip-mid_time").mask("99:99");
				$("#trip-end_time").mask("99:99");

				setTripPoints(modalObj, 'modal-window');
			},
			afterResponseSuccess: function(execution, response){

				$("#trip-start_time").mask("99:99");
				$("#trip-mid_time").mask("99:99");
				$("#trip-end_time").mask("99:99");

				// форма сохранена
				if($('#set-of-trips-page').length > 0) {
					//updateSetTripsPage();
				}
			},
			//save_button_text:'Записать данные рейса',
			save_button_text: false,
			close_button_text: false,
			success_message_response:'Рейс отредактирован успешно',
			label_width:160,
			input_width:200,
			header_color:'blue',
			totalStyle:[
				{selector:'.time_points>div',content:'display:inline-block;'},
				{selector:'.time_points input',content:'width:70px;'},
				{selector:'input,select',content:'margin-left:10px;'},
				{selector:'.time_points>div input',content:'margin-left:0;'},
				{selector:'.form-group button',content:'border:none;'}
			],
			//clientValidation: formTimeValidation
		});

		editTripWindow.open();
	});

}


// функция устанавливает полям точек время после установки значения в первой точке рейса
function setTripPoints(modalObj, modal_id) {

	// тут начинается очередной ебнутый код
	var corrigeTimeObj = new timesOfTrip();

	$('#' + modal_id + ' input[name="Trip[start_time]"]').unbind('keyup focus').bind('keyup focus', function(){
		var minutes_dif = parseInt($('#' + modal_id + ' input[name="Trip[point_interval]"]').val());
		if(minutes_dif > 0) {
			if (isTimeFormat($(this).val())) {
				corrigeTimeObj.set_start_time($(this).val(), minutes_dif,
					function (thisobj) {
						$('#' + modal_id + ' input[name="Trip[mid_time]"]').val(thisobj.get_mid_time());
						$('#' + modal_id + ' input[name="Trip[end_time]"]').val(thisobj.get_end_time());
					}, function () {

					}
				);
			}
		}
	});

	$('#' + modal_id + ' input[name="Trip[point_interval]"]').unbind('keyup focus').bind('keyup focus', function(){

		// эмулируем нажатие клавиши
		var e = $.Event("keyup", { keyCode: 9 });
		$('#' + modal_id + ' input[name="Trip[start_time]"]').trigger(e);
	});

	/*
	$('#' + modal_id + ' input[name="Trip[mid_time]"]').unbind('change').bind('change',
		function(){
			if($(this).val() == corrigeTimeObj.get_mid_time()){
				return false;
			}
			if(isTimeFormat($(this).val())){
				corrigeTimeObj.set_mid_time($(this).val(), function(thisobj){
					$('#' + modal_id + ' input[name="Trip[mid_time]"]').val(thisobj.get_mid_time());
					$('#' + modal_id + ' input[name="Trip[end_time]"]').val(thisobj.get_end_time());
				});
			} else {
				if(isTimeFormat(corrigeTimeObj.get_mid_time())){
					$('#' + modal_id + ' input[name="Trip[mid_time]"]').val(corrigeTimeObj.get_mid_time());
				}
			}
		});

	$('#' + modal_id + ' input[name="Trip[end_time]"]').unbind('change').bind('change',
		function(){
			if(isTimeFormat($(this).val())){
				corrigeTimeObj.set_end_time($(this).val(), function(thisobj){
					$('#' + modal_id + ' input[name="Trip[end_time]"]').val(thisobj.get_end_time());
				}, function(thisobj){
					if(isTimeFormat(thisobj.get_end_time())){
						$('#' + modal_id + ' input[name="Trip[end_time]"]').val(thisobj.get_end_time());
					}
				});
			} else {
				if(isTimeFormat(corrigeTimeObj.get_end_time())){
					$('#' + modal_id + ' input[name="Trip[end_time]"]').val(corrigeTimeObj.get_end_time());
				}
			}
		});
		*/
}


function setCommercialTrips(trips) {

	$.ajax({
		url: '/trip/ajax-set-commercial-trips',
		type: 'post',
		data: {
			trips: trips
		},
		success: function (data) {
			$('input[class="merged"]').prop('checked', false);
			// updateSetTripsPage();
		},
		error: function (data, textStatus, jqXHR) {
			if (textStatus == 'error' && data != undefined) {
				if (void 0 !== data.responseJSON) {
					if (data.responseJSON.message.length > 0) {
						alert(data.responseJSON.message);
					}
				} else {
					if (data.responseText.length > 0) {
						alert(data.responseText);
					}
				}
			}else {
				handlingAjaxError(data, textStatus, jqXHR);
			}
		}
	});
}


function unsetCommercialTrips(trips) {

	$.ajax({
		url: '/trip/ajax-unset-commercial-trips',
		type: 'post',
		data: {
			trips: trips
		},
		success: function (data) {
			$('input[class="merged"]').prop('checked', false);
			// updateSetTripsPage();
		},
		error: function (data, textStatus, jqXHR) {
			if (textStatus == 'error' && data != undefined) {
				if (void 0 !== data.responseJSON) {
					if (data.responseJSON.message.length > 0) {
						alert(data.responseJSON.message);
					}
				} else {
					if (data.responseText.length > 0) {
						alert(data.responseText);
					}
				}
			}else {
				handlingAjaxError(data, textStatus, jqXHR);
			}
		}
	});
}



$(document).ready(function(){

	//setInterval(function() {
	//	updateSetTripsPage();
	//}, 10000);

	// еще одно блятство - нет времени сейчас это исправлять...
	//$('.add_transport_plus').each(function(){
	//	if($(this).parent().parent().eq(0).height() > 36){
	//		$(this).css('top',$(this).parent().parent().eq(0).height() - 16);
	//	}
	//});

	bindEventsToTripsOfSetOfTrips();


	$(document).on('click', '#add-trip', function() {
		var date = $('#set-of-trips-page').attr('date');
		var addTripWindow = new modalWindow({
			url_from:'/trip/add-trip',
			get_from:{
				date: date
			},
			title: 'Добавить новый рейс',
			needRemoveAfterAutomaticalClose: true,
			afterOpenAction:function(modalObj){

				$("#trip-start_time").mask("99:99");
				$("#trip-mid_time").mask("99:99");
				$("#trip-end_time").mask("99:99");

				setTripPoints(modalObj, 'modal-window')
			},
			afterResponseSuccess: function(execution, response){

				$("#trip-start_time").mask("99:99");
				$("#trip-mid_time").mask("99:99");
				$("#trip-end_time").mask("99:99");

				// форма сохранена
				if($('#set-of-trips-page').length > 0) {
					// updateSetTripsPage();
				}
			},
			success_message_response:'Добавление рейса прошло успешно',
			label_width:160,
			input_width:200,
			header_color:'green',
			totalStyle:[
				{selector:'.time_points>div',content:'display:inline-block;'},
				{selector:'.time_points input', content:'width:70px;'},
				{selector:'input,select',content:'margin-left:10px;'},
				{selector:'.time_points>div input',content:'margin-left:0;'},
				{selector:'.form-group button',content:'border:none;'}
			],
			clientValidation: formTimeValidation
		});

		addTripWindow.open();
	});

	// создать резервный рейс
	$(document).on('click', '#create-reserv-trip', function() {

		var date = $('#set-of-trips-page').attr('date');
		var addTripWindow = new modalWindow({
			url_from:'/trip/add-trip',
			get_from:{
				date: date,
				is_reserv_trip: 1
			},
			title: 'Добавить новый резервный рейс',
			needRemoveAfterAutomaticalClose: true,
			afterOpenAction:function(modalObj){

				$("#trip-start_time").mask("99:99");
				$("#trip-mid_time").mask("99:99");
				$("#trip-end_time").mask("99:99");

				setTripPoints(modalObj, 'modal-window')
			},
			afterResponseSuccess: function(execution, response){

				$("#trip-start_time").mask("99:99");
				$("#trip-mid_time").mask("99:99");
				$("#trip-end_time").mask("99:99");

				// форма сохранена
				if($('#set-of-trips-page').length > 0) {
					// updateSetTripsPage();
				}
			},
			success_message_response:'Добавление рейса прошло успешно',
			label_width:160,
			input_width:200,
			header_color:'green',
			totalStyle:[
				{selector:'.time_points>div',content:'display:inline-block;'},
				{selector:'.time_points input', content:'width:70px;'},
				{selector:'input,select',content:'margin-left:10px;'},
				{selector:'.time_points>div input',content:'margin-left:0;'},
				{selector:'.form-group button',content:'border:none;'}
			],
			clientValidation: formTimeValidation
		});

		addTripWindow.open();
	});


	$(document).on('click', '#merge-trips', function() {

		// ищем направление в котором есть выделенные чекбоксы
		var selected_trips = [];
		//$('.info-list').each(function(index) {
		$('.direction').each(function(index) {
			if($(this).find('input.merged:checked').length > 0) {
				var dir_obj = $(this);
				$(this).find('input.merged').each(function() {
					if($(this).is(':checked')) {
						selected_trips.push($(this).val());
					}else if(selected_trips.length > 0){
						// объядиняемые рейсы должны идти подряд
						if(selected_trips.length < dir_obj.find('input.merged:checked').length) {
							alert('Могут объединяться только соседние рейсы. Поэтому часть рейсов не будет учтено при объединении');
							return false;
						}
					}
				});
				return false;
			}
		});


		if(selected_trips.length == 0) {
			alert('Выберите рейсы для объединения');
			return false;
		}else if(selected_trips.length < 2){
			alert('Выберите минимум два рейса подряд на направлении для объединения');
			return false;
		}else if(selected_trips.length > 3) {
			alert('Нельзя объядинять больше 3-х рейсов');
			return false;
		}


		$.ajax({
			url: '/trip/merge-trips?trips_ids=' + selected_trips.join(','),
			type: 'post',
			data: {},
			beforeSend: function () {
				//allow_send_transport = false;
			},
			success: function (response) {

				$('#default-modal').find('.modal-body').html(response);
				//$('#default-modal .modal-dialog').width('400px');
				$('#default-modal .modal-title').html('Объединить рейсы');
				$('#default-modal').modal('show');

				$("#trip-start_time").mask("99:99");
				$("#trip-mid_time").mask("99:99");
				$("#trip-end_time").mask("99:99");

				// объединяем установленные точки в соответствии с интервалом минут в форме
				setTripPoints($('#default-modal'), 'default-modal');

				// эмулируем нажатие клавиши чтобы пересчитать серверные точки уже в соответствии с интервалом минут
				//var e = $.Event("keyup", { keyCode: 9 });
				//$('#default-modal input[name="Trip[start_time]"]').trigger(e);

			},
			error: function (data, textStatus, jqXHR) {
				if (textStatus == 'error' && data != undefined) {
					if (void 0 !== data.responseJSON) {
						if (data.responseJSON.message.length > 0) {
							alert(data.responseJSON.message);
						}
					} else {
						if (data.responseText.length > 0) {
							alert(data.responseText);
						}
					}
				}else {
					handlingAjaxError(data, textStatus, jqXHR);
				}
				//allow_send_transport = true;
			}
		});
	});


	var allow_merge_trips = true;
	$(document).on('submit', '#merge-trip-form', function(event) {

		//alert('click');

		event.preventDefault();
		event.stopImmediatePropagation();


		var form = $(this);
		var formData = $(this).serialize();
		if (form.find('.has-error').length) {
			return false;
		}

		if(allow_merge_trips == true) {

			$.ajax({
				url: form.attr("action"),
				type: form.attr("method"),
				data: formData,
				beforeSend: function () {
					allow_merge_trips = false;
				},
				success: function (response) {

					allow_merge_trips = true;
					if($('#set-of-trips-page').length > 0) {
						// updateSetTripsPage();
					}

					$('#default-modal').modal('hide');

				},
				error: function(data, textStatus, jqXHR) {
					allow_merge_trips = true;
					if (textStatus == 'error' && data != undefined) {
						if(void 0 !== data.responseJSON) {
							if(data.responseJSON.message.length > 0) {
								alert(data.responseJSON.message);
							}
						}else {
							if(data.responseText.length > 0) {
								alert(data.responseText);
							}
						}
					}else {
						handlingAjaxError(data, textStatus, jqXHR);
					}
				}
			});
		}else {
			alert('хватить жать на кнопку - запрос обрабатывается...');
			//LogDispatcherAccounting('кнопка «Отправить» т/с формы отправки');
		}


		return false;
	});

	//$('body').on('submit', '#point-form', function(event) {
	//$(document).on('submit', '#merge-trip-form', function(event) {
    //
	//	event.preventDefault();
	//	event.stopImmediatePropagation();
    //
	//	alert('click');
    //
	//	return false;
	//});


	$(document).on('click', '#add-second-transport-driver-row', function()
	{
		var self = this;

		$.ajax({
			url: '/second-trip-transport/ajax-get-add-car-tr?onDate=' + $(self).attr('onDate'),
			type: 'post',
			data: {},
			contentType: false,
			cache: false,
			processData: false,
			success: function (data) {
				if (data.success == true) {
					$('#add-second-transport-driver-row').parents('.row').before(data.tr_html);
					//bindTransportChange();
					//bindTransportConfirmedClick();
				}
			},
			error: function (data, textStatus, jqXHR) {
				if (textStatus == 'error' && data != undefined) {
					if (void 0 !== data.responseJSON) {
						if (data.responseJSON.message.length > 0) {
							alert(data.responseJSON.message);
						}
					} else {
						if (data.responseText.length > 0) {
							alert(data.responseText);
						}
					}
				}else {
					handlingAjaxError(data, textStatus, jqXHR);
				}
			}
		});

		return false;
	});


	$(document).on('click', '#second-transport', function() {

		var self = this;

		var date = $('#set-of-trips-page').attr('date');
		var secondTransport = new modalWindow({
			id:'default-modal',
			url_from:'/second-trip-transport/ajax-get-add-cars-form',
			get_from:{
				onDate: date
			},
			url_to:'/second-trip-transport/ajax-save-cars-form',
			get_to:{
				onDate: date
			},
			width: '500px',
			needRemoveAfterAutomaticalClose:true,
			dataAsquition: function(modalWindow){
				var transport_ids = [];
				var second_trip_transport_ids = [];
				var i = 0;
				$('#' + modalWindow.id + ' .modal-body .trip-transport-row').each(function() {
					var transport_id = $(this).find('*[name="SecondTripTransport[transport_id]"]').val();
					var second_trip_transport_id = $(this).find('*[name="SecondTripTransport[transport_id]"]').attr('second-trip-transport-id');
					if(transport_id.length > 0) {
						transport_ids[i] = transport_id;
						second_trip_transport_ids[i] = second_trip_transport_id;
						i++;
					}
				});

				return {
					transport_ids: transport_ids,
					second_trip_transport_ids: second_trip_transport_ids
				}
			},

			standartTemplate: false,
			title: 'Вторые ресы на '+date,
			header_color:'black',
			header_type:'simple',
			totalStyle:'.head_block{margin-left:0;text-align:left;background-color:white;font-size:14px;font-weight:normal;color:#333;}',
			save_button_text:'Применить',

			success_message_response:'Транспорт вторых рейсов добавлен'
		});

		secondTransport.open();
	});


	$(document).on('change', '.trip-transport-row', function() {

		if(!$(this).val()){
			return;
		}

		var eventElem = $(this).find('select').eq(0);
		var listElem = $(this).find('select').eq(1);

		var self = this;
		var last_val = listElem.val();
		if(!last_val){
			last_val = '';
		}

		var executeGetDrivers = new modalWindow({
			url_to: '/trip-transport/get-empty-drivers',
			get_to: {
				'transport_id': $(self).val(),
				'selected_driver_id':last_val
			},
			afterResponseSuccess: function(thisExecution, response){

				var str = '<option value="' + '' + '">---</option>';
				$.each(response.list, function(i,option){
					str += '<option value="' + option.id + '">' + option.value + '</option>';
				})

				listElem.html(str);
				listElem.val(last_val);
			},
			success_message_response:null
		});
		executeGetDrivers.execute();
	});


	$(document).on('change', '#set-of-trips-page input[name="direction"]', function() {

		var is_checked = $(this).is(':checked');
		var value = $(this).val();

		if(is_checked == true) {
			$('input.merged[direction-id="' + value + '"]').prop('checked', is_checked);
		}else {
			$('input.merged[direction-id="' + value + '"]').prop('checked', is_checked);
		}
	});


	$(document).on('click', '#set-commercial-trips', function() {

		var trips = [];
		$('input.merged:checked').each(function() {
			trips.push($(this).val());
		});

		if(trips.length == 0) {
			alert('Выберите рейсы');
			return false;
		}

		setCommercialTrips(trips);
	});


	$(document).on('click', '#unset-commercial-trips', function() {

		var trips = [];
		$('input.merged:checked').each(function() {
			trips.push($(this).val());
		});

		if(trips.length == 0) {
			alert('Выберите рейсы');
			return false;
		}

		unsetCommercialTrips(trips);
	});

	$(document).on('click', '#open-transport-efficiency', function() {
		var date = $('#selected-day').attr('date');
		window.open("/transport/efficiency-list?date=" + date, "Эффективность т/с", "width=1000,height=800");
	});


	$(document).on('click', '.mergeTrips .merge-trip', function() {
		$('.merged_trip_list .merge-trip').each(function() {
			$(this).removeAttr('active');
		});
		$(this).attr('active', 'true');

		var trip_name = $.trim($(this).text());
		var start_time = $(this).attr('start_time');
		var mid_time = $(this).attr('mid_time');
		var end_time = $(this).attr('end_time');

		$('.mergeTrips #trip-name').val(trip_name);
		$('.mergeTrips #trip-start_time').val(start_time);
		$('.mergeTrips #trip-mid_time').val(mid_time);
		$('.mergeTrips #trip-end_time').val(end_time);

		// эмулируем нажатие клавиши чтобы пересчитать серверные точки уже в соответствии с интервалом минут
		//var e = $.Event("keyup", { keyCode: 9 });
		//$('#default-modal input[name="Trip[start_time]"]').trigger(e);

		return false;
	});
});