

// обновление страницы "Информация о рейсе"
function updateTripOrdersPage(restore_opened_popup)
{
    if(restore_opened_popup == undefined) {
        restore_opened_popup = false;
    }

    var trip_id = $('#trip-orders-page').attr('trip-id');
    if(trip_id == undefined) {
        return false;
    }


    if(restore_opened_popup == true) {

        // сделаем чтобы после автообновления страницы раскрытые всплывающие окошки остались раскрытыми
        var opened_popup_form_widget = $('.pfw-popup-form:visible');
        if (opened_popup_form_widget.length > 0) {
            var open_popup_order_id = opened_popup_form_widget.parents('tr').attr('order-id');
            var open_popup_column_name = opened_popup_form_widget.parents('td').attr('column-name');
        } else {
            var open_popup_order_id = '';
            var open_popup_column_name = '';
        }

        // сделаем чтобы после автообновления страницы раскрытые в таблице заказов поля редактирования времени осталось на месте
        var opened_editable_time_widgets = {};
        if ($('.etf-block:visible').length > 0) {
            var i = 0;
            $('.etf-block:visible').each(function() {
                var order_id =  $(this).parents('tr').attr('order-id');
                var attr_name = $(this).find('input[name]').attr('name');
                var value = $(this).find('input[name]').val();
                var opened_editable_time_widget = {
                    order_id: order_id,
                    attr_name: attr_name,
                    value: value
                };
                opened_editable_time_widgets[i] = opened_editable_time_widget;
                i++;
            });
        }

        // сделаем чтобы после автообновления страницы в раскрытом вверху блоке "массового присвоения машины/ВРПТ"
        //  все осталось на своих местах
        var opened_orders_buttons_block = $('#orders-buttons-block').is(':visible');
        if (opened_orders_buttons_block == true) {
            //var orders_plan_trip_transport_id = $('#orders-buttons-block *[name="orders_plan_trip_transport_id"]').val();
            var orders_time_confirm = $('#orders-buttons-block *[name="orders_time_confirm"]').val();
            var selection_all_is_checked = $('#orders-grid').find('input[name="selection_all"]').prop('checked');
            var selected_orders = getSelectedOrders();
        }
    }


    $.ajax({
        url: '/trip/ajax-get-trip-orders?trip_id=' + trip_id,
        type: 'post',
        data: {
            url_params: getUrlParams('object')
        },
        success: function (data) {

            if (data.success == true) {
                $('#trip-orders-page').html(data.html);

                if(restore_opened_popup == true)
                {
                    if (open_popup_order_id != '' && open_popup_column_name != '') {
                        $('#orders-grid').find('tr[order-id="' + open_popup_order_id + '"]').find('td[column-name="' + open_popup_column_name + '"]').find('.pfw-element').click();
                    }

                    if (opened_orders_buttons_block == true) {
                        $('#orders-buttons-block').show();
                        //$('#orders-buttons-block *[name="orders_plan_trip_transport_id"]').val(orders_plan_trip_transport_id);
                        $('#orders-buttons-block *[name="orders_time_confirm"]').val(orders_time_confirm);
                        $('#orders-grid').find('input[name="selection_all"]').prop('checked', selection_all_is_checked);
                        for (var key in selected_orders) {
                            var order_id = selected_orders[key];
                            $('#orders-grid').find('input[name="selection[]"][value="' + order_id + '"]').attr('checked', true);
                        }
                    }

                    for(var i in opened_editable_time_widgets) {
                        var opened_editable_time_widget = opened_editable_time_widgets[i];
                        var order_id = opened_editable_time_widget.order_id;
                        var attr_name = opened_editable_time_widget.attr_name;
                        var value = opened_editable_time_widget.value;

                        var etw_td = $('#orders-grid').find('tr[order-id="' + order_id + '"]').find('td[column-name="' + attr_name.replace('_', '-') + '"]');
                        etw_td.find('input[name="' + attr_name + '"]').val(value);
                        etw_td.find('.etw-element').click();
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


// функция возвращает массив id заказов которые выбранны с помощью чекбоксов
function getSelectedOrders()
{
    var orders_id = {};
    $('#orders-grid').find('input[name="selection[]"]:checked').each(function() {
        var order_id = $(this).val();
        orders_id[order_id] = order_id;
    });

    return orders_id;
}

// функция отправляет ajax запрос для обновления данных группы заказов
function updateOrders(trip_id, data) {

    //console.log('url=' + '/order/ajax-update-orders?trip_id='+trip_id);
    //console.log('data:'); console.log(data);
    $.ajax({
        url: '/order/ajax-update-orders?trip_id=' + trip_id,
        type: 'post',
        data: data,
        //contentType: false,
        //cache: false,
        //processData: false,
        success: function (response) {

            if (data.success == true) {
                $('#orders-grid').find('input[name="selection[]"]:checked').prop('checked', false);
                $('#orders-buttons-block').hide();
                // updateTripOrdersPage();

                //alert('sdf');
                //if($(".call-block").length > 0) {
                //    updateCallClientForm();
                //}

                if($('#call-page').length > 0) {
                    location.reload();
                }
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
        }
    });
}


function setTimeConfirm(order_id, time_confirm) {

    $.ajax({
        url: "/order/editable-order?id=" + order_id,
        type: "post",
        data: {
            hasEditable: 1,
            time_confirm: time_confirm
        },
        success: function (data) {

            var trip_id = $('#trip-orders-page').attr('trip-id');
            if(void 0 !== trip_id) {
                // console.log('updateTripOrdersPage');
                updateTripOrdersPage(false, trip_id); // обновляем всю страницу
            }

            //if($('.call-block').length > 0) {
            //    console.log('updateCallClientForm');
            //    updateCallClientForm();
            //}
            updateCallModal();

        },
        error: function (data, textStatus, jqXHR) {
            if (textStatus == "error") {
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
        }
    });
}


$(document).ready(function()
{
    //$('input[name="fact_trip_transport_id"]').datetimepicker(
    //    {pickDate: false, language: 'ru'}
    //);
    $(".kv-editable").on("pjax:end", function() {
        $.pjax.reload({container:"#order"}); //Reload GridView
    });

    // автообновление страницы с заданным интервалом времени
    //setInterval(function() {
    //    updateTripOrdersPage(true);
    //}, 15000);


    $(document).on('click', '#add-order', function() {
        var trip_id = $(this).attr('trip-id');

        //openModalCreateOrder('', trip_id);

        var data = {
            trip_id: trip_id
        }
        openModalCreateOrder(data);

        return false;
    });

    $(document).on('click', '.add-order', function() {

        var trip_id = $(this).attr('trip-id');
        var trip_transport_id = $(this).attr('trip-transport-id');
        var data = {
            trip_id: trip_id,
            trip_transport_id: trip_transport_id
        }

        openModalCreateOrder(data);

        return false;
    });

    $(document).on('click', '.edit-order', function()
    {
        var order_id = $(this).attr('order-id');
        var data = {
            order_id: order_id
        }
        openModalCreateOrder(data);

        return false;
    });

    //$(document).on('click', '.create-order-copy', function()
    //{
    //    var order_id = $(this).attr('order-id');
    //    var data = {
    //        order_id: order_id
    //    }
    //    //openModalCreateOrder(data);
    //
    //    return false;
    //});

    $(document).on('click', '.cancel-order', function()
    {
        var order_id = $(this).attr('order-id');

        var cancelOrder = new modalWindow({
            url_from:'/order/cancel-order-form',
            get_from: {
                id: order_id
            },
            title: 'Укажите причину отмены',
            standartTemplate: false,
            success_message_response: null,
            header_color: '#dd4b39',
            afterResponseSuccess: function(modalWindow, response){
                if(response.success == true) {
                    $('#default-modal').modal('hide');
                    // updateTripOrdersPage(); // и обновляем всю страницу

                    updateCallModal();
                }
            }
            // благодаря этому пустому вызову события, происходит отображение ошибок с сервера (гениальность Славы...)
            ,afterResponseError: function (xz) {
                //console.log('afterResponseError xz:'); console.log(xz);
            }
        });
        cancelOrder.open();

        return false;
    });


    // на странице "Информация о рейсе" нажатие на кнопку "Отправить" в нижний таблице машин
    $(document).on('click', '.question-send-trip-transport', function()
    {
        var trip_transport_id = $(this).attr('trip-transport-id');

        $.ajax({
            url: '/trip-transport/get-send-form?id=' + trip_transport_id,
            type: 'post',
            data: {},
            success: function (response) {

                $('#default-modal').find('.modal-body').html(response);
                $('#default-modal').find('.modal-dialog').width('600px');
                $('#default-modal .modal-title').text('Подтверждение отправки транспорта');
                $('#default-modal').modal('show');
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


    // во всплывающей форме "Подтверждения отправки т/с" выбор/снятие чекбокса "Круг завершен"
    $(document).on('change', 'input[name="round-is-completed"]', function() {

        var is_checked = $(this).is(':checked');

        if(is_checked == true) {
            $('#transport_round_completing_reasons').show(250);
        }else {
            $('#transport_round_completing_reasons').hide(250);
        }

    });

    // в форме подтверждения отправки транспорта щелчек на кнопке "Отправить"
    var allow_send_transport = true;
    $(document).on('click', '#send-trip-transport', function()
    {
        var trip_transport_id = $('#default-modal').find('input[name="trip-transport-id"]').val();
        var round_is_completed = $('#default-modal').find('input[name="round-is-completed"]').is(':checked');
        var transport_round_completing_reason = $('#default-modal').find('select[name="transport_round_completing_reason"]').val();

        if(allow_send_transport == true) {
            $.ajax({
                url: '/trip-transport/ajax-send?id=' + trip_transport_id,
                type: 'post',
                data: {
                    round_is_completed: round_is_completed,
                    transport_round_completing_reason: transport_round_completing_reason
                },
                beforeSend: function () {
                    allow_send_transport = false;
                },
                success: function (data) {
                    if (data.success == true) {
                        $('#default-modal').modal('hide');
                        // updateTripOrdersPage(); // и обновляем всю страницу
                    }
                    allow_send_transport = true;
                },
                error: function (data, textStatus, jqXHR) {
                    allow_send_transport = true;
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
            LogDispatcherAccounting('кнопка «Отправить» т/с формы отправки');
        }

        return false;
    });

    // на странице "Информация о рейсе" нажатие на кнопку "Отменить" в нижний таблице машин
    $(document).on('click', '.cancel-trip-transport', function()
    {
        var trip_transport_id = $(this).attr('trip-transport-id');

        if(confirm('Вы действительно хотите отвязать машину от рейса?'))
        {
            $.ajax({
                url: '/trip-transport/ajax-delete?id=' + trip_transport_id,
                type: 'post',
                data: {},
                success: function (data) {
                    if (data.success == true) {
                        $('#default-modal').modal('hide');
                        // updateTripOrdersPage(); // и обновляем всю страницу
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

        return false;
    });


    // Подтвердить (подтвердить время)
    $(document).on('click', '.to-confirm', function()
    {
        if (void 0 !== $(this).attr('disabled') || $(this).hasClass('disabled')) {
            return false;
        }

        var order_id = $(this).parents('tr').attr('order-id');

        $.ajax({
            url: '/order/ajax-set-confirm?id=' + order_id,
            type: 'post',
            data: {},
            success: function (data) {
                if (data.success == true) {
                    // updateTripOrdersPage(); // и обновляем всю страницу

                    //var trip_id = $('#trip-orders-page').attr('trip-id');
                    //if(void 0 !== trip_id) {
                    //    //updateTripOrdersPage(false, trip_id); // обновляем всю страницу
                    //}else {
                    //    updateCallClientForm();
                    //}

                    //if($('.call-block').length > 0) {
                    //    updateCallClientForm();
                    //}
                    updateCallModal();

                }else {
                    alert('Ошибка изменения поля');
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


    // посадить в машину
    //var allow_put_into_transport_button = true;
    var allow_put_into_transport_button = [];
    $(document).on('click', '.put-into-transport', function()
    {
        if (void 0 !== $(this).attr('disabled') || $(this).hasClass('disabled')) {
            return false;
        }

        var order_id = $(this).parents('tr').attr('order-id');

        //if(allow_put_into_transport_button == true) {
        if(allow_put_into_transport_button[order_id] == undefined || allow_put_into_transport_button[order_id] == true) {

            $.ajax({
                url: '/order/ajax-set-time-sat?id=' + order_id,
                type: 'post',
                data: {},
                beforeSend: function () {
                    allow_put_into_transport_button[order_id] = false;
                },
                success: function (data) {

                    allow_put_into_transport_button[order_id] = true;
                    if (data.success == true) {
                        // updateTripOrdersPage(); // и обновляем всю страницу

                        //if($(".call-block").length > 0) {
                        //    updateCallClientForm();
                        //}
                        updateCallModal();

                    } else {
                        alert('Ошибка изменения поля');
                    }
                },
                error: function (data, textStatus, jqXHR) {
                    allow_put_into_transport_button[order_id] = true;
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
            LogDispatcherAccounting('кнопка «Посадить в машину»');
        }

        return false;
    });


    // подтверждение посадки в машину
    var allow_put_into_transport_confirm_button = [];
    $(document).on('click', '.put-into-transport-confirm', function()
    {
        if (void 0 !== $(this).attr('disabled') || $(this).hasClass('disabled')) {
            return false;
        }

        var order_id = $(this).parents('tr').attr('order-id');

        //if(allow_put_into_transport_confirm_button == true) {
        if(allow_put_into_transport_confirm_button[order_id] == undefined || allow_put_into_transport_confirm_button[order_id] == true) {

            $.ajax({
                url: '/order/ajax-set-time-sat-confirm?id=' + order_id,
                type: 'post',
                data: {},
                beforeSend: function () {
                    allow_put_into_transport_confirm_button[order_id] = false;
                },
                success: function (data) {

                    allow_put_into_transport_confirm_button[order_id] = true;
                    if (data.success == true) {
                        // updateTripOrdersPage(); // и обновляем всю страницу

                        //if($(".call-block").length > 0) {
                        //    updateCallClientForm();
                        //}
                        updateCallModal();

                    }else {
                        alert('Ошибка изменения поля');
                    }
                },
                error: function (data, textStatus, jqXHR) {
                    allow_put_into_transport_confirm_button[order_id] = true;
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
        }

        return false;
    });


    // высадить из машины
    var allow_cancel_put_into_transport_button = [];
    $(document).on('click', '.cancel-put-into-transport', function()
    {
        if (void 0 !== $(this).attr('disabled') || $(this).hasClass('disabled')) {
            return false;
        }

        var order_id = $(this).parents('tr').attr('order-id');

        //if(allow_cancel_put_into_transport_button == true) {
        if(allow_cancel_put_into_transport_button[order_id] == undefined || allow_cancel_put_into_transport_button[order_id] == true) {
            $.ajax({
                url: '/order/ajax-set-time-sat?id=' + order_id + '&set=0',
                type: 'post',
                data: {},
                beforeSend: function () {
                    allow_cancel_put_into_transport_button[order_id] = false;
                },
                success: function (data) {

                    allow_cancel_put_into_transport_button[order_id] = true;
                    if (data.success == true) {
                        // updateTripOrdersPage(); // и обновляем всю страницу

                        //if($(".call-block").length > 0) {
                        //    updateCallClientForm();
                        //}
                        updateCallModal();

                    }else {
                        alert('Ошибка изменения поля');
                    }
                },
                error: function (data, textStatus, jqXHR) {
                    allow_cancel_put_into_transport_button[order_id] = true;
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
            LogDispatcherAccounting('кнопка «Высадить из машины»');
        }

        return false;
    });


    // клиент знает машину
    //var allow_confirm_selected_transport_button = true;
    var allow_confirm_selected_transport_button = [];
    $(document).on('click', '.confirm-selected-transport', function()
    {
        if (void 0 !== $(this).attr('disabled') || $(this).hasClass('disabled')) {
            return false;
        }

        var order_id = $(this).parents('tr').attr('order-id');


        //if(allow_confirm_selected_transport_button == true) {
        if(allow_confirm_selected_transport_button[order_id] == undefined || allow_confirm_selected_transport_button[order_id] == true) {

            $.ajax({
                url: '/order/ajax-confirm-selected-transport?id=' + order_id,
                type: 'post',
                data: {},
                beforeSend: function () {
                    allow_confirm_selected_transport_button[order_id] = false;
                },
                success: function (data) {

                    allow_confirm_selected_transport_button[order_id] = true;

                    if (data.success == true) {
                        // updateTripOrdersPage(); // и обновляем всю страницу

                        //if($(".call-block").length > 0) {
                        //    updateCallClientForm();
                        //}
                        updateCallModal();

                    } else {
                        alert('Ошибка изменения поля');
                    }
                },
                error: function (data, textStatus, jqXHR) {
                    allow_confirm_selected_transport_button[order_id] = true;
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
            LogDispatcherAccounting('кнопка «КЗМ»');
        }

        return false;
    });



    // кнопка "Начать отправку рейса"
    $(document).on('click', '#trip-orders-page #start-sending-reis', function()
    {
        if (void 0 !== $(this).attr('disabled') || $(this).hasClass('disabled')) {
            return false;
        }

        var trip_id = $('#trip-orders-page').attr('trip-id');
        var $obj = $(this);

        $.ajax({
            url: '/trip/ajax-start-sending-reis?trip_id=' + trip_id,
            type: 'post',
            data: {},
            success: function(response) {

                if(response.success == true) {
                    if(response.status == "sended") {
                        updateTripOrdersPage(); // и обновляем всю страницу
                    }else if(response.status == "need_choice") {

                        var html =
                            '<div id="start-sending-reis-form">' +
                            '<div class="row">' +
                            '<div id="select-choice" class="col-sm-12">' +
                            '<p><input style="margin-top:-3px; vertical-align:middle;" name="use_mobile_app" type="radio" value="0"> <span title="Как было раньше - работает только оператор">Стандартный режим</span></p>' +
                            '<p><input style="margin-top:-3px; vertical-align:middle;" name="use_mobile_app" type="radio" value="1"> <span title="С участием водительского приложения">Интерактивный режим</span></p>' +
                            '</div>' +
                            '</div>' +
                            '<hr />' +
                            '<div class="row">' +
                            '<div class="col-sm-2">' +
                            '<div class="form-group">' +
                            '<button type="button" id="start-sending-reis-submit" class="btn btn-success button-submit">Начать отправку рейса</button>' +
                            '</div>' +
                            '</div>' +
                            '</div>' +
                            '</div>';

                        var reis_name = $obj.attr('reis-name');
                        $('#default-modal .modal-title').html('Выберите режим отправки рейса ' + reis_name);
                        $('#default-modal .modal-dialog').css('width', '500px');
                        $('#default-modal .modal-body').html(html);
                        $('#default-modal').modal('show');
                    }
                }
            },
            error: function(data, textStatus, jqXHR) {
                allow_start_sending_reis = true;
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
            }
        });

        return false;
    });




    $(document).on('click', '#start-sending-reis-form #select-choice span', function() {
        $(this).prev().click();
    });

    var allow_start_sending_reis = true;
    $(document).on('click', '#start-sending-reis-submit', function() {

        var use_mobile_app = $('input[name="use_mobile_app"]:checked').val();
        if(use_mobile_app == undefined) {
            alert('Выберите режим работы рейса');
            return false;
        }

        $('#default-modal').modal('hide');


        if(allow_start_sending_reis == true) {

            var trip_id = $('#trip-orders-page').attr('trip-id');
            $.ajax({
                url: '/trip/ajax-start-sending-reis?trip_id=' + trip_id + '&use_mobile_app=' + use_mobile_app + '&start=1',
                type: 'post',
                data: {},
                beforeSend: function() {
                    allow_start_sending_reis = false;
                },
                success: function(data) {
                    allow_start_sending_reis = true;
                    if (data.success == true) {
                        // updateTripOrdersPage(); // и обновляем всю страницу
                    } else {
                        alert('Ошибка изменения поля');
                    }
                },
                error: function(data, textStatus, jqXHR) {
                    allow_start_sending_reis = true;
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
            LogDispatcherAccounting('кнопка «Начать отправку рейса»');
        }
    });


    // кнопка "Отправить рейс"  (Закрыть рейс)
    var allow_send_reis = true;
    $(document).on('click', '#trip-orders-page #send-reis', function()
    {
        if (void 0 !== $(this).attr('disabled') || $(this).hasClass('disabled')) {
            return false;
        }

        var trip_id = $('#trip-orders-page').attr('trip-id');

        if(allow_send_reis == true) {
            $.ajax({
                url: '/trip/ajax-send-reis?trip_id=' + trip_id,
                type: 'post',
                data: {},
                beforeSend: function () {
                    allow_send_reis = false;
                },
                success: function (data) {
                    allow_send_reis = true;
                    if (data.success == true) {
                        // updateTripOrdersPage(); // и обновляем всю страницу
                    } else {
                        alert('Ошибка изменения поля');
                    }
                },
                error: function (data, textStatus, jqXHR) {
                    allow_send_reis = true;
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
            LogDispatcherAccounting('кнопка «Отправить рейс»');
        }

        return false;
    });


    // кнопка "Пересчитать цены"
    var allow_recount_orders_prices = true;
    $(document).on('click', '#trip-orders-page #recount-orders-prices', function()
    {
        if (void 0 !== $(this).attr('disabled') || $(this).hasClass('disabled')) {
            return false;
        }

        var trip_id = $('#trip-orders-page').attr('trip-id');

        if(allow_send_reis == true) {
            $.ajax({
                url: '/trip/ajax-recount-orders-prices?trip_id=' + trip_id,
                type: 'post',
                data: {},
                beforeSend: function () {
                    allow_recount_orders_prices = false;
                },
                success: function (data) {
                    allow_recount_orders_prices = true;

                    alert('Цены пересчитаны');

                    if (data.update_page == true) {
                        updateTripOrdersPage(); // и обновляем всю страницу
                    }
                },
                error: function (data, textStatus, jqXHR) {
                    allow_recount_orders_prices = true;
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
            LogDispatcherAccounting('кнопка «Отправить рейс»');
        }

        return false;
    });


    var allow_set_time_confirm_sort_minus = true;
    $(document).on('click', '.time-confirm-sort-minus', function()
    {
        var order_id = $(this).parents('tr').attr('order-id');
        var time_confirm_sort = parseInt($(this).next('.time-confirm-sort').text());
        time_confirm_sort--;

        if(allow_set_time_confirm_sort_minus == true) {
            $.ajax({
                url: '/order/ajax-set-time-confirm-sort?id=' + order_id + '&value=' + time_confirm_sort,
                type: 'post',
                data: {},
                beforeSend: function () {
                    allow_set_time_confirm_sort_minus = false;
                },
                success: function (data) {

                    allow_set_time_confirm_sort_minus = true;
                    if (data.success == true) {
                        // updateTripOrdersPage(); // и обновляем всю страницу
                    } else {
                        alert('Ошибка изменения поля');
                    }
                },
                error: function (data, textStatus, jqXHR) {
                    allow_set_time_confirm_sort_minus = true;
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
            LogDispatcherAccounting('кнопка «-» поля ручной сортировки');
        }

        return false;
    });

    var allow_set_time_confirm_sort_plus = true;
    $(document).on('click', '.time-confirm-sort-plus', function()
    {
        var order_id = $(this).parents('tr').attr('order-id');
        var time_confirm_sort = parseInt($(this).prev('.time-confirm-sort').text());
        time_confirm_sort++;

        if(allow_set_time_confirm_sort_plus == true) {
            $.ajax({
                url: '/order/ajax-set-time-confirm-sort?id=' + order_id + '&value=' + time_confirm_sort,
                type: 'post',
                data: {},
                beforeSend: function () {
                    allow_set_time_confirm_sort_plus = false;
                },
                success: function (data) {

                    allow_set_time_confirm_sort_plus = true;
                    if (data.success == true) {
                        // updateTripOrdersPage(); // и обновляем всю страницу
                    } else {
                        alert('Ошибка изменения поля');
                    }
                },
                error: function (data, textStatus, jqXHR) {
                    allow_set_time_confirm_sort_plus = true;
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
            LogDispatcherAccounting('кнопка «+» поля ручной сортировки');
        }

        return false;
    });


    // переключения в чекбоксах таблицы заказов
    $(document).on('change', '#orders-grid input[name="selection_all"]', function() {

        if ($(this).is(':checked') == true) {
            $('#orders-grid input[name="selection[]').attr('checked', true);
            $('#orders-buttons-block').show();
        }else {
            $('#orders-grid input[name="selection[]').removeAttr('checked');
            $('#orders-buttons-block').hide();
        }
    });
    $(document).on('change', '#orders-grid input[name="selection[]"]', function() {

        var checked_count = $('#orders-grid').find('input[name="selection[]"]:checked').length;
        if (checked_count > 0) {
            $('#orders-buttons-block').show();
        }else {
            $('#orders-buttons-block').hide();
        }

        var all_selections_count = $('#orders-grid').find('input[name="selection[]"]').length;
        if(checked_count == all_selections_count) {
            $('#orders-grid').find('input[name="selection_all"]').prop('checked', true);
        }else {
            $('#orders-grid').find('input[name="selection_all"]').prop('checked', false);
        }
    });

    // привязка к выбранным заказам транспорта
    $(document).on('click', '#orders_fact_trip_transport_accept', function() {
        var trip_id = $('#trip-orders-page').attr('trip-id');
        var data = {
            orders_id: getSelectedOrders(),
            fact_trip_transport_id: $('#orders-buttons-block *[name="orders_fact_trip_transport_id"]').val()
        };

        updateOrders(trip_id, data);
    });

    // отвязка от выбранных заказов транспорта
    $(document).on('click', '#orders_fact_trip_transport_cancel', function() {
        var trip_id = $('#trip-orders-page').attr('trip-id');
        var data = {
            orders_id: getSelectedOrders(),
            fact_trip_transport_id: null
        };
        updateOrders(trip_id, data);
    });

    // назначение выбранным заказам ВРПТ
    $(document).on('click', '#orders_time_confirm_accept', function() {
        var trip_id = $('#trip-orders-page').attr('trip-id');
        var data = {
            'orders_id': getSelectedOrders(),
            'time_confirm': $('#orders-buttons-block *[name="orders_time_confirm"]').val(),
        };
        updateOrders(trip_id, data);
    });

    // удаление у выбранных заказов ВРПТ
    $(document).on('click', '#orders_time_confirm_cancel', function() {
        var trip_id = $('#trip-orders-page').attr('trip-id');
        var data = {
            'orders_id': getSelectedOrders(),
            'time_confirm_hour': '',
            'time_confirm_minute': '',
        };
        updateOrders(trip_id, data);
    });


    $(document).on('click', '#trip-orders-page .trip_transport', function() {

        var trip_transport_id = $(this).attr('trip_transport_id');

        var transportInfo = new modalWindow({
            url_from:'/trip-transport/show-car-info',
            get_from:{
                trip_transport_id: trip_transport_id
            },
            standartTemplate: false,
            actionType:'submit',
            afterOpenAction: function(modalObj_plus, response){

                $('#' + modalObj_plus.id + ' .transport-confirmed-transport-info').unbind('click').bind('click', function(){

                    transportConfirmedTransportInfoClick($(this), modalObj_plus);
                });


                $('#' + modalObj_plus.id + ' button.change_driver_on_trip_transport').unbind('click').bind('click', function(){

                    if(!$('#' + modalObj_plus.id + ' .change_driver').val() || $('#' + modalObj_plus.id + ' .change_driver').val() == ''){
                        alert('Выберете водителя!');
                        return;
                    }

                    var driver_id = $('#' + modalObj_plus.id + ' .change_driver').val();

                    var execute_change_driver = new modalWindow({
                        url_to:'/trip-transport/change-driver-or-car',
                        get_to:{trip_transport_id: trip_transport_id, driver_id:driver_id },
                        afterResponseSuccess: function(execution, response){
                            var execute_confirm_change = new modalWindow({
                                url_to:'/trip-transport/change-confirm',
                                get_to:{trip_transport_id: trip_transport_id, confirmed:0 },
                                afterResponseSuccess: function(execution, response){
                                    modalObj_plus.refresh();
                                    // updateTripOrdersPage();
                                },
                                afterResponseError: function(execution, response){
                                    modalObj_plus.refresh();
                                },
                                success_message_response:null
                            });

                            execute_confirm_change.execute();
                            //modalObj_plus.refresh();
                        },
                        afterResponseError: function(execution, response){
                            modalObj_plus.refresh();
                        },
                        success_message_response:'Водитель сменён!',
                        error_message_response: 'Не удалось поменять водителя'
                    });

                    execute_change_driver.execute();
                });


                $('#' + modalObj_plus.id + ' .remove_from_trip').unbind('click').bind('click', function(){
                    var execute_remove = new modalWindow({
                        url_to:'/trip-transport/delete-trip-transport',
                        get_to:{trip_transport_id: trip_transport_id },
                        afterResponseSuccess: function(execution, response){
                            modalObj_plus.close();
                            //if($('#trip-orders-page').length > 0) { // обновление страницы "Информация о рейсе"
                                // updateTripOrdersPage();
                            //}else if($('#set-of-trips-page').length > 0) {
                                // updateSetTripsPage();
                            //}
                        },
                        afterResponseError: function(execution, response){
                            modalObj_plus.refresh();
                        },
                        success_message_response:'Этот транспорт снят с рейса',
                        error_message_response: 'Не удалось снять этот транспорт с рейса'
                    });

                    execute_remove.execute();
                });
            },

            title: 'Информация об автомобиле',
            totalStyle:'.head_block{margin-left:0;text-align:left;background-color:white;font-size:14px;font-weight:normal;color:#333;}',
        });

        transportInfo.open();

        return false;
    });



    var trips_map = null;

    // скрытие/отображение точек карты
    //var S1 = 12;
    //var S2 = 12;

    $(document).on('click', '#trip-orders-page #trip-yandex-map', function() {

        var trip_id = $('#trip-orders-page').attr('trip-id');

        $.ajax({
            //url: '/direction/ajax-get-direction-map-data?id=' + direction_id + '&from=1',
            url: '/trip/ajax-get-trip-map-data?id=' + trip_id,
            type: 'post',
            data: {},
            success: function (response) {
                if(response.city == null) {
                    alert('Город не определен');
                    return false;
                }

                $('#default-modal').find('.modal-dialog').width('800px');
                $('#default-modal').find('.modal-body').html('<div id="trips-map"></div>').css('padding', '0');
                $('#default-modal .modal-title').html('Точки рейса');
                $('#default-modal').modal('show');

                $('#default-modal').on('hidden.bs.modal', function(e) {
                    $('#default-modal').find('.modal-body').css('padding', '15px');
                });

                //response.city.search_scale - Приближение карты при поиске
                //response.city.point_focusing_scale - Масштаб фокусировки точки
                //response.city.all_points_show_scale - Масшаб отображения всех точек
                //MAP_SEARCH_SCALE = response.city.search_scale; - на карте рейса нет поиска


                ymaps.ready(function(){
                    trips_map = new ymaps.Map("trips-map", {
                        center: [response.city.center_lat, response.city.center_long], // показываем центр города где осуществляется посадка
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

                    //trips_map.events.add('boundschange', function (event) {
                    //    showHidePlacemarks(trips_map, event.get('newZoom'), response.city.all_points_show_scale)
                    //});


                    //var is_sat_color = '#000080'; //синий - посаженный
                    var is_confirm_color = '#008000'; // зелёный - подтвержденный заказ;
                    var is_not_confirm_color = '#D0D000'; // жёлтый - не подтвержденный;

                    // Множество точек посадки
                    for(var key in response.yandex_points_from) {

                        // yandex_points_from
                        var yandex_point_from = response.yandex_points_from[key];

                        var orders = yandex_point_from.orders;
                        var is_not_places_count = 0;
                        var is_confirmed_places_count = 0;
                        var places_count = 0;
                        for(var key in orders) {
                            var order = orders[key];
                            if(order.is_not_places == 1) {
                                is_not_places_count++;
                            }else {
                                places_count = places_count + order.places_count;
                            }

                            if(order.is_confirmed == 1) {
                                is_confirmed_places_count = is_confirmed_places_count + order.places_count;
                            }
                        }

                        var orders_count = Object.keys(orders).length;
                        var text = yandex_point_from.name + ' - ' +places_count+'('+orders_count+')-'+is_confirmed_places_count;


                        if(orders_count > 1) {

                            var is_sat_count = 0;
                            var is_confirm_count = 0;
                            var is_not_confirm_count = 0;

                            // могут быть только 2 цвета (или комбинация из двух):
                            // подтвержденный заказ - зеленый
                            // не подтвержденны - желтый

                            for(var key in orders) {
                                var order = orders[key];
                                //if(order.time_sat > 0) {
                                //    is_sat_count++; //синий - посаженный
                                //}else {
                                if (order.is_confirmed == 1) {
                                    is_confirm_count++; // зелёный - подтвержденный заказ;
                                }else {
                                    is_not_confirm_count++; // жёлтый - не подтвержденный;
                                }
                                //}
                            }

                            //alert('is_sat_count='+is_sat_count+' is_confirm_count='+is_confirm_count+' is_not_confirm_count='+is_not_confirm_count);

                            var data = new Array();
                            if(is_confirm_count > 0) {
                                data[data.length] = {weight: 1, color: is_confirm_color};
                            }
                            if(is_not_confirm_count > 0) {
                                data[data.length] = {weight: 1, color: is_not_confirm_color};
                            }


                            var placemark = new ymaps.Placemark([yandex_point_from.lat, yandex_point_from.long], {
                                hintContent: text,
                                balloonContent: text,
                                data: data,
                                iconContent: orders_count
                                //iconContent: "Диаграмма"
                            }, {
                                iconLayout: 'default#pieChart',
                                // Радиус диаграммы в пикселях.
                                iconPieChartRadius: 15,
                                // Радиус центральной части макета.
                                iconPieChartCoreRadius: 9,
                                // Стиль заливки центральной части.
                                //    iconPieChartCoreFillStyle: '#ffffff',
                                // Cтиль линий-разделителей секторов и внешней обводки диаграммы.
                                //    iconPieChartStrokeStyle: '#ffffff',
                                // Ширина линий-разделителей секторов и внешней обводки диаграммы.
                                //    iconPieChartStrokeWidth: 3,
                                // Максимальная ширина подписи метки.
                                //    iconPieChartCaptionMaxWidth: 200
                            });

                        }else {

                            var color = '';
                            var order = orders[0];
                            //if(order.time_sat > 0) {
                            //    color = is_sat_color; //синий - посаженный
                            //}else {
                            if (order.is_confirmed == 1) {
                                color = is_confirm_color; // зелёный - подтвержденный заказ;
                            }else {
                                color = is_not_confirm_color; // жёлтый - не подтвержденный;
                            }
                            //}

                            /*
                            var placemark = new ymaps.Placemark([yandex_point_from.lat, yandex_point_from.long], {
                                hintContent: text,
                                balloonContent: text,
                                //iconContent: '12'
                            }, {
                                //iconLayout: 'default#image',
                                //iconLayout: 'islands#icon',
                                //iconLayout: 'islands#dotIcon',
                                iconLayout: 'islands#circleIcon',
                                //iconColor: '#1E98FF',
                                iconColor: color,
                                //iconImageHref: '/img/map-point.png',
                                iconImageSize: [16, 16],
                                iconImageOffset: [-8, -8],
                                // Определим интерактивную область над картинкой.
                                iconShape: {
                                    type: 'Circle',
                                    coordinates: [0, 0],
                                    radius: 8
                                },
                            });*/

                            var data = new Array();
                            if(order.is_confirmed == 1) {
                                data[data.length] = {weight: 1, color: is_confirm_color};
                            }else {
                                data[data.length] = {weight: 1, color: is_not_confirm_color};
                            }

                            var placemark = new ymaps.Placemark([yandex_point_from.lat, yandex_point_from.long], {
                                hintContent: text,
                                balloonContent: text,
                                data: data,
                                iconContent: 1
                                //iconContent: "Диаграмма"
                            }, {
                                iconLayout: 'default#pieChart',
                                // Радиус диаграммы в пикселях.
                                iconPieChartRadius: 15,
                                // Радиус центральной части макета.
                                iconPieChartCoreRadius: 9,
                                // Стиль заливки центральной части.
                                //    iconPieChartCoreFillStyle: '#ffffff',
                                // Cтиль линий-разделителей секторов и внешней обводки диаграммы.
                                //    iconPieChartStrokeStyle: '#ffffff',
                                // Ширина линий-разделителей секторов и внешней обводки диаграммы.
                                //    iconPieChartStrokeWidth: 3,
                                // Максимальная ширина подписи метки.
                                //    iconPieChartCaptionMaxWidth: 200
                            });

                        }

                        trips_map.geoObjects.add(placemark);
                    }

                    // после создания точке на карте обновим их видимость
                    //showHidePlacemarks(trips_map, trips_map.getZoom(), response.city.all_points_show_scale);
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

        return false;
    });


    $(document).on('click', '#cancel-trip-sended', function() {

        var html =
            '<div class="row">' +
                '<div class="col-sm-8 form-group form-group-sm">' +
                    '<div class="form-group required">' +
                        '<label class="control-label" for="password">Введите пароль root`а</label>' +
                        '<input id="password" class="form-control" name="password" value="" maxlength="50" type="password">' +
                    '</div>' +
                '</div>' +
            '</div>' +
            '<div class="form-group">' +
                '<button id="cancel-trip-sended-submit" type="button" class="btn btn-primary">Отменить отправки рейса и машин</button>' +
            '</div>';

        $('#default-modal').find('.modal-body').html(html);
        $('#default-modal').find('.modal-dialog').width('600px');
        $('#default-modal .modal-title').text('Подтвердите право на действие');
        $('#default-modal').modal('show');

        return false;
    });


    $(document).on('click', '#cancel-trip-sended-submit', function() {
        //alert('отправляем пароль');
        var password = $.trim($('#password').val());
        var trip_id = $('#cancel-trip-sended').attr('trip-id');


        $.ajax({
            url: '/trip/ajax-cancel-trip-transports-sended?trip_id=' + trip_id + '&password='+password,
            type: 'post',
            data: {},
            success: function (data) {
                if (data.success == true) {

                    alert('Готово');
                    $('#default-modal').modal('hide');
                    // updateTripOrdersPage(); // и обновляем всю страницу

                } else {
                    alert('Неизвестная ошибка отмены рейса');
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


    // печать рейса
    $(document).on('click', '#print-trip-orders', function() {
        // на рейсе таком-то столько-то мест в стольких-то заказах.
        // Сколько пустых строк нужно дополнительно? В поле по умолчанию поставить 10 и дать возможность вводить

        // нужно:
        // название рейса, например: АК 23.04.2018 10:00 (09:00, 09:30, 10:00)
        // количество заказов, не отмененных
        // количество мест в неотменных заказах
        // - все данные можно извлечь из html и посчитать в js
        var trip_name = $('#reis-panel').attr('reis-name');

        var orders_count = 0;
        var total_places_count = 0;
        var order_status = '';
        var order_places_count = 0;
        $('#orders-grid-container tbody').find('tr').each(function() {
            order_status = $(this).attr('order-status');
            if(order_status != 'canceled') {
                orders_count++;
                order_places_count = parseInt($(this).find('.col-places-count').attr('places-count'));
                if(order_places_count > 0) {
                    total_places_count += order_places_count;
                }
            }
        });

        // далее нужно отобразить форму с полем для количества пустых строк. При нажатии ок, откроется
        //   ссылка в новой вкладке с параметром кол-ва пустых строк. Всё делаю внутри html|js, без запросов на сервер

        var html =
            '<div class="row">' +
                '<div class="col-sm-12 form-group form-group-sm">' +
                    '<div class="form-group">' +
                        'На рейсе &laquo;' + trip_name + '&raquo; ' + total_places_count + getStringWithEnd(total_places_count, ' мест', ' место', ' места') + ' в ' + orders_count + getStringWithEnd(orders_count, ' заказов', ' заказ', ' заказах') + '.<br /><br />' +
                        'Сколько пустых строк нужно дополнительно?<br />' +
                        '<input id="empty_rows_count" class="form-control" name="empty-rows-count" maxlength="`0" type="text" value="10">' +
                    '</div>' +
                '</div>' +
            '</div>' +
            '<div class="row">' +
                '<div class="col-sm-12 form-group form-group-sm">' +
                    '<div class="form-group">' +
                        '<button id="open-print-trip-orders" type="button" class="btn btn-primary">Открыть страницу печати</button>' +
                    '</div>'
                '</div>' +
            '</div>';

        $('#default-modal').find('.modal-body').html(html);
        $('#default-modal').find('.modal-dialog').width('600px');
        $('#default-modal .modal-title').text('Печать рейса');
        $('#default-modal').modal('show');


        // логируем открытие модального окна для печати
        $.ajax({
            url: '/dispatcher-accounting/ajax-create-log-open-print-modal',
            type: 'post',
            data: {
                //value: value
            },
            success: function (data) {

                if (data.success != true) {
                    alert('Ошибка изменения поля');
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


    $(document).on('click', '#open-print-trip-orders', function() {

        // закроем модальное окно
        $('#default-modal').modal('hide');

        var trip_id = $('#trip-orders-page').attr('trip-id');
        var empty_rows_count = parseInt($('#default-modal #empty_rows_count').val());

        window.open("/trip/print?id=" + trip_id + '&empty_rows_count=' + empty_rows_count);
    });



    // печать рейса
    $(document).on('click', '#export-to-csv', function() {

        var html_select_options = '';
        var i = 0;
        $('#trip-transports-grid .confirmed-transport').each(function() {
            var transport_name = $(this).attr('transport-name');
            var transport_id = $(this).attr('data-key');
            html_select_options += '<option value="' + transport_id + '">' + transport_name + '</option>';
        });


        var total_places_count = 0;

        var html =
            '<div class="row">' +
                '<div class="col-sm-8 form-group form-group-sm">' +
                    '<div class="form-group">' +
                        'Выберите машину<br />' +
                            '<select id="export-transport-list" class="form-control">' +
                            '<option value="">---</option>' +
                            html_select_options +
                        '</select>' +
                    '</div>' +
                '</div>' +
            '</div>' +
            '<div class="row">' +
                '<div class="col-sm-12 form-group form-group-sm">' +
                    '<div class="form-group">' +
                        '<button id="export-trip-transport-passengers" type="button" class="btn btn-primary">Экспортировать</button>' +
                    '</div>'
                '</div>' +
            '</div>';

        $('#default-modal').find('.modal-body').html(html);
        $('#default-modal').find('.modal-dialog').width('600px');
        $('#default-modal .modal-title').text('Экспорт пассажиров текущего рейса');
        $('#default-modal').modal('show');


        return false;
    });


    $(document).on('click', '#export-trip-transport-passengers', function() {
        var trip_transport_id = $('#export-transport-list').val();

        //alert('trip_transport_id='+trip_transport_id);

        if(trip_transport_id > 0) {
            location.href = '/trip-transport/export-to-csv?id=' + trip_transport_id;
        }else {
            alert('Выберите машину');
        }
    });



    $(document).on('click', '.time-confirm-auto', function() {

        var new_time_confirm = $(this).text();
        var order_id = $(this).parents('tr').attr('order-id');

        setTimeConfirm(order_id, new_time_confirm);

        return false;
    });


    /*
    // посадить в машину после отправки рейса
    //var allow_put_into_transport_button = true;
    var allow_send_order_to_trip_button = [];
    $(document).on('click', '.send-order-to-trip', function()
    {
        var order_id = $(this).parents('tr').attr('order-id');

        if(allow_send_order_to_trip_button[order_id] == undefined || allow_send_order_to_trip_button[order_id] == true) {

            $.ajax({
                url: '/order/ajax-send-order-to-trip?id=' + order_id,
                type: 'post',
                data: {},
                beforeSend: function () {
                    allow_send_order_to_trip_button[order_id] = false;
                },
                success: function (data) {

                    allow_send_order_to_trip_button[order_id] = true;
                    if (data.success == true) {
                        // updateTripOrdersPage(); // и обновляем всю страницу

                        //if($(".call-block").length > 0) {
                        //    updateCallClientForm();
                        //}
                        updateCallModal();

                    } else {
                        alert('Ошибка изменения поля');
                    }
                },
                error: function (data, textStatus, jqXHR) {
                    allow_send_order_to_trip_button[order_id] = true;
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
            // LogDispatcherAccounting('кнопка «Посадить в машину»');
        }

        return false;
    });


    // посадить в машину после отправки т/с
    var allow_send_order_to_transport_button = [];
    $(document).on('click', '.send-order-to-transport', function()
    {
        var order_id = $(this).parents('tr').attr('order-id');

        if(allow_send_order_to_transport_button[order_id] == undefined || allow_send_order_to_transport_button[order_id] == true) {

            $.ajax({
                url: '/order/ajax-send-order-to-transport?id=' + order_id,
                type: 'post',
                data: {},
                beforeSend: function () {
                    allow_send_order_to_transport_button[order_id] = false;
                },
                success: function (data) {

                    allow_send_order_to_transport_button[order_id] = true;
                    if (data.success == true) {
                        // updateTripOrdersPage(); // и обновляем всю страницу

                        //if($(".call-block").length > 0) {
                        //    updateCallClientForm();
                        //}
                        updateCallModal();

                    } else {
                        alert('Ошибка изменения поля');
                    }
                },
                error: function (data, textStatus, jqXHR) {
                    allow_send_order_to_transport_button[order_id] = true;
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
            // LogDispatcherAccounting('кнопка «Посадить в машину»');
        }

        return false;
    });
    */

    // нажатие на кнопку делает заказ оплаченным и формирует запрос в litebox для получения чека
    $(document).on('click', '.but-pay-and-make-check', function() {

        var order_id = $(this).parents('tr').attr('order-id');

        $.ajax({
            url: '/order/ajax-pay-and-make-check?order_id=' + order_id,
            type: 'post',
            data: {},
            success: function (response) {

                if (response.success == true) {
                    alert('Заказ оплачен. Запрос на чек сформирован');

                }else {
                    alert('Не получилось');
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

    });
});
