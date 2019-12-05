

$(document).ready(function()
{

    // ~~~~~~~~~   страница "Форма редактирования города и точки остановок"    ~~~~~~~~~~~


    // Открытие модального окна 'Добавление ориентира'
    $(document).on('click', '#points-list #add-point', function () {

        $('#default-modal').modal('show')
            .find('.modal-body')
            .load(
                $(this).attr('href'),
                {},
                function() {
                    $('#default-modal .modal-title').text('Добавление ориентира');
                    $('#default-modal .modal-dialog').css('width', '600px');
                }
            );

        return false;
    });
    // Открытие модального окна 'Редактирование ориентира'
    $(document).on('click', '#points-list .edit-point', function () {

        $('#default-modal').modal('show')
            .find('.modal-body')
            .load(
                $(this).attr('href'),
                {},
                function() {
                    $('#default-modal .modal-title').text('Редактирование ориентира');
                    $('#default-modal .modal-dialog').css('width', '600px');
                }
            );

        return false;
    });
    // удаление Ориентара ('Точки остановки')
    $(document).on('click', '#points-list .delete-point', function () {

        if(confirm('Вы уверены, что хотите удалить этот элемент?')) {

            var city_id = $('#city-form').attr('city-id');
            $.ajax({
                type: "POST",
                url: $(this).attr('href'),
                data: {},
                success: function(data, textStatus, jqXHR)
                {
                    $.pjax.reload({
                        container:"#points-grid",
                        data: {
                            'city_id': city_id
                        }
                    });
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
    // отправка данных всплывшей формы создания/редактирования Ориентира (Точки оставноки)
    $('body').on('submit', '#point-form', function(event) {

        event.preventDefault();
        event.stopImmediatePropagation();


        var form = $(this);
        var formData = $(this).serialize();
        if (form.find('.has-error').length) {
            return false;
        }

        $.ajax({
            url: form.attr("action"),
            type: form.attr("method"),
            data: formData,
            success: function (data) {
                // закрытие модального окна
                $('#default-modal').modal('hide');

                // обновление таблицы на странице
                //$.pjax.reload({
                //    container: "#points-grid",
                //    history: false,
                //    type: 'POST',
                //    data: form.serialize(),
                //    url: form.attr('action')
                //});

                $.pjax.reload({
                    container:"#points-grid",
                    data: {
                        'city_id': data.city_id
                    }
                });

            },
            error: function(data, textStatus, jqXHR) {
                if(textStatus == 'error') {
                    if (textStatus == 'error' && data != undefined) {
                        if(data.responseJSON.message.length > 0) {
                            alert(data.responseJSON.message);
                        }
                    }else {
                        if(data.responseText.length > 0) {
                            alert(data.responseText);
                        }
                    }
                }else {
                    handlingAjaxError(data, textStatus, jqXHR);
                }
            }
        });
    });


    // Открытие модального окна 'Добавление яндекс-точки'
    map = null;
    point_placemark = null;
    //point_focusing_scale = 12; // масштаб фокусировки выбранной точки

    // скрытие/отображение точек карты
    //var S1 = 8; // зум меньше S1 (выше над уровнем моря), скрывает выделенную точку
    //var S2 = 10; // зум меньше S2 (выше над уровнем моря), скрывает все опорные точки


    var order_map_html =
        '<div class="map-block">' +
        //'<div class="map-block-header">' +
        //'<button type="button" class="map-block-close">×</button>' +
        //'<span class="map-block-title">Установка точки</span>' +
        //'</div>' +
        //'<div class="map-block-body">' +
        '<div class="map-control-block">' +
        '<input type="text" class="search-point form-control" placeholder="Начните ввод адреса..." />' +
        '<div class="search-result-block sw-select-block" style="display: none;"></div>' +
        '</div>' +
        '<div id="ya-map"></div>' +
        //'</div>' +
        '</div>';


    // открытие яндекс-карты с кучей функционала
    function openMap(selected_yandex_point_id) {

        //console.log('selected_yandex_point_id='+selected_yandex_point_id);

        map = null;
        point_placemark = null;
        var create_base_point_button = null;

        var city_id = $('#city-form').attr('city-id');

        $.ajax({
            url: '/admin/city/ajax-get-city-yandex-points-data?city_id=' + city_id,
            type: 'post',
            data: {},
            success: function (response) {
                if (response.city == null) {
                    alert('Город не определен');
                    return false;
                }

                //response.city.search_scale - Приближение карты при поиске
                //response.city.point_focusing_scale - Масштаб фокусировки карты
                //response.city.all_points_show_scale - Масшаб отображения всех точек
                MAP_SEARCH_SCALE = response.city.search_scale;
                //point_focusing_scale = response.city.point_focusing_scale;

                $('#default-modal').find('.modal-dialog').width('800px');
                $('#default-modal').find('.modal-body').css('padding', '0');
                $('#default-modal .modal-title').text('Создание опорной точки');
                $('#default-modal').find('.modal-body').html(order_map_html);
                $('#default-modal').modal('show');


                $('.map-block').find('.search-point')
                    .attr('city-long', response.city.center_long)
                    .attr('city-lat', response.city.center_lat)
                    .attr('city-name', response.city.name)
                    .attr('city-id', response.city.id)
                    .focus();

                ymaps.ready(function(){

                    map = new ymaps.Map("ya-map", {
                        center: [response.city.center_lat, response.city.center_long],
                        zoom: response.city.map_scale,
                        //type: "yandex#satellite",
                        //controls: []  // Карта будет создана без элементов управления.
                        controls: [
                            'zoomControl',
                            //'searchControl',
                            //'typeSelector',
                            //'routeEditor',  // построитель маршрута
                            'trafficControl' // пробки
                            //'fullscreenControl'
                        ]
                    });

                    //console.log('map.zoom=' + map.getZoom());

                    map.events.add('boundschange', function (event) {
                        console.log('oldZoom='+event.get('oldZoom') + ' newZoom=' + event.get('newZoom'));
                        showHidePlacemarks(map, event.get('newZoom'), response.city.all_points_show_scale)
                    });


                    // Создание кнопки создания опорной точки и добавление ее на карту.
                    create_base_point_button = new ymaps.control.Button({
                        data: {
                            // image: 'images/button.jpg',// иконка для кнопки
                            content: 'Создать опорную точку',// Текст на кнопке.
                            //title: '',// Текст всплывающей подсказки.
                        },
                        options: {
                            selectOnClick: false,
                            maxWidth: [30, 30, 30],
                            layout: ymaps.templateLayoutFactory.createClass('<button id="yamap-create-base-point-but" class="btn btn-primary">{{ data.content }}</button>'),
                        }
                    });
                    map.controls.add(create_base_point_button, {
                        float: 'right',
                        floatIndex: 100
                    });
                    create_base_point_button.events.add('mousedown', function (event) {
                        //var temp_but = document.getElementById('yamap-create-temp-point-but');// create_temp_point_button
                        var base_but = document.getElementById('yamap-create-base-point-but');// create_base_point_button

                        //if (temp_but.className == 'btn btn-default active') {
                        //    temp_but.classList.remove('active');
                        //    map.cursors.push('move');
                        //}

                        if (base_but.className == 'btn btn-primary active') {
                            base_but.classList.remove('active');
                            map.cursors.push('move');
                        } else {
                            base_but.classList.add('active');
                            map.cursors.push('crosshair');
                        }
                    });

                    // Множество существующих точек
                    for (var key in response.yandex_points) {

                        var yandex_point = response.yandex_points[key];
                        var index = map.geoObjects.getLength();

                        //var placemark = createPlacemark(index, yandex_point.name, yandex_point.id, yandex_point.lat, yandex_point.long, false, false, false, true, yandex_point.critical_point, yandex_point.alias);

                        var create_placemark_params = {
                            index: index,
                            point_text: yandex_point.name,
                            point_description: yandex_point.description,
                            point_id: yandex_point.id,
                            point_lat: yandex_point.lat,
                            point_long: yandex_point.long,
                            is_editing: false,
                            create_new_point: false,
                            to_select: false,
                            can_change_params: true,
                            external_use: yandex_point.external_use,
                            point_of_arrival: yandex_point.point_of_arrival,
                            super_tariff_used: yandex_point.super_tariff_used,
                            critical_point: yandex_point.critical_point,
                            alias: yandex_point.alias
                        };
                        var placemark = createPlacemark(create_placemark_params);



                        if (yandex_point.id == selected_yandex_point_id) {
                            // установка point_placemark

                            var select_point_placemark_params = {
                                index: index,
                                point_text: yandex_point.name,
                                point_description: yandex_point.description,
                                point_id: yandex_point.id,
                                is_editing: false,
                                create_new_point: false,
                                can_change_params: true,
                                external_use: yandex_point.external_use,
                                point_of_arrival: yandex_point.point_of_arrival,
                                super_tariff_used: yandex_point.super_tariff_used,
                                critical_point: yandex_point.critical_point,
                                alias: yandex_point.alias,
                                draggable: getDraggable(yandex_point.id),
                                is_allowed_edit: true
                                //point_focusing_scale: point_focusing_scale
                            }
                            selectPointPlacemark(select_point_placemark_params);
                            //selectPointPlacemark(index, yandex_point.name, yandex_point.id, false, false, true, yandex_point.critical_point, yandex_point.alias, true);

                            var coordinates = placemark.geometry.getCoordinates();
                            map.setCenter(coordinates, 15, {duration: 500});
                        }
                    }

                    //if(selected_yandex_point_id == 0) {
                    //
                    //    var index = map.geoObjects.getLength();
                    //
                    //    var placemark = createPlacemark(index, selected_yandex_point_from_name, selected_yandex_point_id, selected_yandex_point_from_lat, selected_yandex_point_from_long)
                    //    selectPointPlacemark(index, selected_yandex_point_from_name, selected_yandex_point_id);
                    //
                    //    var coordinates = placemark.geometry.getCoordinates();
                    //    map.setCenter(coordinates, 15, {duration: 500});
                    //}
                    // после создания точек на карте обновим их видимость
                    showHidePlacemarks(map, map.getZoom(), response.city.all_points_show_scale);

                    //if(search.length > 0) {
                    //    $('#order-create-modal .search-point').val(search);
                    //
                    //    // эмулируем нажатие клавиши в поиске
                    //    var e = $.Event("keyup", { keyCode: 9 });
                    //    $('#order-create-modal .search-point').trigger(e);
                    //}

                    // Создадим маркер в точке клика
                    map.events.add('click', function (e) {

                        //var pixels = e.get('globalPixels');
                        //console.log('pixels:'); console.log(pixels);

                        if ($('#yamap-create-base-point-but').hasClass('active') == true) {

                            var pointPlacemarkGeocoder = ymaps.geocode(e.get('coords'));
                            pointPlacemarkGeocoder.then(
                                function (res) {

                                    //unselectOldPointPlacemark();

                                    var text = res.geoObjects.get(0).properties._data.text;
                                    text = text.replace('Россия, ', '');
                                    text = text.replace('Республика Татарстан, ', '');

                                    var city_name = $('#order-create-modal .search-point').attr('city-name');
                                    text = text.replace(city_name + ', ', '');

                                    var point_coords = e.get('coords');
                                    var point_lat = point_coords[0];
                                    var point_long = point_coords[1];
                                    var index = map.geoObjects.getLength();

                                    //var placemark = createPlacemark(index, text, 0, point_lat, point_long, true, true, false, true, yandex_point.critical_point, yandex_point.alias);
                                    var create_placemark_params = {
                                        index: index,
                                        point_text: text,
                                        point_description: '',
                                        point_id: 0,
                                        point_lat: point_lat,
                                        point_long: point_long,
                                        is_editing: true,
                                        create_new_point: true,
                                        to_select: false,
                                        can_change_params: true,
                                        external_use: yandex_point.external_use,
                                        point_of_arrival: yandex_point.point_of_arrival,
                                        super_tariff_used: yandex_point.super_tariff_used,
                                        critical_point: yandex_point.critical_point,
                                        alias: yandex_point.alias
                                    };
                                    var placemark = createPlacemark(create_placemark_params);

                                    var select_point_placemark_params = {
                                        index: index,
                                        point_text: text,
                                        // point_description: ' 8 ',
                                        point_id: 0,
                                        is_editing: true,
                                        create_new_point: true,
                                        can_change_params: true,
                                        external_use: yandex_point.external_use,
                                        point_of_arrival: yandex_point.point_of_arrival,
                                        super_tariff_used: yandex_point.super_tariff_used,
                                        critical_point: yandex_point.critical_point,
                                        alias: yandex_point.alias,
                                        draggable: getDraggable(yandex_point.id),
                                        is_allowed_edit: true
                                        //point_focusing_scale: point_focusing_scale
                                    }
                                    selectPointPlacemark(select_point_placemark_params);
                                    //selectPointPlacemark(index, text, 0, true, true, true, yandex_point.critical_point, yandex_point.alias, true);
                                    placemark.balloon.open();
                                },
                                function (err) {
                                    // обработка ошибки
                                    alert('ошибка запрос в яндекс');
                                }
                            );

                            var base_but = document.getElementById('yamap-create-base-point-but');
                            base_but.classList.remove('active');
                            map.cursors.push('move');

                        }
                    });

                });

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


    $('#default-modal').on('hidden.bs.modal', function(e) {
        $('#default-modal').find('.modal-body').css('padding', '15px');
    });

    $(document).on('click', '#yandex-point-list #add-yandex-point', function () {

        //$('#default-modal').modal('show')
        //    .find('.modal-body')
        //    .load(
        //        $(this).attr('href'),
        //        {},
        //        function() {
        //            $('#default-modal .modal-title').text('Добавление яндекс-метки');
        //            $('#default-modal .modal-dialog').css('width', '600px');
        //        }
        //    );

        openMap(0);

        return false;
    });


    // Открытие модального окна 'Редактирование яндекс-точки'
    $(document).on('click', '#yandex-point-list .edit-yandex-point', function () {

        //$('#default-modal').modal('show')
        //    .find('.modal-body')
        //    .load(
        //        $(this).attr('href'),
        //        {},
        //        function() {
        //            $('#default-modal .modal-title').text('Редактирование яндекс-точки');
        //            $('#default-modal .modal-dialog').css('width', '600px');
        //        }
        //    );

        var yandex_point_id = $(this).attr('yandex-point-id');
        openMap(yandex_point_id);

        return false;
    });

    // удаление Яндекс-точки
    $(document).on('click', '#yandex-point-list .delete-yandex-point', function () {

        if(confirm('Вы уверены, что хотите удалить этот элемент?')) {

            var city_id = $('#city-form').attr('city-id');
            $.ajax({
                type: "POST",
                url: $(this).attr('href'),
                data: {},
                success: function(data, textStatus, jqXHR)
                {
                    $.pjax.reload({
                        container:"#yandex-points-grid",
                        data: {
                            city_id: city_id
                        }
                    });
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

    // Отправка данных всплывшей формы создания/редактирования Яндекс-точки
    $(document).on('submit', '#yandex-point-form', function(event) {

        event.preventDefault();
        event.stopImmediatePropagation();

        var form = $(this);
        var formData = $(this).serialize();
        if (form.find('.has-error').length) {
            return false;
        }

        $.ajax({
            url: form.attr("action"),
            type: form.attr("method"),
            data: formData,
            success: function (data) {

                if(data.form_saved == 1) {
                    // закрытие модального окна
                    $('#default-modal').modal('hide');

                    // обновление страницы города со списком яндекс-точек
                    $.pjax.reload({
                        container:"#yandex-points-grid",
                        data: {
                            city_id: data.city_id
                        }
                    });

                }else {
                    $('#default-modal').find('.modal-body').html(data);
                }

            },
            error: function(data, textStatus, jqXHR) {
                if (textStatus == 'error' && data != undefined) {
                    if(void 0 !== data.responseJSON) {
                        if(data.responseJSON.message.length > 0) {
                            alert(data.responseJSON.message);
                        }
                    }else {
                        if(data.responseText.length > 0) {
                            alert(data.responseText);
                        }
                    }
                }else {
                    handlingAjaxError(data, textStatus, jqXHR);
                }
            }
        });
    });




    // Открытие модального окна 'Добавление улицы'
    $(document).on('click', '#streets-list #add-street', function () {

        $('#default-modal').modal('show')
            .find('.modal-body')
            .load(
                $(this).attr('href'),
                {},
                function() {
                    $('#default-modal .modal-title').text('Добавление улицы');
                    $('#default-modal .modal-dialog').css('width', '600px');
                }
            );

        return false;
    });
    // Открытие модального окна 'Редактирование улицы'
    $(document).on('click', '#streets-list .edit-street', function () {

        $('#default-modal').modal('show')
            .find('.modal-body')
            .load(
                $(this).attr('href'),
                {},
                function() {
                    $('#default-modal .modal-title').text('Редактирование улицы');
                    $('#default-modal .modal-dialog').css('width', '600px');
                }
            );

        return false;
    });
    // удаление Улицы
    $(document).on('click', '#streets-list .delete-street', function () {

        if(confirm('Вы уверены, что хотите удалить этот элемент?')) {

            var city_id = $('#city-form').attr('city-id');
            $.ajax({
                type: "POST",
                url: $(this).attr('href'),
                data: {},
                success: function(data, textStatus, jqXHR)
                {
                    $.pjax.reload({
                        container:"#streets-grid",
                        data: {
                            city_id: city_id
                        }
                    });
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
    // Отправка данных всплывшей формы создания/редактирования Улицы
    $(document).on('submit', '#street-form', function(event) {

        event.preventDefault();
        event.stopImmediatePropagation();

        var form = $(this);
        var formData = $(this).serialize();
        if (form.find('.has-error').length) {
            return false;
        }

        $.ajax({
            url: form.attr("action"),
            type: form.attr("method"),
            data: formData,
            success: function (data) {
                // закрытие модального окна
                $('#default-modal').modal('hide');

                $.pjax.reload({
                    container:"#streets-grid",
                    data: {
                        'city_id': data.city_id
                    }
                });

            },
            error: function(data, textStatus, jqXHR) {
                if (textStatus == 'error' && data != undefined) {
                    if(void 0 !== data.responseJSON) {
                        if(data.responseJSON.message.length > 0) {
                            alert(data.responseJSON.message);
                        }
                    }else {
                        if(data.responseText.length > 0) {
                            alert(data.responseText);
                        }
                    }
                }else {
                    handlingAjaxError(data, textStatus, jqXHR);
                }
            }
        });
    });



    // ~~~~~~~~~   страница "список Городов"    ~~~~~~~~~~~


    // перехват события удаления города
    $(document).on('click', '#city-page .delete-city', function () {
        if(confirm('Вы уверены, что хотите удалить этот элемент?')) {

            $.ajax({
                type: "POST",
                url: $(this).attr('href'),
                data: {},
                success: function(data, textStatus, jqXHR)
                {
                    location.reload();
                },
                error: function(data, textStatus, jqXHR) {
                    if (textStatus == 'error' && data != undefined) {
                        if(void 0 !== data.responseJSON) {
                            if(data.responseJSON.message.length > 0) {
                                alert(data.responseJSON.message);
                            }
                        }else {
                            if(data.responseText.length > 0) {
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


    // ~~~~~~~~~   страница "Тариф"    ~~~~~~~~~~~
    $(document).on('click', '#tariff-form #update-orders-price', function () {

        var tariff_id = $('#tariff-form').attr('tariff-id');

        $.ajax({
            type: "POST",
            url: '/admin/tariff/ajax-update-orders-price?tariff_id='+tariff_id ,
            data: {},
            success: function (response)
            {
                if(response.success == true) {

                    alert('Пересчет стоимостей заказов завершен');

                }else {
                    alert('неустановленная ошибка пересчета стоимостей заказов');
                }
            },
            error: function(data, textStatus, jqXHR) {
                if (textStatus == 'error' && data != undefined) {
                    if(void 0 !== data.responseJSON) {
                        if(data.responseJSON.message.length > 0) {
                            alert(data.responseJSON.message);
                        }
                    }else {
                        if(data.responseText.length > 0) {
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


    // ~~~~~~~~~~~~ страница "rescue/day-print" ~~~~~~~~~~~~~~~
    $(document).on('change', '#rescue-day-print-page input[name="date"]', function() {
        //alert('date=' + $(this).val());
        var date = $.trim($(this).val());
        if(date.length == 10) {
            location.href = '/admin/rescue/day-print?OrderSearch[date]=' + date;
        }else {
            alert('Неверный формат даты');
        }
    });

    // ~~~~~~~~~~~~ страница "day-report-transport-circle/index" ~~~~~~~~~~~~~~~

    /*
    $(document).on('change', 'input[name="date"]', function() {
        var date = $.trim($(this).val());

        alert('qwe qwe');

        //if(date.length == 10) {
        //    location.href = '/admin/day-report-transport-circle/index?DayReportTransportCircleSearch[date]=' + date;
        //}else {
        //    alert('Неверный формат даты');
        //}

    });*/


    // ~~~~~~~~~~~~ страница "/admin/transport" ~~~~~~~~~~~~~~~
    $(document).on('change', '.transport-active', function() {
        var active = $(this).is(':checked');
        var transport_id = $(this).attr('transport-id');

        $.ajax({
            type: "POST",
            url: '/admin/transport/ajax-set-active?id=' + transport_id + '&active=' + active,
            data: {},
            success: function(data, textStatus, jqXHR)
            {
                //console.log('saved active');
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
    $(document).on('change', '.transport-accountability', function() {
        var is_accountability = $(this).is(':checked');
        var transport_id = $(this).attr('transport-id');

        $.ajax({
            type: "POST",
            url: '/admin/transport/ajax-set-accountability?id=' + transport_id + '&accountability=' + is_accountability,
            data: {},
            success: function(data, textStatus, jqXHR)
            {
                //console.log('saved active');
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

    // ~~~~~~~~~~~~ страница "/admin/driver" ~~~~~~~~~~~~~~~~~~
    $(document).on('change', '.driver-active', function() {
        var active = $(this).is(':checked');
        var driver_id = $(this).attr('driver-id');

        $.ajax({
            type: "POST",
            url: '/admin/driver/ajax-set-active?id=' + driver_id + '&active=' + active,
            data: {},
            success: function(data, textStatus, jqXHR)
            {
                //console.log('saved active');
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
    $(document).on('change', '.driver-accountability', function() {
        var is_accountability = $(this).is(':checked');
        var driver_id = $(this).attr('driver-id');

        $.ajax({
            type: "POST",
            url: '/admin/driver/ajax-set-accountability?id=' + driver_id + '&accountability=' + is_accountability,
            data: {},
            success: function(data, textStatus, jqXHR)
            {
                //console.log('saved active');
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


    // ~~~~~~~~~~~~ страница "/admin/order/abnormal-order-list" ~~~~~~~~~~~~~~~~~~
    $(document).on('click', '#abnormal-order-page .remove-order', function () {

        if(confirm('Вы уверены, что хотите удалить этот заказ?')) {

            //var order_id = $(this).parents('tr').attr('data-key');

            $.ajax({
                type: "POST",
                url: $(this).attr('href'),
                data: {},
                success: function(data, textStatus, jqXHR)
                {
                    $.pjax.reload({
                        container:"#abnormal-order-grid",
                        data: { }
                    });
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



    // ~~~~~~~~~   страница "Агенты"    ~~~~~~~~~~~
    // создание подписки в админке
    /*
    $(document).on('click', '#operator-beeline-subscription-index .create-subsciption', function () {

        var operator_subscription_id = $(this).parents('tr').attr('data-key');
        $.ajax({
            //url: '/beeline/default/create-subscription?mobile_ats_login=' + mobile_ats_login,
            url: '/admin/operator-subscription/create-ats-subscription?operator_subscription_id=' + operator_subscription_id,
            type: 'post',
            data: {},
            success: function (response) {
                if (response.success == true) {
                    alert('Подписка добавлена. Истекает ' + response.expired_at + '. Не забудьте проверить существование подписки в АТС');
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
*/

    $(document).on('click', '#operator-beeline-subscription-index .check-subsription', function () {

        var operator_subscription_id = $(this).parents('tr').attr('data-key');

        $.ajax({
            url: '/admin/operator-subscription/check-ats-subscription?operator_subscription_id=' + operator_subscription_id,
            type: 'post',
            data: {},
            success: function (response) {
                if (response.success == true) {
                    alert('Подписка существует в АТС');
                }else {
                    alert('Подписка в АТС не найдена !');
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
    $(document).on('click', '#operator-beeline-subscription-index .delete-subsription', function () {

        //var subsription_id = $(this).attr('subsription-id');
        var operator_subscription_id = $(this).parents('tr').attr('data-key');

        $.ajax({
            url: '/admin/operator-subscription/delete-subscription?operator_subscription_id=' + operator_subscription_id,
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


    // ~~~~~~~~~   страница "История обращений"    ~~~~~~~~~~~
    $(document).on('click', '#call-case-page .toggle-calls', function () {

        var $obj = $(this);
        var case_id = $(this).parents('tr').attr('data-key');
        var is_open = $(this).attr('is-open');

        if(is_open == "false") {

            $.ajax({
                url: '/admin/call-case/ajax-get-docking-list?case_id=' + case_id,
                type: 'post',
                data: {},
                success: function (response) {
                    $obj.parents('tr').after('<tr class="tr-calls-block"><td></td><td></td><td colspan="6">' + response + '</td></tr>');
                    $obj.attr('is-open', 'true');
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

        }else {
            $obj.parents('tr').next('.tr-calls-block').remove();
            $obj.attr('is-open', 'false');
        }


        return false;
    });


    // ~~~~~~~~~   страница "А-ВРПТ" (пересчет относительных времен сбора в яндекс-точках)    ~~~~~~~~~~~
    $(document).on('click', '#a-vrpt-page #recount-points-time-together', function () {


        $.ajax({
            url: '/admin/yandex-point/ajax-recount-time-together',
            type: 'post',
            data: {},
            beforeSend: function () {
                allow_submit = false;
                $('body').css('cursor', 'wait');
            },
            success: function (response) {
                allow_submit = true;
                $('body').css('cursor', 'default');
                alert('Пересчет завершен');
            },
            error: function (data, textStatus, jqXHR) {
                allow_submit = true;
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



        return false;
    });


});