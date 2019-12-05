
$(document).on('click', '.chat-widget .btn-send-message', function() {

    var lifetime = $('.chat-widget #lifetime').val();
    var to_begining = $('.chat-widget input[name="to-begining"]').is(':checked');
    var message = $.trim($('.chat-widget #chat-message').html());

    if(message === '') {
        alert('Введите сообщение');
        return false;
    }

    if(lifetime === '') {
        alert('Установите время жизни сообщения');
        return false;
    }

    $.ajax({
        url: '/site/ajax-save-chat-message',
        type: 'post',
        data: {
            lifetime: lifetime,
            to_begining: to_begining,
            message: message
        },
        success: function (response) {
            if(response.success === true) {
                alert('Сообщение сохранено');
                location.reload();
            }else {
                alert('неизвестная ошибка');
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
});

$(document).on('click', '.btn-send-message-answer', function() {

    var $message_send_form_block = $(this).parents('.message-send-form-block');
    var dialog_id = $message_send_form_block.attr('dialog-id');
    var message = $.trim($message_send_form_block.find('.chat-message-answer').html());
    if(message === '') {
        alert('Введите сообщение');
        return false;
    }

    // alert('dialog_id='+dialog_id+' message='+message);

    $.ajax({
        url: '/site/ajax-save-chat-message?dialog_id=' + dialog_id,
        type: 'post',
        data: {
            message: message
        },
        success: function (response) {
            if(response.success === true) {
                alert('Сообщение сохранено');
                location.reload();
            }else {
                alert('неизвестная ошибка');
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
});



// вставка текста в выделенную область "текстового" поля чата
function insertHTML(html) {
    try {
        var selection = window.getSelection(),
            range = selection.getRangeAt(0),
            temp = document.createElement('div'),
            insertion = document.createDocumentFragment();

        temp.innerHTML = html;

        while (temp.firstChild) {
            insertion.appendChild(temp.firstChild);
        }

        range.deleteContents();
        range.insertNode(insertion);
    } catch (z) {
        try {
            document.selection.createRange().pasteHTML(html);
        } catch (z) {}
    }
}


var getSelectedText = function() {
    var text = '';
    if (window.getSelection) {
        text = window.getSelection().toString();
    } else if (document.selection) {
        text = document.selection.createRange().text;
    }
    return text;
}

// button-b
$(document).on('click', '.chat-widget #button-b', function() {

    $('#chat-message').trigger('focus');
    var selected_text = getSelectedText();
    insertHTML('<b>' + selected_text + '</b>');
    //alert(window.getSelection().getRangeAt(0));
    window.getSelection().removeAllRanges();

    return false;
});

$(document).on('click', '.chat-widget #button-i', function() {

    $('#chat-message').trigger('focus');
    var selected_text = getSelectedText();
    insertHTML('<i>' + selected_text + '</i>');
    window.getSelection().removeAllRanges();

    return false;
});

$(document).on('click', '.chat-widget #button-u', function() {

    $('#chat-message').trigger('focus');
    var selected_text = getSelectedText();
    insertHTML('<u>' + selected_text + '</u>');
    window.getSelection().removeAllRanges();

    return false;
});

$(document).on('change', '.chat-widget #button-color', function() {

    var color = $(this).val();

    $('#chat-message').trigger('focus');
    var selected_text = getSelectedText();
    insertHTML('<span style="color: ' + color + '">' + selected_text + '</span>');
    window.getSelection().removeAllRanges();
});

// вставка по сути не являющая частью виджета!
$(document).on('click', '.chat-close', function() {

    $('#chat-block').removeAttr('is-open');
    $('.chat-widget').remove();
});

$(document).on('click', '.answer-close', function() {

    $(this).parents('.message-send-form-block').remove();
});