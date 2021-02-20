

var inProcess = false;

$(document).on('submit', '#order-cancel-investigation-form', function()
{
    if(inProcess === true) {
        return false;
    }

    var url = '/order/ajax-save-investigation';
    var investigation_id = $('#order-cancel-investigation-form').attr('investigation-id');
    if(investigation_id != undefined) {
        url = '/order/ajax-save-investigation?investigation_id=' + investigation_id;
    }


    $.ajax({
        type: "POST",
        headers: {
            'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
        },
        url: url,
        data: $('#order-cancel-investigation-form').serializeArray(),
        beforeSend: function() {
            inProcess = true;
        }
    }).done(function (response) {

        inProcess = false;

        if (response.success == true) {

            location.reload();

        }else {

            for (var field in response.errors) {
                var field_errors = response.errors[field];
                $('#lead-create-form').yiiActiveForm('updateAttribute', 'ordercancelinvestigation-' + field, field_errors);
            }
        }
    });

    return false;
});