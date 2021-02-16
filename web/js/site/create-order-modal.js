
function _openModalCreateOrder(data)
{
    if(void 0 === data['date']) {
        data['date'] = '';
    }
    if(void 0 === data['trip_id']) {
        data['trip_id'] = '';
    }
    if(void 0 === data['clientext_id']) {
        data['clientext_id'] = '';
    }
    if(void 0 === data['trip_transport_id']) {
        data['trip_transport_id'] = '';
    }
    if(void 0 === data['ignore_clientext_another_operator']) {
        data['ignore_clientext_another_operator'] = 0;//false
    }


    if($('#directions-block').length > 0) {
        data['direction_id'] = $('input[name="direction"]:checked').val();
        $('input[name="direction"]').attr('checked', false);
    }else {
        data['direction_id'] = null;
    }
    $.ajax({
        url: '/order/ajax-get-form',
        type: 'post',
        data: data,
        success: function (response) {

            if(response.success == true) {

                clearPhonesBlock();
                $('.order-phones-block').remove();

                if($('#order-create-modal .map-block').length > 0) {
                    $('#order-create-modal .map-block').remove();
                }


                $('#order-create-modal').find('.modal-body').html(response.html);
                $('#order-create-modal .modal-title').html(response.title);
                $('#order-create-modal')
                    .removeClass()
                    .addClass('fade modal')
                    //.removeClass('orange-day')
                    //.removeClass('purple-day')
                    //.removeClass('blue-day')
                    .addClass(response.class)
                    .modal('show');

                $('#order-create-modal').css({
                    padding: 0,
                    'z-index': 10000
                });


                $('#order-create-modal').append(response.phones_block);


                setTimeout(function() {
                    $('#order-create-modal').find('input[name="Client[mobile_phone]"]').focus();
                }, 1000);

                $('#order-create-modal').on('shown.bs.modal', function (e) {
                    $('#order-create-modal').css({
                        'padding-left': 0
                    });
                });

                if($('#clientext-modal').length > 0) {
                    $('#clientext-modal').css({
                        'z-index': 1
                    });

                    $('#order-create-modal').on('hidden.bs.modal', function (e) {
                        $('#clientext-modal').css({
                            'z-index': 1045
                        });

                        if($('#inner-call-window').length > 0) {
                            $('#inner-call-window .modal-close').click();
                        }
                    });
                }


            }else {

                if(response.already_in_use != void 0 && response.already_in_use == 1) {
                    if(confirm('Оператор ' + response.clientext_operator_username + ' уже создает заказ на основе заявки, вы уверены что хотите создать заказ?')) {
                        var ignore_clientext_another_operator = true;
                        var data = {
                            date: date,
                            trip_id: trip_id,
                            order_id: order_id,
                            clientext_id: clientext_id,
                            ignore_clientext_another_operator: ignore_clientext_another_operator
                        }
                        openModalCreateOrder(data);
                    }
                }else {
                    alert('неустановленная ошибка загрузки формы записи клиента');
                }
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
}

// загрузка модального окна создания заказа (записи пассажира)
//function openModalCreateOrder(date, trip_id, order_id, clientext_id, ignore_clientext_another_operator)
var need_to_open_after_close = false;
var need_to_open_after_close_data = null;
function openModalCreateOrder(data)
{
    // clientext_id и связанный код в недалеком будущем удалить!!! (от 10.07.2018)
    if($('#order-create-modal').is(':visible')) {

        // console.log('Закрываем');
        $('#order-create-modal').modal('hide');
        need_to_open_after_close = true;
        need_to_open_after_close_data = data;

        // создается что-то вроде замыкания, которое множиться в памяти при каждом открытии-закрытии формы из окно звонков
        //$('#order-create-modal').on('hidden.bs.modal', function() {
        //    $('#order-create-modal .modal-title').html('');
        //    $('#order-create-modal .modal-body').html('');
        //
        //    _openModalCreateOrder(data);
        //});

    }else {
        _openModalCreateOrder(data);
    }

}

$(document).ready(function() {

    $('#order-create-modal').on('hidden.bs.modal', function() {

        // console.log('Закрылось окно заказа need_to_open_after_close='+need_to_open_after_close);
        if(need_to_open_after_close) {
            $('#order-create-modal .modal-title').html('');
            $('#order-create-modal .modal-body').html('');

            _openModalCreateOrder(need_to_open_after_close_data);

            need_to_open_after_close = false;
            need_to_open_after_close_data = null;
        }
    });
});


function getFormData()
{
    var data = {};

    var Order = {
        id: $('#order-client-form').attr('order-id'),
        temp_identifier: $('#order-client-form').attr('order-temp-identifier'),
        //client_ext_id: $('input[name="Order[client_ext_id]"]').val(),
        date: $('input[name="Order[date]"]').val(),
        direction_id: $('*[name="Order[direction_id]"]').val(),
        trip_id: $('*[name="Order[trip_id]"]').val(),
        informer_office_id: $('*[name="Order[informer_office_id]"]').val(),
        is_not_places: 0 + $('input[name="Order[is_not_places]"]').is(':checked'),
        places_count: $('input[name="Order[places_count]"]').val(),
        student_count: $('input[name="Order[student_count]"]').val(),
        child_count: $('input[name="Order[child_count]"]').val(),
        bag_count: $('input[name="Order[bag_count]"]').val(),
        suitcase_count: $('input[name="Order[suitcase_count]"]').val(),
        oversized_count: $('input[name="Order[oversized_count]"]').val(),
        use_fix_price: $('input[name="Order[use_fix_price]"]').is(':checked'),
        fix_price: $('#order-fix_price-disp').val(),
        //price: $('#order-price-disp').val(),
        //price: $('#price').val(),
        comment: $('*[name="Order[comment]"]').val(),
        additional_phone_1: $('input[name="Order[additional_phone_1]"]').val(),
        additional_phone_2: $('input[name="Order[additional_phone_2]"]').val(),
        additional_phone_3: $('input[name="Order[additional_phone_3]"]').val(),
        time_air_train_arrival: $('input[name="Order[time_air_train_arrival]"]').val(),
        street_id_to:  $('input[name="Order[street_id_to]"]').val(),
        point_id_to: $('input[name="Order[point_id_to]"]').val(),
        time_air_train_departure: $('input[name="Order[time_air_train_departure]"]').val(),
        confirm_click_time: $('input[name="Order[confirm_click_time]"]').val(),
        confirm_clicker_id: $('input[name="Order[confirm_clicker_id]"]').val(),
        time_confirm: $('input[name="Order[time_confirm]"]').val(),
        time_confirm_auto: $('input[name="Order[time_confirm_auto]"]').val(),
        trip_transport_id: $('select[name="Order[trip_transport_id]"]').val(), // для этого поля на сервере отдельная обработка в зависимости от того выбран ли radio_group_1
        relation_order_id: $('input[name="Order[relation_order_id]"]').val()
    };


    if($('#order-forced').length > 0) {
        Order.forced = $('#order-forced').is(':checked');
    }

    var value = parseInt($('input[name="Order[radio_confirm_now]"]:checked').val());
    if(value == undefined || isNaN(value)) {
        value = 0;
    }
    Order.radio_confirm_now = value;


    var value = $('input[name="Order[radio_group_1]"]:checked').val();
    if(value == undefined) {
        value = '';
    }
    Order.radio_group_1 = value;

    var value = $('input[name="Order[radio_group_2]"]:checked').val();
    if(value == undefined) { value = ''; }
    Order.radio_group_2 = value;

    var value = $('input[name="Order[radio_group_3]"]:checked').val();
    if(value == undefined) {
        value = '';
    }
    Order.radio_group_3 = value;


    if(Order.fix_price != undefined) {
        Order.fix_price = $.trim(Order.fix_price.replace(/Р/g,''));
    }

    var yandex_point_from = $('input[name="Order[yandex_point_from]"]').val();
    if(yandex_point_from != undefined) {
        var yandex_point_from_data = yandex_point_from.split('_');
        Order.yandex_point_from_id = yandex_point_from_data[0];
        Order.yandex_point_from_lat = yandex_point_from_data[1];
        Order.yandex_point_from_long = yandex_point_from_data[2];
        Order.yandex_point_from_name = yandex_point_from_data[3];
    }

    var yandex_point_to = $('input[name="Order[yandex_point_to]"]').val();
    //console.log('yandex_point_to='+yandex_point_to);
    if(yandex_point_to != undefined) {
        var yandex_point_to_data = yandex_point_to.split('_');
        Order.yandex_point_to_id = yandex_point_to_data[0];
        Order.yandex_point_to_lat = yandex_point_to_data[1];
        Order.yandex_point_to_long = yandex_point_to_data[2];
        Order.yandex_point_to_name = yandex_point_to_data[3];
    }




    var Client = {
        mobile_phone: $('input[name="Client[mobile_phone]"]').val(),
        home_phone: $('input[name="Client[home_phone]"]').val(),
        alt_phone: $('input[name="Client[alt_phone]"]').val(),
        name: $('input[name="Client[name]"]').val(),
        email: $('input[name="Client[email]"]').val()
    };


    var data = {
        Order: Order,
        Client: Client
    };
    //sconsole.log('data:'); console.log(data);

    return data;
}


// функция обновления цены заказа
var allow_updating_price = true;
function updatePrice()
{
    if(allow_updating_price == true) {

        var data = getFormData();
        console.log('data:'); console.log(data);

        if(
            (
                parseInt(data.Order.trip_id) > 0
                && data.Order.yandex_point_from_name != undefined
                && data.Order.yandex_point_to_name != undefined
                && (parseInt(data.Order.places_count) > 0 || data.Order.is_not_places == 1)
                && data.Order.use_fix_price != true
            )
        ) {

            $.ajax({
                url: '/order/ajax-get-calculate-price',
                type: 'post',
                data: data,
                beforeSend: function () {
                    allow_updating_price = false;
                },
                success: function (response) {

                    if (response.success == true) {

                        if(response.loyalty_switch == 'cash_back_on') {

                            $('#order-client-form #price').text(response.price);
                            $('#order-client-form #usedCashBack').text(response.used_cash_back);
                            $('#order-client-form #resultPrice').text(response.result_price);

                        }else { // fifth_place_prize
                            $('#order-client-form #price').text(response.price);
                            $('#order-client-form #prizeTripCount').text(response.prizeTripCount);
                        }


                        if(response.use_fix_price == 1) {
                            $('*[name="Order[use_fix_price]"]').prop('checked', true);
                            $('#order-fix_price-disp').val(response.price);
                        }else {
                            $('*[name="Order[use_fix_price]"]').prop('checked', false);
                            $('#order-fix_price-disp').val(0);
                        }

                        $('*[name="Order[comment]"]').text(response.comment);

                        var point_from_str = '';
                        if(response.point_from_diff < 0) {
                            point_from_str = 'Скидка ' + Math.abs(response.point_from_diff);
                        }else {
                            point_from_str = 'Наценка ' + response.point_from_diff;
                        }

                        var point_to_str = '';
                        if(response.point_to_diff < 0) {
                            point_to_str = 'Скидка ' + Math.abs(response.point_to_diff);
                        }else {
                            point_to_str = 'Наценка ' + response.point_to_diff;
                        }

                        $('#order-client-form .point-from-str').text(point_from_str);
                        $('#order-client-form .point-to-str').text(point_to_str);

                    } else {
                        alert('неустановленная ошибка расчета цены');
                    }
                    allow_updating_price = true;
                },
                error: function (data, textStatus, jqXHR) {
                    allow_updating_price = true;
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
        }else {
            $('#order-client-form #price').text('-');
            $('#order-client-form #prizeTripCount').text('-');
            $('#order-client-form .point-from-str').text('-');
            $('#order-client-form .point-to-str').text('-');
        }
    }
}


function updatePassengerRefer() {


    var order_passengers_count = $('#order-client-form').attr('order-passengers-count');
    var places_count = parseInt($('#order-client-form').find('*[name="Order[places_count]"]').val());

    var show = true;
    var _class = "";
    var text = "";
    if(places_count > 0 && places_count == order_passengers_count) {
        text = "Изменить";
    }else if(places_count > 0 && places_count > order_passengers_count) {
        text = "Добавить";
        _class = "text-danger";
    }else if(places_count > 0 && places_count < order_passengers_count) {
        text = "Удалить";
        _class = "text-danger";
    }else {
        show = false;
    }

    console.log('updatePassengerRefer text='+text+' _class='+_class+' show='+show + ' order_passengers_count='+order_passengers_count+' places_count='+places_count);

    $('#order-create-modal').find('.edit-passengers')
        .text(text)
        .removeClass('text-danger').addClass(_class);

    if(show) {
        $('#order-create-modal').find('.edit-passengers').show();
    }else {
        $('#order-create-modal').find('.edit-passengers').hide();
    }
}


function resetOrderFormRadiobuttons()
{
    $('#order-radio_confirm_now').find('input[name="Order[radio_confirm_now]"]').prop('checked', false);
    $('#order-radio_confirm_now').removeClass('disabled');
    $('#order-radio_confirm_now').find('input[name="Order[radio_confirm_now]"]').removeAttr('disabled');

    $('#order-radio_group_1').find('input[name="Order[radio_group_1]"]').prop('checked', false);
    $('#order-radio_group_1').removeClass('disabled').addClass('disabled');
    $('#order-radio_group_1').find('input[name="Order[radio_group_1]"]').attr('disabled', 'true');
    $('select[name="Order[trip_transport_id]"]').attr('disabled', 'true');

    $('#order-radio_group_2').find('input[name="Order[radio_group_2]"]').prop('checked', false);
    $('#order-radio_group_2').removeClass('disabled').addClass('disabled');
    $('#order-radio_group_2').find('input[name="Order[radio_group_2]"]').attr('disabled', 'true');

    $('#order-radio_group_3').find('input[name="Order[radio_group_3]"]').prop('checked', false);
    $('#order-radio_group_3').removeClass('disabled').addClass('disabled');
    $('#order-radio_group_3').find('input[name="Order[radio_group_3]"]').attr('disabled', 'true');

    $('#order-time_confirm').val('').attr('disabled', 'true');
    $('#confirm-button').val('Установить').attr('disabled', 'true').removeClass('btn-success').removeClass('btn-default').addClass('btn-default');
    $('#writedown-button').removeClass('disabled').addClass('disabled');
}


function updateTrips() {

    var date = $('#date').val();
    var direction_id = $('#order-client-form #direction').val();

    if(direction_id > 0) {
        $.ajax({
            url: '/trip/ajax-index?date=' + date + '&direction_id=' + direction_id,
            type: 'post',
            contentType: false,
            cache: false,
            processData: false,
            success: function (trip_list) {
                var options = '';
                options += '<option value="">Выберите рейс</option>';
                for (var key in trip_list) {
                    var trip = trip_list[key];
                    options += '<option value="' + trip.id + '">' + trip.name + ' (' + trip.start_time + ', ' + trip.mid_time + ', ' + trip.end_time + ')</option>';
                }

                $('#trip').html(options).removeAttr('disabled');

                // снимаем блокировку с полей: мобильный, домашний, другой, ФИО
                $('input[name="Client[mobile_phone]"]').removeAttr('disabled');
                $('input[name="Client[home_phone]"]').removeAttr('disabled');
                $('input[name="Client[alt_phone]"]').removeAttr('disabled');
                $('input[name="Client[name]"]').removeAttr('disabled');
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
                } else {
                    handlingAjaxError(data, textStatus, jqXHR);
                }
            }
        });
    }
}


function setDoTariffParams(do_tariff, form_name) {

    if(form_name == 'order_form') {
        if(do_tariff != null) {

            if (do_tariff.use_fix_price == 0) {
                if ($('*[name="Order[use_fix_price]"]').prop('checked') == true) {
                    $('*[name="Order[use_fix_price]"]').click();
                }
            } else {
                if ($('*[name="Order[use_fix_price]"]').prop('checked') == false) {
                    $('*[name="Order[use_fix_price]"]').click();
                }
            }
        }

        console.log('вызываем updatePrice()');
        updatePrice();

    }else if(form_name == 'phones_form'){
        // ничего тут не делаем
    }
}

searchClientByPhoneResponse = {};

function updateOrderFormByClientData(data, field_name) {

    if(field_name == 'mobile_phone') {

        if (void 0 !== data.client && data.client !== null && Object.keys(data.client).length > 0) {

            // alert('клиент существует');
            $('input[name="Client[name]"]').val(data.client.name);
            $('input[name="Client[home_phone]"]').val(data.client.home_phone);
            $('input[name="Client[alt_phone]"]').val(data.client.alt_phone);

            // индивидуальный тариф проверяем
            if (void 0 !== data.informer_office && data.informer_office !== null) {
                $('select[name="Order[informer_office_id]"]').val(data.informer_office.id);
            }

            if (void 0 !== data.client_do_tariff && data.client_do_tariff !== null) {
                setDoTariffParams(data.client_do_tariff, form_name);
            }

            $('input[name="Client[email]"]').val(data.client.email);
        }
        // else {
        //     alert('Новый клиент');
        //     $('#client-name').focus();
        // }


        if (data.yandex_point_from_lat != '' && data.yandex_point_from_long != '' && data.yandex_point_from_name != '') {
            var key = data.yandex_point_from_id + '_' + data.yandex_point_from_lat + '_' + data.yandex_point_from_long + '_' + data.yandex_point_from_name;
            var value = data.yandex_point_from_name;

            selectWidgetInsertValue($('input[name="Order[yandex_point_from]"]').parents('.sw-element'), key, value);

            if (void 0 !== data.yandexPointFrom && data.yandexPointFrom !== null) {
                if (data.yandexPointFrom.critical_point == 1) {
                    $('input[name="Order[time_air_train_arrival]"]').removeAttr('disabled');
                }
            }
        }


        if (data.yandex_point_to_lat != '' && data.yandex_point_to_long != '' && data.yandex_point_to_name != '') {
            var key = data.yandex_point_to_id + '_' + data.yandex_point_to_lat + '_' + data.yandex_point_to_long + '_' + data.yandex_point_to_name;
            var value = data.yandex_point_to_name;

            selectWidgetInsertValue($('input[name="Order[yandex_point_to]"]').parents('.sw-element'), key, value);

            if (void 0 !== data.yandexPointTo && data.yandexPointTo !== null) {
                if (data.yandexPointTo.critical_point == 1) {
                    $('input[name="Order[time_air_train_departure]"]').removeAttr('disabled');
                }
            }
        }

        $('#order-client-form .edit-passengers').attr('client-id', data.client.id);
    }

    if(field_name != 'mobile_phone') {

        if (data.message != '') {
            var modal_footer =
                '<div id="modal-footer" class="row">' +
                '<div class="col-sm-2">' +
                '<div class="form-group">' +
                '<button id="ischeckedbut-client-last-orders" type="button" class="btn btn-default button-close" data-dismiss="modal" aria-hidden="true">Проверено</button>' +
                '</div>' +
                '</div>' +
                '</div>';

            $('#client-last-orders-modal').find('.modal-body').html(data.message + modal_footer);
            $('#client-last-orders-modal').modal('show');
        }
    }
}


function searchClientByPhone(phone, form_name, field_name) {

    // console.log('searchClientByPhone phone='+phone+' form_name='+form_name+' field_name='+field_name);

    var direction_id = $('#direction').val();
    var current_order_date = $('#order-client-form').find('#date').val();
    var current_order_id = $('#order-client-form').attr('order-id');

    phone = phone.replace(/\*/g,'');

    if (phone.length == 15 && phone[phone.length - 1] != '_' && direction_id > 0) { // считаем что мобильный телефон введен в формате: +7-xxx-xxx-xxxx

        $.ajax({
            url: '/client/ajax-get-client?phone=' + phone + '&direction_id=' + direction_id
            + '&current_order_date=' + current_order_date + '&current_order_id=' + current_order_id
            + '&field_name=' + field_name,
            type: 'post',
            data: {},
            contentType: false,
            cache: false,
            processData: false,
            success: function (data) {
                if (data.success == true) {


                    if (form_name == 'order_form') {

                        //if(field_name == 'mobile_phone') {
                        updateOrderFormByClientData(data, field_name);
                        //}

                        if(data.client.id == undefined) {

                            // alert('Новый клиент');
                            if(data.new_client_question_html != '') {
                                // ...
                                $('#advertising-source-modal').find('.modal-body').html(data.new_client_question_html);
                                $('#advertising-source-modal').modal('show');
                            }
                        }

                    }else if(form_name == 'phones_form') {

                        //if(field_name != 'mobile_phone') {
                        //    updateOrderFormByClientData(data, field_name);
                        //}

                        searchClientByPhoneResponse = data;

                        //if (void 0 !== data.client && data.client !== null && Object.keys(data.client).length > 0) {
                        //
                        //}else {
                        //    alert('Новый клиент');
                        //}

                        if(data.client.id == undefined) {

                            // alert('Новый клиент');
                            if(data.new_client_question_html != '') {
                                $('#advertising-source-modal').find('.modal-body').html(data.new_client_question_html);
                                $('#advertising-source-modal').modal('show');
                            }
                        }

                        if(field_name == 'mobile_phone') {
                            $('input[name="Client[name_new]"]').val(data.client.name);
                            $('input[name="Client[home_phone_new]"]').val(data.client.home_phone);
                            $('input[name="Client[alt_phone_new]"]').val(data.client.alt_phone);
                        }
                    }
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
    }

}

$(document).on('click', '#advertising-source-submit', function() {

    var advertising_source_id = $('select[name="AdvertisingSourceReport[advertising_source_id]"]').val();
    var phone = $('input[name="AdvertisingSourceReport[phone]"]').val();


    $.ajax({
        url: '/client/ajax-save-advertising-source-report',
        type: 'post',
        data: {
            'AdvertisingSourceReport[advertising_source_id]': advertising_source_id,
            'AdvertisingSourceReport[phone]': phone
        },
        success: function (response) {
            if (response.success == true) {
                $('#advertising-source-modal').find('.close').click();
            }else {
                for (var field in response.errors) {
                    var field_errors = response.errors[field];
                    $('#advertising-source-form').yiiActiveForm('updateAttribute', 'advertisingsourcereport-' + field, field_errors);
                }
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

function clearPhonesBlock() {

    searchClientByPhoneResponse = {};

    //$('input[name="Client[name_new]"]').val('');
    //$('input[name="Client[mobile_phone_new]"]').val('');
    //$('input[name="Client[home_phone_new]"]').val('');
    //$('input[name="Client[alt_phone_new]"]').val('');

    //$('input[name="Order[additional_phone_1_new]"]').val('');
    //$('input[name="Order[additional_phone_2_new]"]').val('');
    //$('input[name="Order[additional_phone_3_new]"]').val('');
}


//var order_phones_block =
//    '<div class="order-phones-block">' +
//        '<div class="order-phones-block-header">' +
//            '<button type="button" class="order-phones-block-close">×</button>' +
//            '<span class="order-phones-block-title">Перезапись телефонов</span>' +
//        '</div>' +
//        '<div class="order-phones-block-body"></div>' +
//    '</div>';



var order_map_html =
    '<div class="map-block">' +
    '<div class="map-block-header">' +
    '<button type="button" class="map-block-close">×</button>' +
    '<span class="map-block-title">Установка точки</span>' +
    '</div>' +
    '<div class="map-block-body">' +
    '<div class="map-control-block">' +
    '<input type="text" class="search-point form-control" placeholder="Начните ввод адреса..." />' +
    '<div class="search-result-block sw-select-block" style="display: none;"></div>' +
    '</div>' +
    '<div id="ya-map"></div>' +
    '</div>' +
    '</div>';


var map = null;
//var search_placemark = null;
var point_placemark = null;
var point_focusing_scale = 12; // масштаб фокусировки выбранной точки
//var save_button = null;

// скрытие/отображение точек карты
//var S1 = 12;
//var S2 = 15;

// открытие яндекс-карты с кучей функционала
function openMapWithPointFrom(search) {

    if(search == undefined) {
        search = '';
    }

    map = null;
    var create_base_point_button = null;
    point_placemark = null;

    var direction_id = $('#order-client-form *[name="Order[direction_id]"]').val();
    if(direction_id.length == 0 || direction_id == 0) {
        alert('Необходимо выбрать направление');
        return false;
    }

    if($('#order-create-modal .map-block').length > 0)  {
        $('#order-create-modal .map-block').remove();
    }
    //$('#order-create-modal .modal-body').append(order_map_html);
    $('#order-create-modal').append(order_map_html);
    $('#order-create-modal').find('.map-block-title').text('Установка точки "Откуда"');

    //var selected_yandex_point_id = $('input[name="Order[yandex_point_from]"]').val();
    var yandex_point_from = $('input[name="Order[yandex_point_from]"]').val();

    if(yandex_point_from != '') {
        var yandex_point_from_data = yandex_point_from.split('_');
        var selected_yandex_point_id = parseInt(yandex_point_from_data[0]);
        var selected_yandex_point_from_lat = yandex_point_from_data[1];
        var selected_yandex_point_from_long = yandex_point_from_data[2];
        var selected_yandex_point_from_name = yandex_point_from_data[3];

        //console.log('selected_yandex_point_id='+selected_yandex_point_id);
    }


    $.ajax({
        url: '/direction/ajax-get-direction-map-data?id=' + direction_id + '&from=1',
        type: 'post',
        data: {},
        success: function (response) {
            if (response.city == null) {
                alert('Город не определен');
                return false;
            }

            $('#order-create-modal .map-block').find('.search-point')
                .attr('city-long', response.city.center_long)
                .attr('city-lat', response.city.center_lat)
                .attr('city-name', response.city.name)
                .attr('city-id', response.city.id)
                .focus();

            //response.city.search_scale - Приближение карты при поиске
            //response.city.point_focusing_scale - Масштаб фокусировки точки
            //response.city.all_points_show_scale - Масшаб отображения всех точек
            point_focusing_scale = response.city.point_focusing_scale;


            ymaps.ready(function(){

                //console.log('создаем карту city.map_scale='+response.city.map_scale);

                map = new ymaps.Map("ya-map", {
                    center: [response.city.center_lat, response.city.center_long],
                    zoom: response.city.map_scale,
                    //type: "yandex#satellite",
                    //controls: []  // Карта будет создана без элементов управления.
                    controls: [
                        'zoomControl',
                        //'searchControl',
                        //'typeSelector',
                        //'routeEditor',  // построитель маршрута
                        'trafficControl' // пробки
                        //'fullscreenControl'
                    ]
                });

                //console.log('map.zoom=' + map.getZoom());

                map.events.add('boundschange', function (event) {
                    //console.log('oldZoom='+event.get('oldZoom') + ' newZoom=' + event.get('newZoom'));
                    // response.city.point_focusing_scale
                    showHidePlacemarks(map, event.get('newZoom'), response.city.all_points_show_scale)
                });

                //var searchControl = map.controls.get('searchControl');
                //searchControl.options.set('provider', 'yandex#search');

                // Создание кнопки создания разовой точки и добавление ее на карту.
                create_temp_point_button = new ymaps.control.Button({
                    data: {
                        // image: 'images/button.jpg',// иконка для кнопки
                        content: 'Создать разовую точку',// Текст на кнопке.
                        //title: '',// Текст всплывающей подсказки.
                    },
                    options: {
                        selectOnClick: false,
                        maxWidth: [30, 30, 30],
                        layout: ymaps.templateLayoutFactory.createClass('<button id="yamap-create-temp-point-but" class="btn btn-default">{{ data.content }}</button>'),
                    }
                });
                map.controls.add(create_temp_point_button, {
                    float: 'left',
                    floatIndex: 100,
                    position: {
                        top: '40px',
                        left: '10px'
                    }
                });
                create_temp_point_button.events.add('mousedown', function (event) {
                    var temp_but = document.getElementById('yamap-create-temp-point-but');// create_temp_point_button
                    var base_but = document.getElementById('yamap-create-base-point-but');// create_base_point_button

                    if (base_but.className == 'btn btn-primary active') {
                        base_but.classList.remove('active');
                        map.cursors.push('move');
                    }

                    if (temp_but.className == 'btn btn-default active') {
                        temp_but.classList.remove('active');
                        map.cursors.push('move');
                    } else {
                        temp_but.classList.add('active');
                        map.cursors.push('crosshair');
                    }
                });

                // Создание кнопки создания опорной точки и добавление ее на карту.
                create_base_point_button = new ymaps.control.Button({
                    data: {
                        // image: 'images/button.jpg',// иконка для кнопки
                        content: 'Создать опорную точку',// Текст на кнопке.
                        //title: '',// Текст всплывающей подсказки.
                    },
                    options: {
                        selectOnClick: false,
                        maxWidth: [30, 30, 30],
                        layout: ymaps.templateLayoutFactory.createClass('<button id="yamap-create-base-point-but" class="btn btn-primary">{{ data.content }}</button>'),
                    }
                });
                map.controls.add(create_base_point_button, {
                    float: 'right',
                    floatIndex: 100
                });
                create_base_point_button.events.add('mousedown', function (event) {
                    var temp_but = document.getElementById('yamap-create-temp-point-but');// create_temp_point_button
                    var base_but = document.getElementById('yamap-create-base-point-but');// create_base_point_button

                    if (temp_but.className == 'btn btn-default active') {
                        temp_but.classList.remove('active');
                        map.cursors.push('move');
                    }

                    if (base_but.className == 'btn btn-primary active') {
                        base_but.classList.remove('active');
                        map.cursors.push('move');
                    } else {
                        base_but.classList.add('active');
                        map.cursors.push('crosshair');
                    }
                });


                //console.log('yandex_points:'); console.log(response.yandex_points);


                // Множество существующих точек
                for (var key in response.yandex_points) { // только базовые точки в списке

                    var yandex_point = response.yandex_points[key];

                    var index = map.geoObjects.getLength();
                    //var placemark = createPlacemark(index, yandex_point.name, yandex_point.id, yandex_point.lat, yandex_point.long);
                    var create_placemark_params = {
                        index: index,
                        point_text: yandex_point.name,
                        point_description: yandex_point.description,
                        point_id: yandex_point.id,
                        point_lat: yandex_point.lat,
                        point_long: yandex_point.long,
                        //is_editing: true,
                        //create_new_point: true,
                        to_select: true,
                        can_change_params: false,
                        //critical_point: yandex_point.critical_point,
                        //alias: yandex_point.alias
                    };
                    var placemark = createPlacemark(create_placemark_params);


                    if (yandex_point.id == selected_yandex_point_id) {
                        // установка point_placemark


                        var select_point_placemark_params = {
                            index: index,
                            point_text: yandex_point.name,
                            // point_description: ' 9 ',
                            point_id: yandex_point.id,
                            //is_editing: true,
                            //create_new_point: true,
                            can_change_params: false,
                            //critical_point: yandex_point.critical_point,
                            //alias: yandex_point.alias,
                            draggable: false,
                            is_allowed_edit: false,
                            point_focusing_scale: point_focusing_scale
                        }
                        selectPointPlacemark(select_point_placemark_params);

                        //selectPointPlacemark(index, yandex_point.name, yandex_point.id, null, null, null, null, null, false);

                        var coordinates = placemark.geometry.getCoordinates();
                        map.setCenter(coordinates, response.city.map_scale, {duration: 500});
                    }
                }


                if(selected_yandex_point_id == 0) { // если выбрана временная точка

                    var index = map.geoObjects.getLength();

                    //var placemark = createPlacemark(index, selected_yandex_point_from_name, selected_yandex_point_id, selected_yandex_point_from_lat, selected_yandex_point_from_long)
                    var create_placemark_params = {
                        index: index,
                        point_text: selected_yandex_point_from_name,
                        point_description: '',
                        point_id: selected_yandex_point_id,
                        point_lat: selected_yandex_point_from_lat,
                        point_long: selected_yandex_point_from_long,
                        //is_editing: true,
                        //create_new_point: true,
                        to_select: false,
                        can_change_params: false
                        //critical_point: yandex_point.critical_point,
                        //alias: yandex_point.alias
                    };
                    var placemark = createPlacemark(create_placemark_params);


                    var select_point_placemark_params = {
                        index: index,
                        point_text: selected_yandex_point_from_name,
                        // point_description: ' 10 ',
                        point_id: selected_yandex_point_id,
                        //is_editing: true,
                        //create_new_point: true,
                        can_change_params: false,
                        //critical_point: yandex_point.critical_point,
                        //alias: yandex_point.alias,
                        draggable: false,
                        is_allowed_edit: false,
                        point_focusing_scale: point_focusing_scale
                    }
                    selectPointPlacemark(select_point_placemark_params);
                    //selectPointPlacemark(index, selected_yandex_point_from_name, selected_yandex_point_id, null, null, null, null, null, true);

                    var coordinates = placemark.geometry.getCoordinates();
                    map.setCenter(coordinates, response.city.map_scale, {duration: 500});
                }
                // после создания точек на карте обновим их видимость
                // response.city.point_focusing_scale
                showHidePlacemarks(map, map.getZoom(), response.city.all_points_show_scale);

                if(search.length > 0) {
                    $('#order-create-modal .search-point').val(search);

                    // эмулируем нажатие клавиши в поиске
                    var e = $.Event("keyup", { keyCode: 9 });
                    $('#order-create-modal .search-point').trigger(e);
                }

                // Создадим маркер в точке клика
                map.events.add('click', function (e) {

                    if ($('#yamap-create-base-point-but').hasClass('active') == true) { // базовая точка

                        var pointPlacemarkGeocoder = ymaps.geocode(e.get('coords'));
                        pointPlacemarkGeocoder.then(
                            function (res) {

                                //unselectOldPointPlacemark();

                                var text = res.geoObjects.get(0).properties._data.text;
                                text = text.replace('Россия, ', '');
                                text = text.replace('Республика Татарстан, ', '');

                                var city_name = $('#order-create-modal .search-point').attr('city-name');
                                text = text.replace(city_name + ', ', '');

                                var point_coords = e.get('coords');
                                var point_lat = point_coords[0];
                                var point_long = point_coords[1];
                                var index = map.geoObjects.getLength();

                                var create_placemark_params = {
                                    index: index,
                                    point_text: text,
                                    point_description: '',
                                    point_id: 0,
                                    point_lat: point_lat,
                                    point_long: point_long,
                                    is_editing: true,
                                    create_new_point: true,
                                    to_select: false,
                                    can_change_params: false,
                                    //critical_point: yandex_point.critical_point,
                                    //alias: yandex_point.alias,
                                    is_base_point: true
                                };
                                var placemark = createPlacemark(create_placemark_params);

                                var select_point_placemark_params = {
                                    index: index,
                                    point_text: text,
                                    // point_description: ' 11 ',
                                    point_id: 0,
                                    is_editing: true,
                                    create_new_point: true,
                                    can_change_params: false,
                                    //critical_point: yandex_point.critical_point,
                                    //alias: yandex_point.alias,
                                    draggable: true,
                                    is_allowed_edit: true,
                                    point_focusing_scale: point_focusing_scale
                                }
                                selectPointPlacemark(select_point_placemark_params);
                                //selectPointPlacemark(index, text, 0, true, true, true, null, null, null, true);

                                placemark.balloon.open();
                            },
                            function (err) {
                                // обработка ошибки
                                alert('ошибка запрос в яндекс');
                            }
                        );

                        var base_but = document.getElementById('yamap-create-base-point-but');
                        base_but.classList.remove('active');
                        map.cursors.push('move');

                        //$('#yamap-save-point').show();

                    }else if ($('#yamap-create-temp-point-but').hasClass('active') == true) {// временная точка

                        //alert('добавь функционал создания временной точки');

                        var pointPlacemarkGeocoder = ymaps.geocode(e.get('coords'));
                        pointPlacemarkGeocoder.then(
                            function (res) {

                                //unselectOldPointPlacemark();

                                var text = res.geoObjects.get(0).properties._data.text;
                                text = text.replace('Россия, ', '');
                                text = text.replace('Республика Татарстан, ', '');

                                var city_name = $('#order-create-modal .search-point').attr('city-name');
                                text = text.replace(city_name + ', ', '');

                                var point_coords = e.get('coords');
                                var point_lat = point_coords[0];
                                var point_long = point_coords[1];
                                var index = map.geoObjects.getLength();

                                //var placemark = createPlacemark(index, text, 0, point_lat, point_long, true, false, false);
                                var create_placemark_params = {
                                    index: index,
                                    point_text: text,
                                    point_description: '',
                                    point_id: 0,
                                    point_lat: point_lat,
                                    point_long: point_long,
                                    is_editing: true,
                                    create_new_point: false,
                                    to_select: false,
                                    can_change_params: false,
                                    //critical_point: yandex_point.critical_point,
                                    //alias: yandex_point.alias
                                    is_temp_point: true
                                };
                                var placemark = createPlacemark(create_placemark_params);

                                var select_point_placemark_params = {
                                    index: index,
                                    point_text: text,
                                    //point_description: ' 12 ',
                                    point_id: 0,
                                    is_editing: true,
                                    create_new_point: false,
                                    can_change_params: false,
                                    //critical_point: yandex_point.critical_point,
                                    //alias: yandex_point.alias,
                                    draggable: true,
                                    is_allowed_edit: true,
                                    point_focusing_scale: point_focusing_scale
                                }
                                selectPointPlacemark(select_point_placemark_params);
                                //selectPointPlacemark(index, text, 0, true, false, true, null, null, null, null, true);
                                placemark.balloon.open();
                            },
                            function (err) {
                                // обработка ошибки
                                alert('ошибка запрос в яндекс');
                            }
                        );

                        var temp_but = document.getElementById('yamap-create-temp-point-but');
                        temp_but.classList.remove('active');
                        map.cursors.push('move');
                    }

                });

            });

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


$(document).ready(function()
{
    // перехват автопроверок полей формы
    //$(document).on('ajaxBeforeSend', '#order-client-form', function(event, jqXHR, textStatus) {
    //    // очищаем ошибки формы
    //    $('#order-client-form').yiiActiveForm('updateMessages', {
    //        //'bonuscardssettings-work_with_service_products': ['Ошибка']
    //    }, true);
    //});
    //$(document).on('ajaxComplete', '#order-client-form', function(event, jqXHR, textStatus) {
    //    var data = jqXHR.responseJSON;
    //    if(data.success == false) {
    //        for(var field in data.order_errors) {
    //            var field_errors = data.order_errors[field];
    //            $('#order-client-form').yiiActiveForm('updateAttribute', 'order-' + field, field_errors);
    //        }
    //
    //        for(var field in data.client_errors) {
    //            var field_errors = data.client_errors[field];
    //            $('#order-client-form').yiiActiveForm('updateAttribute', 'client-' + field, field_errors);
    //        }
    //    }
    //});


    // во время обновления даты сбрасываются (обнуляются) поля: Направление (и следом должен обнулиться Рейс)
    $(document).on('change', '#date', function ()
    {

        //var start_date = $('input[name="Order[date]"]').attr('start-date');
        //var current_date = $('input[name="Order[date]"]').val();
        //if (start_date != current_date) {
        //    $('#direction').val('').change();
        //}

        var date = $.trim($(this).val());

        var radio_group_2_1 = $('#radio_group_2_1').attr('text');
        var radio_group_2_2 = $('#radio_group_2_2').attr('text');
        if (date.length > 0) { // 30.05.2017
            var day = parseInt(date.substr(0, 2));
            var month = parseInt(date.substr(3, 2)) - 1;
            var year = parseInt(date.substr(6, 4));

            var yesterdayDate = new Date(year, month, day - 1, 3);
            var day = yesterdayDate.getUTCDate();
            if (day < 10) {
                day = '0' + day;
            }
            var month = yesterdayDate.getUTCMonth() + 1;
            if (month < 10) {
                month = '0' + month;
            }
            var year = yesterdayDate.getUTCFullYear();
            var day_before_date = day + '.' + month + '.' + year;

            var now = new Date();
            var today_year = now.getFullYear();
            var today_month = now.getMonth() + 1;
            if (today_month < 10) {
                today_month = '0' + today_month;
            }
            var today_day = now.getDate();
            if (today_day < 10) {
                today_day = '0' + today_day;
            }
            var today_date = today_day + '.' + today_month + '.' + today_year;

            var yesterday_milliseconds = (now.getTime() - 86400000);
            var yesterday = new Date(yesterday_milliseconds);
            var yesterday_year = yesterday.getFullYear();
            var yesterday_month = yesterday.getMonth() + 1;
            if (yesterday_month < 10) {
                yesterday_month = '0' + yesterday_month;
            }
            var yesterday_day = yesterday.getDate();
            if (yesterday_day < 10) {
                yesterday_day = '0' + yesterday_day;
            }
            var yesterday_date = yesterday_day + '.' + yesterday_month + '.' + yesterday_year;

            var tomorrow_milliseconds = (now.getTime() + 86400000);
            var tomorrow = new Date(tomorrow_milliseconds);
            var tomorrow_year = tomorrow.getFullYear();
            var tomorrow_month = tomorrow.getMonth() + 1;
            if (tomorrow_month < 10) {
                tomorrow_month = '0' + tomorrow_month;
            }
            var tomorrow_day = tomorrow.getDate();
            if (tomorrow_day < 10) {
                tomorrow_day = '0' + tomorrow_day;
            }
            var tomorrow_date = tomorrow_day + '.' + tomorrow_month + '.' + tomorrow_year;

            //alert('date='+date+' day_before_date='+day_before_date+' today_date='+today_date+' yesterday_date='+yesterday_date+' tomorrow_date='+tomorrow_date);

            if (date == today_date)
                date = 'сегодня';
            else if (date == yesterday_date) {
                date = 'вчера';
            }
            if (date == tomorrow_date) {
                date = 'завтра';
            }

            if (day_before_date == today_date)
                day_before_date = 'Сегодня';
            else if (date == yesterday_date) {
                day_before_date = 'Вчера';
            }
            if (date == tomorrow_date) {
                day_before_date = 'Завтра';
            }

            radio_group_2_1 = radio_group_2_1.replace('{ДАТА1}', date);
            radio_group_2_2 = radio_group_2_2.replace('{ДАТА2}', day_before_date);
        }
        $('#radio_group_2_1').text(radio_group_2_1);
        $('#radio_group_2_2').text(radio_group_2_2);

        $('#direction').removeAttr('disabled');

        //var options = '';
        //options += '<option value="">---</option>';
        //$('#trip').html(options).attr('disabled', true);
        //$('#trip').val('');
        // обновляем рейсы
        updateTrips();

        resetOrderFormRadiobuttons();
    });




    // При выборе направления в форме обновляем список рейсов,
    // и обновляем точки "Откуда" и точки "Куда"
    $(document).on('change', '#order-client-form #direction', function ()
    {
        resetOrderFormRadiobuttons();

        var direction_id = $(this).val();

        // сброс полей "Откуда" и "Куда"
        $('input[name="Order[yandex_point_from]"]').parents('.sw-element').find('.sw-delete').click();
        $('input[name="Order[yandex_point_to]"]').parents('.sw-element').find('.sw-delete').click();

        // обновление списка рейсов
        if (direction_id > 0) {

            // обновляем рейсы
            updateTrips();

            // обновление точке откуда/куда - эмулируем нажатие клавиши в поле телефон, чтобы запустить обновление точке рейса
            var e = $.Event("keyup", { keyCode: 9 });
            $('#client-mobile_phone').trigger(e);

            //var direction_name = $('*[name="Order[direction_id]"]').find('option[selected]').text();

            //var direction_id = $('#order-client-form #direction').val();
            //var direction_name = '';
            //if(direction_id == 1) {
            //    direction_name = 'АК';
            //}else {
            //    direction_name = 'КА';
            //}
            //$('#radio_group_1_1').find('.npr').text(direction_name);
            //$('#radio_group_1_2').find('.npr').text(direction_name);


        } else {
            var options = '';
            options += '<option value="">---</option>';
            $('#trip').html(options).attr('disabled', true);

            $('input[name="Client[mobile_phone]"]').attr('disabled', true);
            $('input[name="Client[home_phone]"]').attr('disabled', true);
            $('input[name="Client[alt_phone]"]').attr('disabled', true);
            $('input[name="Client[name]"]').attr('disabled', true);
        }
    });


    // при смене рейса сбрасываются "нижние" поля формы в "нулевое" состояние
    $(document).on('change', '#trip', function()
    {
        //console.log('change trip');
        resetOrderFormRadiobuttons();

        var date = $('#date').val();
        var time_confirm = $('input[name="Order[time_confirm]"]').val();

        //var street_id_from = $('input[name="Order[street_id_from]"]').val();
        //var point_id_from = $('input[name="Order[point_id_from]"]').val();
        var yandex_point_from = $('input[name="Order[yandex_point_from]"]').val();
        if(yandex_point_from != undefined && yandex_point_from.length > 0) {
            var yandex_point_from_data = yandex_point_from.split('_');
            var yandex_point_from_id = yandex_point_from_data[0];
            var yandex_point_from_lat = yandex_point_from_data[1];
            var yandex_point_from_long = yandex_point_from_data[2];
            var yandex_point_from_name = yandex_point_from_data[3];
        }else {
            var yandex_point_from_id = 0;
            var yandex_point_from_lat = 0;
            var yandex_point_from_long = 0;
            var yandex_point_from_name = '';
        }

        var trip_id = $('#trip').val();

        $.ajax({
            url: '/order/ajax-get-radio-group1',
            type: 'post',
            data: {
                date: date,
                time_confirm: time_confirm,
                yandex_point_from_name: yandex_point_from_name,
                trip_id: trip_id
            },
            success: function (response) {
                $('#radio_group_1_1').attr('text', response.radio_group_1[1]).html(response.radio_group_1[1]);
                $('#radio_group_1_2').attr('text', response.radio_group_1[2]).html(response.radio_group_1[2]);
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

        updatePrice();
    });


    // при клике на заблокированные поля открываем окно редактирования телефонов
    // - не работает если поле заблокировано!
    //$(document).on('click', '#client-mobile_phone', function() {
    //    alert('qwe');
    //    //alert('disabled=' + $(this).attr('disabled'));
    //});


    $(document).on('change', '*[name="Order[trip_transport_id]"]', function() {
        var val = $(this).val();
        $('*[name="Order[trip_transport_id]"]').val(val);
    });

    // нажатие на кнопку "Подтвердить"
    var allow_confirm_button = true;
    $(document).on('click', '#confirm-button', function () {

        if (void 0 !== $(this).attr('disabled')) {
            return false;
        }

        var formData = getFormData();
        if(formData.Order.time_confirm == '') {
            alert('Установите время подтверждения');
            return false;
        }

        var order_id = $('#order-client-form').attr('order-id');

        if(allow_confirm_button == true) {
            $.ajax({
                url: '/order/ajax-check-time-getting-into-car?order_id=' + order_id,
                type: 'post',
                data: formData,
                beforeSend: function () {
                    allow_confirm_button = false;
                },
                success: function (data) {

                    allow_confirm_button = true;
                    if (data.success == true) {

                        //if (data.order_id == 0) {
                        $('input[name="Order[confirm_click_time]"]').val(data.confirm_click_time);
                        $('input[name="Order[confirm_clicker_id]"]').val(data.confirm_clicker_id);
                        //}

                        $('input[name="Order[time_confirm]"]').attr('disabled', true);
                        $('#confirm-button').attr('disabled', true).val('Назначено').removeClass('btn-default').addClass('btn-success');

                        $('#order-radio_confirm_now').addClass('disabled');
                        $('*[name="Order[radio_confirm_now]"]').attr('disabled', true);

                        $('#order-radio_group_1').removeClass('disabled');
                        $('*[name="Order[radio_group_1]"]').removeAttr('disabled');

                        var radio_group_1_1_value = $('#radio_group_1_1').attr('text');
                        var date = formData.Order.time_confirm;
                        //var direction_id = $('#order-client-form #direction').val();
                        //var direction_name = '';
                        //if(direction_id == 1) {
                        //    direction_name = 'АК';
                        //}else {
                        //    direction_name = 'КА';
                        //}
                        radio_group_1_1_value = radio_group_1_1_value.replace(/{ВРПТ}/g, date);

                        $('#radio_group_1_1').html(radio_group_1_1_value);
                        //$('#radio_group_1_1').find('.npr').text(direction_name);

                        var radio_group_1_2_value = $('#radio_group_1_2').attr('text');
                        radio_group_1_2_value = radio_group_1_2_value.replace(/{ВРПТ}/g, date);

                        //radio_group_1_2_value = radio_group_1_2_value.replace(/{НПР}/g, direction_name);
                        var yandex_point_value = $('.sw-element[attribute-name="Order[yandex_point_from]"]').find('.sw-value').text();

                        radio_group_1_2_value = radio_group_1_2_value.replace(/{ТЧК_ОТКУДА}/g, '«' + yandex_point_value + '»');
                        $('#radio_group_1_2').html(radio_group_1_2_value);
                        //$('#radio_group_1_2').find('.npr').text(direction_name);

                        $('select[name="Order[trip_transport_id]"]').removeAttr('disabled');

                        if ($('#trip-orders-page').length > 0) { // обновление страницы "Информация о рейсе"
                            // updateTripOrdersPage();
                        }


                    } else {
                        var errors = '';
                        for (var field in data.client_errors) {
                            var field_errors = data.client_errors[field];
                            for (var key in field_errors) {
                                errors += field_errors[key] + ' ';
                            }
                        }
                        for (var field in data.order_errors) {
                            var field_errors = data.order_errors[field];
                            for (var key in field_errors) {
                                errors += field_errors[key] + ' ';
                            }
                        }

                        alert(errors);
                    }
                },
                error: function (data, textStatus, jqXHR) {
                    allow_confirm_button = true;
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

        }else {
            alert('хватить жать на кнопку - запрос обрабатывается...');
            LogDispatcherAccounting('кнопка «Подтвердить» заказа');
        }
    });


    // нажатие на кнопку "Записать"
    var allow_send_order = true;
    $(document).on('click', '#writedown-button', function()
    {
        if ($(this).hasClass('disabled')) {
            return false;
        }

        if(allow_send_order == true) {

            var formData = getFormData();
            //console.log('formData:'); console.log(formData);


            // здесь /order/ajax-update-order?id=89842  и  /order/ajax-create-order?id=89842
            $.ajax({
                url: $('#order-client-form').attr('action'),
                type: 'post',
                data: formData,
                beforeSend: function () {
                    allow_send_order = false;
                },
                success: function (data) {

                    allow_send_order = true;
                    if (data.success == true) {

                        clearPhonesBlock();
                        $('.order-phones-block').remove();

                        //updateCallModal();
                        $('#order-create-modal').modal('hide');

                        if ($('#directions-trips-block').length > 0) { // обновление рейсов на главной странице
                            // updateDirectionsTripBlock(); // directions-trips-block
                        }

                        if ($('#trip-orders-page').length > 0) { // обновление страницы "Информация о рейсе"
                            //updateTripOrdersPage();
                        }

                        // открытие формы на обратное направление
                        // опции:
                        //  - Когда поедете обратно
                        //  - +1 адрес к заказу
                        if (void 0 !== data.reverse_form_html && data.reverse_form_html.length > 0) { // возникла ошибка с data.reverse_form_html is underfined - но сложно сгенерировать ошибку

                            setTimeout(function() {

                                alert('Успешно сохранено');

                                $('#order-create-modal').find('.modal-body').html(data.reverse_form_html);
                                $('#order-create-modal').modal('show');
                                $('#order-client-form').attr('order-temp-identifier', data.reverse_order_temp_identifier)
                                $('#client-mobile_phone').focus();

                                $('#order-create-modal').append(data.reverse_phones_block);

                            }, 500);

                        } else {
                            alert('Успешно сохранено');
                        }

                        // если открыто окно со списком заявок, то удаляем заявку соответствующую заказа
                        // (и возможно закрываем окно списка заявок)
                        if($('#clientext-modal').length > 0) {
                            setTimeout(function() {
                                $('#clientext-list .clientext[order-id="' + formData.Order.id + '"]').remove();
                                if($('#clientext-list .clientext').length == 0) {
                                    $('#clientext-modal .close').click();
                                }
                            }, 500);
                        }

                    } else {
                        var errors = '';
                        for (var field in data.client_errors) {
                            var field_errors = data.client_errors[field];
                            for (var key in field_errors) {
                                errors += field_errors[key] + ' ';
                            }
                        }
                        for (var field in data.order_errors) {
                            var field_errors = data.order_errors[field];
                            for (var key in field_errors) {
                                errors += field_errors[key] + ' ';
                            }
                        }

                        alert(errors);
                    }
                },
                error: function (data, textStatus, jqXHR) {

                    allow_send_order = true;
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
                        resetOrderFormRadiobuttons();

                    }else {
                        handlingAjaxError(data, textStatus, jqXHR);
                    }
                }
            });

        }else {
            alert('хватить жать на кнопку - запрос обрабатывается...');
            LogDispatcherAccounting('кнопка «Записать» заказа');
        }
    });


    // чекбокс снятия/установки блокировки для списка информаторских
    //$(document).on('change', '#informer-office-disable', function () {
    //    if ($(this).is(':checked')) {
    //        $('#order-informer_office_id').removeAttr('disabled');
    //        updatePrice();
    //    } else {
    //        $('#order-informer_office_id').val('');
    //        $('#order-informer_office_id').attr('disabled', true);
    //        updatePrice();
    //    }
    //});


    // когда выбирается Источник у которого cashless-payment=1 (чекбокс "Безналичная оплата" установлен)
    // то заказу ставиться цена 0 рублей (и далее в накоплении лояльности этот заказ не будет учавствовать)
    $(document).on('change', 'select[name="Order[informer_office_id]"]', function() {
        //updatePrice();

        var informer_office_id = $(this).val();

        // когда меняется источник, то в форме меняются поля: чекбокс. фикс-прайс, цена. А затем происходит пересчет цены в форме
        $.ajax({
            url: '/order/ajax-get-informer-office-do-tariff?informer_office_id=' + informer_office_id,
            type: 'post',
            data: {},
            //contentType: false,
            //cache: false,
            //processData: false,
            success: function (response) {

                //if(response.do_tariff != null) {
                //    if (response.do_tariff.use_fix_price == 0) {
                //        //$('*[name="Order[use_fix_price]"]').prop('checked', false);
                //        if ($('*[name="Order[use_fix_price]"]').prop('checked') == true) {
                //            $('*[name="Order[use_fix_price]"]').click();
                //        }
                //    } else {
                //        //$('*[name="Order[use_fix_price]"]').prop('checked', true);
                //        if ($('*[name="Order[use_fix_price]"]').prop('checked') == false) {
                //            $('*[name="Order[use_fix_price]"]').click();
                //        }
                //    }
                //}
                //
                //updatePrice();

                setDoTariffParams(response.do_tariff, 'order_form');
                //updatePrice();

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
    });

    // чекбокс "фикс." - снятие/установка блокировки для фиксированной цены
    $(document).on('change', 'input[name="Order[use_fix_price]"]', function () {
        if ($(this).is(':checked') == true) {
            $('input[name="order-fix_price-disp"]').removeAttr('disabled');
        } else {
            $('input[name="order-fix_price-disp"]').val('');
            $('input[name="order-fix_price-disp"]').attr('disabled', true);
        }

        updatePrice();
    });


    $(document).on('keyup', 'input[name="order-fix_price-disp"]', function () {
        $('#order-client-form #price').text($(this).val());
        $('#order-client-form .point-from-str').text('-');
        $('#order-client-form .point-to-str').text('-');
    });


    // чекбокс снятия/установки блокировки для полей: мест и т.п.
    $(document).on('change', '#places-count-disable', function () {

        if ($(this).is(':checked')) {
            $('#order-places_count').attr('disabled', true);
            $('#order-student_count').attr('disabled', true);
            $('#order-child_count').attr('disabled', true);
            $('#order-bag_count').attr('disabled', true);
            $('#order-suitcase_count').attr('disabled', true);
            $('#order-oversized_count').attr('disabled', true);
            $('#order-places_count').val('');
            $('#order-student_count').val('');
            $('#order-child_count').val('');
            $('#order-bag_count').val('');
            $('#order-suitcase_count').val('');
            $('#order-oversized_count').val('');

        } else {
            $('#order-places_count').removeAttr('disabled');
            $('#order-student_count').removeAttr('disabled');
            $('#order-child_count').removeAttr('disabled');
            $('#order-bag_count').removeAttr('disabled');
            $('#order-suitcase_count').removeAttr('disabled');
            $('#order-oversized_count').removeAttr('disabled');
        }

        updatePassengerRefer();
    });




    //$(document).on('keyup', 'input[name="Client[mobile_phone]"]', function() {
    //    resetOrderFormRadiobuttons();
    //});
    $(document).on('keyup', 'input[name="Client[name]"]', function() {
        resetOrderFormRadiobuttons();
    });
    //$(document).on('keyup', 'input[name="Client[home_phone]"]', function() {
    //    resetOrderFormRadiobuttons();
    //});
    //$(document).on('keyup', 'input[name="Client[alt_phone]"]', function() {
    //    resetOrderFormRadiobuttons();
    //});


    // поиск по номеру телефона существующего клиента 1
    $(document).on('keyup', '#client-mobile_phone', function ()
    {
        if(client_mobile_phone_is_active == true) {
            return false;
        }
        var mobile_phone = $(this).val();
        mobile_phone = mobile_phone.replace(/\*/g,'');
        if (mobile_phone.length == 15 && mobile_phone[mobile_phone.length - 1] != '_') {

            client_mobile_phone_is_active = true;
            // showCallModal('', mobile_phone);
            searchClientByPhone(mobile_phone, 'order_form', 'mobile_phone');
            resetOrderFormRadiobuttons();

            setTimeout(function() {
                //console.log('set false client_mobile_phone_is_active');
                client_mobile_phone_is_active = false;
            }, 100);
        }
    });

    // поиск в копии телефона "Моб. основной" существующего клиента 1
    var client_mobile_phone_is_active = false;
    $(document).on('keyup', '#client-mobile_phone_new', function ()
    {
    //    var mobile_phone = $(this).val();
    //    mobile_phone = mobile_phone.replace(/\*/g,'');
    //    if (mobile_phone.length == 15 && mobile_phone[mobile_phone.length - 1] != '_') {
    //        showCallModal('', mobile_phone);
    //    }

        if(client_mobile_phone_is_active == true) {
            return false;
        }
        var mobile_phone = $(this).val();
        mobile_phone = mobile_phone.replace(/\*/g,'');
        if (mobile_phone.length == 15 && mobile_phone[mobile_phone.length - 1] != '_') {

            client_mobile_phone_is_active = true;
            //showCallModal('', mobile_phone);
            searchClientByPhone(mobile_phone, 'phones_form', 'mobile_phone');
            //resetOrderFormRadiobuttons();

            setTimeout(function() {
                //console.log('set false client_mobile_phone_is_active');
                client_mobile_phone_is_active = false;
            }, 100);
        }
    });


    // client-home_phone_new
    //$(document).on('keyup', '#client-home_phone_new', function ()
    //{
    //    var mobile_phone = $(this).val();
    //    mobile_phone = mobile_phone.replace(/\*/g,'');
    //
    //    if (mobile_phone.length == 15 && mobile_phone[mobile_phone.length - 1] != '_') {
    //        searchClientByPhone(mobile_phone, 'phones_form', 'home_phone');
    //    }
    //});
    $(document).on('keyup', '#client-home_phone', function ()
    {
        var mobile_phone = $(this).val();
        mobile_phone = mobile_phone.replace(/\*/g,'');
        if (mobile_phone.length == 15 && mobile_phone[mobile_phone.length - 1] != '_') {
            searchClientByPhone(mobile_phone, 'order_form', 'home_phone');

            resetOrderFormRadiobuttons();
        }
    });

    //$(document).on('keyup', '#client-alt_phone_new', function ()
    //{
    //    var mobile_phone = $(this).val();
    //    mobile_phone = mobile_phone.replace(/\*/g,'');
    //    if (mobile_phone.length == 15 && mobile_phone[mobile_phone.length - 1] != '_') {
    //        searchClientByPhone(mobile_phone, 'phones_form', 'alt_phone');
    //    }
    //});
    $(document).on('keyup', '#client-alt_phone', function ()
    {
        var mobile_phone = $(this).val();
        mobile_phone = mobile_phone.replace(/\*/g,'');
        if (mobile_phone.length == 15 && mobile_phone[mobile_phone.length - 1] != '_') {
            var mobile_phone = $(this).val();
            searchClientByPhone(mobile_phone, 'order_form', 'alt_phone');

            resetOrderFormRadiobuttons();
        }
    });

    //$(document).on('keyup', '#order-additional_phone_1_new', function ()
    //{
    //    var mobile_phone = $(this).val();
    //    mobile_phone = mobile_phone.replace(/\*/g,'');
    //    if (mobile_phone.length == 15 && mobile_phone[mobile_phone.length - 1] != '_') {
    //        searchClientByPhone(mobile_phone, 'phones_form', 'additional_phone_1');
    //    }
    //});
    $(document).on('keyup', '#order-additional_phone_1', function ()
    {
        var mobile_phone = $(this).val();
        mobile_phone = mobile_phone.replace(/\*/g,'');
        if (mobile_phone.length == 15 && mobile_phone[mobile_phone.length - 1] != '_') {
            searchClientByPhone(mobile_phone, 'order_form', 'additional_phone_1');
        }
    });

    //$(document).on('keyup', '#order-additional_phone_2_new', function ()
    //{
    //    var mobile_phone = $(this).val();
    //    mobile_phone = mobile_phone.replace(/\*/g,'');
    //    if (mobile_phone.length == 15 && mobile_phone[mobile_phone.length - 1] != '_') {
    //        searchClientByPhone(mobile_phone, 'phones_form', 'additional_phone_2');
    //    }
    //});
    $(document).on('keyup', '#order-additional_phone_2', function ()
    {
        var mobile_phone = $(this).val();
        mobile_phone = mobile_phone.replace(/\*/g,'');
        if (mobile_phone.length == 15 && mobile_phone[mobile_phone.length - 1] != '_') {
            searchClientByPhone(mobile_phone, 'order_form', 'additional_phone_2');
        }
    });

    //$(document).on('keyup', '#order-additional_phone_1_new', function ()
    //{
    //    var mobile_phone = $(this).val();
    //    mobile_phone = mobile_phone.replace(/\*/g,'');
    //    if (mobile_phone.length == 15 && mobile_phone[mobile_phone.length - 1] != '_') {
    //        searchClientByPhone(mobile_phone, 'phones_form', 'additional_phone_3');
    //    }
    //});
    $(document).on('keyup', '#order-additional_phone_3', function ()
    {
        var mobile_phone = $(this).val();
        mobile_phone = mobile_phone.replace(/\*/g,'');
        if (mobile_phone.length == 15 && mobile_phone[mobile_phone.length - 1] != '_') {
            searchClientByPhone(mobile_phone, 'order_form', 'additional_phone_3');
        }
    });


    $('#client-last-orders-modal').on('hidden.bs.modal', function(e) {
        $('body').css('overflow', 'hidden');
    });
    $('#order-create-modal').on('hidden.bs.modal', function(e) {
        $('body').css('overflow', 'auto');
    });

    // логируется нажатие на кнопку "Проверено"
    $(document).on('click', '#ischeckedbut-client-last-orders', function() {
        // AjaxSetCheckedClientLastOrders

        var order_id = $('#order-client-form').attr('order-id');
        if(order_id == undefined) {
            order_id = 0;
        }
        var order_temp_identifier = $('#order-client-form').attr('order-temp-identifier');
        if(order_temp_identifier == undefined) {
            order_temp_identifier = '';
        }

        $.ajax({
            url: '/order/ajax-set-checked-client-last-orders?order_temp_identifier=' + order_temp_identifier + '&order_id=' + order_id,
            type: 'post',
            data: {},
            contentType: false,
            cache: false,
            processData: false,
            success: function () {},
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
    });



    // изменение значения поля "Откуда" - Яндекс -точки
    $(document).on('change', 'input[name="Order[yandex_point_from]"]', function ()
    {
        var yandex_point_from = $(this).val();
        var yandex_point_from_data = yandex_point_from.split('_');
        var yandex_point_from_id = yandex_point_from_data[0];
        var yandex_point_from_lat = yandex_point_from_data[1];
        var yandex_point_from_long = yandex_point_from_data[2];
        var yandex_point_from_name = yandex_point_from_data[3];


        //var direction_name = $('#direction').find('option[selected]').text();
        var direction_id = $('#order-client-form #direction').val();
        var direction_name = '';
        if(direction_id == 1) {
            direction_name = 'АК';
        }else {
            direction_name = 'КА';
        }
        var yandex_point_from_value = $('.sw-element[attribute-name="Order[yandex_point_from]"]').find('.sw-value').text();

        if (yandex_point_from_id > 0) {
            $.ajax({
                url: '/yandex-point/ajax-get-yandex-point?id=' + yandex_point_from_id,
                type: 'post',
                data: {},
                success: function (yandex_point) {
                    if (yandex_point.critical_point == 1) {
                        $('input[name="Order[time_air_train_arrival]"]').removeAttr('disabled');
                    } else {
                        $('input[name="Order[time_air_train_arrival]"]').attr('disabled', true);
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
        } else {
            $('input[name="Order[time_air_train_arrival]"]').attr('disabled', true);
        }

        resetOrderFormRadiobuttons();


        //var radio_group_1_1_value = $('#radio_group_1_1').attr('text');
        // <b>{НПР}:</b> подъедет машина {ТС}, будьте собраны и готовы в {ВРПТ}, без звонка не выходите
        //radio_group_1_1_value = radio_group_1_1_value.replace(/{НПР}/g, direction_name);
        //$('#radio_group_1_1').html(radio_group_1_1_value);

        //var radio_group_1_2_value = $('#radio_group_1_2').attr('text');
        //radio_group_1_2_value = radio_group_1_2_value.replace(/{ТЧК_ОТКУДА}/g, '«' + yandex_point_from_value + '»');
        //$('#radio_group_1_2').html(radio_group_1_2_value);

        $('#radio_group_1_1').find('.vrpt').text('{ВРПТ}');
        $('#radio_group_1_2').find('.vrpt').text('{ВРПТ}');

        updatePrice();

        return false;
    });

    // изменение значения поля "Куда" - Яндекс - точки
    $(document).on('change', 'input[name="Order[yandex_point_to]"]', function()
    {
        //var yandex_point_to_id = $(this).val();
        var yandex_point_to = $(this).val();
        var yandex_point_to_data = yandex_point_to.split('_');
        var yandex_point_to_id = yandex_point_to_data[0];
        var yandex_point_to_lat = yandex_point_to_data[1];
        var yandex_point_to_long = yandex_point_to_data[2];
        var yandex_point_to_name = yandex_point_to_data[3];

        if (yandex_point_to_id > 0) {
            $.ajax({
                url: '/yandex-point/ajax-get-yandex-point?id=' + yandex_point_to_id,
                type: 'post',
                data: {},
                success: function (yandex_point) {
                    if (yandex_point.critical_point == 1) {
                        $('input[name="Order[time_air_train_departure]"]').removeAttr('disabled');
                    } else {
                        $('input[name="Order[time_air_train_departure]"]').attr('disabled', true);
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
        } else {
            $('input[name="Order[time_air_train_departure]"]').attr('disabled', true);
        }

        updatePrice();

        return false;
    });



    // нажатие на "выбрать на карте" для яндекс-точки "откуда"
    $(document).on('click', '#select-yandex-point-from', function() {

        openMapWithPointFrom();

        return false;
    });



    $(document).on('click', '.map-block-close', function() {

        $('#order-create-modal .map-block').remove();
        return false;
    });

    $(document).on('click', '.map-control-block', function() {
        $('#order-create-modal .search-point').next('.search-result-block').html('').hide();
    });








    // чекбокс "Без места"
    $(document).on('change', 'input[name="Order[is_not_places]"]', function () {
        updatePrice();
    });
    // Мест
    $(document).on('keyup', 'input[name="Order[places_count]"]', function () {
        updatePrice();
        updatePassengerRefer();
    });
    $(document).on('blur', 'input[name="Order[places_count]"]', function () {
        var mobile_phone = $('#client-mobile_phone').val();
        mobile_phone = mobile_phone.replace(/\*/g,'');
        if (mobile_phone.length == 15 && mobile_phone[mobile_phone.length - 1] != '_') {
            showInnerCallModal('', mobile_phone);
        }
    });
    // Студ.
    $(document).on('keyup', 'input[name="Order[student_count]"]', function () {
        updatePrice();
    });
    // Дет.
    $(document).on('keyup', 'input[name="Order[child_count]"]', function () {
        updatePrice();
    });
    //  Сумки
    $(document).on('keyup', 'input[name="Order[bag_count]"]', function () {
        updatePrice();
    });
    //  Чемод.
    $(document).on('keyup', 'input[name="Order[suitcase_count]"]', function () {
        updatePrice();
    });
    // Негабариты
    $(document).on('keyup', 'input[name="Order[oversized_count]"]', function () {
        updatePrice();
    });

    // Фикс. цена
    $(document).on('keyup', '#order-price-disp', function () {
        var price = $(this).val();
        $('#order-client-form #price').text(price);
    });


    // выбор radio-кнопки "Подтвердить сейчас" / "Не подтверждать" - проверка всех полей формы выше переключателя и их блокировка
    $(document).on('change', 'input[name="Order[radio_confirm_now]"]', function(e)
    {
        if ($(this).hasClass('disabled')) {
            return false;
        }

        var formData = getFormData();
        //console.log(formData);

        $.ajax({
            url: '/order/ajax-check-form-fields',
            method:"POST",
            type: "POST",
            cache: false,
            dataType : "json",
            data: formData,
            //contentType: false,
            //contentType: 'application/json; charset=UTF-8',
            //cache: false,
            //processData: false,
            success: function (data) {
                if (data.success == true) {

                    if (formData.Order.radio_confirm_now == 1) {
                        $('input[name="Order[time_confirm]"]').removeAttr('disabled');
                        $('input[name="Order[time_confirm_auto]"]').val(data.time_confirm_auto);
                        //$('input[name="Order[time_confirm]"]').val(data.time_confirm_auto_min_sec);
                        $('#confirm-button').removeAttr('disabled');

                        $('#order-radio_group_2').addClass('disabled');
                        $('input[name="Order[radio_group_2]"]').attr('disabled', true);


                    } else if (formData.Order.radio_confirm_now == 2) {

                        $('input[name="Order[time_confirm]"]').attr('disabled', true);
                        $('input[name="Order[time_confirm_auto]"]').val(data.time_confirm_auto);
                        //$('input[name="Order[time_confirm]"]').val(data.time_confirm_auto_min_sec);
                        //$('input[name="Order[time_confirm]"]').val('');
                        $('#confirm-button').attr('disabled', true);

                        $('#order-radio_group_2').removeClass('disabled');
                        $('input[name="Order[radio_group_2]"]').removeAttr('disabled');

                    } else {
                        alert('ошибка после=' + radio_confirm_now);
                    }


                    updatePrice();

                } else {
                    var errors = '';
                    for (var field in data.client_errors) {
                        var field_errors = data.client_errors[field];
                        for (var key in field_errors) {
                            errors += field_errors[key] + ' ';
                        }
                    }
                    for (var field in data.order_errors) {
                        var field_errors = data.order_errors[field];
                        for (var key in field_errors) {
                            errors += field_errors[key] + ' ';
                        }
                    }

                    alert(errors);

                    resetOrderFormRadiobuttons();
                }
            },
            error: function (data, textStatus, jqXHR) {
                if (textStatus == 'error') {
                    if (void 0 !== data.responseJSON) {
                        if (data.responseJSON.message.length > 0) {
                            alert(data.responseJSON.message);
                        }
                    } else {
                        if (data.responseText.length > 0) {
                            alert(data.responseText);
                        }
                    }
                }

                resetOrderFormRadiobuttons();
            }
        });

        if (formData.Order.radio_confirm_now == 1) {
            $('input[name="Order[time_confirm]"]').removeAttr('disabled');
            $('#confirm-button').removeAttr('disabled');

            $('#order-radio_group_2').addClass('disabled');
            $('input[name="Order[radio_group_2]"]').attr('disabled', true);

        } else if (formData.Order.radio_confirm_now == 2) {
            $('#order-radio_group_2').removeClass('disabled');
            $('input[name="Order[radio_group_2]"]').removeAttr('disabled');

            $('input[name="Order[time_confirm]"]').attr('disabled', true);
            $('input[name="Order[time_confirm]"]').val('');
            $('#confirm-button').attr('disabled', true);

        } else {
            alert('ошибка');
        }

    });

    $(document).on('change', 'input[name="Order[radio_group_1]"]', function (e) {
        $('#order-radio_group_3').removeClass('disabled');
        $('*[name="Order[radio_group_3]"]').removeAttr('disabled');
    });

    $(document).on('change', 'input[name="Order[radio_group_3]"]', function (e) {
        $('#order-radio_group_1').addClass('disabled');
        $('*[name="Order[radio_group_1]"]').attr('disabled', 'true');
        $('select[name="Order[trip_transport_id]"]').attr('disabled', 'true');

        $('#order-radio_group_2').addClass('disabled');
        $('*[name="Order[radio_group_2]"]').attr('disabled', 'true');

        $('#writedown-button').removeClass('disabled');
    });

    $(document).on('change', 'input[name="Order[radio_group_2]"]', function (e) {

        $('#order-radio_confirm_now').addClass('disabled');
        $('*[name="Order[radio_confirm_now]"]').attr('disabled', true);

        $('#order-radio_group_3').removeClass('disabled');
        $('*[name="Order[radio_group_3]"]').removeAttr('disabled');
    });


    //$(document).on('change', '*[name="Order[point_id_from]"]', function()
    //{
    //    console.log('change *[name="Order[point_id_from]"]');
    //    var street_from_value = $('.sw-element[attribute-name="Order[street_id_from]"]').find('.sw-value').text();
    //    var point_from_value = $('.sw-element[attribute-name="Order[point_id_from]"]').find('.sw-value').text();
    //    var text = $('#radio_group_1_2').attr('text');
    //
    //    var date = $('*[name="Order[time_confirm]"]').val();
    //    if(date.length > 0) {
    //        text = text.replace(/{ВРПТ}/g, date);
    //    }
    //
    //    if(point_from_value.length > 0) {
    //        text = text.replace(/{ТЧК_ОТКУДА}/g, '«' + street_from_value + '»' + ' «' + point_from_value + '»');
    //    }
    //
    //    $('#radio_group_1_2').text(text);
    //});


    // обработка потери фокуса - установки фокуса полей
    $(document).on('blur', '#order-additional_phone_3', function() {
        //$('.sw-element[attribute-name="Order[street_id_from]"]').focus();
        $('.sw-element[attribute-name="Order[yandex_point_from]"]').focus();
        return false;
    });

    //$(document).on('blur', '.sw-outer-block[attribute-name="Order[street_id_from]"]', function() {
    //    $('.sw-element[attribute-name="Order[point_id_from]"]').focus();
    //    return false;
    //});

    //$(document).on('blur', '.sw-outer-block[attribute-name="Order[point_id_from]"]', function() {
    //    if($('#order-time_air_train_arrival').is(':disabled')) {
    //        $('.sw-element[attribute-name="Order[point_id_to]"]').focus();
    //    }else {
    //        $('#order-time_air_train_arrival').focus();
    //    }
    //
    //    return false;
    //});

    $(document).on('blur', '.sw-outer-block[attribute-name="Order[yandex_point_from]"]', function() {
        if($('#order-time_air_train_arrival').is(':disabled')) {
            $('.sw-element[attribute-name="Order[yandex_point_to]"]').focus();
        }else {
            $('#order-time_air_train_arrival').focus();
        }

        return false;
    });

    $(document).on('blur', '#order-time_air_train_arrival', function() {
        //$('.sw-element[attribute-name="Order[point_id_to]"]').focus();
        $('.sw-element[attribute-name="Order[yandex_point_to]"]').focus();
    });

    //$(document).on('blur', '.sw-outer-block[attribute-name="Order[point_id_to]"]', function() {
    //    if($('#order-time_air_train_departure').is(':disabled')) {
    //        $('input[name="Order[radio_confirm_now]"][value="1"]').focus();
    //    }else {
    //        $('#order-time_air_train_departure').focus();
    //    }
    //
    //    return false;
    //});

    $(document).on('blur', '.sw-outer-block[attribute-name="Order[yandex_point_to]"]', function() {
        if($('#order-time_air_train_departure').is(':disabled')) {
            $('input[name="Order[radio_confirm_now]"][value="1"]').focus();
        }else {
            $('#order-time_air_train_departure').focus();
        }

        return false;
    });

    $(document).on('blur', '#order-time_air_train_departure', function() {
        if(!$('input[name="Order[radio_confirm_now]"][value="1"]').is(':disabled')) {
            $('input[name="Order[radio_confirm_now]"][value="1"]').focus();
        }else if(!$('input[name="Order[radio_group_1]"][value="1"]').is(':disabled')) {
            $('input[name="Order[radio_group_1]"][value="1"]').focus();
        }else if(!$('input[name="Order[radio_group_2]"][value="1"]').is(':disabled')) {
            $('input[name="Order[radio_group_2]"][value="1"]').focus();
        }else if(!$('input[name="Order[radio_group_3]"][value="1"]').is(':disabled')) {
            $('input[name="Order[radio_group_3]"][value="1"]').focus();
        }else if(!$('#writedown-button').hasClass('disabled')) {
            $('#writedown-button').focus();
        }else {
            $('#cancel-button').focus();
        }
    });

    $(document).on('blur', 'input[name="Order[radio_confirm_now]"][value="1"]', function() {
        $('input[name="Order[radio_confirm_now]"][value="2"]').focus();
    });

    $(document).on('blur', 'input[name="Order[radio_confirm_now]"][value="2"]', function() {
        if(!$('#order-time_confirm').is(':disabled')) {
            $('#order-time_confirm').focus();
        }else if(!$('#confirm-button').is(':disabled')) {
            $('#confirm-button').focus();
        }else if(!$('input[name="Order[radio_group_1]"][value="1"]').is(':disabled')) {
            $('input[name="Order[radio_group_1]"][value="1"]').focus();
        }else if(!$('input[name="Order[radio_group_2]"][value="1"]').is(':disabled')) {
            $('input[name="Order[radio_group_2]"][value="1"]').focus();
        }else if(!$('input[name="Order[radio_group_3]"][value="1"]').is(':disabled')) {
            $('input[name="Order[radio_group_3]"][value="1"]').focus();
        }else if(!$('#writedown-button').hasClass('disabled')) {
            $('#writedown-button').focus();
        }else {
            $('#cancel-button').focus();
        }
    });

    $(document).on('blur', 'input[name="Order[radio_group_1]"][value="2"]', function() {
        if(!$('input[name="Order[radio_group_2]"][value="1"]').is(':disabled')) {
            $('input[name="Order[radio_group_2]"][value="1"]').focus();
        }else if(!$('input[name="Order[radio_group_3]"][value="1"]').is(':disabled')) {
            $('input[name="Order[radio_group_3]"][value="1"]').focus();
        }else if(!$('#writedown-button').hasClass('disabled')) {
            $('#writedown-button').focus();
        }else {
            $('#cancel-button').focus();
        }
    });

    $(document).on('blur', '#confirm-button', function() {
        if(!$('input[name="Order[radio_group_1]"][value="1"]').is(':disabled')) {
            $('input[name="Order[radio_group_1]"][value="1"]').focus();
        }else if(!$('input[name="Order[radio_group_2]"][value="1"]').is(':disabled')) {
            $('input[name="Order[radio_group_2]"][value="1"]').focus();
        }else if(!$('input[name="Order[radio_group_3]"][value="1"]').is(':disabled')) {
            $('input[name="Order[radio_group_3]"][value="1"]').focus();
        }else if(!$('#writedown-button').hasClass('disabled')) {
            $('#writedown-button').focus();
        }else {
            $('#cancel-button').focus();
        }
    });



    // При потери фокуса полей: ФИО, Примечания, (Откуда, Куда) первая буква становиться заглавной
    $(document).on('blur', 'input[name="Client[name]"]', function() {
        var fio = $.trim($(this).val());
        var ar_fio = fio.split(' ');
        var ar_new_fio = [];
        for(var i = 0; i < ar_fio.length; i++) {
            var string = $.trim(ar_fio[i]);
            ar_new_fio.push(toUpperCaseFirstLetter(string));
        }

        $(this).val(ar_new_fio.join(' '));
    });
    $(document).on('blur', 'textarea[name="Order[comment]"]', function() {
        var comment = $.trim($(this).val());
        $(this).val(toUpperCaseFirstLetter(comment));
    });



    // открытие/закрытие формы "Редактирование контактов"
    $(document).on('click', '#order-client-form #edit-by-hand', function() {

        clearPhonesBlock();
        if($('#order-create-modal .order-phones-block').is(':visible')) {
            $('#order-create-modal .order-phones-block').hide();
        }else {
            $('#order-create-modal .order-phones-block').show();
        }

        return false;
    });

    // закрытие формы "Редактирование контактов"
    $(document).on('click', '.order-phones-block-close', function() {

        clearPhonesBlock();
        $('#order-create-modal .order-phones-block').hide();

        return false;
    });

    // открытие окна "Звонка" (окна наступающих заказов клиента)
    $(document).on('click', '#order-client-form #info-window', function() {

        var mobile_phone = $('#order-client-form #client-mobile_phone').val();
        if(mobile_phone != '') {
            showInnerCallModal('', mobile_phone);
        }

        return false;
    });

    // order-phones-copy-button
    $(document).on('click', '#order-phones-copy-button', function() {

        if(searchClientByPhoneResponse.success !== void 0) {

            var mobile_phone_new = $('#client-mobile_phone_new').val();
            if(mobile_phone_new != '') {
                $('#client-mobile_phone').val(mobile_phone_new);
                $('#client-mobile_phone_view').val(mobile_phone_new);
            }

            var name_new = $('#client-name_new').val();
            if(name_new != '') {
                $('#client-name').val(name_new);
                $('#client-name_view').val(name_new);
            }else {

            }

            var home_phone_new = $('#client-home_phone_new').val();
            if(home_phone_new != '') {
                $('#client-home_phone').val(home_phone_new);
                $('#client-home_phone_view').val(home_phone_new);
            }else {
                if(searchClientByPhoneResponse.client.id > 0) {
                    $('#client-home_phone').val('');
                    $('#client-home_phone_view').val('');
                }
            }

            var alt_phone_new = $('#client-alt_phone_new').val();
            if(alt_phone_new != '') {
                $('#client-alt_phone').val(alt_phone_new);
                $('#client-alt_phone_view').val(alt_phone_new);
            }else {
                if(searchClientByPhoneResponse.client.id > 0) {
                    $('#client-alt_phone').val('');
                    $('#client-alt_phone_view').val('');
                }
            }

            var additional_phone_1_new = $('#order-additional_phone_1_new').val();
            if(additional_phone_1_new != '') {
                $('#order-additional_phone_1').val(additional_phone_1_new);
                $('#order-additional_phone_1_view').val(additional_phone_1_new);
            }else {
                //if(searchClientByPhoneResponse.client.id == undefined) {
                //    $('#client-additional_phone_1_view').val('');
                //}
            }

            var additional_phone_2_new = $('#order-additional_phone_2_new').val();
            if(additional_phone_2_new != '') {
                $('#order-additional_phone_2').val(additional_phone_2_new);
                $('#order-additional_phone_2_view').val(additional_phone_2_new);
            }else {
                //if(searchClientByPhoneResponse.client.id == undefined) {
                //    $('#client-additional_phone_2_view').val('');
                //}
            }

            var additional_phone_3_new = $('#order-additional_phone_3_new').val();
            if(additional_phone_3_new != '') {
                $('#order-additional_phone_3').val(additional_phone_3_new);
                $('#order-additional_phone_3_view').val(additional_phone_3_new);
            }else {
                //if(searchClientByPhoneResponse.client.id == undefined) {
                //    $('#client-additional_phone_3_view').val('');
                //}
            }

            updateOrderFormByClientData(searchClientByPhoneResponse);



            $('.order-phones-block-close').click();

        }else {
            //alert('нет данных');
            var mobile_phone_new = $('#client-mobile_phone_new').val();
            //alert('mobile_phone_new=' + mobile_phone_new);
            if(mobile_phone_new != '') {
                $('#client-mobile_phone').val(mobile_phone_new);
                $('#client-mobile_phone_view').val(mobile_phone_new);
            }

            var name_new = $('#client-name_new').val();
            if(name_new != '') {
                $('#client-name').val(name_new);
                $('#client-name_view').val(name_new);
            }

            var home_phone_new = $('#client-home_phone_new').val();
            if(home_phone_new != '') {
                $('#client-home_phone').val(home_phone_new);
                $('#client-home_phone_view').val(home_phone_new);
            }

            var alt_phone_new = $('#client-alt_phone_new').val();
            if(alt_phone_new != '') {
                $('#client-alt_phone').val(alt_phone_new);
                $('#client-alt_phone_view').val(alt_phone_new);
            }

            var additional_phone_1_new = $('#order-additional_phone_1_new').val();
            if(additional_phone_1_new != '') {
                $('#order-additional_phone_1').val(additional_phone_1_new);
                $('#order-additional_phone_1_view').val(additional_phone_1_new);
            }

            var additional_phone_2_new = $('#order-additional_phone_2_new').val();
            if(additional_phone_2_new != '') {
                $('#order-additional_phone_2').val(additional_phone_2_new);
                $('#order-additional_phone_2_view').val(additional_phone_2_new);
            }

            var additional_phone_3_new = $('#order-additional_phone_3_new').val();
            if(additional_phone_3_new != '') {
                $('#order-additional_phone_3').val(additional_phone_3_new);
                $('#order-additional_phone_3_view').val(additional_phone_3_new);
            }

            $('.order-phones-block-close').click();
        }

        return false;
    });
});