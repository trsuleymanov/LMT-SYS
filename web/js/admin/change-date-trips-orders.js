
var allow_submit = true;
$(document).on('click', '#change-date-trips-orders', function() {

    var date = $('#date-trips-orders').val();

    if(allow_submit == true) {

        $.ajax({
            url: '/admin/rescue/change-date-trips-orders',
            type: 'post',
            data: {
                date: date
            },
            beforeSend: function () {
                allow_submit = false;
            },
            success: function (response) {
                allow_submit = true;
                alert('Готово');
            },
            error: function (data, textStatus, jqXHR) {
                allow_submit = true;
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
});
