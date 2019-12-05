

$(document).ready(function() {
    $('#ejsv-form input[name="date"]').attr('disabled', true);
});

$(document).on('change', '#ejsv-form input[name="driver_id"]', function() {

    // console.log('change driver_id');

    $('#pl-list-block-1').hide();
    $('#pl-list-block-2').hide();
    $('#day_report_transport_circle-block').hide();
    $('#check-data').html('').hide();
    $('#check-data-notaccountability-transport').html('').hide();
    selectWidgetInsertValue($('input[name="transport_id"]').parents('.sw-element'), 0, '');


    var driver_id = $(this).val();
    if(driver_id > 0) {
        $('#ejsv-form input[name="date"]').removeAttr('disabled');

        var date = $.trim($('#ejsv-form input[name="date"]').val());
        if(date == $('#ejsv-form input[name="date"]').attr('today-date')) {
            $('#ejsv-form input[name="date"]').addClass('today-date');
        }else {
            $('#ejsv-form input[name="date"]').removeClass('today-date');
        }

    }else {
        if($('#ejsv-form input[name="date"]').is(':disabled') == false) {
            $('#ejsv-form input[name="date"]').attr('disabled', true).removeClass('today-date');
        }
        if($('#ejsv-form input[name="transport_id"]').is(':disabled') == false) {
            $('#ejsv-form input[name="transport_id"]').attr('disabled', true);
            $('#ejsv-form .sw-element[attribute-name="transport_id"]').attr('disabled', true).removeClass('today-date');
        }
    }
    $('#date-end-circle-block').hide();
    $('#select-notaccountability-transport-circle-block').html('').hide();
    $('input[name="date_end_circle"]').val('');

    loadPls();
});




$(document).on('change', '#ejsv-form input[name="date"]', function() {

    $('#pl-list-block-1').hide();
    $('#pl-list-block-2').hide();
    $('#day_report_transport_circle-block').hide();
    $('#check-data').html('').hide();
    $('#check-data-notaccountability-transport').html('').hide();
    selectWidgetInsertValue($('input[name="transport_id"]').parents('.sw-element'), 0, '');

    var date = $.trim($(this).val());
    if(date.length == 10) {
        $('#ejsv-form input[name="transport_id"]').removeAttr('disabled');
        $('#ejsv-form .sw-element[attribute-name="transport_id"]').removeAttr('disabled');
    }else {
        if($('#ejsv-form input[name="transport_id"]').is(':disabled') == false) {
            $('#ejsv-form input[name="transport_id"]').attr('disabled', true);
            $('#ejsv-form .sw-element[attribute-name="transport_id"]').attr('disabled', true);
        }
    }

    if(date == $(this).attr('today-date')) {
        $(this).addClass('today-date');
    }else {
        $(this).removeClass('today-date');
    }

    $('#date-end-circle-block').hide();
    $('#select-notaccountability-transport-circle-block').html('').hide();
    $('input[name="date_end_circle"]').val('');

    loadPls();
});

//


