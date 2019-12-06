/* jshint strict: false */
/* js-код используемый и на сайте, и в админке */

MAP_SEARCH_SCALE = 16;

// глобальная переменная - определяет находиться ли пользователь именно на текущей отктой странице
current_page_is_active = true;

calls = [];


var seconds = new Date().getSeconds();
setInterval(function()
{
    var now = new Date();
    var now_seconds = now.getSeconds();

    //console.log('seconds='+seconds+' now_seconds='+now_seconds);
    if(now_seconds != seconds) { // срабатывает раз в секунду условие
        seconds = now_seconds;
        //console.log('now_seconds='+now_seconds);
        $('#calls-window .time[active="true"]').each(function() {
            var sec = parseInt(0);
            sec++;
            if(sec < 10) {
                sec = '0' + sec;
            }
            $(this).text(sec + ' сек');
        });
    }
}, 200); // погрешность (каждые 200 мл.секунды проверяем не прошла ли секунда)



function updateCallModal() {

    console.log('updateCallModal()');

    if($('#calls-window').length == 0) {
        return false;
    }

    var call_id = $('#calls-window').attr('call-id');
    var url = '';
    if(call_id > 0) {
        url = '/call/get-call-window?call_id=' + call_id; // + "&without_json=1";
    }else {
        var operand_phone = $('#client-mobile_phone').val();
        if(operand_phone == '') {
            alert('Нельзя обновить окно заказов, нет id звонка и нет номера телефона');
            return false;
        }
        url = '/call/get-call-window?operand_phone=' + operand_phone;// + "&without_json=1";
    }

    $.ajax({
        url: url,
        type: 'post',
        //data: data,
        data: {},
        success: function (response) {
            //$('#call-page').after(response.html).remove();
            //$('#inner-call-window .modal-body').html(response.html);
            //$('#inner-call-window .modal-body').html(response);

            $('#inner-call-window #call-page').html(response.html);
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


function showInnerCallModal(call_id, operand_phone) {

    console.log('showInnerCallModal call_id=' + call_id + ' operand_phone=' + operand_phone);

    var url = '';
    if(void 0 !== operand_phone && operand_phone != undefined) {
        url = "/call/get-call-window?operand_phone=" + operand_phone + "&without_json=1";
    }else {
        url = "/call/get-call-window?call_id=" + call_id + "&without_json=1";
    }


    $.ajax({
        url: url,
        type: 'post',
        data: {},
        success: function (response) {
            //if(response.success == true) {
            //    // console.log('openCallWindow new');
            //    // _openCallWindow(url, response.html);
            //    // show_call_modal_is_active = false;
            //
            //    $('#inner-call-window .modal-body').html(response.html);
            //    $('#inner-call-window').show();
            //}

            $('#inner-call-window .modal-body').html(response);
            $('#inner-call-window').show();
        },
        error: function (data, textStatus, jqXHR) {

            // show_call_modal_is_active = false;

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
}



function closeCallModalWithDelay() {

    setTimeout(function(){
        $('#inner-call-window .modal-close').click();
    }, 3000);

    //if(call_window != null) {
    //    console.log('Закрытие мод.окна через 5 секунд');
    //    setTimeout(function () {
    //        call_window.close();
    //    }, 5000);
    //}else {
    //    console.log('Нельзя закрыть окно. ');
    //}
}

function deactivateMissedCall(operand) {
    if($('#missed-call-list').length > 0) {
        $('.missed-call[operand="' + operand + '"]').addClass('disable');
    }
}

function activateMissedCall(operand, call_status) {

    if($('#missed-call-list').length > 0) {
        if (call_status == 'successfully_completed') {
            $('.missed-call[operand="' + operand + '"]').remove();
        }else {
            $('.missed-call[operand="' + operand + '"]').removeClass('disable');
        }
    }
}


//console.log('WebSocket бущет вызван');
//var user = '85ae20ab477977a9d3772b0e64f24538';
//console.log('url=' + location.href + ' user=' + user + ' socket_url='+socket_url);
ws = new WebSocket(socket_url + '/?url=' + location.href + '&user=' + user);
ws.onmessage = function(evt) {

    // в текущую страницу может придти команда, и на следующей секунде такая же команда - вторая дублирующая
    // команда не должна выполняться
    //console.log('mes=' + evt.data);

    var message_data = JSON.parse(evt.data);
    switch (message_data.command) {

        case 'logout':
            console.log('пришел выход!!!');
            location.reload();
            break;

        case 'updateTripOrdersPage()':
            updateTripOrdersPage();
            console.log('перезагрузка контента страницы рейса');
            //alert('страница обновляется');
            //alert('вызывается функция updateTripOrdersPage()');
            break;

        case 'updateDirectionsTripBlock()':
            if($('#call-page').length > 0) {

            }else {
                updateDirectionsTripBlock();
                console.log('перезагрузка контента Главной страницы');
            }
            break;

        case 'updateSetTripsPage()':
            updateSetTripsPage();
            console.log('перезагрузка страницы Расстановка');
            break;

        //case 'updateClientextBlock()':
        //    updateClientextBlock();
        //    console.log('обновление блока кол-ва заявок на всех страницах сайта');
        //    break;

        case 'updateChat()':
            updateChat();
            console.log('Обновляется блок чата с уведомлениями на всех страницах сайта');
            break;


        // что-то изменилось в поступающих заявко-заказах
        case 'updateIncomingClientextOrders':
            console.log('updateIncomingClientextOrders');
            console.log(message_data);
            $('#incoming-clientext-orders-modal').html(message_data.data.incoming_clientext_orders_modal_html);
            //$('#incoming-orders-widget .incoming-orders-count').text(message_data.data.incoming_clientext_orders_count);

            break;

        case 'updateIncomingRequestOrders':

            console.log('updateIncomingRequestOrders');
            $('#incoming-request-orders-modal').html(message_data.data.incoming_request_orders_modal_html);
            $('#incoming-orders-widget .incoming-orders-count').text(message_data.data.incoming_request_orders_count);

            break;

        // изменилось в активных рейсах что-то
        case 'updateActiveTrips':

            $('#active-trips-modal').html(message_data.data.active_trips_modal_html);
            if(message_data.data.direction1_has_bad_trip == true) {
                $('#incoming-orders-widget .direction-1').addClass('red');
            }else {
                $('#incoming-orders-widget .direction-1').removeClass('red');
            }
            $('#incoming-orders-widget .direction-1 .orders-count').text(message_data.data.direction1_trips_count);

            if(message_data.data.direction2_has_bad_trip == true) {
                $('#incoming-orders-widget .direction-2').addClass('red');
            }else {
                $('#incoming-orders-widget .direction-2').removeClass('red');
            }
            $('#incoming-orders-widget .direction-2 .orders-count').text(message_data.data.direction2_trips_count);

            break;

        case 'sendMsgToOperator':
            console.log('sendMsgToOperator');
            console.log('message_data.data:');console.log(message_data.data);

            var height = 0;
            if($('.msg-from-driver').length == 0) {
                height = parseInt($('#incoming-orders-widget').offset().top);
            }else {
                var $last_msg_block = $('.msg-from-driver:last');
                height = parseInt($last_msg_block.offset().top) + parseInt($last_msg_block.outerHeight(true)) + 5;
                // console.log('offset_top = ' + $last_msg_block.offset().top + ' outerHeight = ' + $last_msg_block.outerHeight(true));
            }


            var html =
            '<div class="msg-from-driver" style="top: ' + height + 'px;" chat_id="' + message_data.data.chat_id + '">' +
                '<div class="modal-title"><span>' + message_data.data.title + '</span>&nbsp;<button type="button" class="modal-close">×</button></div>' +
                '<div class="modal-body">' + message_data.data.msg + '<div class="answer-block"><textarea class="answer" rows="1"></textarea><br /><input class="send-to-driver-answer" type="button" value="Ответить" /></div></div>' +
            '</div>';

            //$('#msg-from-driver .modal-body').text(message_data.data.msg);
            //$('#msg-from-driver .modal-title span').text(message_data.data.title);
            //$('#msg-from-driver').show();
            $('body').children('div:last').before(html);
            ion.sound.play("metal_plate_2");
            break;

        // изменилось количество пропущенных звонков
        case 'updateMissedCallsCount':

            console.log('updateMissedCallsCount');
            console.log('message_data:'); console.log(message_data);

            $('#missed-calls .missed-calls-count').text(message_data.data.missed_cases_count);
            if(message_data.data.missed_cases_count > 0) {
                if($('#missed-calls').hasClass('active') == false) {
                    $('#missed-calls').addClass('active');
                }
            }else {
                if($('#missed-calls').hasClass('active') == true) {
                    $('#missed-calls').removeClass('active');
                }
            }
            //console.log('message_data:'); console.log(message_data);

            break;


        // изменилось количество входящих звонков
        case 'updateIncomingCallsCount':

            console.log('incoming_calls_count=' + message_data.data.incoming_calls_count);
            if (typeof setIncomingCallsCount == 'function') {
                console.log('функция setIncomingCallsCount существует');
                setIncomingCallsCount(message_data.data.incoming_calls_count);
            }else {
                console.log('функция setIncomingCallsCount НЕ существует');
            }
            break;


        //case 'updateActiveCall':
        //
        //    console.log('updateActiveCall=' +message_data.data.is_active_calling);
        //    if(message_data.data.is_active_calling == true) {
        //        $('#is-calling').addClass('active');
        //    }else {
        //        $('#is-calling').removeClass('active');
        //    }
        //    break;

        case 'openCallWindow':

            console.log('openCallWindow'); // по непонятным причинам лог не выводиться в окно браузера
            //_openCallWindow(message_data.data.new_page_url, message_data.data.html);

            $('#inner-call-window .modal-body').html(message_data.data.html);
            $('#inner-call-window').show();

            break;


        // появление/сброс звонка
        case 'updateCall':

            //console.log('message_data:'); console.log(message_data);
            var data = message_data.data;
            console.log('event_name=' + data.event_name);


            switch(data.event_name) {

                case 'input_call_created_by_client': // поступил звонок от клиента - дубли могут приходить
                    console.log('Входящий дозвон от клиента');

                    var is_new_call = true;
                    for(var key in calls) {
                       if(calls[key] == data.call_id) {
                           is_new_call = false;
                           break;
                       }
                    }
                    if(is_new_call == true) {
                        calls.push(data.call_id);
                    }

                    deactivateMissedCall(data.client_phone);

                    break;


                case 'output_call_created_by_operator': // Был создан оператором исходящий звонок
                    console.log('Был создан оператором исходящий звонок');

                    //var is_new_call = true;
                    //for(var key in calls) {
                    //    if(calls[key] == data.call_id) {
                    //        is_new_call = false;
                    //        break;
                    //    }
                    //}
                    //if(is_new_call == true) {
                    //    calls.push(data.call_id);
                    //    if(current_page_is_active == true) {
                    //        showCallModal(data);
                    //    }
                    //}

                    deactivateMissedCall(data.client_phone);

                    break;


                case 'input_call_accepted_by_operator':
                    console.log('Оператор взял трубку на входящем звонке от клиента');

                    //if(current_page_is_active == true) {
                    //    console.log('текущая вкладка активна');
                    //}else {
                    //    console.log('текущая вкладка не активна');
                    //}
                    //
                    //if(current_page_is_active == true) {
                    //    showCallModal(data);
                    //}

                    $('#is-calling').addClass('active');
                    $('#is-calling').attr('client_phone', data.client_phone);
                    $('#is-calling').attr('call_id', data.call_id);

                    break;

                case 'output_call_accepted_by_client': // Был принят клиентом исходящий вызов, начат разговор
                    console.log('Был принят клиентом исходящий вызов, начат разговор');

                    // появление красной точки
                    $('#is-calling').addClass('active');
                    $('#is-calling').attr('client_phone', data.client_phone);
                    $('#is-calling').attr('call_id', data.call_id);

                    // в окне звонков запускаем счет времени разговора
                    $('#call-page .call-block[call-id="' + data.call_id + '"]').find('.time').attr('active', "true");

                    break;


                // по этому событию сигнал с сервера не приходит, т.к. оператор остается висеть на линии когда оператор пытается сбросить входящий
                case 'input_call_cancelled_by_operator':
                    console.log('input_call_cancelled_by_operator - событие которое не обрабатывается');
                    break;

                case 'input_call_cancelled_by_client':

                    console.log('Отменен клиентом входящий вызов (свой вызов отменил)');

                    calls.pop(data.call_id);
                    //incomingCalls.pop(data.call_id);
                    //setIncomingCallsCount(incomingCalls.length);

                    closeCallModalWithDelay();
                    activateMissedCall(data.client_phone, data.call_status);

                    break;

                case 'output_call_cancelled_by_operator': // Сброшен/отменен оператором исходящий вызов (свой вызов прервал)
                    console.log('Сброшен/отменен оператором исходящий вызов (свой вызов прервал)');

                    calls.pop(data.call_id);
                    closeCallModalWithDelay();
                    activateMissedCall(data.client_phone, data.call_status);

                    break;

                case 'output_call_cancelled_by_client': // Сброшен/отменен клиентом исходящий вызов  (оператора вызов прервал)
                    console.log('Сброшен/отменен клиентом исходящий вызов  (свой вызов прервал)');

                    calls.pop(data.call_id);
                    closeCallModalWithDelay();
                    activateMissedCall(data.client_phone, data.call_status);

                    break;

                case 'call_finished_by_operator':

                    // звонок завершен
                    console.log('Звонок завершен оператором');
                    $('#is-calling').removeClass('active');
                    $('#is-calling').removeAttr('client_phone');

                    calls.pop(data.call_id);
                    closeCallModalWithDelay();
                    activateMissedCall(data.client_phone, data.call_status);

                    // в окне звонков останавливаем счет времени разговора
                    $('#call-page .call-block[call-id="' + data.call_id + '"]').find('.time').attr('active', "false");
                    $('#call-page .call-block[call-id="' + data.call_id + '"]').find('.time').html(data.speaking_seconds + ' сек');

                    break;

                case 'call_finished_by_client':

                    // звонок завершен
                    console.log('Звонок завершен клиентом');
                    $('#is-calling').removeClass('active');
                    $('#is-calling').removeAttr('client_phone');

                    calls.pop(data.call_id);

                    closeCallModalWithDelay();
                    activateMissedCall(data.client_phone, data.call_status);

                    // в окне звонков останавливаем счет времени разговора
                    $('#call-page .call-block[call-id="' + data.call_id + '"]').find('.time').attr('active', "false");
                    $('#call-page .call-block[call-id="' + data.call_id + '"]').find('.time').html(data.speaking_seconds + ' сек');
                    break;


                case 'operator_temporarily_unavailable': // Оператор временно недоступен
                    console.log('Оператор временно недоступен');

                    // отключаю мигающую зеленую лампочку у всех,
                    // следом мгновенно какой-нибудь input_call_created_by_client для другого оператора или повторный для этого оператора
                    // может включить снова мигание лампочти на текущем звонке


                    break;

                case 'call_from_ats_to_operator': // АТС звонит к оператору
                    console.log('АТС звонок к оператору');

                    // это может быть как первый сигнал от АТС при начале звонка без номера телефона,
                    // либо это повторный сигнал уже с телефоном клиента,
                    // либо первым был output_call_created_by_operator

                    var is_new_call = true;
                    for(var key in calls) {
                        if(calls[key] == data.call_id) {
                            is_new_call = false;
                            break;
                        }
                    }
                    if(is_new_call == true) {
                        console.log('новый звонок');
                        calls.push(data.call_id);

                        // всплывает окно начала звонка
                        //if(current_page_is_active == true) {
                        //    showCallModal(data);
                        //}
                    }

                    deactivateMissedCall(data.client_phone);

                    break;

                case 'output_call_dial_to_client': // Был принят оператором исходящий вызов от АТС, начался дозвон до клиента
                    console.log('Был принят оператором исходящий вызов');
                    break;

                case 'output_call_cancelled_by_ats': // АТС не дождолась оператора и сбросила исходящий вызов
                    console.log('АТС не дождолась оператора и сбросила исходящий вызов');

                    calls.pop(data.call_id);

                    closeCallModalWithDelay();
                    activateMissedCall(data.client_phone, data.call_status);
                    break;

                case 'server_failure': // Ошибка на сервере в АТС или АТС сбросила соединение
                    console.log('server_failure - АТС сбросила соединение');

                    calls.pop(data.call_id);
                    closeCallModalWithDelay();
                    activateMissedCall(data.client_phone, data.call_status);
                    break;
            }


            break;


        case 'setAnswerForDriverMessage':

            console.log('setAnswerForDriverMessage');
            var data = message_data.data;

            if (typeof setAnswerForDriverMessage == 'function') {
                setAnswerForDriverMessage(data.chat_id, data.answer)
            }

            break;

        case 'closeMessage':

            console.log('closeMessage');
            var data = message_data.data;

            //$('.msg-from-driver[chat_id="' + data.chat_id + '"]').remove();
            $('.msg-from-driver[chat_id="' + data.chat_id + '"] .modal-close').click();

            break;
    }
};



// в случае если в json-запросе происходит ошибка и она не обрабатывается стандартным образом, то вызывается эта функция
function handlingAjaxError(data, textStatus, jqXHR) {
    console.log('handlingAjaxError data:'); console.log(data);
    console.log('textStatus='+textStatus); console.log(data);
    console.log('jqXHR:'); console.log(jqXHR);
}


$(window).focus(function() { //Во вкладке
    //console.log('во вкладке');
    current_page_is_active = true;
});
$(window).blur(function() { //Покинули вкладку
    //console.log('покинули вкладку');
    current_page_is_active = false;
});



$(document).ready(function() {

    //current_page_is_active = true;

    if($('#call-page').length > 0) {
        //alert('перезапуск');
        //location.reload();
        var call_id = $('#calls-window').attr('call-id');
        //call_window = window.open("/call/get-call-window?id=" + call_id, "Окно звонка2", "width=1000,height=800");
    }else {
        //alert('call_window не существует');
    }

    ion.sound({
        sounds: [
            {name: "beer_can_opening"},
            {name: "bell_ring"},
            {name: "metal_plate_2"}
        ],
        path: "/js/ion-sound/sounds/",
        preload: true,
        volume: 1.0
    });


    //ion.sound.play("bell_ring");
    //ion.sound.play("metal_plate_2");

    // обновление часов в верхнем меню
    var minutes = new Date().getMinutes();
    setInterval(function()
    {
        var now = new Date();
        var now_minutes = now.getMinutes();
        if(now_minutes != minutes) {
            minutes = now_minutes;

            // обновим часы
            $.ajax({
                url: '/site/get-ajax-time',
                type: 'post',
                data: {},
                contentType: false,
                cache: false,
                processData: false,
                success: function (response) {
                    if(response.success == true) {
                        $('#system-time').html(response.time);
                        if(
                            (location.pathname == '/' || location.pathname == '/trip/set-trips' || location.pathname == '/trip/trip-orders')
                            && response.restart_page == true
                        ) {
                            alert('Наступил новый день');
                            location.reload();
                        }
                    }else {
                        alert('ошибка');
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
    }, 1000);
});

// format date_str - 'dd.mm.YYYY' (30.01.2017)
function getDateObject(date_str) {

    var objDate = new Date(parseInt(date_str.substr(6, 4)), parseInt(date_str.substr(3, 2)) - 1, parseInt(date_str.substr(0, 2)));

    return objDate;
}

function getWeekDay(objDate)
{
    var weekDayNum = objDate.getDay();

    var weekDay = '';
    switch (weekDayNum) {
        case 0:
            weekDay = 'воскресенье';
            break;
        case 1:
            weekDay = 'понедельник';
            break;
        case 2:
            weekDay = 'вторник';
            break;
        case 3:
            weekDay = 'среда';
            break;
        case 4:
            weekDay = 'четверг';
            break;
        case 5:
            weekDay = 'пятница';
            break;
        case 6:
            weekDay = 'суббота';
            break;
    }

 return weekDay;
}

// Функция первую букву строки string переводит в верхний регистр
function toUpperCaseFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

// использование Math.round() даст неравномерное распределение!
function getRandomInt(min, max)
{
    return Math.floor(Math.random() * (max - min + 1)) + min;
}


// функция возвращает окончания строки перед которой указано число. Например 7 заказ'ов'
function getStringWithEnd(num, zero_string, one_string, two_string) {

    var string = '';

    // получаем последнюю цифру числа
    var str_num = '' + num;
    var finish_number = str_num[(str_num.length - 1)];

    //console.log('num=' + num + ' finish_number='+finish_number);
    if(finish_number == 0 || finish_number == 5 || finish_number == 6 || finish_number == 7
        || finish_number == 8 || finish_number == 9) {
        string = zero_string;
    }else {
        if(num >= 10) {
            var prev_finish_number = str_num[(str_num.length - 2)];
            if(prev_finish_number == 1) {
                string = zero_string;
            }else {
                if(finish_number == 1) {
                    string = one_string;
                }else { // остались 2,3,4
                    string = two_string;
                }
            }
        }else {
            if(finish_number == 1) {
                string = one_string;
            }else { // остались 2,3,4
                string = two_string;
            }
        }
    }

    return string;
}



/* **********   ФУНКЦИИ ДЛЯ ЯНДЕКС-КАРТЫ    **************/

// можно ли редактировать данные точки
function getIsAllowedEditParam() {

    if($('.city-update').length > 0) {
        return true;
    }else {
        return false;
    }
}

// можно ли перемещать точку
function getDraggable(yandex_point_id) {

    if($('#order-create-modal').length > 0) {
        //if (yandex_point_id > 0) {
        //    var draggable = false;
        //} else {
        //    var draggable = true;
        //}
        var draggable = false;
    }else if($('.city-update').length > 0) {
        var draggable = true;
    }else {
        var draggable = false;
    }

    return draggable;
}


// функция возвращает html-контент для точки

// is_editing - true/false/не передается - форма с "раскрытыми" полями для редактирования
// can_change_params - true/false/не передается - изменение дополнительных параметров
// is_allowed_edit - true/false/не передается - разрешение на возможность активизации редактирования данных
// create_new_point - true/false/не передается - параметр в аттрибутах шаблона, обозначающий что после нажатия "ок" будет создана точка

// point_text - текст яндекс-точки
// index - индекс элемента на карте (point_placemark = map.geoObjects.get(index);)
// point_id - id точки
// critical_point - true/false  (была 1) - критическая ли точка
// alias - элиас точки - текст

// function getPlacemarketTemplate(point_text, index, point_id, is_editing, create_new_point, can_change_params, critical_point, alias) {

function getPlacemarketTemplate(params) {

    if(params['point_text'] == undefined) {
        params['point_text'] = '';
    }
    if(params['point_description'] == undefined) {
        params['point_description'] = '';
    }
    if(params['index'] == undefined) {
        params['index'] = '';
    }
    if(params['point_id'] == undefined) {
        params['point_id'] = 0;
    }
    if(params['critical_point'] != 1) {
        params['critical_point'] = 0;
    }
    if(params['popular_departure_point'] != 1) {
        params['popular_departure_point'] = 0;
    }
    if(params['popular_arrival_point'] != 1) {
        params['popular_arrival_point'] = 0;
    }
    if(params['external_use'] != 1) {
        params['external_use'] = 0;
    }
    if(params['point_of_arrival'] != 1) {
        params['point_of_arrival'] = 0;
    }
    if(params['super_tariff_used'] != 1) {
        params['super_tariff_used'] = 0;
    }
    if(params['alias'] == undefined) {
        params['alias'] = '';
    }

    if(params['is_editing'] != true) {
        params['is_editing'] = false;
    }
    if(params['can_change_params'] != true) {
        params['can_change_params'] = false;
    }
    if(params['is_allowed_edit'] != true) {
        params['is_allowed_edit'] = false;
    }
    if(params['create_new_point'] != true) {
        params['create_new_point'] = false;
    }
    //if(params['is_temp_point'] != true) {
    //    params['is_temp_point'] = false;
    //}
    //if(params['is_base_point'] != true) {
    //    params['is_base_point'] = false;
    //}


    if(params['can_change_params'] == true) {
        params['is_editing'] = true;
    }


    if(params['is_allowed_edit'] == false) {

        var content =
            '<div class="placemark-balloon-content" index="' + params['index'] + '" yandex-point-id="' + params['point_id'] + '">';
        content +=
            '<input class="input-placemark" style="display: none;" type="text" value="' + params['point_text'] + '" />' +
            '<span class="span-placemark not-edit">' + params['point_text'] + '</span>' +
            '</div>';// point_description

    }else if(params['is_editing'] == true) {

        var content =
            '<div class="placemark-balloon-content" index="' + params['index'] + '" yandex-point-id="' + params['point_id'] + '" create-new-point="' + params['create_new_point'] + '">';
        if(params['can_change_params'] == true) {

            content +=  '<input class="external-use" type="checkbox" ' + (params['external_use'] == true ? "checked" : "") + ' /> внешнее использование (да/нет) <br />' +
                        '<input class="point-of-arrival" type="checkbox" ' + (params['point_of_arrival'] == true ? "checked" : "") + ' /> является точкой прибытия <br />' +
                        '<input class="super-tariff-used" type="checkbox" ' + (params['super_tariff_used'] == true ? "checked" : "") + ' /> применяется супер тариф <br />' +
                        '<input class="critical-point" type="checkbox" ' + (params['critical_point'] == true ? "checked" : "") + ' /> критическая точка <br />' +
                        '<input class="popular-departure-point" type="checkbox" ' + (params['popular_departure_point'] == true ? "checked" : "") + ' /> популярная точка отправления <br />' +
                        '<input class="popular-arrival-point" type="checkbox" ' + (params['popular_arrival_point'] == true ? "checked" : "") + ' /> популярная точка прибытия <br />' +
                        '<input class="alias" type="text" value="' + params['alias'] + '" placeholder="airport" /><br />';
        }
        content +=
                '<input class="input-placemark" type="text" value="' + params['point_text'] + '" />' +
                '<input class="input-description" type="text" placeholder="Описание" value="' + params['point_description'] + '" />' +
                '<button class="ok-placemark">Ок</button>' +
                '<span class="span-placemark" style="display: none;">' + params['point_text'] + '</span>' +
            '</div>';

    }else {

        var content =
            '<div class="placemark-balloon-content" index="' + params['index'] + '" yandex-point-id="' + params['point_id'] + '" create-new-point="' + params['create_new_point'] + '">';

            if(params['can_change_params'] == true) {
                content +=  '<input class="external-use" type="checkbox" ' + (params['external_use'] == true ? "checked" : "") + ' /> внешнее использование (да/нет) <br />' +
                            '<input class="point-of-arrival" type="checkbox" ' + (params['point_of_arrival'] == true ? "checked" : "") + ' /> является точкой прибытия <br />' +
                            '<input class="super-tariff-used" type="checkbox" ' + (params['super_tariff_used'] == true ? "checked" : "") + ' /> применяется супер тариф <br />' +
                            '<input class="critical-point" type="checkbox" ' + (params['critical_point'] == true ? "checked" : "") + ' /> критическая точка <br />' +
                            '<input class="popular-departure-point" type="checkbox" ' + (params['popular_departure_point'] == true ? "checked" : "") + ' /> популярная точка отправления <br />' +
                            '<input class="popular-arrival-point" type="checkbox" ' + (params['popular_arrival_point'] == true ? "checked" : "") + ' /> популярная точка прибытия <br />' +
                            '<input class="alias" type="text" value="' + params['alias'] + '" placeholder="airport" /><br />';
            }

        content +=
            '<input class="input-placemark" style="display: none;" type="text" value="' + params['point_text'] + '" />' +
            '<input class="input-description" type="text" value="' + params['point_description'] + '" />' +
            '<button class="ok-placemark" style="display: none;">Ок</button>' +
            '<span class="span-placemark">' + params['point_text'] + '</span>' +
            '</div>';

    }

    return content;
}

// извлечение из html-контента названия яндекс-точки
function parseNameFromTemplate(content) {

    var pos_start = content.indexOf('span-placemark') + 14;
    var text = content.substring(pos_start);
    var pos_start = text.indexOf('>') + 1;
    var text = text.substring(pos_start);
    var pos_end = text.indexOf('</span></div>');
    var text = text.substring(0, pos_end);

    return text;
}

function parseDescriptionFromTemplate(content) {

    // var pos_start = content.indexOf('span-placemark') + 14;
    // var text = content.substring(pos_start);
    // var pos_start = text.indexOf('>') + 1;
    // var text = text.substring(pos_start);
    // var pos_end = text.indexOf('</span></div>');
    // var text = text.substring(0, pos_end);
    //
    // return text;

    return '';
}

// извлечение из html-контента id яндекс-точки
function parseIdFromTemplate(content) {

    var pos_start = content.indexOf('yandex-point-id=') + 17;
    var id = content.substring(pos_start);
    var pos_end = id.indexOf('"');
    var id = id.substring(0, pos_end);

    return id;
}

// извлечение из html-контента индекса placemark в коллекции
function parseIndexFromTemplate(content) {

    var pos_start = content.indexOf('index=') + 7;
    var index = content.substring(pos_start);
    var pos_end = index.indexOf('"');
    var index = index.substring(0, pos_end);

    return index;
}

// сохранение изменений в данных точки в полях формы создания/редактирования заказа
function updateOrderFormYandexPoint(point_from, point_id, point_lat, point_long, point_name) {
    var name = point_id + '_' + point_lat + '_' + point_long + '_' + point_name;

    if(point_from == true) {
       $('input[name="Order[yandex_point_from]"]').val(name);
    }else {
        $('input[name="Order[yandex_point_to]"]').val(name);
    }
}



// создание точки на карте
//function createPlacemark(index, point_text, point_id, point_lat, point_long, is_editing, create_new_point, to_select, can_change_params, critical_point, alias) {
//var create_placemark_params = {
//    index: index,
//    point_text: point_text,
//    point_id: point_id,
//    point_lat: point_lat,
//    point_long: point_long,
//    is_editing: is_editing,
//    create_new_point: create_new_point,
//    to_select: to_select,
//    can_change_params: can_change_params,
//    critical_point: critical_point,
//    alias: alias
//};
function createPlacemark(params) {
    //console.log('can_change_params='+can_change_params);

    //var create_placemark_params = {
    //    index: index,
    //    point_text: yandex_point.name,
    //    point_id: yandex_point.id,
    //    point_lat: yandex_point.lat,
    //    point_long: yandex_point.long,
    //    //is_editing: true,
    //    //create_new_point: true,
    //    //to_select: false,
    //    //can_change_params: false,
    //    //critical_point: yandex_point.critical_point,
    //    //alias: yandex_point.alias
    //};

    if(params['index'] == undefined) {
        params['index'] = '';
    }
    if(params['point_text'] == undefined) {
        params['point_text'] = '';
    }
    if(params['point_description'] == undefined) {
        params['point_description'] = '';
    }
    if(params['point_id'] == undefined) {
        params['point_id'] = 0;
    }
    if(params['point_lat'] == undefined) {
        params['point_lat'] = 0;
    }
    if(params['point_long'] == undefined) {
        params['point_long'] = 0;
    }
    if(params['is_editing'] != true) {
        params['is_editing'] = false;
    }
    if(params['create_new_point'] != true) {
        params['create_new_point'] = false;
    }
    if(params['to_select'] != true) {
        params['to_select'] = false;
    }
    if(params['can_change_params'] != true) {
        params['can_change_params'] = false;
    }
    if(params['external_use'] != 1) {
        params['external_use'] = 0;
    }
    if(params['point_of_arrival'] != 1) {
        params['point_of_arrival'] = 0;
    }
    if(params['super_tariff_used'] != 1) {
        params['super_tariff_used'] = 0;
    }
    if(params['critical_point'] != 1) {
        params['critical_point'] = 0;
    }
    if(params['popular_departure_point'] != 1) {
        params['popular_departure_point'] = 0;
    }
    if(params['popular_arrival_point'] != 1) {
        params['popular_arrival_point'] = 0;
    }
    if(params['alias'] == undefined) {
        params['alias'] = '';
    }

    if(params['is_temp_point'] != true) {
        params['is_temp_point'] = false;
    }
    if(params['is_base_point'] != true) {
        params['is_base_point'] = false;
    }



    var placemarket_template_params = {
        point_text: params['point_text'],
        point_description: params['point_description'],
        index: params['index'],
        point_id: params['point_id'],
        external_use: params['external_use'],
        point_of_arrival: params['point_of_arrival'],
        super_tariff_used: params['super_tariff_used'],
        critical_point: params['critical_point'],
        popular_departure_point: params['popular_departure_point'],
        popular_arrival_point: params['popular_arrival_point'],
        alias: params['alias'],
        create_new_point: params['create_new_point'],
        is_editing: params['is_editing'],
        can_change_params: params['can_change_params'],
        is_allowed_edit: getIsAllowedEditParam(),
        is_temp_point: params['is_temp_point'],
        is_base_point: params['is_base_point'],
    };

    var balloonContentHeader = '';
    if(params['is_temp_point'] == true) {
        balloonContentHeader = '<div style="width: 100%; background: #f4f4f4; color: #000000;">&nbsp;&nbsp;Создание разовой точки</div>';
    }else if(params['is_base_point']) {
        balloonContentHeader = '<div style="width: 100%; background: #367FA9; color: #FFFFFF;">&nbsp;&nbsp;Создание опорной точки</div>';
    }

    if(params['to_select'] == false) {
        var hintContent = params['point_text'];

        //var balloonContent = getPlacemarketTemplate(params['point_text'], params['index'], params['point_id'], params['is_editing'], params['create_new_point'], params['can_change_params'], params['critical_point'], alias);
        var balloonContent = getPlacemarketTemplate(placemarket_template_params);
    }else {
        var hintContent = '<button class="btn-select-placemark hint-btn" placemark-index="' + params['index'] + '">Выбрать</button> ' + params['point_text'];
        var balloonContent = getPlacemarketTemplate(placemarket_template_params)
            + '<button class="btn-select-placemark content-btn" placemark-index="' + params['index'] + '">Выбрать</button>';
    }

    var placemark = new ymaps.Placemark([params['point_lat'], params['point_long']], {
        hintContent: hintContent,
        //balloonContentHeader: '<div style="width: 100%; background: #FF0000; color: #FFFFFF;">qqq</div>',
        balloonContentHeader: balloonContentHeader,
        balloonContent: balloonContent,
    }, {
        iconLayout: 'islands#circleIcon',
        iconColor: '#1E98FF',
        iconImageSize: [16, 16],
        iconImageOffset: [-8, -8],
        // Определим интерактивную область над картинкой.
        iconShape: {
            type: 'Circle',
            coordinates: [0, 0],
            radius: 8
        }
    });

    map.geoObjects.add(placemark);

    placemark.events
        .add('dragend', function (event) {

            var coordinates = point_placemark.geometry.getCoordinates();
            var balloonContent = point_placemark.properties.get('balloonContent');
            var index = parseIndexFromTemplate(balloonContent);
            var yandex_point_id = parseIdFromTemplate(balloonContent);

            if(yandex_point_id > 0) {
                //updateYandexPoint(index, yandex_point_id, null, coordinates[0], coordinates[1]);
                var update_yandex_point_params = {
                    index: index,
                    point_id: yandex_point_id,
                    lat: coordinates[0],
                    long: coordinates[1]
                };
                updateYandexPoint(update_yandex_point_params);
            }
            if($('#order-create-modal').length > 0) {
                var point_name = parseNameFromTemplate(balloonContent);
                updateOrderFormYandexPoint(true, yandex_point_id, coordinates[0], coordinates[1], point_name)
            }
        })
        .add('mouseenter', function (e) {
            e.get('target').options.set('iconColor', '#56DB40');

        })
        .add('mouseleave', function (e) {
            e.get('target').options.unset('iconColor');
        });

    return placemark;
}



// Метод реализует выбор точки
// index - индекс элемента на карте (point_placemark = map.geoObjects.get(index);)
// point_text - текст яндекс-точки
// point_id - id точки
// critical_point - true/false  (была 1) - критическая ли точка
// alias - элиас точки - текст
// is_editing - true/false/не передается - форма с "раскрытыми" полями для редактирования
// create_new_point - true/false/не передается - параметр в аттрибутах шаблона, обозначающий что после нажатия "ок" будет создана точка
// can_change_params - true/false/не передается - изменение дополнительных параметров
// is_allowed_edit - true/false/не передается - разрешение на возможность активизации редактирования данных
// draggable - true/false/не передается - может ли точка перемещаться
//function selectPointPlacemark(index, point_text, point_id, is_editing, create_new_point, can_change_params, critical_point, alias, draggable, is_allowed_edit) {
function selectPointPlacemark(params) {

    //select_point_placemark_params = {
    //    index: index,
    //    point_text: point_text,
    //    point_id: point_id,
    //    is_editing: is_editing,
    //    create_new_point: create_new_point,
    //    can_change_params: can_change_params,
    //    critical_point: critical_point,
    //    alias: alias,
    //    draggable: draggable,
    //    is_allowed_edit: is_allowed_edit
    //}

    if(params['index'] == undefined) {
        params['index'] = '';
    }
    if(params['point_text'] == undefined) {
        params['point_text'] = '';
    }
    if(params['point_description'] == undefined) {
        params['point_description'] = '';
    }
    if(params['point_id'] == undefined) {
        params['point_id'] = 0;
    }
    if(params['critical_point'] != 1) {
        params['critical_point'] = 0;
    }
    if(params['popular_departure_point'] != 1) {
        params['popular_departure_point'] = 0;
    }
    if(params['popular_arrival_point'] != 1) {
        params['popular_arrival_point'] = 0;
    }
    if(params['external_use'] != 1) {
        params['external_use'] = 0;
    }
    if(params['point_of_arrival'] != 1) {
        params['point_of_arrival'] = 0;
    }
    if(params['super_tariff_used'] != 1) {
        params['super_tariff_used'] = 0;
    }
    if(params['alias'] == undefined) {
        params['alias'] = '';
    }
    if(params['is_editing'] != true) {
        params['is_editing'] = false;
    }
    if(params['can_change_params'] != true) {
        params['can_change_params'] = false;
    }
    if(params['is_allowed_edit'] == undefined) {
        params['is_allowed_edit'] = getIsAllowedEditParam();
    }
    if(params['create_new_point'] != true) {
        params['create_new_point'] = false;
    }
    if(params['draggable'] != true) {
        params['draggable'] = false;
    }
    if(params['point_focusing_scale'] == undefined) {
        params['point_focusing_scale'] = 0;
    }


    //console.log('selectPointPlacemark');
    unselectOldPointPlacemark();

    point_placemark = map.geoObjects.get(params['index']);

    var hintContent = params['point_text'] + ' (точка выбрана)';

    var placemarket_template_params = {
        point_text: params['point_text'],
        point_description: params['point_description'],
        index: params['index'],
        point_id: params['point_id'],
        critical_point: params['critical_point'],
        popular_departure_point: params['popular_departure_point'],
        popular_arrival_point: params['popular_arrival_point'],
        external_use: params['external_use'],
        point_of_arrival: params['point_of_arrival'],
        super_tariff_used: params['super_tariff_used'],
        alias: params['alias'],
        create_new_point: params['create_new_point'],
        is_editing: params['is_editing'],
        can_change_params: params['can_change_params'],
        is_allowed_edit: params['is_allowed_edit']
    };

    //var balloonContent = getPlacemarketTemplate(point_text, index, point_id, edit, create_new_point, can_change_params, critical_point, alias)
    var balloonContent = getPlacemarketTemplate(placemarket_template_params)
        + ' (точка выбрана)';

    var fio = $.trim($('#client-name').val());
    if(fio == '') {
        fio = 'Выбран';
    }

    point_placemark.properties.set({
        //preset: 'islands#blackStretchyIcon',
        visible: false,
        hintContent: hintContent,
        balloonContent: balloonContent,
        iconContent: fio
    });

    point_placemark.options.unset('iconImageOffset');
    point_placemark.options.unset('iconImageSize');
    point_placemark.options.unset('iconLayout');
    point_placemark.options.unset('iconShape');

    point_placemark.options.set({
        //iconColor: '#1E98FF',
        preset: 'islands#darkGreenStretchyIcon',
        draggable: params['draggable'],
    });
    //point_placemark.balloon.open();

    if(params['point_focusing_scale'] > 0) {
        var coordinates = point_placemark.geometry.getCoordinates();
        map.setCenter(coordinates, params['point_focusing_scale'], {duration: 500});
    }

    return point_placemark;
}


// метод снимает выделение с точки
function unselectOldPointPlacemark() {

    if (point_placemark != null) {

        //var dbSavedPlacemark = point_placemark.options.get('dbSavedPlacemark');

        var balloonContent = point_placemark.properties.get('balloonContent');
        var index = parseIndexFromTemplate(balloonContent);
        var yandex_point_id = parseIdFromTemplate(balloonContent);
        var yandex_point_name = parseNameFromTemplate(balloonContent);
        var yandex_point_description = parseDescriptionFromTemplate(balloonContent);

        var hintContent = '<button class="btn-select-placemark hint-btn" placemark-index="' + index + '">Выбрать</button> ' + yandex_point_name;


        var params = {
            point_text: yandex_point_name,
            point_description: yandex_point_description,
            index: index,
            point_id: yandex_point_id,
            is_allowed_edit: getIsAllowedEditParam()
        };
        //var balloonContent = getPlacemarketTemplate(yandex_point_name, index, yandex_point_id)
        var balloonContent = getPlacemarketTemplate(params)
            + '<button class="btn-select-placemark content-btn" placemark-index="' + index + '">Выбрать</button>';

        point_placemark.properties.set('hintContent', hintContent);
        point_placemark.properties.set('balloonContent', balloonContent);

        if (yandex_point_id == 0) { // вновь созданная точка становиться серой
            point_placemark.options.set({'iconColor': '#BFBFBF'});
        } else {// а у старой точки просто меняются свойства на обычную точку
            point_placemark.options.set({'iconColor': '#1E98FF'});
        }

        point_placemark.options.unset('preset');
        point_placemark.options.unset('draggable');
        point_placemark.properties.unset('iconContent');

        point_placemark.options.set({
            iconLayout: 'islands#circleIcon',
            //iconColor: '#1E98FF',
            iconImageSize: [16, 16],
            iconImageOffset: [-8, -8],
            // Определим интерактивную область над картинкой.
            iconShape: {
                type: 'Circle',
                coordinates: [0, 0],
                radius: 8
            }
        });
    }
}


// отображение/скрытие точек в зависимости от зума карты
function showHidePlacemarks(current_map, map_zoom, all_points_show_scale) {

    //console.log('showHidePlacemarks all_points_show_scale=' + all_points_show_scale + ' map_zoom='+map_zoom);

    var point_placemark_index = -1;
    if(point_placemark != null) {
        var balloonContent = point_placemark.properties.get('balloonContent');
        var point_placemark_index = parseIndexFromTemplate(balloonContent);
    }

    if(current_map != null) {
        if (map_zoom < all_points_show_scale) { // прячу все точки кроме выбранной точки
            current_map.geoObjects.each(function (placemark, i) {
                if (i != point_placemark_index) {
                    placemark.options.set('visible', false);
                }
            })
        } else { // отображаю все точки кроме выбранной
            current_map.geoObjects.each(function (placemark, i) {
                if (i != point_placemark_index) {
                    placemark.options.set('visible', true);
                }
            })
        }
    }
}

// функция создания яндекс-точки
//function createYandexPoint(placemark, can_change_params, critical_point, alias) {
function createYandexPoint(placemark, params) {

    //var create_yandex_point_params = {
    //    can_change_params: can_change_params,
    //    point_of_arrival: point_of_arrival,
    //    critical_point: critical_point,
    //    alias: alias
    //};

    if(params['can_change_params'] != true) {
        params['can_change_params'] = false;
    }
    if(params['external_use'] != 1) {
        params['external_use'] = 0;
    }
    if(params['point_of_arrival'] != 1) {
        params['point_of_arrival'] = 0;
    }
    if(params['critical_point'] != 1) {
        params['critical_point'] = 0;
    }
    if(params['popular_departure_point'] != 1) {
        params['popular_departure_point'] = 0;
    }
    if(params['popular_arrival_point'] != 1) {
        params['popular_arrival_point'] = 0;
    }
    if(params['alias'] == undefined) {
        params['alias'] = '';
    }



    if($('#order-create-modal').length > 0) { // вызывается со страницы сайта где есть форма создания заказа
        var city_id = $('#order-create-modal .search-point').attr('city-id');
    }else {                 // иначе вызывается из админки
        var city_id = $('#city-form').attr('city-id');
    }

    var balloonContent = placemark.properties.get('balloonContent');
    var name = parseNameFromTemplate(balloonContent);
    var coordinates = placemark.geometry.getCoordinates();
    var lat = coordinates[0];
    var long = coordinates[1];

    var data = {};
    if(params['can_change_params'] == true) {
        data.external_use = params['external_use'];
        data.point_of_arrival = params['point_of_arrival'];
        data.critical_point = params['critical_point'];
        data.popular_departure_point = params['popular_departure_point'];
        data.popular_arrival_point = params['popular_arrival_point'];
        data.alias = params['alias'];
    }

    $.ajax({
        url: '/yandex-point/ajax-create-yandex-point?city_id='+city_id+'&name='+name+'&lat='+lat+'&long='+long,
        type: 'post',
        data: data,
        success: function (data) {

            if(data.success == true) {
                if ($('#order-create-modal').length > 0) { // вызывается со страницы сайта где есть форма создания заказа

                    // обновление контента точки чтобы там в скрытом поле прописался id точки
                    var index = parseIndexFromTemplate(balloonContent);
                    //console.log('name='+name+' index='+index+' id='+data.yandex_point.id);

                    var select_point_placemark_params = {
                        index: index,
                        point_text: data.yandex_point.name,
                        point_description: data.yandex_point.description,
                        point_id: data.yandex_point.id
                    }
                    if(typeof point_focusing_scale != "undefined") {
                        select_point_placemark_params.point_focusing_scale = point_focusing_scale;
                    }
                    //selectPointPlacemark(index, data.yandex_point.name, data.yandex_point.id);
                    selectPointPlacemark(select_point_placemark_params);

                    // обновление поля "откуда" в форме создания заказа
                    var key = data.yandex_point.id + '_' + data.yandex_point.lat + '_' + data.yandex_point.long + '_' + data.yandex_point.name;
                    selectWidgetInsertValue($('input[name="Order[yandex_point_from]"]').parents('.sw-element'), key, data.yandex_point.name);

                } else {                // иначе вызывается из админки

                    alert('Точка создана и сохранена');

                    // закрытие модального окна
                    $('#default-modal').modal('hide');

                    // обновление страницы города со списком яндекс-точек
                    $.pjax.reload({
                        container: "#yandex-points-grid",
                        data: {
                            city_id: city_id
                        }
                    });
                }

            }else {

                var errors = '';
                for (var field in data.errors) {
                    var field_errors = data.errors[field];
                    for (var key in field_errors) {
                        errors += field_errors[key] + ' ';
                    }
                }
                alert(errors);

                map.geoObjects.remove(placemark);
                placemark = null;

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

    return true;
}


// функция изменения названия яндекс-точки
//function updateYandexPoint(index, yandex_point_id, name, lat, long, can_change_params, critical_point, alias) {
function updateYandexPoint(params) {

    //var update_yandex_point_params = {
    //    index: index,
    //    point_id: point_id,
    //    point_text: point_text,
    //    lat: lat,
    //    long: long,
    //    can_change_params: can_change_params,
    //    critical_point: critical_point,
    //    alias: alias
    //};

    if(params['index'] == undefined) {
        params['index'] = '';
    }
    if(params['point_id'] == undefined) {
        params['point_id'] = 0;
    }
    if(params['point_text'] == undefined) {
        params['point_text'] = '';
    }
    if(params['point_description'] == undefined) {
        params['point_description'] = '';
    }
    if(params['lat'] == undefined) {
        params['lat'] = 0;
    }
    if(params['long'] == undefined) {
        params['long'] = 0;
    }
    if(params['can_change_params'] != true) {
        params['can_change_params'] = false;
    }
    if(params['external_use'] != 1) {
        params['external_use'] = 0;
    }
    if(params['point_of_arrival'] != 1) {
        params['point_of_arrival'] = 0;
    }
    if(params['super_tariff_used'] != 1) {
        params['super_tariff_used'] = 0;
    }
    if(params['critical_point'] != 1) {
        params['critical_point'] = 0;
    }
    if(params['popular_departure_point'] != 1) {
        params['popular_departure_point'] = 0;
    }
    if(params['popular_arrival_point'] != 1) {
        params['popular_arrival_point'] = 0;
    }
    if(params['alias'] == undefined) {
        params['alias'] = '';
    }



    var data = {};
    if(params['point_text'] != undefined && params['point_text'] != '') {
        data.name = params['point_text'];
    }
    if(params['point_description'] != undefined && params['point_description'] != '') {
        data.description = params['point_description'];
    }

    if(params['lat'] != undefined && params['long'] != undefined) {
        data.lat = params['lat'];
        data.long = params['long'];
    }

    if(params['can_change_params'] == true) {
        data.critical_point = params['critical_point'];
        data.popular_departure_point = params['popular_departure_point'];
        data.popular_arrival_point = params['popular_arrival_point'];
        data.external_use = params['external_use'];
        data.point_of_arrival = params['point_of_arrival'];
        data.super_tariff_used = params['super_tariff_used'];
        data.alias = params['alias'];
    }

    $.ajax({
        //url: '/yandex-point/ajax-update-yandex-point?id=' + params['point_id'] + '&name=' + params['point_text'],
        url: '/yandex-point/ajax-update-yandex-point?id=' + params['point_id'],
        type: 'post',
        data: data,
        success: function (data) {

            if(data.success == true) {
                if ($('#order-create-modal').length > 0) { // вызывается со страницы сайта где есть форма создания заказа


                    var select_point_placemark_params = {
                        index: params['index'],
                        point_text: params['point_text'],
                        point_description: params['point_description'],
                        point_id: params['point_id'],
                        is_editing: false,
                        create_new_point: false,
                        can_change_params: params['can_change_params'],
                        external_use: params['external_use'],
                        point_of_arrival: params['point_of_arrival'],
                        critical_point: params['critical_point'],
                        popular_departure_point: params['popular_departure_point'],
                        popular_arrival_point: params['popular_arrival_point'],
                        alias: params['alias']
                        //draggable: draggable,
                        //is_allowed_edit: is_allowed_edit
                    }
                    if(typeof point_focusing_scale != "undefined") {
                        select_point_placemark_params.point_focusing_scale = point_focusing_scale;
                    }
                    selectPointPlacemark(select_point_placemark_params);
                    //selectPointPlacemark(params['index'], params['point_text'], params['point_id'], false, false, params['can_change_params'], params['critical_point'], alias);

                    // обновление поля "откуда" в форме создания заказа
                    var key = data.yandex_point.id + '_' + data.yandex_point.lat + '_' + data.yandex_point.long + '_' + data.yandex_point.name;
                    selectWidgetInsertValue($('input[name="Order[yandex_point_from]"]').parents('.sw-element'), key, data.yandex_point.name);

                } else {  // иначе вызывается из админки

                    alert('Изменения точки сохранены');

                    // закрытие модального окна
                    //$('#default-modal').modal('hide');

                    // обновление страницы города со списком яндекс-точек
                    var city_id = $('#city-form').attr('city-id');
                    $.pjax.reload({
                        container: "#yandex-points-grid",
                        data: {
                            city_id: city_id
                        }
                    });
                }
            }else {

                var errors = '';
                for (var field in data.errors) {
                    var field_errors = data.errors[field];
                    for (var key in field_errors) {
                        errors += field_errors[key] + ' ';
                    }
                }

                alert(errors);

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

    return true;
}



function updateChat()
{
    console.log('updateChat()');
    var is_open = $('#chat-block').attr('is-open');

    $.ajax({
        url: '/site/ajax-get-chat',
        type: 'post',
        data: {
            is_open: is_open
        },
        success: function (response) {

            //$('#chat-temp-block').html(response);
            if(response.html.length < 2) { // 1 символ все таки может придти в "пустом ответе"

                $('#chat-block').html(response.html);

            }else {
                if($('#chat-block #message-send-form-block').length == 0) { // значит обновляем весь чат
                    $('#chat-block').html(response.html);
                }else {
                    // если на странице в чате уже отображена форма, то из пришедшего окна формы извлекаем
                    // левую часть чата с сообщениями (без формы) и обновляем левую часть чата.
                    if($('#chat-temp-block').length == 0) {
                        $('#chat-block').after('<div id="chat-temp-block" style="display: none;"></div>');
                    }
                    $('#chat-temp-block').html(response.html);
                    var left_part_chat = $('#chat-temp-block #messages-list-block').html();
                    $('#chat-block #messages-list-block').html(left_part_chat);
                    $('#chat-temp-block').remove();
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

$(document).on('click', '.message', function () {

    $('.message-send-form-block .answer-close').each(function() {
        $(this).click();
    });

    var dialog_id = $(this).parents('.group-messages').attr('dialog-id');
    var dialog_num = $(this).parents('.group-messages').attr('dialog-num');

    var answer_html =
        '<div class="message-send-form-block" dialog-id="' + dialog_id + '">' +
        '<div class="answer-name-section">' +
            '<div class="answer-capt">' +
                '<p class="chat-name">Ответ на сообщение #' + dialog_num + '</p>' +
                '<span class="answer-close"><i class="glyphicon glyphicon-remove"></i></span>' +
            '</div>' +
        '</div>' +
        '<div id="fields-block">' +
            '<div class="chat-message-answer" contenteditable="true"></div>' +
            '<input class="btn-send-message-answer btn-xml btn-default" type="button" value="Ответить">' +
        '</div>' +
        '</div>'
    ;

    var wintop = parseInt($(window).scrollTop());
    var top = parseInt($(this).offset().top);
    var default_top = top - wintop;
    var left = $(this).offset().left + 200;

    // $(this).next('.message-send-form-block').remove();
    // $(this).after(answer_html);

    $('#chat-block').after(answer_html);
    $('.message-send-form-block[dialog-id="' + dialog_id + '"]').css({
        top: top,
        left: left
    });
    $('.message-send-form-block[dialog-id="' + dialog_id + '"]').attr('default-top', default_top);

    return false;
});

//$(document).on('scroll', 'body', function() {
// $('body').scroll(function() {
//     var scroll_top = $("body").scrollTop();
//     console.log('top=' + scroll_top);
// });

$(window).scroll(function(){
    var wintop = parseInt($(window).scrollTop());
    $('.message-send-form-block').each(function() {
        var top = parseInt($(this).attr('default-top')) + wintop;
        console.log('top='+top + ' wintop='+wintop);
        $(this).css('top', top);
    });
});


$(document).on('click', '.btn-select-placemark', function() {

    if(map != null) {

        var index = $(this).attr('placemark-index');
        var placemark = map.geoObjects.get(index);

        // в hintContent и balloonContent прячу кнопки "Выбрать"
        var balloonContent = placemark.properties.get('balloonContent');
        var yandex_point_id = parseIdFromTemplate(balloonContent);
        var yandex_point_name = parseNameFromTemplate(balloonContent);
        var draggable = getDraggable(yandex_point_id);

        var select_point_placemark_params = {
            index: index,
            point_text: yandex_point_name,
            // point_description: '',
            point_id: yandex_point_id,
            is_editing: false,
            //create_new_point: false,
            can_change_params: false,
            //critical_point: critical_point,
            //alias: alias,
            draggable: draggable
            //is_allowed_edit: is_allowed_edit
        }
        if(typeof point_focusing_scale != "undefined") {
            select_point_placemark_params.point_focusing_scale = point_focusing_scale;
        }
        selectPointPlacemark(select_point_placemark_params);
        //selectPointPlacemark(index, yandex_point_name, yandex_point_id, false, null, false, null, null, draggable);


        if($('#order-create-modal').length > 0) { // вызывается со страницы сайта где есть форма создания заказа

            // обновление поля "откуда" в форме создания заказа
            var coordinates = placemark.geometry.getCoordinates();
            var lat = coordinates[0];
            var long = coordinates[1];
            var key = yandex_point_id + '_' + lat + '_' + long + '_' + yandex_point_name;
            selectWidgetInsertValue($('input[name="Order[yandex_point_from]"]').parents('.sw-element'), key, yandex_point_name);

        }else {  // иначе вызывается из админки

        }
    }
});


$(document).on('click', '#order-create-modal .span-placemark, #default-modal .span-placemark', function() {

    if($(this).hasClass('not-edit')) {
        return false;
    }

    $(this).hide();
    $(this).parent().find('.input-placemark').show();
    $(this).parent().find('.ok-placemark').show();
});

$(document).on('click', '#order-create-modal .ok-placemark, #default-modal .ok-placemark', function() {

    var text = $.trim($(this).parent().find('.input-placemark').val());
    var description = $.trim($(this).parent().find('.input-description').val());

    if(text == '') {
        alert('Необходимо заполнить текст');
    }else {
        var index = $(this).parent().attr('index');
        var yandex_point_id = $(this).parent().attr('yandex-point-id');
        var create_new_point = $(this).parent().attr('create-new-point');

        if($(this).parent().find('.critical-point').length > 0) {
            var can_change_params = true;

            var external_use = ($(this).parent().find('.external-use').is(':checked') == true ? 1 : 0);
            var point_of_arrival = ($(this).parent().find('.point-of-arrival').is(':checked') == true ? 1 : 0);
            var critical_point = ($(this).parent().find('.critical-point').is(':checked') == true ? 1 : 0);
            var popular_departure_point = ($(this).parent().find('.popular-departure-point').is(':checked') == true ? 1 : 0);
            var popular_arrival_point = ($(this).parent().find('.popular-arrival-point').is(':checked') == true ? 1 : 0);
            var super_tariff_used = ($(this).parent().find('.super-tariff-used').is(':checked') == true ? 1 : 0);
            var alias = $(this).parent().find('.alias').val();
        }else {
            var can_change_params = false;
            var external_use = 0;
            var point_of_arrival = 0;
            var super_tariff_used = 0;
            var critical_point = 0;
            var popular_departure_point = 0;
            var popular_arrival_point = 0;
            var alias = '';
        }

        // функция выбора точки обновляет контент/название точки
        // selectPointPlacemark(index, point_text, point_id, edit, create_new_point, can_change_params, critical_point, alias)
        var draggable = getDraggable(yandex_point_id);

        var select_point_placemark_params = {
            index: index,
            point_text: text,
            point_description: description,
            point_id: yandex_point_id,
            is_editing: false,
            create_new_point: false,
            can_change_params: can_change_params,
            critical_point: critical_point,
            popular_departure_point: popular_departure_point,
            popular_arrival_point: popular_arrival_point,
            external_use: external_use,
            point_of_arrival: point_of_arrival,
            super_tariff_used: super_tariff_used,
            alias: alias,
            draggable: draggable
            //is_allowed_edit: is_allowed_edit
        }
        if(typeof point_focusing_scale != "undefined") {
            select_point_placemark_params.point_focusing_scale = point_focusing_scale;
        }
        var placemark = selectPointPlacemark(select_point_placemark_params);
        //var placemark = selectPointPlacemark(index, text, yandex_point_id, false, false, can_change_params, critical_point, alias, draggable);

        if(yandex_point_id > 0) {

            // сохраняем в базе изменения в точке
            var coordinates = placemark.geometry.getCoordinates();
            var lat = coordinates[0];
            var long = coordinates[1];

            //updateYandexPoint(index, yandex_point_id, text, lat, long, can_change_params, critical_point, alias);
            var update_yandex_point_params = {
                index: index,
                point_id: yandex_point_id,
                point_text: text,
                point_description: description,
                lat: lat,
                long: long,
                can_change_params: can_change_params,
                external_use: external_use,
                point_of_arrival: point_of_arrival,
                super_tariff_used: super_tariff_used,
                critical_point: critical_point,
                popular_departure_point: popular_departure_point,
                popular_arrival_point: popular_arrival_point,
                alias: alias
            };
            updateYandexPoint(update_yandex_point_params);


        }else {
            // создаем точку и обновляем контент кнопки чтобы там прописался id точки
            if(create_new_point == 'true') {
                //createYandexPoint(placemark, can_change_params, critical_point, alias);

                var create_yandex_point_params = {
                    can_change_params: can_change_params,
                    external_use: external_use,
                    point_of_arrival: point_of_arrival,
                    super_tariff_used: super_tariff_used,
                    critical_point: critical_point,
                    popular_departure_point: popular_departure_point,
                    popular_arrival_point: popular_arrival_point,
                    alias: alias
                };
                createYandexPoint(placemark, create_yandex_point_params);

            }else {
                if($('#order-create-modal').length > 0) { // вызывается со страницы сайта где есть форма создания заказа
                    // обновление поля "откуда" в форме создания заказа
                    var coordinates = placemark.geometry.getCoordinates();
                    var lat = coordinates[0];
                    var long = coordinates[1];
                    var key = '0_' + lat + '_' + long + '_' + text;
                    selectWidgetInsertValue($('input[name="Order[yandex_point_from]"]').parents('.sw-element'), key, text);

                    var select_point_placemark_params = {
                        index: index,
                        point_text: text,
                        point_description: description,
                        point_id: yandex_point_id,
                        is_editing: false,
                        create_new_point: false,
                        can_change_params: can_change_params,
                        external_use: external_use,
                        point_of_arrival: point_of_arrival,
                        super_tariff_used: super_tariff_used,
                        critical_point: critical_point,
                        popular_departure_point: popular_departure_point,
                        popular_arrival_point: popular_arrival_point,
                        alias: alias,
                        draggable: getDraggable(yandex_point_id)
                        //is_allowed_edit: is_allowed_edit
                    }
                    if(typeof point_focusing_scale != "undefined") {
                        select_point_placemark_params.point_focusing_scale = point_focusing_scale;
                    }
                    selectPointPlacemark(select_point_placemark_params);
                    //selectPointPlacemark(index, text, yandex_point_id, false, false, can_change_params, critical_point, alias, draggable);

                }else {  // иначе вызывается из админки

                    alert('Не предполагается в админке сохранение временной точки');
                }
            }
        }
    }
});


// поиск точки на яндекс-карте
$(document).on('keyup', '#order-create-modal .search-point, #default-modal .search-point', function(e) {

    var $search_obj = $(this);
    var search = $.trim($(this).val());

    if(search.length > 3) {

        var city_long = $search_obj.attr('city-long');
        var city_lat = $search_obj.attr('city-lat');
        var city_name = $search_obj.attr('city-name');

        //var url = 'https://suggest-maps.yandex.ru/suggest-geo?lang=ru_RU&ll='
        //    + city_long + ',' + city_lat + '&part=Республика Татарстан, ' + search;

        var url = 'https://suggest-maps.yandex.ru/suggest-geo?' +
                //'&v=5' +
            '&v=9' +
                //'&search_type=tp' +
                //'&search_type=tune' +
            '&search_type=all' +
            '&part=Республика Татарстан, ' + search +
            '&lang=ru_RU' +
            '&origin=jsapi2Geocoder' +
            '&ll=' + city_long + ',' + city_lat;

        $.ajax({
            url: url,
            dataType: 'jsonp',
            type: 'post',
            data: {},
            success: function (response) {

                var html = '';
                //var results = response[1];
                //console.log('results:'); console.log(response.results);


                if(response.results.length > 0) {

                    html = '<ul class="main-list">';
                    var lis = [];
                    var first_lis = []; // для мини-сортировки - строки в которых используется название города попадают в первый список
                    for(var i = 0; i < response.results.length; i++) {
                        //    var result = results[i][1]; // массив частей строк (территориальных делений)
                        //    //console.log('result:'); console.log(result);
                        //
                        //    if(result.length > 0) {
                        //        var str = '';
                        //        for(var j = 0; j < result.length; j++) { // 3 - это в конце масива: [..., 'Республика', ' ', 'Татарстан']
                        //            //console.log('type=' + typeof(result[j]));
                        //            if(typeof(result[j]) == 'object') {
                        //                str += result[j][1];
                        //            }else { // type = string
                        //                str += result[j];
                        //            }
                        //        }
                        //        str = str.replace(', Республика Татарстан', '');
                        //        str = '<li>' + str + '</li>';
                        //
                        //        if(str.indexOf(city_name) > -1) {
                        //            first_lis.push(str);
                        //        }else {
                        //            lis.push(str);
                        //        }
                        //    }


                        var result = response.results[i];
                        //console.log('result:'); console.log(result);

                        // Орион, Россия, Республика Татарстан
                        // Орион, Россия, Республика Татарстан, Набережные Челны
                        // Орион, Россия, Республика Татарстан, Казань

                        // Россия, Республика Татарстан, Альметьевск, улица Ленина
                        // Россия, Республика Татарстан, Бугульма, улица Ленина
                        // ...

                        var str = result.text; //
                        str = str.replace('Россия, Республика Татарстан, ', '');
                        str = '<li>' + str + '</li>';

                        if(str.indexOf(city_name) > -1) {
                            first_lis.push(str);
                        }else {
                            lis.push(str);
                        }
                    }

                    html += first_lis.join('') + lis.join('');
                    html += '</ul>';
                    $search_obj.next('.search-result-block').html(html).show();

                }else {
                    $search_obj.next('.search-result-block').html('').hide();
                }
            },
            error: function (data, textStatus, jqXHR) {
                alert('request error');
            }
        });

    }else {
        $search_obj.next('.search-result-block').html('').hide();
    }
});


// в яндекс-карте щелчек на строке в выпадающем списке результатов поиска
$(document).on('click', '.map-control-block .search-result-block li', function() {

    if(map == null) {
        return false;
    }

    var str = $(this).text();
    var myGeocoder = ymaps.geocode(str);
    myGeocoder.then(
        function (res) {

            var coordinates = res.geoObjects.get(0).geometry.getCoordinates();
            str = str.replace(', Республика Татарстан', '');


            if($('#order-create-modal').length > 0) { // вызывается со страницы сайта где есть форма создания заказа

                // создание точки - соответствующей выбранной в результатах поиска строке
                var index = map.geoObjects.getLength();


                //var placemark = createPlacemark(str, index, 0, coordinates[0], coordinates[1], true, false, false);
                var create_placemark_params = {
                    index: index,
                    point_text: str,
                    point_description: '',
                    point_id: 0,
                    point_lat: coordinates[0],
                    point_long: coordinates[1],
                    is_editing: true,
                    create_new_point: false,
                    to_select: true
                    //can_change_params: can_change_params,
                    //point_of_arrival: point_of_arrival,
                    //critical_point: critical_point,
                    //alias: alias
                };
                var placemark = createPlacemark(create_placemark_params);


                //var select_point_placemark_params = {
                //    index: index,
                //    point_text: str,
                //    point_id: 0,
                //    is_editing: true,
                //    create_new_point: false
                //    //can_change_params: can_change_params,
                //    //point_of_arrival: point_of_arrival,
                //    //critical_point: critical_point,
                //    //alias: alias,
                //    //draggable: getDraggable(yandex_point_id)
                //    //is_allowed_edit: is_allowed_edit
                //}
                //selectPointPlacemark(select_point_placemark_params);
                //selectPointPlacemark(index, str, 0, true, false);


                //placemark.balloon.open(); // если его поставить после " map.setCenter(coordinates, point_focusing_scale, {duration: 500});", то установка центра карты не успевает завершиться

                $('#order-create-modal .search-point').next('.search-result-block').html('').hide();


                if(typeof point_focusing_scale != "undefined") {
                    var coordinates = placemark.geometry.getCoordinates();
                    map.setCenter(coordinates, point_focusing_scale, {duration: 500}); // , iconColor: '#FF0000'
                }

                placemark.options.unset('iconLayout');
                placemark.options.unset('iconColor');
                placemark.options.unset('iconImageSize');
                placemark.options.unset('iconImageOffset');
                placemark.options.unset('iconShape');
                placemark.options.set({
                    iconColor: '#ff0000'
                });
                placemark.events
                    .add('mouseenter', function (e) {
                        e.get('target').options.set('iconColor', '#56DB40');
                    })
                    .add('mouseleave', function (e) {
                        e.get('target').options.set('iconColor', '#ff0000');
                    });


            }else {  // иначе вызывается из админки

                // создание точки - соответствующей выбранной в результатах поиска строке
                var index = map.geoObjects.getLength();
                //var placemark = createPlacemark(str, index, 0, coordinates[0], coordinates[1], true, true, false);
                var create_placemark_params = {
                    index: index,
                    point_text: str,
                    point_description: '',
                    point_id: 0,
                    point_lat: coordinates[0],
                    point_long: coordinates[1],
                    is_editing: true,
                    create_new_point: true,
                    to_select: false
                    //can_change_params: can_change_params,
                    //point_of_arrival: point_of_arrival,
                    //critical_point: critical_point,
                    //alias: alias
                };
                var placemark = createPlacemark(create_placemark_params);
                //placemark.events.remove('mouseenter');
                //placemark.events.remove('mouseleave');


                var select_point_placemark_params = {
                    index: index,
                    point_text: str,
                    // point_description: ' 6 ',
                    point_id: 0,
                    is_editing: true,
                    create_new_point: true
                    //can_change_params: can_change_params,
                    //point_of_arrival: point_of_arrival,
                    //critical_point: critical_point,
                    //alias: alias,
                    //draggable: getDraggable(yandex_point_id)
                    //is_allowed_edit: is_allowed_edit
                }
                if(typeof point_focusing_scale != "undefined") {
                    select_point_placemark_params.point_focusing_scale = point_focusing_scale;
                }
                selectPointPlacemark(select_point_placemark_params);
                //selectPointPlacemark(index, str, 0, true, true);

                placemark.balloon.open();

                $('#default-modal .search-point').next('.search-result-block').html('').hide();
            }


            //var coordinates = placemark.geometry.getCoordinates();
            //map.setCenter(coordinates, MAP_SEARCH_SCALE, {duration: 500});

        },
        function (err) {
            alert('Ошибка');
        }
    );

    return false;
});


$(document).on('click', '#inner-call-window .modal-close', function() {

    $('#inner-call-window .modal-body').html('');
    $('#inner-call-window').hide();
});