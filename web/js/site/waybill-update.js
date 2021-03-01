

$(document).on('click', '.add-transport-expenses', function() {

    //var transport_expenses_key = $('.transport-expenses').length;
    var $table = $(this).prev('.transport-expenses-table');

    var transport_waybill_id = $('#waybill-form').attr('transport-waybill-id');

    var table_type = $(this).attr('table-type');


    $.ajax({
        url: '/waybill/transport-waybill/ajax-get-transport-expenses-row?transport_waybill_id='+transport_waybill_id,
        type: 'post',
        data: {
            table_type: table_type
        },
        success: function (response) {
            //$('.transport-expenses:last').after(html);

            //$table.find('tr:last').after(response.html);
            //$('#transportexpenses-payment_date_' + transport_expenses_key).datepicker();
            //$('#transportexpenses-need_pay_date_' + transport_expenses_key).datepicker();
            //transport_expenses_key++;

            location.reload();

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
                //handlingAjaxError(data, textStatus, jqXHR);
            }
        }
    });

    return false;
});


$(document).on('click', '.delete-transport-expenses', function() {

    //$(this).parents('tr').remove();
    var $row = $(this).parents('.transport-expenses');
    var transport_expenses_id = $row.attr('transport-expenses-id');

    $.ajax({
        url: '/waybill/transport-waybill/ajax-delete-transport-expenses-row?transport_expenses_id=' + transport_expenses_id,
        type: 'post',
        data: {},
        success: function (response) {
            $row.remove();
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
                //handlingAjaxError(data, textStatus, jqXHR);
            }
        }
    });

    return false;
});


$(document).on('click', '.waybill-block-toogle', function() {

    $(this).next('.waybill-body').toggle(0, function() {
        if($(this).is(':visible') == true) {
            $(this).prev('.waybill-block-toogle').find('.glyphicon').removeClass('glyphicon-menu-up').addClass('glyphicon-menu-down');
        }else {
            $(this).prev('.waybill-block-toogle').find('.glyphicon').removeClass('glyphicon-menu-down').addClass('glyphicon-menu-up');
        }
    });
});



// --------------  ФОРМА ДЕТАЛИЗАЦИИ РАСХОДА  --------------------------

