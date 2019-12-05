
$(document).ready(function()
{
    // в модальном окне "Привязка транспортного средства к рейсу" щелчек по кнопке "Добавить еще"
    
    $(document).on('click', '#add-transport-driver-row', function()
    {
        $.ajax({
            url: '/trip-transport/ajax-get-add-car-tr?trip_id='+$(this).attr('trip_id'),
            type: 'post',
            data: {},
            contentType: false,
            cache: false,
            processData: false,
            success: function (data) {
                if (data.success == true) {
                    $('#add-transport-driver-row').parents('.row').before(data.tr_html);
		            //bindTransportConfirmedClick();
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

    
    // в модальном окне "Привязка транспортного средства к рейсу" щелчек по кнопке удаления строки
    $(document).on('click', '.delete-trip-transport', function()
    {
        $(this).parents('.trip-transport-row').remove();

        return false;
    });


    // кнопка изменения сортировки для строки trip-transport
    $(document).on('click', '.trip-transport-sort-up', function() {

        // текущую строку поднимаю выше строки что выше находиться
        var currect_row = $(this).parents('.trip-transport-row');
        var prev_row = currect_row.prev('.trip-transport-row');
        currect_row.after(prev_row);
        prev_row.find('.trip-transport-sort-up').removeAttr('disabled');

        if(currect_row.prev('.trip-transport-row').length == 0) {
            currect_row.find('.trip-transport-sort-up').attr('disabled', "true");
        }

        return false;
    });
});