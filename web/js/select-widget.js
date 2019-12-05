
//function SWConvertName(field_name) {
//    return field_name.replace('[','-').replace(']','').toLowerCase();
//}

// получение имени элемента и преобразование имени в подходящее для работы с событиями элемента
//function getYiiFieldName(obj) {
//    var field_name = obj.find('input[type="hidden"]').attr('name'); // например: DocumentPko[parentDoc]
//    if(field_name == undefined) {
//        return '';
//    }
//    return SWConvertName(field_name); // получаем: documentpko-parentdoc
//}


// загрузка списка
var load_list_response = [];
function loadList(obj) {

    var search = $.trim(obj.next('.sw-outer-block').find('.sw-search').val());
    var current_value = obj.find('input[type="hidden"]').val();
    //var element_name = getYiiFieldName(obj);
    var element_id = obj.find('input[type="hidden"]').attr('id');
    //console.log('element_id=' + element_id);


    if(sw_setting[element_id].ajax_url != undefined) {


        // передаваемые значения в ajax-запрос по умолчанию
        var data = {
            search: search
        };

        // получим набор передаваемых значений для ajax-запроса из вызова виджета, если таковой был задан (параметр ['ajax']['data'])
        if (sw_setting[element_id].ajax_data != undefined) {
            var ajax_data_expression = sw_setting[element_id].ajax_data; // получили
            data = ajax_data_expression(data, obj);           // применили
        }

        $.ajax({
            url: sw_setting[element_id].ajax_url,
            type: "POST",
            data: data,
            success: function (response) {

                var html = '';
                if (response.results.length > 0) {

                    load_list_response = response.results;

                    var group_name = '';
                    var elem = null;
                    var is_simple_list = false;

                    if (void 0 !== response.results[0]['children']) { // значит это список с делением по группам

                        //html += '<li class="empty-value" value="">Удалить значение</li>';
                        for (var key in response.results) {
                            group_name = response.results[key].text;

                            html += '<li class="section-list"><strong>' + group_name + '</strong><ul class="simple-list">';
                            if (response.results[key].children.length > 0) {
                                for (var key2 in response.results[key].children) {
                                    elem = response.results[key].children[key2];

                                    if (elem.id == current_value) {
                                        html += '<li value="' + elem.id + '" aria-selected="true">' + elem.text + '</li>';
                                    } else {
                                        html += '<li value="' + elem.id + '">' + elem.text + '</li>';
                                    }
                                }
                            }
                            html += '</ul></li>';
                        }

                    } else { // обычный простой линейный список
                        is_simple_list = true;
                        //html += '<li class="empty-value" value="">Удалить значение</li>';
                        for (var key in response.results) {
                            if (response.results[key].id == current_value) {
                                html += '<li value="' + response.results[key].id + '" aria-selected="true">' + response.results[key].text + '</li>';
                            } else {
                                html += '<li value="' + response.results[key].id + '">' + response.results[key].text + '</li>';
                            }
                        }
                    }

                } else {

                    load_list_response = [];

                    if (sw_setting[element_id].add_new_value_url != undefined) {
                        html = '<li class="empty-value">Не найдено <a id="add-new-value" data-toggle="tooltip" title="Добавить новое значение"><i class="glyphicon glyphicon-plus-sign">&nbsp;</i></a></li>';
                    } else {
                        html = '<li class="empty-value">Не найдено</li>';
                    }
                    is_simple_list = true;
                }

                if (html != '') {
                    html = '<ul class="main-list ' + (is_simple_list ? 'simple-list' : '') + '">' + html + '</ul>';
                    obj.next('.sw-outer-block').find('.sw-select-block').html(html);
                    obj.next('.sw-outer-block').find('.sw-select-block').show();
                }


                if (sw_setting[element_id].afterRequest != undefined) {
                    sw_setting[element_id].afterRequest(response);
                }
            },
            error: function (data, textStatus, jqXHR) {
                // сворачиваем виджет до первоначального вида и отображаем ошибку
                toggleSelect(obj, false);

                //var field_name = getYiiFieldName(obj);
                if (textStatus == 'error' && data != undefined) {
                    if (void 0 !== data.responseJSON) {
                        if (data.responseJSON.message.length > 0) {
                            //$(obj).parents('form').yiiActiveForm('updateAttribute', field_name, [data.responseJSON.message]);
                            alert(data.responseJSON.message);
                        }
                    } else {
                        if (data.responseText != undefined && data.responseText.length > 0) {
                            //$(obj).parents('form').yiiActiveForm('updateAttribute', field_name, [data.responseText]);
                            alert(data.responseText);
                        }
                    }
                } else {
                    handlingAjaxError(data, textStatus, jqXHR);
                }
            }
        });

    }else if(sw_setting[element_id].list_data != undefined) {

        var is_simple_list = true;
        var html = '';
        var list = sw_setting[element_id].list_data;

        if(search != '') {
            var new_list = {};
            for (var key in list) {
                var check_search = search.toLowerCase();
                var value = (list[key]).toLowerCase() ;
                if(value.indexOf(check_search) !== -1) {
                    new_list[key] = list[key];
                }
            }

            list = new_list;
        }

        for (var key in list) {

            var value = list[key];

            if (key == current_value) {
                html += '<li value="' + key + '" aria-selected="true">' + value + '</li>';
            } else {
                html += '<li value="' + key + '">' + value + '</li>';
            }
        }

        if (html != '') {
            html = '<ul class="main-list ' + (is_simple_list ? 'simple-list' : '') + '">' + html + '</ul>';
            obj.next('.sw-outer-block').find('.sw-select-block').html(html);
            obj.next('.sw-outer-block').find('.sw-select-block').show();
        }


        if (sw_setting[element_id].afterRequest != undefined) {
            sw_setting[element_id].afterRequest(response);
        }
    }
}

