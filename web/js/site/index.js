
// функция обновления главной страницы (рейсов)
function updateDirectionsTripBlock()
{
    // на вход: дата
    // html блока страницы с рейсами
    var date = $('#selected-day').attr('date');

    // если зажато АК/КА на главной странице, то восстановлю их после обновления страницы
    var direction_id = $('input[name="direction"]:checked').val();
    //console.log('direction_id='+direction_id);

    $.ajax({
        url: '/site/ajax-get-directions-trips-block?date=' + date,
        type: 'post',
        data: {},
        contentType: false,
        cache: false,
        processData: false,
        success: function (response) {
            if(response.success == true) {

                $('#directions-trips-block').html(response.html);
                if(direction_id != undefined) {
                    $('input[name="direction"][value="' + direction_id + '"]').prop("checked", "checked");
                }

            }else {
                alert('неустановленная ошибка обновления блока с рейсами');
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
}


function menu($obj, event, type_menu, data, horizontal_position, position_through_obj, shift_to_bottom, shift_to_right)
{
    // Блокируем всплывание события contextmenu
    event = event || window.event;
    event.cancelBubble = true;
    // Показываем собственное контекстное меню
    var menu = document.getElementById("contextMenuId");

    var html = "";


    if (horizontal_position == undefined) {
        horizontal_position = 'right'; // default
    }


    if (void 0 === type_menu) {
        type_menu = "";
    }


    console.log('type_menu='+type_menu);
    //console.log('data:'); console.log(data);

    var close_menu = false;

    if($obj.hasClass('openContextMenu') || $obj.parents('openContextMenu').length > 0)
    {
        close_menu = true;

    }else
    {
        $obj.addClass('openContextMenu');
        switch (type_menu) {

            case 'incoming-calls':

                html = "<label id='delete-incoming-calls'>удалить входящие звонки</label>";
                break;

            //case 'user_list_visibility':
            //
            //    //$(menu).attr('username', data['username']);
            //
            //    html = "<label class='row user-list-set-visibility'>private</label>";
            //    html += "<label class='row user-list-set-visibility'>friends</label>";
            //    html += "<label class='row user-list-set-visibility'>public</label>";
            //    html += "<label class='row user-list-set-visibility'>social</label>";
            //    break;

            //case 'user_list_types':
            //
            //    html = '';
            //    for(var i = 0; i < data['types'].length; i++) {
            //        var obType = data['types'][i];
            //        html += "<label class='row user-list-set-type' type-id='" + obType['id'] + "'>" + obType['name'] + "</label>";
            //    }
            //    break;

            //default:
            //    if(param == 'username') {
            //        html = "<label class='row link' href='" + site_url + 'u/' + param_value + "'>open in new tab</label>";
            //    }else {
            //        if (param_value.length > 0) {
            //            param_value = '=' + param_value;
            //        }
            //        html = "<label class='row link' href='" + site_url + "?" + param + param_value + else_url + "'>open in new tab</label>";
            //    }
            //    break;
        }
    }

    if (close_menu) {
        $obj.removeClass('openContextMenu');
        contextMenuClose();
    }

    //console.log('screen_width=' + screen.width + ' window_width=' + $(window).width());

    // на маленьких экранах меню показываем слева, а не справа.
    if($(window).width() <= 800 && position_through_obj == 'right_elem') {
        position_through_obj = 'left_elem';
    }

    // Если есть что показать - показываем
    if (close_menu == false && html)
    {
        menu.innerHTML = html;
        menu.style.display = "block";
        var menu_width = parseInt($(menu).css('width'));

        if (position_through_obj == undefined || position_through_obj == '')
        {
            menu.style.top = defPosition(event).y + "px";

            if (horizontal_position == 'right') {
                menu.style.left = defPosition(event).x + "px";
            }else {
                menu.style.left = defPosition(event).x - menu_width + "px";
            }

        }else if(position_through_obj == 'right_elem')
        {
            // у переданного объекта $obj вычисляю координаты правого верхнего угла, и там размещаю меню
            var offset = $obj.offset();
            var top = offset.top;
            var left = offset.left;
            var width = $obj.outerWidth();


            menu.style.top = top + (void 0 !== shift_to_bottom ? shift_to_bottom : 0) + "px";
            menu.style.left = left + width + (void 0 !== shift_to_right ? shift_to_right : 0) + "px";

        }else if(position_through_obj == 'left_elem') {

            // у переданного объекта $obj вычисляю координаты правого верхнего угла, и там размещаю меню
            var offset = $obj.offset();
            var top = offset.top;
            var left = offset.left;
            var width = $obj.outerWidth();

            menu.style.top = top + "px";
            menu.style.left = (left + width - menu_width - 38) + "px";

        }else if(position_through_obj == 'fbonotemenu') {

            var $kmblock = $obj.parents('.kmbox').find('.fbonote');

            var offset = $kmblock.offset();
            var top = offset.top;
            var left = offset.left;
            var kmblock_width = $kmblock.outerWidth();
            var kmblock_top = $kmblock.outerHeight();
            var menu_height = parseInt($(menu).css('height'));

            menu.style.top = top + kmblock_top - menu_height - 2 + "px";
            menu.style.left = (left + kmblock_width - menu_width - 2) + "px";

        }else if(position_through_obj == 'user_list_visibility') {

            var $visibility_parent = $obj.parent();

            var offset = $visibility_parent.offset();
            var top = offset.top;
            var left = offset.left;

            // теперь нужно чтобы левый нижний угол menu соответствовал левому верхнему углу $visibility_parent
            var menu_height = parseInt($(menu).css('height'));

            menu.style.top = top - menu_height + "px";
            menu.style.left = left + "px";

        }
    }

    return false;
}

function contextMenuClose()
{
    if ($('#contextMenuId').length > 0) {
        $('#contextMenuId').html('').hide();
    }

    $('.openContextMenu').removeClass('openContextMenu');
}


$(document).ready(function()
{
    // обновление блока с рейсами на главной странице
    //setInterval(function() {
    //    updateDirectionsTripBlock();
    //}, 10000);

    //setInterval(function() {
    //    updateChat();
    //}, 15000);

    // добавляем html для меню открываемого кликом правой кнопкой мыши
    $('<div id="contextMenuId" style="display:none; z-index: 10000;"></div>').appendTo("body");

    // закрытие меню при клике левой кнопкой мыши вне области меню
    $(document).on('click', function(e) {
        if (e.button == 0) {
            var contextMenuDiv = $('#contextMenuId');
            var ui_list = $('.ui-autocomplete');
            if (!contextMenuDiv.is(e.target) && contextMenuDiv.has(e.target).length === 0 && !ui_list.is(e.target) && ui_list.has(e.target).length === 0) { // если клик был не по нашему блоку и не по его дочерним элементам
                contextMenuClose();
            }
        }
    });



    $(document).on('click', '#day-report', function()
    {
        var date = $('#selected-day').attr('date');
        var objDate = getDateObject(date);
        //var prev_date = $(this).attr('prev-date');
        //var next_date = $(this).attr('next-date');

        $.ajax({
            url: '/site/ajax-get-day-report?date=' + date,
            type: 'post',
            data: {},
            success: function (response) {

                var width = $(window).width() - 50;
                if(width < 800) {
                    width = 800;
                }

                $('#default-modal').find('.modal-body').html(response.html);
                $('#default-modal').find('.modal-dialog').width(width + 'px');
                $('#default-modal .modal-title').html(response.title);
                //$('#default-modal .modal-title').html('<a href="?date=' + prev_date + '&day-report" id="day-report-arrow-left">&larr; </a> Текущий отчет дня ' + date + ' (' + getWeekDay(objDate) + ')' + ' <a href="?date=' + next_date + '&day-report" id="day-report-arrow-right">&rarr; </a>');
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

        return false;
    });

    // если в урле есть параметр day-report, значит откроем текущий отчет дня
    var url = location.href;
    if(url.indexOf('day-report') > -1) {
        $('#day-report').click();
    }


    // навигация по меню направлений
    $('body').on('click', '#directions-menu li a', function()
    {
        var elems_count = $('#directions-menu').attr('elems-count');
        var selected_key = parseInt($(this).parent('li').attr('key')) + 1;

        // изменение активности у элементов меню
        $('#directions-menu li').each(function() {
            var key = parseInt($(this).attr('key')) + 1;
            if((key >= selected_key - 1) && (key <= selected_key + 1)) {
                $(this).addClass('active');
            }else {
                $(this).removeClass('active');
            }

            if(key == selected_key) {
                $(this).addClass('selected');
            }else {
                $(this).removeClass('selected');
            }
        });

        // движение ползунка в полосе прокрутки
        var scroll_left = $('#directions-block').scrollLeft();
        var scroll_left_max = $('#directions-block').prop("scrollWidth") - $('#directions-block').width();

        var scroll = 0;
        var left_selected_part = selected_key - 2;
        if(left_selected_part > 0) {
            var total_parts_count = elems_count - 2;
            scroll = scroll_left_max * left_selected_part / total_parts_count;
        }
        $('#directions-block').scrollLeft(scroll);

        return false;
    });

    $('#directions-block').scroll(function() {
        var scroll_left_max = $('#directions-block').prop("scrollWidth") - $('#directions-block').width();
        var scroll_left = $('#directions-block').scrollLeft();

        var elems_count = $('#directions-menu').attr('elems-count');

        var left_active_key = Math.round(scroll_left/scroll_left_max * (elems_count - 2));

        // изменение активности у элементов меню
        $('#directions-menu li').each(function() {
            var key = parseInt($(this).attr('key'));
            if(key >= left_active_key && key <= left_active_key + 2) {
                $(this).addClass('active');
            }else {
                $(this).removeClass('active');
            }

            if(key == left_active_key + 1) {
                $(this).addClass('selected');
            }else {
                $(this).removeClass('selected');
            }
        });
    });


    $(document).on('click', '.add-order-plus', function() {

        var trip_id = $(this).attr('trip-id');
        var date = $('#selected-day').attr('date');

        var data = {
            date: date,
            trip_id: trip_id
        }
        openModalCreateOrder(data);
    });

    $(document).on('click', '#main-page .place', function() {

        var trip_id = $(this).attr('trip-id');
        var date = $('#selected-day').attr('date');

        var data = {
            date: date,
            trip_id: trip_id
        }
        openModalCreateOrder(data);
    });


    // текстовое поле "Поиск пассажира по номеру"
    $(document).on('keyup', '#client-search', function ()
    {
        var mobile_phone = $(this).val();
        if (mobile_phone.length == 15 && mobile_phone[mobile_phone.length - 1] != '_') {// считаем что мобильный телефон введен в формате: +7-xxx-xxx-xxxx
            $.ajax({
                url: '/client/ajax-get-client-form?mobile_phone=' + mobile_phone,
                type: 'post',
                data: {},
                success: function (response) {

                    $('#default-modal').find('.modal-body').html(response);
                    $('#default-modal').find('.modal-dialog').width('600px');
                    $('#default-modal .modal-title').text('Информация о клиенте');
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
        }
    });

    // нажатие на кнопку "Оперативный чат" открывает чат
    $(document).on('click', '#open-chat', function()
    {
        $('#chat-block').attr('is-open', 'true');
        updateChat();

        return false;
    });


    // смена выбранной подписки
    $(document).on('click', '#main-page #subscriptions-list ul li', function() {
        var text = $(this).text() + ' <span class="caret"></span>';
        $('#main-page #subscriptions-list button').html(text);
        $('#main-page #subscriptions-list').attr('sub-id', $(this).attr('sub-id'));
    });

    // создание подписки
    $(document).on('click', '#main-page .btn-create-subscription', function () {

        var operator_subscription_id = $('#main-page #subscriptions-list').attr('sub-id');
        if(operator_subscription_id == undefined) {
            alert('Выберите СИП');
            return false;
        }

        $.ajax({
            url: '/operator-subscription/create-ats-subscription?subscription_id=' + operator_subscription_id,
            type: 'post',
            data: {},
            success: function (response) {
                if (response.success == true) {

                    if(response.is_deleted_anomal_subscription == true) {
                        alert('Аномальная подписка удалена. Новая добавлена.');
                    }else {
                        alert('Подписка создана');
                        //alert('Подписка добавлена. Истекает ' + response.expired_at + '.');
                    }
                    alert('Агент со статусом Отключен');
                    location.reload();

                }else {
                    alert('Не удалось создать подписку');
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

    // удаление подписки
    $(document).on('click', '#main-page .btn-delete-subscription', function () {

        $.ajax({
            url: '/operator-subscription/delete-subscription',
            type: 'post',
            data: {},
            success: function (response) {
                if (response.success == true) {
                    alert('Подписка удалена');
                    location.reload();
                }else {
                    alert('Не удалось удалить подписку');
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


    $(document).on('click', '#calculations', function()
    {
        $.ajax({
            url: '/formula/ajax-get-calculate-form',
            type: 'post',
            data: {},
            success: function (response) {

                $('#default-modal').find('.modal-body').html(response);
                $('#default-modal').find('.modal-dialog').width('600px');
                $('#default-modal .modal-title').text('Расчет');
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

        return false;
    });

    $(document).on('keypress', '.calculate-form #argument', function(e) {

        if(e.keyCode == 13) {
            $('#formula_id').focus();

            return false;
        }
    });

    $(document).on('click', '.calculate-form #calculate', function() {

        var formula_id = $('#calculate-form #formula_id').val();
        var argument = $.trim($('#calculate-form #argument').val());
        if(argument == '') {
            alert('Введите аргумент');
            return false;
        }
        argument = parseInt(argument);
        if(argument <= 0) {
            alert('Установите аргумент больше нуля');
            return false;
        }

        $('.calculate-form #result-value').text('');
        $('.calculate-form #result').hide();

        $.ajax({
            url: '/formula/ajax-calculate',
            type: 'post',
            data: {
                formula_id: formula_id,
                argument: argument
            },
            success: function (result) {

                $('.calculate-form #result-value').text(result);
                $('.calculate-form #result').show();
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


    // щелчек правой кнопкой мыши на входящем звонке
    $(document).on('contextmenu', '#incoming-orders-widget #incoming-calls-count', function(event)
    {
        var data = {};
        return menu($(this), event, 'incoming-calls', data, 'right', 'right_elem');
    });


    // щелчек на кнопке ЕЖСВ (электоронный журнал сдачи выручки)
    $(document).on('click', '#ejsv', function()
    {
        $.ajax({
            url: '/site/ajax-get-ejsv-form',
            type: 'post',
            data: {},
            success: function (response) {

                $('#default-modal').find('.modal-body').html(response.html);
                $('#default-modal').find('.modal-dialog').width('600px');
                $('#default-modal .modal-title').text('Учет ведет: ' + response.operator_name);
                $('#default-modal').modal('show');

                $('#ejsv-form input[name="date"]').datepicker();
                $('#ejsv-form input[name="date_end_circle"]').datepicker();

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


$(document).on('click', '#delete-incoming-calls', function() {

    $.ajax({
        url: '/call/ajax-delete-incoming-calls',
        type: 'post',
        data: {},
        success: function (result) {
            alert('удалено');
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