$(document).on('click', '.open-transport-expenses-detailing', function() {

    //var html = '<tr><td colspan="3">&nbsp;</td>  <td>Наим.док</td><td>Дата</td><td>Наим.</td><td>Цена</td><td>Тип</td><td>закр.крест</td> <td colspan="8"></td></tr>';
    //$(this).parent('td').parent('tr').after(html);

    //var transport_waybill_id = $('#waybill-form').attr('transport-waybill');
    var transport_expenses_id = $(this).parent('td').parent('tr').attr('transport-expenses-id');
    var allow_minus_opearation = $('#waybill-form').attr('allow-minus-opearation');

    $.ajax({
        url: '/waybill/transport-expenses-detailing/ajax-get-form?transport_expenses_id=' + transport_expenses_id,
        type: 'post',
        data: {},
        success: function (response) {

            $('#default-modal .modal-title').html(response.title);
            $('#default-modal .modal-title').css('line-height', '2.4');
            $('#default-modal .modal-header').css('height', '84px');
            $('#default-modal').find('.modal-body').html(response.html);
            //$('#default-modal').find('.modal-body').html(response);
            $('#default-modal .modal-dialog').css('width', '800px');


            $('#default-modal').modal('show');

            $('#detailing-title-form .transportexpenses-need_pay_date').datepicker();

            $('#transport-expenses-detailing-form .price').each(function() {

                var id = $(this).attr('id');
                var numberMask = new IMask(
                    document.getElementById(id),
                    {
                        mask: Number,
                        min: (allow_minus_opearation === "1" ? -10000000 : 0),
                        max: 10000000,
                        thousandsSeparator: ' '
                    });
            });


            // response.phones_block
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

$(document).on('click', '.add-transport-expenses-detailing', function() {

    //var transport_expenses_key = $('.transport-expenses').length;
    var transport_expenses_id = $('#transport-expenses-detailing-form').attr('transport-expenses-id');
    var $block = $(this).parents('.detailings-block');
    var block_type = $block.attr('block-type');
    var $table = $block.find('.transport-expenses-detailing-table');
    var allow_minus_opearation = $('#waybill-form').attr('allow-minus-opearation');


    $.ajax({
        url: '/waybill/transport-expenses-detailing/ajax-get-detailing-row?transport_expenses_id=' + transport_expenses_id,
        type: 'post',
        data: {
            block_type: block_type
        },
        success: function (response) {
            //$('.transport-expenses:last').after(html);
            $table.find('tr:last').after(response.html);

            //$('#transportexpenses-payment_date_' + transport_expenses_key).datepicker();
            //$('#transportexpenses-need_pay_date_' + transport_expenses_key).datepicker();

            var id = $table.find('tr:last').find('.price').attr('id');
            var numberMask = new IMask(
                document.getElementById(id),
                {
                    mask: Number,
                    min: (allow_minus_opearation === "1" ? -10000000 : 0),
                    max: 10000000,
                    thousandsSeparator: ' '
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
                //handlingAjaxError(data, textStatus, jqXHR);
            }
        }
    });

    return false;
});

// изменение цены в форме детализации пересчитывает отображаемую цену в заголовке формы
$(document).on('change', '#transport-expenses-detailing-form .price', function() {

    var price = 0;
    $('#transport-expenses-detailing-form .price').each(function() {
        price += parseFloat($(this).val());
    });
    $('#detailing-title-form-price').text(price);

});

$(document).on('click', '.delete-transport-expenses-detailing', function() {

    var $row = $(this).parent('td').parent('tr');
    var detailing_id = $row.attr('detailing-id');

    $.ajax({
        url: '/waybill/transport-expenses-detailing/ajax-delete-detailing-row?detailing_id=' + detailing_id,
        type: 'post',
        data: {},
        success: function (response) {
            $row.remove();

            updateWorksBlockTotalPrice();
            updateGoodsBlockTotalPrice();
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
                //handlingAjaxError(data, textStatus, jqXHR);
            }
        }
    });

    return false;
});


function updateWorksBlockTotalPrice() {

    var price = 0;
    $('.detailings-block[block-type="works"] .price').each(function() {
        price += parseFloat($(this).val());
    });
    price = Math.round(price * 100) / 100;
    $('.detailings-block[block-type="works"] .total-price').text(price);
}

function updateGoodsBlockTotalPrice() {

    var price = 0;
    $('.detailings-block[block-type="goods"] .price').each(function() {
        price += parseFloat($(this).val());
    });
    price = Math.round(price * 100) / 100;
    $('.detailings-block[block-type="goods"] .total-price').text(price);
}

// изменение цены в таблице Работ/Услуг
$(document).on('change', '.detailings-block[block-type="works"] .price', function() {
    updateWorksBlockTotalPrice();
});
// изменение цены в таблице Запчастей/Товаров
$(document).on('change', '.detailings-block[block-type="goods"] .price', function() {
    updateGoodsBlockTotalPrice();
});


// сбор данных формы Детализации-расшифровки расхода
//function getDetailingFormData() {
//
//    var formData = {};
//    var TransportExpensesDetailings = {};
//    $('#transport-expenses-detailing-form tr[detailing-id]').each(function() {
//        var TransportExpensesDetailing = {
//            id: '',
//            name: '',
//            price: '',
//            type: ''
//        };
//    });
//}


var allow_detailing_form_submit = true;
$(document).on('submit', '#transport-expenses-detailing-form', function () {

    var $form = $(this);

    if(allow_detailing_form_submit == true) {

        $.ajax({
            url: $form.attr('action'),
            type: 'post',
            data: $form.serialize(),
            beforeSend: function () {
                allow_detailing_form_submit = false;
            },
            success: function (response) {

                allow_detailing_form_submit = true;
                if (response.success == true) {

                    if(response.html != undefined) {
                        $('#default-modal').find('.modal-body').html(response.html);
                    }else {
                        $('#default-modal .close').click();
                        $('#transportexpenses-price-' + response.tr_expenses_id).val(response.tr_expenses_price);
                    }

                } else {
                    alert('errors');
                }
            },
            error: function (data, textStatus, jqXHR) {

                allow_detailing_form_submit = true;

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


// -------------- конец: ФОРМА ДЕТАЛИЗАЦИИ РАСХОДА  --------------------------


// события потери фокуса любого поля - сохранение изменения в базе данных (+ валидация)

// блок "Эксплуатация", "Корректировка", "Начисления"
$(document).on('change',

    'input[name="TransportWaybill[is_visible]"],' +
    // блок "Эксплуатация"
    'input[name="TransportWaybill[number]"],' +
    'input[name="TransportWaybill[date_of_issue]"],' +
        //'select[name="TransportWaybill[transport_id]"],' +
    'input[name="TransportWaybill[transport_id]"],' +
        //'select[name="TransportWaybill[driver_id]"],' +
    'input[name="TransportWaybill[driver_id]"],' +
    'textarea[name="TransportWaybill[trip_comment]"],' +
    'input[name="TransportWaybill[pre_trip_med_check]"],' +
    'input[name="TransportWaybill[pre_trip_med_check_time]"],' +
    'input[name="TransportWaybill[pre_trip_tech_check]"],' +
    'input[name="TransportWaybill[pre_trip_tech_check_time]"],' +
    'input[name="TransportWaybill[departure_time]"],' +
    'input[name="TransportWaybill[mileage_before_departure]"],' +
    'select[name="TransportWaybill[trip_transport_start]"],' +
    'select[name="TransportWaybill[trip_transport_end]"],' +
    'input[name="TransportWaybill[after_trip_med_check]"],' +
    'input[name="TransportWaybill[after_trip_med_check_time]"],' +
    'input[name="TransportWaybill[after_trip_tech_check]"],' +
    'input[name="TransportWaybill[after_trip_tech_check_time]"],' +
    'input[name="TransportWaybill[return_time]"],' +
    'input[name="TransportWaybill[mileage_after_departure]"],' +
    'select[name="TransportWaybill[waybill_state]"],' +
    'select[name="TransportWaybill[values_fixed_state]"],' +
    'select[name="TransportWaybill[gsm]"],' +
    'select[name="TransportWaybill[klpto]"],' +
    'textarea[name="TransportWaybill[klpto_comment]"],' +
    'input[name="TransportWaybill[trip_event1_id]"],' +
    'textarea[name="TransportWaybill[trip_event1_comment]"],' +
    'input[name="TransportWaybill[trip_event2_id]"],' +
    'textarea[name="TransportWaybill[trip_event2_comment]"],' +
    'input[name="TransportWaybill[trip_event3_id]"],' +
    'textarea[name="TransportWaybill[trip_event3_comment]"],' +
    'input[name="TransportWaybill[trip_event4_id]"],' +
    'textarea[name="TransportWaybill[trip_event4_comment]"],' +
    'input[name="TransportWaybill[trip_event5_id]"],' +
    'textarea[name="TransportWaybill[trip_event5_comment]"],' +
    'input[name="TransportWaybill[trip_event6_id]"],' +
    'textarea[name="TransportWaybill[trip_event6_comment]"],' +
    'input[name="TransportWaybill[trip_event7_id]"],' +
    'textarea[name="TransportWaybill[trip_event7_comment]"],' +
    'input[name="TransportWaybill[trip_event8_id]"],' +
    'textarea[name="TransportWaybill[trip_event8_comment]"],' +

        // блок "Корректировка"
    'input[name="TransportWaybill[camera_val]"],' +
    'input[name="TransportWaybill[camera_driver_val]"],' +
    'input[name="TransportWaybill[camera_eduction]"],' +
    'input[name="TransportWaybill[camera_no_record]"],' +
    'textarea[name="TransportWaybill[camera_no_record_comment]"],' +
    'input[name="TransportWaybill[hand_over_b1]"],' +
    'input[name="TransportWaybill[hand_over_b1_data]"],' +
    'input[name="TransportWaybill[hand_over_b2]"],' +
    'input[name="TransportWaybill[hand_over_b2_data]"],' +
    'textarea[name="TransportWaybill[correct_comment]"],' +

        // блок "Начисления"
    'input[name="TransportWaybill[accruals_to_issue_for_trip]"],' +
    'input[name="TransportWaybill[accruals_given_to_hand]"],' +
    'input[name="TransportWaybill[fines_gibdd]"],' +
    'textarea[name="TransportWaybill[fines_gibdd_comment]"],' +
    'input[name="TransportWaybill[another_fines]"],' +
    'textarea[name="TransportWaybill[another_fines_comment]"]',

    function() {

        var waybill_id = $('#waybill-form').attr('transport-waybill-id');
        var field_name = $(this).attr('name');
        var field_value = $(this).val();
        //console.log('change name='+name);

        var allow_minus_opearation = $('#waybill-form').attr('allow-minus-opearation');

        if(
            field_name.indexOf('[is_visible]') > -1
            || field_name.indexOf('[pre_trip_med_check]') > -1
            || field_name.indexOf('[pre_trip_tech_check]') > -1
            || field_name.indexOf('[after_trip_med_check]') > -1
            || field_name.indexOf('[after_trip_tech_check]') > -1
        ) {
            field_value = $(this).is(':checked');
            if(field_value == true) {
                field_value = 1;
            }else {
                field_value = 0;
            }
        }

        $.ajax({
            url: '/waybill/transport-waybill/ajax-save-waybill-field?waybill_id=' + waybill_id,
            type: 'post',
            data: {
                field_name: field_name,
                field_value: field_value
            },
            success: function (response) {

                if (response.success == true) { // field changes saved

                    if(response.preliminary_results_html != undefined && response.preliminary_results_html != '') {
                        $('#preliminary-results-block').html(response.preliminary_results_html);
                    }
                    if(response.accruals_html != undefined && response.accruals_html != '') {
                        $('#accruals-block').html(response.accruals_html);

                        updateAccrualsBlockJS(allow_minus_opearation);
                    }
                    if(response.correct_html != undefined && response.correct_html != '') {
                        $('#correct-block').html(response.correct_html);

                        updateCorrectBlockJS(allow_minus_opearation);
                    }


                    if(
                        response.field_name == 'date_of_issue'
                        || response.field_name == 'transport_id'
                        || response.field_name == 'driver_id'

                        || response.field_name == 'pre_trip_med_check'
                        || response.field_name == 'pre_trip_tech_check'
                        || response.field_name == 'after_trip_med_check'
                        || response.field_name == 'after_trip_tech_check'
                    ) {
                        location.reload();
                    }


                } else {
                    //alert('errors');
                    $('#waybill-form').yiiActiveForm('updateAttribute', 'transportwaybill-' + response.field_name, response.errors);

                    var error = '';
                    for(var key in response.errors) {
                        error += response.errors[key];
                    }
                    alert(error);
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


// Блок "Учет расходов" - обработка ошибок
$(document).on('change',
    '.transportexpenses-expenses_seller_type_id,' +
    '.transportexpenses-expenses_seller_id,' +
    '.transportexpenses-price,' +
    '.transportexpenses-count,' +
    '.transportexpenses-points,' +
    '.transportexpenses-expenses_doc_type_id,' +
    '.transportexpenses-expenses_type_id,' +
    '.transportexpenses-doc_number,' +
    '.transportexpenses-need_pay_date,' +
    '.transportexpenses-check_attached,' +
    '.transportexpenses-expenses_is_taken,' +
    '.transportexpenses-expenses_is_taken_comment,' +
    '.transportexpenses-payment_date,' +
    '.transportexpenses-payment_method_id,' +
    '.transportexpenses-transport_expenses_paymenter_id,' +
    '.transportexpenses-payment_comment',
    function() {

        var $obj = $(this);

        var allow_minus_opearation = $('#waybill-form').attr('allow-minus-opearation');

        var transport_expenses_id = $(this).parents('.transport-expenses').attr('transport-expenses-id');
        var is_form_detailing_form = false;
        if(transport_expenses_id == undefined) {
            transport_expenses_id = $('#detailing-title-form').attr('transport_expenses_id');
            is_form_detailing_form = true;
        }
        var field_name = $(this).attr('name');
        var field_value = $(this).val();
        //console.log('change name='+field_name);

        if(
            field_name.indexOf('[price]') > -1
            || field_name.indexOf('[points]') > -1
            || field_name.indexOf('[count]') > -1
        ) {
            field_value = field_value.replace(',', '.');
            field_value = field_value.replace(/\s/g,"");
        }

        if(
            field_name.indexOf('[expenses_is_taken]') > -1
            || field_name.indexOf('[check_attached]') > -1
        ) {
            field_value = $(this).is(':checked');
            if(field_value == true) {
                field_value = 1;
            }else {
                field_value = 0;
            }
        }

        // если имени поля отсутствует TransportExpenses, значит это что-то левое - не сохраняем
        if(field_name.indexOf('TransportExpenses') == -1) {
            return true;
        }

        $.ajax({
            url: '/waybill/transport-expenses-detailing/ajax-save-expenses-field?transport_expenses_id=' + transport_expenses_id,
            type: 'post',
            data: {
                field_name: field_name,
                field_value: field_value
            },
            success: function (response) {

                if (response.success == true) {// field changes saved

                    if(response.preliminary_results_html != undefined && response.preliminary_results_html != '') {
                        $('#preliminary-results-block').html(response.preliminary_results_html);
                    }
                    if(response.accruals_html != undefined && response.accruals_html != '') {
                        $('#accruals-block').html(response.accruals_html);

                        updateAccrualsBlockJS(allow_minus_opearation);
                    }
                    if(response.correct_html != undefined && response.correct_html != '') {
                        $('#correct-block').html(response.correct_html);

                        updateCorrectBlockJS(allow_minus_opearation);
                    }

                    if(is_form_detailing_form == true) {
                        $('*[name="' + field_name + '"]').val(field_value);
                        //console.log('установили значение '+field_value);
                    }

                } else {

                    var error = '';
                    for(var key in response.errors) {
                        error += response.errors[key];
                    }
                    alert(error);

                    //$('#waybill-form').yiiActiveForm('updateAttribute', 'transportexpenses-' + response.field_name, response.errors);
                    //console.log('field=transportexpenses-' + response.field_name + transport_expenses_id);
                    //$('#waybill-form').yiiActiveForm('updateAttribute', 'transportexpenses-' + response.field_name + transport_expenses_id, response.errors);
                    var class_name = 'field-transportexpenses-' + response.field_name + '-' + transport_expenses_id;
                    //console.log('class_name='+class_name);
                    //$('.' + class_name).addClass('has-error');

                    setTimeout(function() {
                        $('.' + class_name).addClass('has-error');
                    }, 300);


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


function updateCorrectBlockJS(allow_minus_opearation) {

    $('#transportwaybill-hand_over_b1_data').datepicker();
    $('#transportwaybill-hand_over_b2_data').datepicker();

    // По камерам
    var numberMask = new IMask(
        document.getElementById('transportwaybill-camera_val'),
        {
            mask: Number,
            min: (allow_minus_opearation === "1" ? -10000000 : 0),
            max: 10000000,
            thousandsSeparator: ''
        });
    // Из них указано водителем
    var numberMask = new IMask(
        document.getElementById('transportwaybill-camera_driver_val'),
        {
            mask: Number,
            min: (allow_minus_opearation === "1" ? -10000000 : 0),
            max: 10000000,
            thousandsSeparator: ''
        });
    // Вычет
    var currencyMask = new IMask(
        document.getElementById('transportwaybill-camera_eduction'),
        {
            mask: 'num',
            blocks: {
                num: {
                    // nested masks are available!
                    mask: Number,
                    thousandsSeparator: ' ',
                    min: (allow_minus_opearation === "1" ? -10000000 : 0)
                }
            }
        });
    // B1 - Сдано
    var currencyMask = new IMask(
        document.getElementById('transportwaybill-hand_over_b1'),
        {
            mask: 'num',
            blocks: {
                num: {
                    // nested masks are available!
                    mask: Number,
                    thousandsSeparator: ' ',
                    min: (allow_minus_opearation === "1" ? -10000000 : 0)
                }
            }
        });
    // B2 - Сдано
    var currencyMask = new IMask(
        document.getElementById('transportwaybill-hand_over_b2'),
        {
            mask: 'num',
            blocks: {
                num: {
                    // nested masks are available!
                    mask: Number,
                    thousandsSeparator: ' ',
                    min: (allow_minus_opearation === "1" ? -10000000 : 0)
                }
            }
        });
    // Без записи
    var currencyMask = new IMask(
        document.getElementById('transportwaybill-camera_no_record'),
        {
            mask: 'num',
            blocks: {
                num: {
                    // nested masks are available!
                    mask: Number,
                    thousandsSeparator: ' ',
                    min: (allow_minus_opearation === "1" ? -10000000 : 0)
                }
            }
        });
}

function updateAccrualsBlockJS(allow_minus_opearation) {

    // К выдаче на рейс
    var currencyMask = new IMask(
        document.getElementById('transportwaybill-accruals_to_issue_for_trip'),
        {
            mask: 'num',
            blocks: {
                num: {
                    // nested masks are available!
                    mask: Number,
                    thousandsSeparator: ' ',
                    min: (allow_minus_opearation === "1" ? -10000000 : 0)
                }
            }
        });
    // Выдано на руки
    var currencyMask = new IMask(
        document.getElementById('transportwaybill-accruals_given_to_hand'),
        {
            mask: 'num',
            blocks: {
                num: {
                    // nested masks are available!
                    mask: Number,
                    thousandsSeparator: ' ',
                    min: (allow_minus_opearation === "1" ? -10000000 : 0)
                }
            }
        });
    // Штрафы ГИБДД
    var currencyMask = new IMask(
        document.getElementById('transportwaybill-fines_gibdd'),
        {
            mask: 'num',
            blocks: {
                num: {
                    // nested masks are available!
                    mask: Number,
                    thousandsSeparator: ' ',
                    min: (allow_minus_opearation === "1" ? -10000000 : 0)
                }
            }
        });
    // Прочие штрафы
    var currencyMask = new IMask(
        document.getElementById('transportwaybill-another_fines'),
        {
            mask: 'num',
            blocks: {
                num: {
                    // nested masks are available!
                    mask: Number,
                    thousandsSeparator: ' ',
                    min: (allow_minus_opearation === "1" ? -10000000 : 0)
                }
            }
        });
}

$(document).ready(function() {

    var allow_minus_opearation = $('#waybill-form').attr('allow-minus-opearation');

    $('.transportexpenses-price').each(function() {

        var id = $(this).attr('id');

        var currencyMask = new IMask(
            document.getElementById(id),
            {
                mask: 'num',
                blocks: {
                    num: {
                        // nested masks are available!
                        mask: Number,
                        thousandsSeparator: ' ',
                        min: (allow_minus_opearation === "1" ? -10000000 : 0)
                    }
                }
            });
    });

    $('.transportexpenses-points').each(function() {

        var id = $(this).attr('id');

        var currencyMask = new IMask(
            document.getElementById(id),
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

    // transportexpenses-count
    $('.transportexpenses-count').each(function() {

        var id = $(this).attr('id');

        var currencyMask = new IMask(
            document.getElementById(id),
            {
                mask: Number,
                min: 0,
                max: 10000,
                thousandsSeparator: ' '
            });
    });

    // Показания пробега
    var numberMask = new IMask(
        document.getElementById('transportwaybill-mileage_before_departure'),
        {
            mask: Number,
            min: 0,
            max: 10000000,
            thousandsSeparator: ' '
        });
    // Показания пробега
    var numberMask = new IMask(
        document.getElementById('transportwaybill-mileage_after_departure'),
        {
            mask: Number,
            min: 0,
            max: 10000000,
            thousandsSeparator: ' '
        });

    updateCorrectBlockJS(allow_minus_opearation);
    updateAccrualsBlockJS(allow_minus_opearation);
});

// min: (allow_minus_opearation === "1" ? -10000000 : 0)


function addNewSeller($obj) {

    var new_name = $obj.next(".sw-outer-block").find("input.sw-search").val();

    $.ajax({
        url: '/waybill/transport-expenses-detailing/ajax-add-new-seller?new_name=' + new_name,
        type: 'post',
        data: {},
        success: function (response) {

            if (response.success == true) {
                selectWidgetInsertValue($obj, response.seller_id, response.name)
            } else {

                var error = '';
                for(var key in response.errors) {
                    error += response.errors[key];
                }
                alert(error);
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

function addNewSellerType($obj) {

    var new_name = $obj.next(".sw-outer-block").find("input.sw-search").val();

    $.ajax({
        url: '/waybill/transport-waybill/ajax-add-new-seller-type?new_name=' + new_name,
        type: 'post',
        data: {},
        success: function (response) {

            if (response.success == true) {
                selectWidgetInsertValue($obj, response.seller_type_id, response.name)
            } else {

                var error = '';
                for(var key in response.errors) {
                    error += response.errors[key];
                }
                alert(error);
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


