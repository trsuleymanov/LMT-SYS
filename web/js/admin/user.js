
$(document).on('click', '#change-password', function() {

    var user_id = $(this).parents('#user-page').attr('user-id');

    $.ajax({
        url: '/admin/user/set-password?id='+user_id,
        type: 'post',
        data: {},
        success: function (response) {
            $('#default-modal').find('.modal-body').html(response);
            $('#default-modal .modal-dialog').width('400px');
            $('#default-modal .modal-title').html('Изменение пароля');
            $('#default-modal').modal('show');
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




$(document).ready(function()
{
    $(document).on('submit', '#change-password-form', function(event, jqXHR, settings)
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
                    $('#default-modal .modal-dialog').width('600px');
                    $('#default-modal').modal('hide');
                }else {
                    alert('ошибка создания пароля');
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
});


