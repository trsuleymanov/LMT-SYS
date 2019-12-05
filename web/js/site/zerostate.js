
// Очищаем заказы и связанные данные
$(document).on('click', '#clear-order-data', function() {
    if(confirm('Вы уверены?')) {
        $.ajax({
            url: '/zerostate/ajax-clear-order',
            type: 'post',
            data: {},
            success: function (data) {
                alert('Данные очищены');
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

// Очищаем рейсы (trip)
$(document).on('click', '#clear-trip-data', function() {
    if(confirm('Вы уверены?')) {
        $.ajax({
            url: '/zerostate/ajax-clear-trip',
            type: 'post',
            data: {},
            success: function (data) {
                alert('Данные очищены');
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

// Очищаем машины на рейсе (trip_transport)
$(document).on('click', '#clear-trip-transport-data', function() {
    if(confirm('Вы уверены?')) {
        $.ajax({
            url: '/zerostate/ajax-clear-trip-transport',
            type: 'post',
            data: {},
            success: function (data) {
                alert('Данные очищены');
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

// Очищаем отчет отображаемого дня (day_report_trip_transport)
$(document).on('click', '#clear-day-report-trip-transport-data', function() {
    if(confirm('Вы уверены?')) {
        $.ajax({
            url: '/zerostate/ajax-clear-day-report',
            type: 'post',
            data: {},
            success: function (data) {
                alert('Данные очищены');
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

// Очищаем клиентов таблицу (client)
$(document).on('click', '#clear-client-data', function() {
    if(confirm('Вы уверены?')) {
        $.ajax({
            url: '/zerostate/ajax-clear-client',
            type: 'post',
            data: {},
            success: function (data) {
                alert('Данные очищены');
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