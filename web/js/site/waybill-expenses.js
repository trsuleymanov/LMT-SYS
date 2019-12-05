
$(document).ready(function() {

    // прохожусь по каждой строчке таблицы и устанавливаю фиксированным колонкам ширину соответствующую не фиксированным строкам
    var num = 1;
    $('#expenses-data-table tr').each(function() {
        var $tr = $(this);
        if(num == 1) {
            var height = $tr.find('th').eq(8).css('height');

            height = parseInt(height) + 2 + 'px';
            //console.log('num='+num + ' height='+height);
            $tr.find('th').eq(0).css('height', height);
            $tr.find('th').eq(1).css('height', height);
            $tr.find('th').eq(2).css('height', height);
            $tr.find('th').eq(3).css('height', height);
            $tr.find('th').eq(4).css('height', height);
            $tr.find('th').eq(5).css('height', height);

        }else {
            var height = $tr.find('td').eq(8).css('height');

            if(num == 2) {
                height = parseInt(height) + 1 + 'px';
            }

            //console.log('num='+num + ' height='+height);
            $tr.find('td').eq(0).css('height', height);
            $tr.find('td').eq(1).css('height', height);
            $tr.find('td').eq(2).css('height', height);
            $tr.find('td').eq(3).css('height', height);
            $tr.find('td').eq(4).css('height', height);
            $tr.find('td').eq(5).css('height', height);
        }

        num++;

    });
});


// изменение админом в таблице Расходы значений в колонках: Дата оплаты, Кто оплатил, Комментарий к оплате приводит к
// сохранению значения
/*
// все автосохранения полей на странице Расходы заменяются сохранением по нажатию на "Сохранить изменения"
function updateExpenseField($obj, expense_id, field_name, field_value) {

    if(expense_id == undefined) {
        var expense_id = $obj.attr('expense-id');
    }
    if(field_name == undefined) {
        var field_name = $obj.attr('field');
    }
    if(field_value == undefined) {
        var field_value = $obj.val();
    }

    $.ajax({
        url: '/waybill/transport-waybill/ajax-update-expense-field?expense_id=' + expense_id + '&field_name=' + field_name + '&field_value=' + field_value,
        type: 'post',
        data: {},
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
}

$(document).on('change', '*[field="expenses_is_taken"]', function() {

    var expense_id = $(this).attr('expense-id');
    var field_name = $(this).attr('field');

    var field_value = $(this).is(':checked');
    if(field_value == true) {
        field_value = 1;
    }else {
        field_value = 0;
    }

    updateExpenseField($(this), expense_id, field_name, field_value);
});

$(document).on('change', '*[field="payment_date"]', function() {
    updateExpenseField($(this));
});
$(document).on('change', '*[field="payment_method_id"]', function() {
    updateExpenseField($(this));
});
$(document).on('change', '*[field="transport_expenses_paymenter_id"]', function() {
    updateExpenseField($(this));
});
$(document).on('change', '*[field="payment_comment"]', function() {
    updateExpenseField($(this));
});
*/

// сохранение изменений в таблице нажатием на кнопку "Сохранить изменения"
$(document).on('click', '#save-expenses-table', function() {

    // по всем открытым строкам по полю "Оплата" собираем данные
    var form_data = [];
    $('#expenses-data-table tbody').find('tr').each(function() {

        var id = $(this).attr('data-key');
        var expenses_is_taken = $(this).find('input[field="expenses_is_taken"]').is(':checked');
        var payment_date = $(this).find('input[field="payment_date"]').val();
        var payment_method_id = $(this).find('*[field="payment_method_id"]').val();
        var transport_expenses_paymenter_id = $(this).find('input[field="transport_expenses_paymenter_id"]').val();
        var payment_comment = $(this).find('*[field="payment_comment"]').val();

        //console.log('id=' + id + ' value='+value);
        form_data.push({
            id: id,
            expenses_is_taken: expenses_is_taken,
            payment_date: payment_date,
            payment_method_id: payment_method_id,
            transport_expenses_paymenter_id: transport_expenses_paymenter_id,
            payment_comment: payment_comment
        });
    });

    //console.log(form_data);

    $.ajax({
        url: '/waybill/transport-waybill/ajax-update-expenses-fields',
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