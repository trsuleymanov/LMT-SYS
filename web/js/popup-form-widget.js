$(document).on('click', '.pfw-element', function() {

    //alert('click');

    /*
    var id = $(this).attr('id');
    $('.pfw-popup-form').hide();

    if($('#' + id + '-pfw-popup-form').length > 0) {

        var popup_form = $('#' + id + '-pfw-popup-form');

        if($('#' + id + '-pfw-popup-form').hasClass('left')) {
            var top = -$(this).height()/2 - popup_form.height()/2 - 8;
            var left = -popup_form.width() - 12;
        }else { //right
            var top = -$(this).height()/2 - popup_form.height()/2 - 8;
            var left = $(this).width() + 11;
        }

        popup_form.css({
            top: top,
            left: left
        }).show();
    }*/

    $('.pfw-popup-form').hide();

    var $popup_form = $(this).next('.pfw-popup').find('.pfw-popup-form');
    if($popup_form.hasClass('left')) {
        var top = -$(this).height()/2 - $popup_form.height()/2 - 8;
        var left = -$popup_form.width() - 12;
    }else { //right
        var top = -$(this).height()/2 - $popup_form.height()/2 - 8;
        var left = $(this).width() + 11;
    }

    $popup_form.css({
        top: top,
        left: left
    }).show();
});


$(document).on('click', '.pfw-popup-form .close', function() {
    $(this).parents('.pfw-popup-form').hide();
});

// accept button
$(document).on('click', '.pfw-popup-form .pfw-accept', function() {

    var form = $(this).parents('.pfw-popup-form');
    var element_id = form.attr('for');
    var elem = $('#' + element_id);

    if(pfw_setting[element_id].onAccept != undefined) {
        pfw_setting[element_id].onAccept(elem, form);
    }else
    {
        var text = $.trim($(this).parents('.pfw-content').find('.pfw-input').val());

        if (text.length == 0) {
            var text = elem.attr('default-value');
        }

        elem.text(text);
        form.hide();
    }
});

// cancel button
$(document).on('click', '.pfw-popup-form .pfw-cancel', function() {

    var form = $(this).parents('.pfw-popup-form');
    var element_id = form.attr('for');
    var elem = $('#' + element_id);

    if(pfw_setting[element_id].onCancel != undefined) {
        pfw_setting[element_id].onCancel(elem, form);
    }else
    {
        var text = elem.attr('default-value');
        $(this).parents('.pfw-content').find('.pfw-input').val('');
        elem.text(text);
        form.hide();
    }
});