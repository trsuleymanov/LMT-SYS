


$(document).on('mouseenter', '.waybill-share-comment', function () {
    $(this).next('.waybill-comment').show(300);
});
$(document).on('mouseleave', '.waybill-share-comment', function () {
    $(this).next('.waybill-comment').hide(300);
});

// перенос расхода в другой путевой лист
$(document).on('click', '.move-expense-to-another-pl', function() {

    var expense_id = $(this).attr('expense-id');

    $.ajax({
        url: '/waybill/transport-waybill/ajax-get-move-expense-form?expense_id=' + expense_id,
        type: 'post',
        data: {},
        success: function (response) {

            if(response.success === true) {


                $('#move-expense-to-another-pl-modal').find('.modal-body').html(response.html);
                //$('#move-expense-to-another-pl-modal .modal-title').html(response.title);
                $('#move-expense-to-another-pl-modal').modal('show');

                // $('#order-create-modal').css({
                //     padding: 0,
                //     'z-index': 10000
                // });


            }else {
                alert('ошибка загрузки формы');
            }
        },
        error: function (data, textStatus, jqXHR) {
            if (textStatus === 'error' && data !== undefined) {
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

function loadPlList() {

    var date = $.trim($('#move-expense-date-to').val());
    var transport_id = $('#move-expense-transport-to').val()

    if(date.length != 10 || transport_id == 0) {
        return false;
    }


    $.ajax({
        url: '/waybill/transport-waybill/ajax-search-waybills?date=' + date + '&transport_id=' + transport_id,
        type: 'post',
        data: {},
        success: function (response) {

            if(response.success === true) {

                var options = '<option value="0"></option>';
                var waybill_title = '';

                var num = 0;
                for (var waybill_id in response.list) {
                    options += '<option value="' + waybill_id + '">' + response.list[waybill_id] + '</option>';
                    num++;
                }

                if(num > 0) {
                    //console.log('options='+options);
                    $('#move-expense-waybill-to').html(options);
                    $('#select-pl-block').show();
                }else {
                    alert('Путевые листы не найдены');
                    $('#select-pl-block').hide();
                    $('#submit-button-block').hide();
                }

            }else {
                alert('ошибка');
            }
        },
        error: function (data, textStatus, jqXHR) {
            if (textStatus === 'error' && data !== undefined) {
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

$(document).on('change', '#move-expense-date-to', function() {
    //console.log('#move-expense-date-to');
    loadPlList();
});

$(document).on('change', '#move-expense-transport-to', function() {
    //console.log('#move-expense-transport-to');
    loadPlList();
});

$(document).on('change', '#move-expense-waybill-to', function() {
    var new_waybill_to_id = $(this).val();
    if(new_waybill_to_id > 0) {
        $('#submit-button-block').show();
    }else {
        $('#submit-button-block').hide();
    }
});

// нажатие на кнопку "Перенести" расход в другой путевой лист
$(document).on('click', '#move-expense-to-another-pl-modal #move-button', function() {

    var expense_id = $('#move-expense-id').val();
    var new_waybill_to_id = $('#move-expense-waybill-to').val();


    if(confirm("Вы уверены что нужно перенести расход?")) {

        $.ajax({
            url: '/waybill/transport-waybill/ajax-move-expense?expense_id=' + expense_id + '&waybill_to_id=' + new_waybill_to_id,
            type: 'post',
            data: {},
            success: function (response) {
                if (response.success === true) {
                    location.reload();
                } else {
                    alert('ошибка');
                }
            },
            error: function (data, textStatus, jqXHR) {
                if (textStatus === 'error' && data !== undefined) {
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

    return false;
});


// сохранение изменений в таблице нажатием на кнопку "Сохранить изменения"
$(document).on('click', '#save-transport-waybill-table', function() {

    // по всем открытым строкам по полю "Оплата" собираем данные
    var form_data = [];
    var id = 0;
    var hand_over_b1 = 0;
    var hand_over_b1_data = 0;
    var hand_over_b2 = 0;
    var hand_over_b2_data = 0;
    $('#list-data-table tbody').find('tr').each(function() {

        id = $(this).attr('data-key');
        // var expenses_is_taken = $(this).find('input[field="expenses_is_taken"]').is(':checked');
        // var payment_date = $(this).find('input[field="payment_date"]').val();
        // var payment_method_id = $(this).find('*[field="payment_method_id"]').val();
        // var transport_expenses_paymenter_id = $(this).find('input[field="transport_expenses_paymenter_id"]').val();
        // var payment_comment = $(this).find('*[field="payment_comment"]').val();


        hand_over_b1 = $(this).find('input[field="hand_over_b1"]').val();
        hand_over_b1_data = $(this).find('input[field="hand_over_b1_data"]').val();
        hand_over_b2 = $(this).find('input[field="hand_over_b2"]').val();
        hand_over_b2_data = $(this).find('input[field="hand_over_b2_data"]').val();
        //console.log('id=' + id + ' value='+value);
        form_data.push({
            id: id,
            hand_over_b1: hand_over_b1,
            hand_over_b1_data: hand_over_b1_data,
            hand_over_b2: hand_over_b2,
            hand_over_b2_data: hand_over_b2_data
        });
    });


    $.ajax({
        url: '/waybill/transport-waybill/ajax-update-waybill-fields',
        type: 'post',
        data: {
            form_data: form_data
        },
        success: function (response) {

            if(response.success == true) {
                location.reload();
            }else {
                alert('Неизвестная ошибка');
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
                //handlingAjaxError(data, textStatus, jqXHR);
            }
        }
    });

    return false;
});