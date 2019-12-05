
$(document).ready(function() {

    $(document).on('change', '*[name="CreateTestOrders[date]"]', function () {
        // сбрасывается выбранное направление
        $('*[name="CreateTestOrders[direction_id]"]').val('');
        $('*[name="CreateTestOrders[trip_id]"]').html('<option value="0"></option>');
    });

    $(document).on('change', '*[name="CreateTestOrders[direction_id]"]', function () {
        //alert('direction change');

        // обновляем список рейсов и сбрасываем выбранный рейс
        var date = $.trim($('*[name="CreateTestOrders[date]"]').val());
        if(parseInt($(this).val()) > 0 && date.length > 0) {

            // в зависимости от даты и направления обновляется список рейсов
            $.ajax({
                url: '/trip/ajax-index?date=' + date + '&direction_id=' + $(this).val(),
                type: 'post',
                contentType: false,
                cache: false,
                processData: false,
                success: function (trip_list) {
                    var options = '';
                    options += '<option value="">Выберите рейс</option>';
                    for (var key in trip_list) {
                        var trip = trip_list[key];
                        options += '<option value="' + trip.id + '">' + trip.name + ' (' + trip.start_time + ', ' + trip.mid_time + ', ' + trip.end_time + ')</option>';
                    }

                    $('*[name="CreateTestOrders[trip_id]"]').html(options);
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
            $('*[name="CreateTestOrders[trip_id]"]').html('<option value="0"></option>');
        }
    });
});