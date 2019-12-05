
// обновление списка онлайн пользователей
function updateOnlineDriver() {

    $.ajax({
        url: '/admin/driver/get-online-drivers',
        type: 'post',
        data: {},
        contentType: false,
        cache: false,
        processData: false,
        success: function (html) {
            $('#active-drivers-list').html(html);
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

function updateMap() {

    $.ajax({
        url: '/admin/user/ajax-get-user-locations',
        type: 'post',
        data: {},
        success: function (data) {

            //map.removeAllOverlays();
            //map.removeAll();

            //console.log('users_0:'); console.log(data.users);
            map.geoObjects.removeAll();

            if(data.users.length > 0) {
                for (var key in data.users) {

                    var user = data.users[key];
                    //console.log('user:'); console.log(user);

                    //var placemark = new YMaps.Placemark(new YMaps.GeoPoint(user.long, user.lat), {
                    //    style: "default#truckIcon",
                    //    //draggable : true
                    //});
                    ////placemark.name = user.lastname + ' ' + user.firstname;
                    //placemark.name = user.driver_fio;
                    //placemark.description =
                    //    user.phone + "<br />"
                    //    + '<a target="_blank" href="/trip/trip-orders?trip_id=' + user.trip_id + '">'
                    //        + user.transport_car_reg + " " + user.transport_sh_model + ", "
                    //        //+ user.driver_fio + ", "
                    //        + user.direction_sh_name + " " + user.trip_name + ", "
                    //        + user.trip_date
                    //    + '</a>';
                    //
                    //// Добавляет метку на карту
                    //map.addOverlay(placemark);


                    var balloonContent =
                        user.phone + "<br />"
                        + '<a target="_blank" href="/trip/trip-orders?trip_id=' + user.trip_id + '">'
                        + user.transport_car_reg + " " + user.transport_sh_model + ", "
                        + user.direction_sh_name + " " + user.trip_name + ", "
                        + user.trip_date
                        + '</a>';

                    var placemark = new ymaps.Placemark([user.lat, user.long],
                        {
                            balloonContentHeader: user.driver_fio,
                            balloonContent: balloonContent
                        },
                        {
                            //draggable: true, // Метку можно перемещать.
                            preset: 'islands#blueAutoCircleIcon'
                        }
                    );

                    map.geoObjects.add(placemark);
                }
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



var placemarks = [];
//var placemark;
var map = null;
var myCollection;

// Создает обработчик события window.onLoad
//YMaps.jQuery(function () {
ymaps.ready(function(){
    // Создает экземпляр карты и привязывает его к созданному контейнеру
    //map = new YMaps.Map(YMaps.jQuery("#YMapsID")[0]);

    map = new ymaps.Map("YMapsID", {
        center: [// центр между Казанью и Альметьевском [55.49122774712018,50.517468278447666]
            55.4912,
            50.5174
        ],
        zoom: 8,
        //type: "yandex#satellite",
        //controls: []  // Карта будет создана без элементов управления.
        //controls: [
        //    'zoomControl',
        //    //'searchControl',
        //    //'typeSelector',
        //    //'routeEditor',  // построитель маршрута
        //    'trafficControl' // пробки
        //    //'fullscreenControl'
        //]
    });



    //ymaps.geolocation.latitude, ymaps.geolocation.longitude
    //console.log('latitude=' + YMaps.location.latitude);


    // Устанавливает начальные параметры отображения карты: центр карты и коэффициент масштабирования
    //map.setCenter(new YMaps.GeoPoint(36.73, 55.39), 12);
    //map.setCenter(new YMaps.GeoPoint(YMaps.location.longitude, YMaps.location.latitude), 12);

    //var zoom = new YMaps.Zoom({
    //    customTips: [
    //        { index: 1, value: "Мелко" },
    //        { index: 9, value: "Средне" },
    //        { index: 16, value: "Крупно" }
    //    ]
    //});
    ////Добавление элемента управления на карту
    //map.addControl(zoom);

    //console.log('users:'); console.log(users);

    //map.events.add('click', function(e) {
    //    //var crd = undefined;// координаты точки под мышью
    //    //mark.geometry.setCoordinates(crd);
    //
    //    console.log('coordinates:'); console.log(e.get('coords') );
    //});


    for(var i in users) {

        var user = users[i];

        //var placemark = new YMaps.Placemark(new YMaps.GeoPoint(user.long, user.lat), {
        //    style: "default#truckIcon",
        //    draggable : true
        //});
        //placemark.name = user.driver_fio;
        ////placemark.description = "ширина: " + user.lat + "<br />" + "долгота: " + user.long + ;
        //placemark.description =
        //    user.phone + "<br />"
        //    + '<a target="_blank" href="/trip/trip-orders?trip_id=' + user.trip_id + '">'
        //        + user.transport_car_reg + " " + user.transport_sh_model + ", "
        //        //+ user.driver_fio + ", "
        //        + user.direction_sh_name + " " + user.trip_name + ", "
        //        + user.trip_date
        //    + '</a>';
        //// Добавляет метку на карту
        //map.addOverlay(placemark);
        //placemarks.push(placemark);

        //var coordinates = placemark.geometry.getCoordinates();
        //console.log('coordinates:'); console.log(coordinates);

        //var placemark = new ymaps.Placemark([user.lat, user.long], {
        //    //hintContent: hintContent,
        //    //balloonContentHeader: '<div style="width: 100%; background: #FF0000; color: #FFFFFF;">qqq</div>',
        //    balloonContentHeader: 'www',
        //    balloonContent: 'qqq',
        //}, {
        //    //iconLayout: 'islands#circleIcon',
        //    //iconColor: '#1E98FF',
        //    //iconImageSize: [16, 16],
        //    //iconImageOffset: [-8, -8],
        //    //// Определим интерактивную область над картинкой.
        //    //iconShape: {
        //    //    type: 'Circle',
        //    //    coordinates: [0, 0],
        //    //    radius: 8
        //    //}
        //    //iconLayout: "default#truckIcon",
        //    //draggable : true
        //
        //    //iconLayout: 'islands#circleIcon',
        //    iconLayout: "islands#blueAutoIcon",
        //    iconColor: '#1E98FF'
        //    //iconImageSize: [16, 16],
        //    //iconImageOffset: [-8, -8],
        //    // Определим интерактивную область над картинкой.
        //    //iconShape: {
        //    //    type: 'Circle',
        //    //    coordinates: [0, 0],
        //    //    radius: 8
        //    //}
        //});

        var balloonContent =
            user.phone + "<br />"
            + '<a target="_blank" href="/trip/trip-orders?trip_id=' + user.trip_id + '">'
            + user.transport_car_reg + " " + user.transport_sh_model + ", "
            + user.direction_sh_name + " " + user.trip_name + ", "
            + user.trip_date
            + '</a>';

        var placemark = new ymaps.Placemark([user.lat, user.long],
            {
                balloonContentHeader: user.driver_fio,
                balloonContent: balloonContent
            },
            {
                //draggable: true, // Метку можно перемещать.
                preset: 'islands#blueAutoCircleIcon'
            }
        );

        map.geoObjects.add(placemark);
    }

    //map.geoObjects.add(
    //    new ymaps.Placemark(
    //        [ymaps.geolocation.latitude, ymaps.geolocation.longitude],
    //        {
    //            balloonContentHeader: ymaps.geolocation.country,
    //            balloonContent: ymaps.geolocation.city,
    //            balloonContentFooter: ymaps.geolocation.region
    //        }
    //    )
    //);

    setInterval(function() {
        updateMap(true);
    }, 10000);
});



$(document).ready(function() {

    // обновление списка онлайн пользователей
    setInterval(function () {
        updateOnlineDriver();
    }, 10000);
});

$(document).on('click', '.online-driver-position', function() {

    var lat = $(this).attr('lat');
    var long = $(this).attr('long');

    if(map != null) {
        //map.setCenter(coordinates, 12, {duration: 500});
        map.setCenter([lat, long], 12, {
            duration: 500
        });
    }
});