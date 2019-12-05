

$(document).ready(function() {

    // прохожусь по каждой строчке таблицы и устанавливаю фиксированным колонкам ширину соответствующую не фиксированным строкам
    var num = 1;
    $('#list-data-table tr').each(function() {
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

function updateWaybillField(waybill_id, field_name, field_value) {

    $.ajax({
        url: '/waybill/transport-waybill/ajax-update-waybill-field?waybill_id=' + waybill_id + '&field_name=' + field_name + '&field_value=' + field_value,
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

$(document).on('click', '#list-data-table .to-hide-pl', function() {
    var waybill_id = $(this).parents('tr').attr('data-key');
    var field_name = 'is_visible';
    var field_value = 0;
    updateWaybillField(waybill_id, field_name, field_value)
});
$(document).on('click', '#list-data-table .to-show-pl', function() {
    var waybill_id = $(this).parents('tr').attr('data-key');
    var field_name = 'is_visible';
    var field_value = 1;
    updateWaybillField(waybill_id, field_name, field_value)
});


$(document).on('click', '#show-hidden-waybills', function() {

    location.href = '?TransportWaybillSearch[is_visible]=0';
    return false;
});
$(document).on('click', '#show-all-waybills', function() {

    location.href = 'list';
    return false;
});