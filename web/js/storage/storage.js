
detail_measurement_value_list = {}; // используется также в views/storage-operation/create-income-form.php
storage_detail_hint_list = {}; // /views/storage-operation/create-expenditure-form.php


function openModalStorageOperation(type, operation_id) {

    if(type == undefined || type == '') {
        type = '';
    }
    if(operation_id == undefined || isNaN(operation_id)) {
        operation_id = 0;
    }

    $.ajax({
        url: '/storage/storage-operation/ajax-get-operation-form',
        type: 'post',
        data: {
            type: type,
            operation_id: operation_id
        },
        success: function (response) {
            if(response.success == true) {

                $('#default-modal .modal-dialog').css('width', '990px');
                $('#default-modal').find('.modal-body').html(response.html);
                if(response.operation_type == 1) { // 'income'

                    if(operation_id > 0) {
                        $('#default-modal .modal-title').html("Изменение проведенной операции прихода");
                    }else {
                        $('#default-modal .modal-title').html("Операция прихода");
                    }
                    $('#default-modal').removeClass().addClass('fade modal').addClass('modal-storage-operation-income').modal('show');
                }else if(response.operation_type == 0) { // 'expenditure'
                    $('#default-modal .modal-title').html("Операция расхода");
                    $('#default-modal').removeClass().addClass('fade modal').addClass('modal-storage-operation-expenditure').modal('show');
                }

                updateDetailMeasurementValue(); // обновление единицы измерения
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



function getOperationFormData(operation_type) {

    var data = {};
    if(operation_type == 'income') {

        var StorageOperation = {
            //date: $('#create-income-form').find('*[name="StorageOperation[date]"]').val(),
            // operation_type_id: $('#create-income-form').find('*[name="StorageOperation[operation_type_id]"]').val(),
            without_transport: $('#create-income-form').find('*[name="StorageOperation[without_transport]"]').is(':checked') == true ? 1 : 0,
            transport_id: $('#create-income-form').find('*[name="StorageOperation[transport_id]"]').val(),
            driver_id: $('#create-income-form').find('*[name="StorageOperation[driver_id]"]').val(),
        };

        if($('#create-income-form').find('*[name="StorageOperation[date]"]').val() != 0) {
            StorageOperation.date = $('#create-income-form').find('*[name="StorageOperation[date]"]').val();
        }
        if($('#create-income-form').find('*[name="StorageOperation[operation_type_id]"]').val() != 0) {
            StorageOperation.operation_type_id = $('#create-income-form').find('*[name="StorageOperation[operation_type_id]"]').val();
        }
        var count = $('#create-income-form').find('*[name="StorageOperation[count]"]').val();
        if(count.length > 0 && count.indexOf(",") !== false) {
            count = count.replace(",", ".");
        }

        if(count != 0 && !isNaN(count)) {
            StorageOperation.count = count;
        }

        if($('#create-income-form').find('*[name="StorageOperation[comment]"]').val() != 0) {
            StorageOperation.comment = $('#create-income-form').find('*[name="StorageOperation[comment]"]').val();
        }

        var StorageDetail = {
            //storage_id: $('#create-income-form').find('*[name="StorageDetail[storage_id]"]').val(),
            //detail_state_id: $('#create-income-form').find('*[name="StorageDetail[detail_state_id]"]').val(),
            //detail_origin_id: $('#create-income-form').find('*[name="StorageDetail[detail_origin_id]"]').val(),
        };

        if($('#create-income-form').find('*[name="StorageDetail[storage_id]"]').val() != 0) {
            StorageDetail.storage_id = $('#create-income-form').find('*[name="StorageDetail[storage_id]"]').val();
        }
        if($('#create-income-form').find('*[name="StorageDetail[detail_state_id]"]').val() != 0) {
            StorageDetail.detail_state_id = $('#create-income-form').find('*[name="StorageDetail[detail_state_id]"]').val();
        }
        if($('#create-income-form').find('*[name="StorageDetail[detail_origin_id]"]').val() != 0) {
            StorageDetail.detail_origin_id = $('#create-income-form').find('*[name="StorageDetail[detail_origin_id]"]').val();
        }

        var NomenclatureDetail = {
            //name: $('#create-income-form').find('.sw-element[attribute-name="NomenclatureDetail[name]"] .sw-value').text(),
            temp_name: $('#create-income-form').find('.sw-element[attribute-name="NomenclatureDetail[temp_name]"] .sw-value').text(),
            installation_place: $('#create-income-form').find('*[name="NomenclatureDetail[installation_place]"]').val(),
            installation_side: $('#create-income-form').find('*[name="NomenclatureDetail[installation_side]"]').val(),

        };
        if($('#create-income-form').find('*[name="NomenclatureDetail[model_id]"]').val() != 0) {
            NomenclatureDetail.model_id = $('#create-income-form').find('*[name="NomenclatureDetail[model_id]"]').val();
        }

        var DetailMeasurementValue = {
            name: $('#create-income-form').find('*[name="DetailMeasurementValue[name]"]').val(),
        };

        var data = {
            type: 'income',
            StorageOperation: StorageOperation,
            StorageDetail: StorageDetail,
            NomenclatureDetail: NomenclatureDetail,
            DetailMeasurementValue: DetailMeasurementValue
        }



    }else if(operation_type == 'expenditure') {

        //data['type'] = 'expenditure';// storage_detail_id

        StorageOperation = {
            //нафиг нужен этот параметр тут: storage_id: $('#create-expenditure-form').find('*[name="StorageDetail[storage_id]"]').val(),
            without_transport: $('#create-expenditure-form').find('*[name="StorageOperation[without_transport]"]').is(':checked') == true ? 1 : 0,
            transport_id: $('#create-expenditure-form').find('*[name="StorageOperation[transport_id]"]').val(),
            driver_id: $('#create-expenditure-form').find('*[name="StorageOperation[driver_id]"]').val(),
        };


        if($('#create-expenditure-form').find('*[name="StorageOperation[date]"]').val().length != 0) {
            StorageOperation.date = $('#create-expenditure-form').find('*[name="StorageOperation[date]"]').val();
        }else {
            StorageOperation.date = '';
        }
        if($('#create-expenditure-form').find('*[name="StorageOperation[operation_type_id]"]').val().length != 0) {
            StorageOperation.operation_type_id = $('#create-expenditure-form').find('*[name="StorageOperation[operation_type_id]"]').val();
        }else {
            StorageOperation.operation_type_id = '';
        }
        if($('#create-expenditure-form').find('*[name="StorageOperation[storage_detail_id]"]').val() != 0) {
            StorageOperation.storage_detail_id = $('#create-expenditure-form').find('*[name="StorageOperation[storage_detail_id]"]').val();
        }else {
            StorageOperation.storage_detail_id = '';
        }

        StorageOperation.count = $('#create-expenditure-form').find('*[name="StorageOperation[count]"]').val();
        if(StorageOperation.count.length > 0 && StorageOperation.count.indexOf(",") !== false) {
            StorageOperation.count = StorageOperation.count.replace(",", ".");
        }

        if(StorageOperation.comment != 0) {
            StorageOperation.comment = $('#create-expenditure-form').find('*[name="StorageOperation[comment]"]').val()
        }


        var data = {
            type: 'expenditure',
            StorageOperation: StorageOperation
        }
    }

    return data;
}


$(document).on('click', '#storage-operation-income', function() {

    openModalStorageOperation('income');

    return false;
});


$(document).on('click', '#storage-operation-expenditure', function() {

    openModalStorageOperation('expenditure');

    return false;
});

$(document).on('click', '.storage-operation-update', function() {

    var operation_id = $(this).parents('tr').attr('data-key');
    openModalStorageOperation('', operation_id);

    return false;
});



// нажатие на кнопку "Записать" в форме Операции Прихода
var allow_create_income_operation = true;
$(document).on('click', '#create-income-button', function()
{
    if ($(this).hasClass('disabled')) {
        return false;
    }

    if(allow_create_income_operation == true) {

        var formData = getOperationFormData('income');
        //console.log('formData:'); console.log(formData);

        var operation_id = $('#create-income-form').attr('operation-id');
        if(operation_id != undefined && operation_id > 0) {
            var url = '/storage/storage-operation/ajax-update-operation?id=' + operation_id;
        }else {
            var url = '/storage/storage-operation/ajax-create-operation';
        }

        $.ajax({
            url: url,
            type: 'post',
            data: formData,
            beforeSend: function () {
                allow_create_income_operation = false;
            },
            success: function (data) {

                allow_create_income_operation = true;

                if (data.success == true) {

                    $('#default-modal').modal('hide');
                    alert('Проведено');
                    location.reload();

                } else {

                    var errors = '';
                    for (var field in data.nomenclature_detail_errors) {
                        var field_errors = data.nomenclature_detail_errors[field];
                        for (var key in field_errors) {
                            errors += field_errors[key] + ' ';
                        }
                    }
                    for (var field in data.storage_detail_errors) {
                        var field_errors = data.storage_detail_errors[field];
                        for (var key in field_errors) {
                            errors += field_errors[key] + ' ';
                        }
                    }
                    for (var field in data.storage_operation_errors) {
                        var field_errors = data.storage_operation_errors[field];
                        for (var key in field_errors) {
                            errors += field_errors[key] + ' ';
                        }
                    }

                    alert(errors);
                }

            },
            error: function (data, textStatus, jqXHR) {
                allow_create_income_operation = true;
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
                    //resetOrderFormRadiobuttons();
                }else {
                    handlingAjaxError(data, textStatus, jqXHR);
                }
            }
        });

    }else {
        alert('хватить жать на кнопку - запрос обрабатывается...');
        //LogDispatcherAccounting('кнопка «Записать» заказа');
    }
});




// нажатие на кнопку "Записать" в форме Операции Расхода
var allow_create_expenditure_operation = true;
$(document).on('click', '#create-expenditure-button', function()
{
    if ($(this).hasClass('disabled')) {
        return false;
    }

    if(allow_create_expenditure_operation == true) {

        var formData = getOperationFormData('expenditure');
        //console.log('formData:'); console.log(formData);

        var operation_id = $('#create-expenditure-form').attr('operation-id');
        if(operation_id != undefined && operation_id > 0) {
            var url = '/storage/storage-operation/ajax-update-operation?id=' + operation_id;
        }else {
            var url = '/storage/storage-operation/ajax-create-operation';
        }


        $.ajax({
            url: url,
            type: 'post',
            data: formData,
            beforeSend: function () {
                allow_create_expenditure_operation = false;
            },
            success: function (data) {

                allow_create_expenditure_operation = true;

                if (data.success == true) {

                    $('#default-modal').modal('hide');
                    alert('Проведено');
                    location.reload();

                } else {
                    var errors = '';
                    for (var field in data.storage_operation_errors) {
                        var field_errors = data.storage_operation_errors[field];
                        for (var key in field_errors) {
                            errors += field_errors[key] + ' ';
                        }
                    }

                    alert(errors);
                }

            },
            error: function (data, textStatus, jqXHR) {
                allow_create_expenditure_operation = true;
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
        alert('хватить жать на кнопку - запрос обрабатывается...');
        //LogDispatcherAccounting('кнопка «Записать» заказа');
    }
});



$(document).on('change', '#create-expenditure-form #storage', function() {

    var $storage_detail_sw_element = $('.sw-element[attribute-name="StorageOperation[storage_detail_id]"]');
    selectWidgetInsertValue($storage_detail_sw_element, '', '');
    $('#storage_detail_id_hint').text('');
});



function updateDetailMeasurementValue() {

    var name = $('input[name="NomenclatureDetail[temp_name]"]').val();
    var $obj = $('.sw-element[attribute-name="DetailMeasurementValue[name]"]');

    if(name != undefined && name != "") {
        $.ajax({
            url: '/storage/nomenclature-detail/ajax-get-detail-measurement-value',
            type: "POST",
            data: {
                detail_name: name
            },
            success: function (response) {
                detail_measurement_value_list = {};
                if(response != null) {
                    selectWidgetInsertValue($obj, response.name, response.name);
                    detail_measurement_value_list[response.name] = response.count_is_double;
                }else {
                    selectWidgetInsertValue($obj, "", "");
                }

                //console.log('detail_measurement_value_list_0:'); console.log(detail_measurement_value_list);
                updateCountField();
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
        selectWidgetInsertValue($obj, "", "");
    }
}

// операция Прихода
// если меняется Наименование з/ч, то вставляю автоматом Ед.изм.
$(document).on('change', 'input[name="NomenclatureDetail[temp_name]"]', function() {
    updateDetailMeasurementValue();
});



// операция Расхода
// Если меняется поле "Запчасть", то вставляю автоматом Ед.изм.
var detail_measurement_value_name = '';
$(document).on('change', 'input[name="StorageOperation[storage_detail_id]"]', function() {

    var storage_detail_id = $(this).val(); // storage_detail_id
    //var $obj = $('.sw-element[attribute-name="DetailMeasurementValue[name]"]');

    if(storage_detail_id != "") {
        $.ajax({
            url: '/storage/nomenclature-detail/ajax-get-detail-measurement-value',
            type: "POST",
            data: {
                storage_detail_id: storage_detail_id
            },
            success: function (response) {
                detail_measurement_value_list = {};
                if(response != null) {
                    //selectWidgetInsertValue($obj, response.name, response.name);
                    detail_measurement_value_name = response.name;
                    detail_measurement_value_list[response.name] = response.count_is_double;
                }else {
                    detail_measurement_value_name = '';
                    //selectWidgetInsertValue($obj, "", "");
                }

                //console.log('detail_measurement_value_list_0:'); console.log(detail_measurement_value_list);
                //alert('input[name="StorageOperation[storage_detail_id]"] change');
                updateCountField();
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
        detail_measurement_value_name = '';
    }
});


$(document).on('change', 'input[name="StorageOperation[without_transport]"]', function() {

    var $obj_transport_input = $('input[name="StorageOperation[transport_id]"]');
    var $obj_driver_input = $('input[name="StorageOperation[driver_id]"]');
    var $obj_transport = $('.sw-element[attribute-name="StorageOperation[transport_id]"]');
    var $obj_driver = $('.sw-element[attribute-name="StorageOperation[driver_id]"]');

    if($(this).is(':checked') == true) {

        selectWidgetInsertValue($obj_transport, "", "");
        selectWidgetInsertValue($obj_driver, "", "");

        $obj_transport_input.attr('disabled', true);
        $obj_driver_input.attr('disabled', true);

        $obj_transport.attr('disabled', true);
        $obj_driver.attr('disabled', true);


    }else {
        $obj_transport_input.removeAttr('disabled');
        $obj_driver_input.removeAttr('disabled');

        $obj_transport.removeAttr('disabled');
        $obj_driver.removeAttr('disabled');
    }
});


function fieldCountIsFloat() {

    var $obj = $('input[name="DetailMeasurementValue[name]"]');
    if($obj.length > 0) {
        var val = $('input[name="DetailMeasurementValue[name]"]').val();
    }else {
        var val = detail_measurement_value_name;
    }

    //console.log('detail_measurement_value_list_x:'); console.log(detail_measurement_value_list);
    var list_is_empty = true;
    for(var k in detail_measurement_value_list) {
        list_is_empty = false;
        break;
    }

    if(list_is_empty == true) {
        return 1; // если объект значений единиц измерения пуст, то не сокращаем размер числа (чтобы 16.5 не превратилось в 16)
    }

    if(val.length > 0) {
        return detail_measurement_value_list[val];
    }else {
        return 0;
    }
}

function updateCountField() {

    var val = $('input[name="StorageOperation[count]"]').val();
    if(val.length == 0) {
        return true;
    }

    // теперь вместо точки используется запятая, поэтому обрабатывать как число float не получиться
    // поэтому на входе функции val с запятой преобразуем в val с точкой, а на выходе - наоборот
    if(val.length > 0 && val.indexOf(",") > - 1) {
        val = val.replace(",", ".");
    }

    if(fieldCountIsFloat()) {
        if (val.indexOf(".") != val.length - 1) {
            val = parseFloat(val);
        }
    }else {
        val = parseInt(val);
    }
    if(isNaN(val)) {
        val = 0;
    }

    val = String(val);
    if(val.length > 0 && val.indexOf(".") > -1) {
        val = val.replace(".", ",");
    }
    $('input[name="StorageOperation[count]"]').val(val);
}

$(document).on('keyup', 'input[name="StorageOperation[count]"]', function() {
    updateCountField();
});