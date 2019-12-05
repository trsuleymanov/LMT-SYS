

$(document).on('click', '#dump-database', function() {

    // 1. посылается запрос на сервер "нужно создать дамп базы" - он создается и сохраняется в файле
    // 2. в результате должна быть возможность сохранить копию файла к себе на комп
    $.ajax({
        url: '/admin/rescue/ajax-dump-database',
        type: 'post',
        data: {},
        success: function (response) {
            alert('Готово (но лучше подождать пару секунд)');
            $('#download-file').attr('href', response.file_href).show();
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


$(document).on('click', '#dump-storage', function() {

    // 1. посылается запрос на сервер "нужно создать дамп базы" - он создается и сохраняется в файле
    // 2. в результате должна быть возможность сохранить копию файла к себе на комп
    $.ajax({
        url: '/admin/rescue/ajax-dump-storage',
        type: 'post',
        data: {},
        success: function (response) {
            alert('Готово (но лучше подождать пару секунд)');
            $('#download-file').attr('href', response.file_href).show();
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
