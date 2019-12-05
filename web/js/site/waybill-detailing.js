
$(document).ready(function() {

    // прохожусь по каждой строчке таблицы и устанавливаю фиксированным колонкам ширину соответствующую не фиксированным строкам
    var num = 1;
    $('#detailing-data-table tr').each(function() {
        var $tr = $(this);
        if(num == 1) {
            var height = $tr.find('th').eq(8).css('height');

            height = parseInt(height) + 2 + 'px';
            //console.log('num='+num + ' height='+height);
            $tr.find('th').eq(0).css('height', height);
            $tr.find('th').eq(1).css('height', height);
            $tr.find('th').eq(2).css('height', height);
            $tr.find('th').eq(3).css('height', height);
            $tr.find('th').eq(4).css('height', height);
            $tr.find('th').eq(5).css('height', height);

        }else {
            var height = $tr.find('td').eq(8).css('height');

            if(num == 2) {
                height = parseInt(height) + 1 + 'px';
            }

            //console.log('num='+num + ' height='+height);
            $tr.find('td').eq(0).css('height', height);
            $tr.find('td').eq(1).css('height', height);
            $tr.find('td').eq(2).css('height', height);
            $tr.find('td').eq(3).css('height', height);
            $tr.find('td').eq(4).css('height', height);
            $tr.find('td').eq(5).css('height', height);
        }

        num++;

    });
});