
// функция обновления окошка показывающего количество заявок
function updateClientextBlock()
{
    $.ajax({
        url: '/order/ajax-get-clientext-block',
        type: 'post',
        data: {},
        contentType: false,
        cache: false,
        processData: false,
        success: function (response) {
            if(response.success == true) {

                var colors = ['#FBF600', '#A6DEFF', '#C3FFD0', '#B8A2DC', '#FFC6F1'];
                var num = getRandomInt(0, 4);
                $('#clientext-block').html(response.html);
                $('.clientext-widget').css('background-color', colors[num]);

            }else {
                alert('неустановленная ошибка обновления блока с количеством заявок');
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

$(document).ready(function()
{
    // обновление блока с количеством заявок на всех страницах сайта (кроме тех где Слава переопределил шаблон)
    //setInterval(function() {
    //    updateClientextBlock();
    //}, 10000);
});
