

$(document).ready(function()
{
    // Панель управления
    $('.main-header').on('click', '.sidebar-toggle', function(){
        var $status;
        if($('body').hasClass('sidebar-collapse')){
            $status = 0;
        }else{
            $status = 1;
        }
        $.ajax({
            url: '/admin/setting/main-menu-status',
            type: 'POST',
            data: {
                'status': $status
            },
            error: function(){
                console.log('Внутренняя ошибка сервера');
            }
        });
    });


    $(document).on('click', '#create-driver-user', function() {

        if (void 0 !== $(this).attr('disabled') || $(this).hasClass('disabled')) {
            alert('Вначале сохраните текущего водителя');
            return false;
        }

        var driver_id = $(this).attr('driver-id');

        $.ajax({
            url: '/admin/driver/ajax-create-user-like-driver?id=' + driver_id,
            type: 'post',
            data: {},
            success: function (data) {

                if (data.success == true) {

                    selectWidgetInsertValue($('input[name="Driver[user_id]"]').parents('.sw-element'), data.user.id, data.user.lastname + ' ' + data.user.firstname);

                    alert('Пользователь создан. Не забудьте сохранить изменения в водителе.');

                } else {
                    var errors = '';
                    for (var key in data.errors) {
                        errors += data.errors[key] + '. ';
                    }

                    alert(errors);
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

    $(document).on('click', '.detail-name-page .btn-edit-detail-name', function() {
        $(this).parent().find('.detail-name').hide();
        $(this).hide();
        $(this).parent().find('.edit-detail-name').show().focus();
    });

    $(document).on('keyup', '.detail-name-page .edit-detail-name', function(e) {

        var $obj = $(this);
        var detail_name_id = $(this).parents('tr').attr('data-key');
        var new_name = $.trim($(this).val());

        if(e.keyCode == 13) {

            if(confirm('Сохранить изменения')) {
                $.ajax({
                    url: '/admin/detail-name/ajax-update?id=' + detail_name_id,
                    type: 'post',
                    data: {
                        name: new_name
                    },
                    success: function (response) {

                        if (response.success == true) {

                            $obj.val(response.model.name).hide();
                            $obj.parent().find('.detail-name').text(response.model.name).show();
                            $obj.parent().find('.btn-edit-detail-name').show();

                        } else {
                            var errors = '';
                            for (var key in response.errors) {
                                errors += response.errors[key] + '. ';
                            }

                            alert(errors);
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
                        } else {
                            handlingAjaxError(data, textStatus, jqXHR);
                        }
                    }
                });

            }else {
                $obj.hide();
                $obj.parent().find('.detail-name').show();
                $obj.parent().find('.btn-edit-detail-name').show();
            }

        }else if(e.keyCode == 27) {
            $obj.hide();
            $obj.parent().find('.detail-name').show();
            $obj.parent().find('.btn-edit-detail-name').show();
        }
    });
});