// открытие ссылки (открытие окна)
function openLink(obj) {

    //var element_name = getYiiFieldName(obj);
    var element_id = obj.find('input[type="hidden"]').attr('id');

    // запустим переданную из-вне функцию чтобы получить её результат
    var open_url = '';
    if(sw_setting[element_id].open_url != undefined) {
        var ajax_data_expression = sw_setting[element_id].open_url; // получили
        open_url = ajax_data_expression(open_url);           // применили
    }

    return window.open(open_url);
}


// открытие/закрытие списка с поиском
function toggleSelect(obj, set_state) {

    var sw_block = obj.next('.sw-outer-block');
    var search_obj = sw_block.find('.sw-search');
    //var element_name = getYiiFieldName(obj);
    var element_id = obj.find('input[type="hidden"]').attr('id');
    //console.log('element_id_2='+element_id);


    //console.log('sw_setting:'); console.log(sw_setting);
    if(obj.find('input[type="hidden"]').attr('disabled') != undefined) {
        return false;
    }

    if (set_state != undefined) {
        if (set_state == true) {
            sw_setting[element_id].sw_is_open = false;
        }else if(set_state == false) {
            sw_setting[element_id].sw_is_open = true;
        }
    }


    if(sw_setting[element_id].sw_is_open == false) {

        $('.sw-outer-block').hide();
        $('.sw-search').val('');
        for(var key in sw_setting) {
            sw_setting[key].sw_is_open = false;
        }

        // открываем поиск со списком
        sw_block.show();
        search_obj.focus();
        sw_setting[element_id].sw_is_open = true;
        loadList(obj);

    }else {
        // закрываем поиск со списком
        sw_block.hide();
        search_obj.val('');
        sw_setting[element_id].sw_is_open = false;

        //var field_name = getYiiFieldName(obj);
        //if(field_name.length > 0) {
        //    //console.log('отправлен запрос на проверку валидности field_name='+field_name);
        //    obj.parents('form').yiiActiveForm('validateAttribute', field_name);
        //}
    }
}


// Добавление значение в результат поиска (в виджет)
function selectWidgetInsertValue(obj, value, text)
{
    if(value != '') {
        obj.find('.simple-list li[aria-selected="true"]').attr('aria-selected', "false");
        obj.find('.simple-list li[value="' + value + '"]').attr('aria-selected', "true");
        obj.find('.sw-text .sw-value').html(text);
        obj.find('.sw-text .sw-delete').show();
        obj.find('.sw-text .sw-open').show();
        obj.find('input[type="hidden"]').val(value).change();
    }else {
        obj.find('.simple-list li[aria-selected="true"]').attr('aria-selected', "false");
        obj.find('.sw-delete').hide();
        obj.find('.sw-open').hide();
        obj.find('.sw-value').html('');
        obj.find('input[type="hidden"]').val('').change();
    }
    toggleSelect(obj, false);

    //var element_name = getYiiFieldName(obj);
    var element_id = obj.find('input[type="hidden"]').attr('id');
    if(sw_setting[element_id].afterChange != undefined) {

        if(value != '') {

            for(var key in load_list_response) {
                var item_data = load_list_response[key];
                if(item_data.id == value) {
                    response_item_data = item_data;
                    break;
                }
            }

        }else {
            response_item_data = [];
        }
        // console.log('response_item_data:'); console.log(response_item_data);

        sw_setting[element_id].afterChange(obj, value, text, response_item_data);
    }

    return false;
}