var allow_load_pls = true;
function loadPls() {

    var transport_id = $('#ejsv-form input[name="transport_id"]').val();
    var driver_id = $('#ejsv-form input[name="driver_id"]').val();
    var date = $.trim($('#ejsv-form input[name="date"]').val());


    if(transport_id > 0 && driver_id > 0 && date.length == 10 && allow_load_pls == true) {

        $.ajax({
            url: '/site/ajax-search-transport-waybills?date=' + date + '&transport_id=' + transport_id + '&driver_id=' + driver_id,
            type: 'post',
            data: {},
            beforeSend: function () {
                allow_load_pls = false;
                $('body').css('cursor', 'wait');
            },
            success: function (response) {
                //alert('удалено');

                allow_load_pls = true;
                $('body').css('cursor', 'default');

                $('#pl-list-block-1').hide();
                $('#pl-list').html('');
                $('input[name="pl-number"]').val('');
                $('#pl-list-block-2').hide();


                if (response.transport_waybills.length == 0) {
                    //alert('не найдены');

                    $('#pl-list-block-2').show();

                } else {
                    //alert('найдены');

                    var html = '';
                    for(var pl_number in response.transport_waybills) {
                        var pl_text = response.transport_waybills[pl_number];
                        html += '<input type="radio" name="pl-list" value="' + pl_number + '"> ' + pl_text + '<br />';
                    }
                    $('#pl-list').html(html);

                    $('#pl-list-block-1').show();
                }
            },
            error: function (data, textStatus, jqXHR) {

                allow_load_pls = true;
                $('body').css('cursor', 'default');

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


var allow_day_report_circles = true;
function loadDayReportTransportCircles() {

    var pl_number = $('input[name="pl-list"]:checked').val();
    var transport_id = $('#ejsv-form input[name="transport_id"]').val();
    var driver_id = $('#ejsv-form input[name="driver_id"]').val();
    var date = $.trim($('#ejsv-form input[name="date"]').val());
    $('#check-data').html('').hide();
    $('#check-data-notaccountability-transport').html('').hide();

    if(transport_id > 0 && driver_id > 0 && date.length == 10) {

        $.ajax({
            url: '/site/ajax-search-day-report-transport-circle?pl_number=' + pl_number + '&date=' + date + '&transport_id=' + transport_id + '&driver_id=' + driver_id,
            type: 'post',
            data: {},
            beforeSend: function () {
                allow_day_report_circles = false;
                $('body').css('cursor', 'wait');
            },
            success: function (response) {

                allow_day_report_circles = true;
                $('body').css('cursor', 'default');

                /*
                if (response.circle_trips.length == 0) {

                    $('#day_report_transport_circle-block').html('').hide();
                    alert('не найдены круги рейсов');

                } else {
                    //alert('найдены');

                    // var html = '';
                    // for(var circle_id in response.circle_trips) {
                    //     var text = response.circle_trips[circle_id];
                    //     html += '<input type="radio" name="day_report_transport_circle" value="' + circle_id + '"> ' + text + '<br />';
                    // }
                    //
                    // $('#day_report_transport_circle-list').html(html);
                    // $('#day_report_transport_circle-block').show();
                }*/

                $('#day_report_transport_circle-block').html(response.html);
                $('#day_report_transport_circle-block').show();
            },
            error: function (data, textStatus, jqXHR) {

                allow_day_report_circles = true;
                $('body').css('cursor', 'default');

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


var allow_load_transport_circles = true;
$(document).on('change', '#ejsv-form input[name="date_end_circle"]', function() {

    var date_start = $.trim($('#ejsv-form input[name="date"]').val());
    var date_end = $.trim($('#ejsv-form input[name="date_end_circle"]').val());
    var transport_id = $('#ejsv-form input[name="transport_id"]').val();
    var driver_id = $('#ejsv-form input[name="driver_id"]').val();


    if(transport_id > 0 && driver_id > 0 && date_start.length == 10 && date_end.length == 10 && allow_load_transport_circles == true) {

        $.ajax({
            url: '/site/ajax-search-notaccountability-transport-circles?date_start=' + date_start + '&date_end=' + date_end + '&transport_id=' + transport_id + '&driver_id=' + driver_id,
            type: 'post',
            data: {},
            beforeSend: function () {
                allow_load_transport_circles = false;
                $('body').css('cursor', 'wait');
            },
            success: function (response) {
                //alert('удалено');

                allow_load_transport_circles = true;
                $('body').css('cursor', 'default');

                $('#select-notaccountability-transport-circle-block').html(response.html).show();

                /*
                $('#pl-list-block-1').hide();
                $('#pl-list').html('');
                $('input[name="pl-number"]').val('');
                $('#pl-list-block-2').hide();


                if (response.transport_waybills.length == 0) {
                    //alert('не найдены');
                    $('#pl-list-block-2').show();
                } else {
                    //alert('найдены');

                    var html = '';
                    for(var pl_number in response.transport_waybills) {
                        var pl_number = response.transport_waybills[pl_number];
                        html += '<input type="radio" name="pl-list" value="' + pl_number + '"> ' + pl_number + '<br />';
                    }
                    $('#pl-list').html(html);

                    $('#pl-list-block-1').show();
                }*/
            },
            error: function (data, textStatus, jqXHR) {

                allow_load_transport_circles = true;
                $('body').css('cursor', 'default');

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
});


$(document).on('change', '#ejsv-form input[name="pl-list"]', function() {
    // загружаем список кругов рейсов машины
    loadDayReportTransportCircles();
});

$(document).on('change', '#ejsv-form input[name="notaccountability-transport-circle"]', function() {

    var transport_id = $('#ejsv-form input[name="transport_id"]').val();
    var driver_id = $('#ejsv-form input[name="driver_id"]').val();
    var day_report_transport_circle_id = $('input[name="notaccountability-transport-circle"]:checked').val();

    $.ajax({
        url: '/site/get-check-data-notaccountability-transport?transport_id=' + transport_id + '&driver_id=' + driver_id + '&day_report_transport_circle_id=' + day_report_transport_circle_id,
        type: 'post',
        data: {},
        success: function (html) {
            $('#check-data-notaccountability-transport').html(html).show();
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
});

$(document).on('click', '#ejsv-form #set-pl-number', function() {

    var pl_number = $.trim($('input[name="pl-number"]').val());

    // ПЛ не существует
    alert('Путевой лист с таким номером не существует, будет создан новый ПЛ');

    $('#pl-list-block-1').hide();
    $('#pl-list').html('');
    $('input[name="pl-number"]').val('');
    $('#pl-list-block-2').hide();

    var html = '<input type="radio" name="pl-list" value="' + pl_number + '"> ' + pl_number + '<br />';
    $('#pl-list').html(html);
    $('#pl-list-block-1').show();

    $('input[name="pl-list"][value="' + pl_number + '"]').click();

    /*
    if(pl_number != '') {

        $.ajax({
            url: '/site/ajax-check-pl-number?pl_number=' + pl_number,
            type: 'post',
            data: {},
            success: function (response) {

                allow_load_pls = true;

                if(response.success == true) { // ПЛ найден
                    alert('Найден путевой лист. Водитель: ' + response.driver_name + ' Дата: ' + response.date + ' Транспорт: ' + response.transport_name);

                    allow_load_pls = false;
                    selectWidgetInsertValue($('input[name="driver_id"]').parents('.sw-element'), response.driver_id, response.driver_name);
                    $('input[name="date"]').val(response.date);
                    selectWidgetInsertValue($('input[name="transport_id"]').parents('.sw-element'), response.transport_id, response.transport_name);

                    $('#pl-list-block-1').hide();
                    $('#pl-list').html('');
                    $('input[name="pl-number"]').val('');
                    $('#pl-list-block-2').hide();

                    var html = '<input type="radio" name="pl-list" value="' + pl_number + '"> ' + pl_number + '<br />';
                    $('#pl-list').html(html);
                    $('#pl-list-block-1').show();

                    $('input[name="pl-list"][value="' + pl_number + '"]').click();


                }else { // ПЛ не существует
                    // alert('Путевой лист с таким номером не найден');
                    alert('Путевой лист с таким номером не существует, будет создан новый ПЛ');

                    $('#pl-list-block-1').hide();
                    $('#pl-list').html('');
                    $('input[name="pl-number"]').val('');
                    $('#pl-list-block-2').hide();

                    var html = '<input type="radio" name="pl-list" value="' + pl_number + '"> ' + pl_number + '<br />';
                    $('#pl-list').html(html);
                    $('#pl-list-block-1').show();

                    $('input[name="pl-list"][value="' + pl_number + '"]').click();
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
                } else {
                    handlingAjaxError(data, textStatus, jqXHR);
                }
            }
        });
    }*/

    return false;
});


/*
$(document).on('change', '#ejsv-form input[name="day_report_transport_circle"]', function() {
    //alert('day_report_transport_circle change');

    // отображаем проверочные данные вида:
    // ---
    // ПЛ #607091 от 06.07.2019
    // Белый Ситроен 401
    // Каюмов Ренат Дамирович
    //
    // АК 7:30, КА 13:50
    //
    // Верно?
    // ---
    //
    // 5) если оператор выбирает верно, то отображаем табличку 2х2:
    //     в верхней строчке а левой ячейке - В1, в правой - В2. Под ними - дата сдачи В1 и дата сдачи В2.

    var pl_id = $('input[name="pl-list"]:checked').val();
    if(pl_id == undefined) {
        pl_id = 0;
    }
    var transport_id = $('#ejsv-form input[name="transport_id"]').val();
    var driver_id = $('#ejsv-form input[name="driver_id"]').val();
    // var date = $.trim($('#ejsv-form input[name="date"]').val());
    var day_report_transport_circle_id = $('input[name="day_report_transport_circle"]:checked').val();


    $.ajax({
        url: '/site/get-check-data?pl_id=' + pl_id + '&day_report_transport_circle_id=' + day_report_transport_circle_id + '&transport_id=' + transport_id + '&driver_id='+driver_id,
        type: 'post',
        data: {},
        success: function (html) {
            $('#check-data').html(html).show();
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

});*/

$(document).on('click', '#btn-selected-day-report-transport-circle', function () {

    // отображаем проверочные данные вида:
    // ---
    // ПЛ #607091 от 06.07.2019
    // Белый Ситроен 401
    // Каюмов Ренат Дамирович
    //
    // АК 7:30, КА 13:50
    //
    // Верно?
    // ---
    //
    // 5) если оператор выбирает верно, то отображаем табличку 2х2:
    //     в верхней строчке а левой ячейке - В1, в правой - В2. Под ними - дата сдачи В1 и дата сдачи В2.


    var pl_number = $('input[name="pl-list"]:checked').val();
    if(pl_number == undefined) {
        pl_number = 0;
    }
    var transport_id = $('#ejsv-form input[name="transport_id"]').val();
    var driver_id = $('#ejsv-form input[name="driver_id"]').val();
    var date = $('#ejsv-form input[name="date"]').val();
    // var date = $.trim($('#ejsv-form input[name="date"]').val());
    // var day_report_transport_circle_id = $('input[name="day_report_transport_circle"]:checked').val();

    var trip_transport_start = $('select[name="trip_transport_start"]').val();
    var trip_transport_end = $('select[name="trip_transport_end"]').val();

    //alert('trip_transport_start='+trip_transport_start+' trip_transport_end='+trip_transport_end);
    // if (trip_transport_start == 0 || trip_transport_end == 0) {
    //     alert('Выберите начало и окончание');
    //     return false;
    // }

    $.ajax({
        url: '/site/get-check-data?pl_number=' + pl_number + '&trip_transport_start=' + trip_transport_start + '&trip_transport_end=' + trip_transport_end + '&transport_id=' + transport_id + '&driver_id=' + driver_id + '&date=' + date,
        type: 'post',
        data: {},
        success: function (html) {
            $('#check-data').html(html).show();

            //$('#btn-selected-day-report-transport-circle').hide();

            $('#pl-list-block-1').hide();
            $('#day_report_transport_circle-block').hide();
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
});



function loadWaybillHandoverbbForm(pl_number, trip_transport_start, trip_transport_end, transport_id, driver_id, date) {

    // если ПЛ не существует с такими[pl_number, transport_id, driver_id, date], то он будет создан
    $.ajax({
        url: '/site/get-handoverbb-form?pl_number=' + pl_number + '&trip_transport_start=' + trip_transport_start + '&trip_transport_end=' + trip_transport_end + '&transport_id=' + transport_id + '&driver_id=' + driver_id + '&date=' + date,
        type: 'post',
        data: {},
        success: function (html) {

            $('#default-modal').find('.modal-body').html(html);
            $('#input-bb-group').hide();
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

function loadNotaccountabilityTransportHandoverbbForm(notaccountability_transport_report_id) {

    $.ajax({
        url: '/site/get-notaccountability-transport-handoverbb-form?notaccountability_transport_report_id=' + notaccountability_transport_report_id,
        type: 'post',
        data: {},
        success: function (html) {

            $('#default-modal').find('.modal-body').html(html);
            // $('#input-bb-group').hide();

            // B1 - Сдано
            var currencyMask = new IMask(
                document.getElementById('hand_over_bb'),
                {
                    mask: 'num',
                    blocks: {
                        num: {
                            mask: Number,
                            thousandsSeparator: ' '
                        }
                    }
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
            } else {
                handlingAjaxError(data, textStatus, jqXHR);
            }
        }
    });
}

$(document).on('click', '#accept-check-form', function() {

    var pl_number = $('input[name="pl-list"]:checked').val();
    if(pl_number == undefined) {
        pl_number = 0;
    }
    // var notaccountability_transport_report_id = $('#notaccountability-transport-report-id').val();
    // if(notaccountability_transport_report_id == undefined) {
    //     notaccountability_transport_report_id = 0;
    // }
    // var day_report_transport_circle_id = $('input[name="day_report_transport_circle"]:checked').val();
    var transport_id = $('#ejsv-form input[name="transport_id"]').val();
    var driver_id = $('#ejsv-form input[name="driver_id"]').val();
    var date = $.trim($('#ejsv-form input[name="date"]').val());

    var trip_transport_start = $('select[name="trip_transport_start"]').val();
    var trip_transport_end = $('select[name="trip_transport_end"]').val();

    loadWaybillHandoverbbForm(pl_number, trip_transport_start, trip_transport_end,  transport_id, driver_id, date);
});

$(document).on('click', '#accept-check-notaccountability-transport-form', function() {

    var date_start = $.trim($('#ejsv-form input[name="date"]').val());
    var date_end = $.trim($('#ejsv-form input[name="date_end_circle"]').val());
    var transport_id = $('#ejsv-form input[name="transport_id"]').val();
    var driver_id = $('#ejsv-form input[name="driver_id"]').val();
    var day_report_transport_circle_id = $('input[name="notaccountability-transport-circle"]:checked').val();

    $.ajax({
        url: '/site/ajax-save-notaccountability-transport-form?date_start=' + date_start + '&date_end=' + date_end + '&transport_id=' + transport_id + '&driver_id=' + driver_id + '&day_report_transport_circle_id=' + day_report_transport_circle_id,
        type: 'post',
        data: {},
        success: function (response) {

            loadNotaccountabilityTransportHandoverbbForm(response.notaccountability_transport_report_id);
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
});

$(document).on('click', '#close-ejsv-waybill-handoverbb-form', function() {
    $(this).parents('#default-modal').find('.close').click();
});

// нажатие на кнопку "Водитель сдает выручку"
$(document).on('click', '#driver-gives-proceeds', function() {

    // var role = $('input[name="role"]').val();
    // if(!(role == 'root')) {
    //     alert('Редактировать выручку может только Root');
    //     return false;
    // }

    // var pl_number = $('#waybill-number').val();
    //
    // // отправляется запрос на сервер чтобы пересчитать индикаторы в ПЛ
    // $.ajax({
    //     url: '/site/ajax-recount-pl-indicators?pl_number=' + pl_number,
    //     type: 'post',
    //     data: {},
    //     success: function () {},
    //     error: function (data, textStatus, jqXHR) {
    //
    //         if (textStatus == 'error' && data != undefined) {
    //             if (void 0 !== data.responseJSON) {
    //                 if (data.responseJSON.message.length > 0) {
    //                     alert(data.responseJSON.message);
    //                 }
    //             } else {
    //                 if (data.responseText.length > 0) {
    //                     alert(data.responseText);
    //                 }
    //             }
    //         } else {
    //             handlingAjaxError(data, textStatus, jqXHR);
    //         }
    //     }
    // });


    $(this).hide();
    $('#input-bb-group').show();

    var exist_b1 = ($('#exist-b1').attr('is-exist') == "true" ? true : false);
    var exist_b2 = ($('#exist-b2').attr('is-exist') == "true" ? true : false);
    if(exist_b1 == false) {
        //$('#new_date').attr('placeholder', 'дата B1');
        //$('#hand_over_bb').attr('placeholder', 'B1');
        //$('#bb-title').text('Заполните поля для B1:');
    }else {
        //$('#new_date').attr('placeholder', 'дата B2');
        //$('#hand_over_bb').attr('placeholder', 'B2');
        //$('#bb-title').text('Заполните поля для B2:');
    }

    // B1 - Сдано
    var currencyMask = new IMask(
        document.getElementById('hand_over_bb'),
        {
            mask: 'num',
            blocks: {
                num: {
                    // nested masks are available!
                    mask: Number,
                    thousandsSeparator: ' '
                }
            }
        });
});


$(document).on('click', '#submit-bb-data', function() {

    // var new_date = $.trim($('input[name="new_date"]').val());
    var hand_over_bb = $('#hand_over_bb').val();

    if(hand_over_bb != '') {
        $(this).hide();
        $('#input-password').show();
    }else {
        alert('Заполните сумму');
    }
});

$(document).on('click', '#submit-notaccountability-transport-bb-data', function() {

    // var notaccountability_transport_report_id = $(this).attr('notaccountability-transport-report-id');
    var hand_over_bb = $('#hand_over_bb').val();

    if(hand_over_bb != '') {
        $('#hand-over-bb-block').hide();
        $('#input-password').show();
    }else {
        alert('Заполните сумму');
    }
});


$(document).on('click', '#submit-password-with-bb-data', function() {

    var data = {};

    var transport_id = $('#transport-id').val();
    var driver_id = $('#driver-id').val();
    var date = $.trim($('#date').val());


    // var new_date = $.trim($('input[name="new_date"]').val());
    var hand_over_bb = $('#hand_over_bb').val();
    hand_over_bb = hand_over_bb.replace(/\s/g, '');

    var exist_b1 = ($('#exist-b1').attr('is-exist') == "true" ? true : false);
    var exist_b2 = ($('#exist-b2').attr('is-exist') == "true" ? true : false);
    if(exist_b1 == false) {
        // data.hand_over_b1_data = new_date;
        data.hand_over_b1 = hand_over_bb;
    }else {
        // data.hand_over_b2_data = new_date;
        data.hand_over_b2 = hand_over_bb;
    }

    data.password = $('#password').val();

    var pl_number = $('#waybill-number').val();
    if(pl_number == undefined) {
        pl_number = 0;
    }
    // var notaccountability_transport_report_id = $('#notaccountability-transport-report-id').val();
    // if(notaccountability_transport_report_id == undefined) {
    //     notaccountability_transport_report_id = 0;
    // }
    var day_report_transport_circle_id = $('#day_report_transport_circle-id').val();

    // alert('trip_transport_start, trip_transport_end - ?');
    // return false;
    var trip_transport_start = 0;
    var trip_transport_end = 0;

    if(/*new_date.length == 10 &&*/ hand_over_bb != '') {

        $.ajax({
            url: '/site/ajax-save-ejsv-data?pl_number=' + pl_number + '&day_report_transport_circle_id=' + day_report_transport_circle_id + '&transport_id=' + transport_id + '&driver_id=' + driver_id + '&date=' + date,
            type: 'post',
            data: data,
            success: function (response) {

                // if(response.notaccountability_transport_report_id !== void 0) {
                //     notaccountability_transport_report_id = response.notaccountability_transport_report_id;
                // }else {
                //     notaccountability_transport_report_id = 0;
                // }

                loadWaybillHandoverbbForm(pl_number, trip_transport_start, trip_transport_end, transport_id, driver_id, date);
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

    }else {
        alert('Заполните сумму');
        return false;
    }
});


$(document).on('click', '#submit-password-with-notaccountability-transport-bb-data', function() {

    var notaccountability_transport_report_id = $(this).attr('notaccountability-transport-report-id');
    var hand_over_bb = $('#hand_over_bb').val();
    var password = $('#password').val();

    if(password.length > 5) {

        $.ajax({
            url: '/site/ajax-save-notaccountability-transport-ejsv-data?notaccountability_transport_report_id=' + notaccountability_transport_report_id + '&hand_over_bb=' + hand_over_bb + '&password=' + password,
            type: 'post',
            data: {},
            success: function (response) {

                alert('Сохранено');
                $('#default-modal').find('.close').click();

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

});