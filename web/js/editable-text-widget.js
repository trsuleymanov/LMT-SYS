
$(document).ready(function() {
    $(document).on('click', '.etw-element', function()
    {
        //console.log('.etw-element click');
        if($(this).attr('disabled') != 'disabled') {
            var id = $(this).attr('id');
            $(this).hide();
            $('.etf-block[for="' + id + '"]').show();
            if (etw_setting[id].mask != undefined) {
                $('.etf-block[for="' + id + '"]').find('.etf-input input').mask(etw_setting[id].mask);
            }

            $('.etf-block[for="' + id + '"]').find('.etf-input input').focus();
        }

        return false;
    });

    $(document).on('click', '.etf-clear-x', function() {

        //console.log('.etf-clear-x click');
        $(this).prev('input').val('').focus();

        return false;
    });

    $(document).on('click', '.etf-but-cancel', function() {

        //console.log('.etf-but-cancel click');

        var $etf_block = $(this).parents('.etf-block');
        var id = $etf_block.attr('for');
        $etf_block.hide();
        var $my_elem = $etf_block.parent().find('#' + id);
        //$('#' + id).show();
        $my_elem.show();
        var value = $my_elem.text();
        //$('.etf-block[for="' + id + '"]')
        $etf_block.find('.etf-input input').val(value);

        return false;
    });


    $(document).on('click', '.etf-but-accept', function() {

        //console.log('.etf-but-accept click');

        var etf_block = $(this).parents('.etf-block');
        var input_elem = etf_block.find('.etf-input input');
        var new_value = input_elem.val();
        var name = input_elem.attr('name');
        var id = etf_block.attr('for');

        if(etw_setting[id].onChange != undefined) {
            etw_setting[id].onChange(id, etf_block, name, new_value);
        }else {
            etf_block.hide();
            $('#' + id).html(new_value).show();
        }
        return false;
    });

    //$(document).on('click', '.etf-block .etf-input input', function(e) {
    //    console.log('.etf-block .etf-input input click');
    //    //$(this).focus();
    //});

    $(document).on('focus', '.etf-block .etf-input input', function(e) {
        //console.log('.etf-block .etf-input input focus');

        return false;
    });

    $(document).on('blur', '.etf-block .etf-input input', function(e) {
        console.log('.etf-block .etf-input input blur');
    });

    $(document).on('keyup', '.etf-block .etf-input input', function(e) {

        //console.log('.etf-block .etf-input input keyup');

        if(e.keyCode == 13) {
            var etf_block = $(this).parents('.etf-block');
            var id = etf_block.attr('for');
            var new_value = $(this).val();
            var name = $(this).attr('name');

            if(etw_setting[id].onChange != undefined) {
                etw_setting[id].onChange(id, etf_block, name, new_value);
            }else {
                etf_block.hide();
                $('#' + id).html(new_value).show();
            }
            return false;
        }
    });

    $(document).on('keyup', '.etf-input', function(e) {

        //console.log('.etf-input keyup');

        if(e.keyCode == 13) {
            var etf_block = $(this).parents('.etf-block');
            var new_value = etf_block.find('.etf-input input').val();
            var id = etf_block.attr('for');
            etf_block.hide();
            $('#' + id).text(new_value).show();

            return false;
        }
    });
});