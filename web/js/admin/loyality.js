
var allow_submit = true;
$(document).on('click', '#rewrite-loyality', function() {

    var date_from = $('#rewrite-date-from').val();
    var date_to = $('#rewrite-date-to').val();

    if(allow_submit == true) {

        $.ajax({
            url: '/admin/loyality/ajax-rewrite',
            type: 'post',
            data: {
                date_from: date_from,
                date_to: date_to
            },
            beforeSend: function () {
                allow_submit = false;
                $('body').css('cursor', 'wait');
            },
            success: function (response) {
                allow_submit = true;
                alert('Готово');
                location.reload();
            },
            error: function (data, textStatus, jqXHR) {
                allow_submit = true;
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
                $('body').css('cursor', 'default');
            }
        });
    }else {
        alert('хватить жать на кнопку - запрос обрабатывается...');
    }
});



function recountClientsCounters(limit, client_id_from, last_client_id) {

    $.ajax({
        url: '/admin/loyality/ajax-rewrite-clients-counters?limit='+limit+'&client_id_from='+client_id_from,
        type: 'post',
        data: {},
        success: function (response) {
            // allow_submit_rewrite_clients_counters = true;
            // $('body').css('cursor', 'default');
            // alert('Готово');

            var client_id_from = parseInt(response.current_step_last_client_id);
            if (client_id_from < last_client_id)
            {
                var persent = Math.round(100*client_id_from/last_client_id);
                $('#recount_clients_progress').show();
                $('#recount_clients_progress progress').val(persent);

                recountClientsCounters(limit, client_id_from, last_client_id);

            }else {

                $('#recount_clients_progress progress').val(0);
                $('#recount_clients_progress').hide();

                allow_submit_rewrite_clients_counters = true;
                $('body').css('cursor', 'default');

                alert('Готово');
            }
        },
        error: function (data, textStatus, jqXHR) {
            allow_submit_rewrite_clients_counters = true;
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
            $('body').css('cursor', 'default');
        }
    });
}


var allow_submit_rewrite_clients_counters = true;
$(document).on('click', '#rewrite-clients-counters', function() {

    if(allow_submit_rewrite_clients_counters == true) {

        allow_submit_rewrite_clients_counters = false;
        $('body').css('cursor', 'wait');
        $.ajax({
            url: '/admin/loyality/ajax-get-last-client-id',
            type: 'post',
            data: {},
            success: function (response) {
                // allow_submit_rewrite_clients_counters = true;
                // $('body').css('cursor', 'default');
                // alert('Готово');

                //$('#recount_clients').val('IN PROGRESS...');
                // response = $.parseJSON(response);
                // 2. разбить film_id по 500 шагов, и отправляя на каждый шаг по ajax-запросу  обновлять прогресс-бар
                recountClientsCounters(500, 0, parseInt(response.last_client_id)); // поехали!    - 208384
            },
            error: function (data, textStatus, jqXHR) {

                allow_submit_rewrite_clients_counters = true;
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