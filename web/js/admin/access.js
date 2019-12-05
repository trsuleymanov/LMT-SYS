

$(document).on('click', '.toggle-module', function() {

    var $obj = $(this);
    var module = $(this).parents('.access-place').attr('module');
    var count = $('.access-place[parent-module="' + module + '"]').length;
    if($obj.attr('is-open') === "true") {

        var i = 0;
        $('.access-place[parent-module="' + module + '"]').hide(250, function () {
            i++;
            if (i === count) {
                $obj.attr('is-open', false);
                $obj.removeClass('glyphicon-minus-sign').addClass('glyphicon-plus-sign');
            }
        });

    }else {

        var i = 0;
        $('.access-place[parent-module="' + module + '"]').show(250, function () {
            i++;
            if (i === count) {
                $obj.attr('is-open', true);
                $obj.removeClass('glyphicon-plus-sign').addClass('glyphicon-minus-sign');
            }
        });
    }
});

$(document).on('change', '.access-checkbox', function() {

    var role_id = $(this).attr('role-id');
    var place_id = $(this).attr('place-id');
    var access_value = ($(this).is(':checked') === true ? 1 : 0);

    $.ajax({
        url: '/admin/access/ajax-set-access?role_id='+role_id+'&place_id=' + place_id + '&access_value=' + access_value,
        type: 'post',
        data: {},
        success: function (response) {
            //location.reload();
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