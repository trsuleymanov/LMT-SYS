$(document).ready(function() {

    $(document).on('click', '.btn-penalty', function() {

        if ($(this).hasClass('disabled')) {
            return false;
        }

        var $obj = $(this);
        var order_id = $(this).parents('tr').attr('data-key');

        //$.ajax({
        //    url: '/client/ajax-set-penalty?order_id='+order_id,
        //    type: 'post',
        //    data: {},
        //    success: function (response) {
        //        if(response.success == true) {
        //            $obj.addClass('disabled');
        //        }else {
        //            alert('неопределенная ошибка установки штрафа клиенту');
        //        }
        //    },
        //    error: function (data, textStatus, jqXHR) {
        //        if (textStatus == 'error') {
        //            if (void 0 !== data.responseJSON) {
        //                if (data.responseJSON.message.length > 0) {
        //                    alert(data.responseJSON.message);
        //                }
        //            } else {
        //                if (data.responseText.length > 0) {
        //                    alert(data.responseText);
        //                }
        //            }
        //        }
        //    }
        //});


        $.ajax({
            url: '/order/ajax-get-penalty-form?order_id='+order_id,
            type: 'post',
            data: {},
            success: function (response) {

                $('#default-modal').find('.modal-body').html(response.html);
                $('#default-modal').find('.modal-dialog').width('600px');
                $('#default-modal .modal-title').html('Штраф для клиента ' + response.client.name);
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

    var allow_submit_penalty_form = true;
    $(document).on('submit', '#penalty-form', function() {

        var order_id = $(this).attr('order-id');

        if(allow_submit_penalty_form == true) {

            allow_submit_penalty_form = false;
            $.ajax({
                url: '/order/ajax-get-penalty-form?order_id=' + order_id,
                type: 'post',
                data: $('#penalty-form').serialize(),
                success: function (response) {

                    if (response.success == true) {
                        $('tr[data-key="' + order_id + '"] .btn-penalty').addClass('disabled');
                        $('#default-modal').modal('hide');
                    } else {
                        alert('Ошибка');
                    }
                    allow_submit_penalty_form = true;
                },
                error: function (data, textStatus, jqXHR) {
                    allow_submit_penalty_form = true;
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

        return false;
    });
});