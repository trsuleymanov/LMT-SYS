
$(document).ready(function()
{
    /*$(document).on('submit', '#login-form', function(event, jqXHR, settings)
    {
        event.preventDefault();
        event.stopImmediatePropagation();

        var form = $(this);
        var formData = new FormData($(this)[0]);

        $.ajax({
            url: form.attr('action'),
            type: 'post',
            data:  formData,
            contentType: false,
            cache: false,
            processData:false,
            success: function(response) {
                if (response.success == true) {
                    if(response.redirect_url != void 0) {
                        alert('совершаем переход на страницу http://tobus-yii2.ru/  ' + response.redirect_url);
                        window.location.reload('http://tobus-yii2.ru/');
                    }
                }else {
                    alert('ошибка');
                }
            },
            error: function (data, textStatus, jqXHR) {
                if (textStatus == 'error') {
                    if (void 0 !== data.responseJSON) {
                        if (data.responseJSON.message.length > 0) {
                            alert(data.responseJSON.message);
                        }
                    } else {
                        if (data.responseText.length > 0) {
                            alert(data.responseText);
                        }
                    }
                }
            }
        });

        return false;
    });*/

    $('*[name="LoginForm[username]"]').focus();
});