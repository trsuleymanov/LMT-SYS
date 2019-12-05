
// обновление списка онлайн пользователей
function updateOnlineOperator() {

    $.ajax({
        url: '/admin/operator/get-online-operators',
        type: 'post',
        data: {},
        contentType: false,
        cache: false,
        processData: false,
        success: function (html) {
            $('#active-operators-list').html(html);
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


$(document).ready(function() {

    // обновление списка онлайн пользователей
    setInterval(function () {
        updateOnlineOperator();
    }, 10000);

});