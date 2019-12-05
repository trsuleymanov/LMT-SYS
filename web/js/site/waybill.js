


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