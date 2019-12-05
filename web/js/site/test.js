//$.getJSON('https://json.geoiplookup.io/api?callback=?', function(data) {
//    //console.log(JSON.stringify(data, null, 2));
//
//    console.log(data);
//
//    var str = '';
//    for(var key in data) {
//        str += key + ' = ' + data[key] + '<br />';
//    }
//    $('#result').html(str);
//});

$(document).ready(function() {

    //var x = window.open();
    //var winref = window.open("/call/get-call-window?id=" + data.call_id, "Окно звонка", "width=1000,height=800");

    var x = window.open('?id=123', "Окно звонка", "width=1000,height=800");
    x.document.open();
    x.document.write('content');
    x.document.close();
});