// щелчек где-либо на странице закрывает виджет
$(document).on('click', function() {
    $('.sw-element').each(function() {
        toggleSelect($(this), false);
    });
});


// щелчек на поле виджета "раскрывает" поле
$(document).on('click', '.sw-element', function() {
    //console.log('click 123');
    toggleSelect($(this));
    return false;
});

$(document).on('click', '.sw-search', function() {
    return false;
});

// написание в поле поиска обновляет результаты поиска
$(document).on('keyup', '.sw-search', function() {
    //loadList($(this).parents('.sw-outer-block').prev('.sw-element'));
});
$(document).on('keydown', '.sw-search', function(e) {

    if(e.keyCode == 13) {
        //var $obj = $(this).parents('.sw-outer-block').prev('.sw-element');
        //selectWidgetInsertValue($obj, "0", $(this).val());
        return false;
    }else {
        loadList($(this).parents('.sw-outer-block').prev('.sw-element'));
    }
});



// выбор элемента в списке
$(document).on('click', '.sw-outer-block .simple-list li:not(.empty-value)', function() {

    var value = $(this).attr('value');
    var text =  $(this).text();

    var obj = $(this).parents('.sw-outer-block').prev('.sw-element');
    selectWidgetInsertValue(obj, value, text);

    //return false;
});

//$(document).on('blur', '.sw-outer-block', function(e)
//{
//    var name = $(this).attr('attribute-name');
//
//    setTimeout(function() {
//        console.log('sw_is_clicked='+sw_is_clicked);
//        if(sw_is_clicked == false) {
//            toggleSelect($('.sw-element[attribute-name="' + name + '"]'), false);
//        }
//        sw_is_clicked = false;
//    }, 300);
//});

// удаление выбранного элемента
$(document).on('click', '.sw-element:not([disabled]) .sw-delete', function()
{
    var obj = $(this).parents('.sw-element');

    selectWidgetInsertValue(obj, '');

    //obj.find('input[type="hidden"]').val('').change();
    //obj.find('.simple-list li[aria-selected="true"]').attr('aria-selected', "false");
    //obj.find('.sw-delete').hide();
    //obj.find('.sw-open').hide();
    //obj.find('.sw-value').html('');

    return false;
});

// щелчек по "глазу" открывает ссылку
$(document).on('click', '.sw-open', function()
{
    var obj = $(this).parents('.sw-element');

    openLink(obj);

    return false;
});

// ! эту функцию вынеси отсюда или перепиши!!! (в частности вынеси обработку success-ответа с сервера)
$(document).on('click', '#add-new-value', function() {

    var obj = $(this).parents('.sw-outer-block').prev('.sw-element');
    var element_id = obj.find('input[type="hidden"]').attr('id');

    var url = '';
    var ajax_data_expression = sw_setting[element_id].add_new_value_url; // получили
    var url = ajax_data_expression(url, obj);                             // применили

    if(url != undefined) {
        $.ajax({
            url: url,
            type: "POST",
            data: {},
            success: function (response) {
                var html = '';
                if (response.success == true) {
                    selectWidgetInsertValue(obj, response.point.id, response.point.name);
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

    return false;
});


$(document).on('click', '.sw-element[attribute-name="DetailMeasurementValue[name]"]', function() {

    var errors = new Array();

    var model_id = parseInt($('*[name="NomenclatureDetail[model_id]"]').val());
    if(model_id == 0) {
        errors[errors.length] = "Необходимо выбрать Принадлежность. ";
    }

    var name = $('*[name="NomenclatureDetail[name]"]').val();
    if(name == '') {
        errors[errors.length] = "Необходимо выбрать Наименование з/ч. ";
    }

    //var installation_place = parseInt($('*[name="NomenclatureDetail[installation_place]"]').val());
    //if(installation_place == 0) {
    //    errors[errors.length] = "Необходимо выбрать Место установки. ";
    //}

    //var installation_side = parseInt($('*[name="NomenclatureDetail[installation_side]"]').val());
    //if(installation_side == 0) {
    //    errors[errors.length] = "Необходимо выбрать Сторону установки. ";
    //}

    if(errors.length > 0) {
        var str_errors = '';
        for(var i = 0; i < errors.length; i++) {
            str_errors += errors[i] + ' ';
        }
        alert(str_errors);

        return false;
    }
});