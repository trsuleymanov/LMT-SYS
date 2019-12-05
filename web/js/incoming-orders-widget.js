
/*
// функция обновления окошка показывающего количество заявок
function updateClientextBlock()
{
    $.ajax({
        url: '/order/ajax-get-clientext-block',
        type: 'post',
        data: {},
        contentType: false,
        cache: false,
        processData: false,
        success: function (response) {
            if(response.success == true) {

                var colors = ['#FBF600', '#A6DEFF', '#C3FFD0', '#B8A2DC', '#FFC6F1'];
                var num = getRandomInt(0, 4);
                $('#clientext-block').html(response.html);
                $('.clientext-widget').css('background-color', colors[num]);

            }else {
                alert('неустановленная ошибка обновления блока с количеством заявок');
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
*/

$(document).on('click', '#incoming-orders-widget .direction-1, #incoming-orders-widget .direction-2', function() {

    if($('#active-trips-modal').is(':visible')) {
        $('#active-trips-modal').hide();

        var href = location.href.replace('&trips-modal-is-open', '').replace('?trips-modal-is-open', '');
        href = href.substr(href.indexOf('?'));
        history.pushState(null, null, href);

    } else {
        $('#active-trips-modal').show();

        var href = location.href;
        if(href.indexOf('?') > -1) {
            href = href.substr(href.indexOf('?'));
            href = href + '&trips-modal-is-open';
        }else {
            href = href + '?trips-modal-is-open';
        }
        history.pushState(null, null, href);
    }
});

$(document).on('click', '#incoming-orders-widget .incoming-orders-count', function() {

    $('#incoming-clientext-orders-modal').hide();
    $('#incoming-request-orders-modal').hide();

    var href = location.href;
    href = href.replace('&incoming-clientext-orders-is-open', '').replace('incoming-clientext-orders-is-open&', '').replace('?incoming-clientext-orders-is-open', '');
    href = href.replace('&incoming-request-orders-is-open', '').replace('incoming-request-orders-is-open&', '').replace('?incoming-request-orders-is-open', '');
    href = href.substr(href.indexOf('?'));
    history.pushState(null, null, href);


    $('#incoming-clientext-orders-modal').show();
    if(href.indexOf('?') > -1) {
        href = href.substr(href.indexOf('?'));
        href = href + '&incoming-clientext-orders-is-open';
    }else {
        href = href + '?incoming-clientext-orders-is-open';
    }


    $('#incoming-request-orders-modal').show();
    if(href.indexOf('?') > -1) {
        href = href.substr(href.indexOf('?'));
        href = href + '&incoming-request-orders-is-open';
    }else {
        href = href + '?incoming-request-orders-is-open';
    }
    history.pushState(null, null, href);

});

$(document).on('click', '#incoming-clientext-orders-modal .clientext', function() {

    var data = {
        order_id: $(this).attr('order-id')
    }
    //openModalCreateOrder(null, null, order_id);
    openModalCreateOrder(data);
});

//$(document).on('click', '#incoming-request-orders-modal .request');

$(document).on('click', '#active-trips-modal .modal-close', function() {
    $('#active-trips-modal').hide();

    var href = location.href.replace('&trips-modal-is-open', '').replace('?trips-modal-is-open', '');
    href = href.substr(href.indexOf('?'));
    history.pushState(null, null, href);
});
$(document).on('click', '#incoming-clientext-orders-modal .modal-close', function() {
    $('#incoming-clientext-orders-modal').hide();

    var href = location.href.replace('&incoming-clientext-orders-is-open', '').replace('incoming-clientext-orders-is-open&', '').replace('?incoming-clientext-orders-is-open', '');

    href = href.substr(href.indexOf('?'));
    history.pushState(null, null, href);
});

$(document).on('click', '#incoming-request-orders-modal .modal-close', function() {
    $('#incoming-request-orders-modal').hide();

    var href = location.href.replace('&incoming-request-orders-is-open', '').replace('incoming-request-orders-is-open&', '').replace('?incoming-request-orders-is-open', '');
    href = href.substr(href.indexOf('?'));
    history.pushState(null, null, href);
});


$(document).on('click', '#incoming-orders-widget #missed-calls', function () {
    //alert('не сделано');

    $.ajax({
        url: '/call/ajax-get-missed-call-list',
        type: 'post',
        data: {},
        //contentType: false,
        //cache: false,
        //processData: false,
        success: function (response) {
            if(response.success == true) {

                //$('#default-modal').find('.modal-dialog').width('600px').css('margin-top', "150px");
                $('#default-modal').find('.modal-body').html(response.html);
                $('#default-modal .modal-title').html('Пропущенные звонки');
                $('#default-modal').modal('show');

            }else {
                alert('неустановленная ошибка обновления блока с количеством заявок');
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

$(document).on('click', '#incoming-orders-widget #incoming-call', function () {
    //alert('не сделано');
});


$(document).on('click', '#incoming-orders-widget #is-calling', function () {

    console.log('is-calling click');

    var call_window_is_open = sessionStorage.getItem('call_window');
    //console.log('call_window_is_open='+call_window_is_open);


    var data = {
        client_phone: $(this).attr('client_phone'),
        call_id: $(this).attr('call_id')
    }

    if(void 0 !== data.client_phone) {

        // $('#calls-window').html() - undefined - как-бы данные звонка...
        // data.client_data_html - undefined - этот как раз данные клиента с заказами

        // также нет отображенных данных о звонках, но это можно и отдельно потом создать
        //console.log('data'); console.log(data);
        //showCallModal(data.call_id);
        showInnerCallModal(data.call_id);

        // значит нужно сделать запрос на сервер, который вернет
        //updateCallClientForm(data.client_phone, true);
    }
});


// при принятии входящего звонка застявляем моргать зеленый кружок
function setIncomingCallsCount(count) {

    if(count > 0) {
        $('#incoming-calls-count').text(count);
        $('#incoming-call').addClass('active');
    }else {
        $('#incoming-calls-count').text('');
        $('#incoming-call').removeClass('active');
    }
}

// при появлении пропущенного вызова отображаем трубку с куличеством пропущенных звонков
setInterval(function(){
    if($('#incoming-call').hasClass('active')) {
        $('#incoming-call').fadeTo(100, 0.1).fadeTo(200, 1.0);
    }
}, 1000);

//function setMissedCallsCount(count) {
//
//    if(count > 0) {
//        $('#missed-calls').find('.missed-calls-count').text(count);
//        $('#missed-calls').addClass('active');
//    }else {
//        $('#missed-calls').find('.missed-calls-count').text('');
//        $('#missed-calls').removeClass('active');
//    }
//}



$(document).ready(function()
{
    // обновление блока с количеством заявок на всех страницах сайта (кроме тех где Слава переопределил шаблон)
    //setInterval(function() {
    //    updateClientextBlock();
    //}, 10000);


    // при принятии входящего звонка застявляем моргать зеленый кружок
    //setIncomingCallsCount(4);

    // при появлении пропущенного вызова отображаем трубку с куличеством пропущенных звонков
    //setMissedCallsCount(13);
});
