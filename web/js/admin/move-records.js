
var allow_move_records_submit = true;
$(document).on('click', '#move-records', function() {

    if(allow_move_records_submit == true) {

        var ftp_server = $.trim($('#ftp_server').val());
        var ftp_login = $.trim($('#ftp_login').val());
        var ftp_password = $.trim($('#ftp_password').val());
        var ftp_path = $.trim($('#ftp_path').val());
        var beeline_token_api = $.trim($('#beeline_token_api').val());

        if(ftp_server == '') {
            alert('Установите адрес сервера');
            return false;
        }
        if(ftp_login == '') {
            alert('Установите ftp-логин');
            return false;
        }
        if(ftp_password == '') {
            alert('Установите ftp-пароль');
            return false;
        }
        if(ftp_path == '') {
            alert('Установите ftp директорию');
            return false;
        }
        if(beeline_token_api == '') {
            alert('Установите токен API');
            return false;
        }


        $.ajax({
            url: '/admin/rescue/ajax-move-records',
            type: 'post',
            data: {
                ftp_server: ftp_server,
                ftp_login: ftp_login,
                ftp_password: ftp_password,
                ftp_path: ftp_path,
                beeline_token_api: beeline_token_api
            },
            beforeSend: function () {
                allow_move_records_submit = false;
                $('body').css('cursor', 'wait');
            },
            success: function (response) {
                allow_move_records_submit = true;
                $('body').css('cursor', 'default');
                alert('Готово');
            },
            error: function (data, textStatus, jqXHR) {
                allow_move_records_submit = true;
                $('body').css('cursor', 'default');
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
