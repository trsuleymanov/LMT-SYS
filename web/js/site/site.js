
var modalWindow = function(parameters){

	var self = this;
	
	//alert('YES');

	this.getVacantId = function(template){
		if(template === undefined || !(typeof(template) != 'string' && typeof(template) != 'number')){
			template = 'modal-window';
		}
		var id = template;
		
		if($(''+'#'+id).length){
			var mw_i = 0;
			var mw_name = '' + template + '-' + mw_i;
			while($(''+'#'+mw_name).length){
				mw_i++;
				mw_name = '' + template + '-' + mw_i;
			}
			
			id = mw_name;
		}
		
		return id; 
	}

	if(parameters.id === undefined || (typeof(parameters.id) != 'string' && typeof(parameters.id) != 'number')){
		this.id = this.getVacantId();
	} else {
		this.id = parameters.id;
	}
	
	if(parameters.actionType === undefined || typeof(parameters.actionType) != 'string'){
		this.actionType = 'submit';
	} else {
		this.actionType = parameters.actionType;
	}
	
	var getSaveButtonColorByActionType = function(){
		switch(self.actionType){
			case 'submit' : return '#5cb85c'; break;
			case 'edit' : return '#FF8C00'; break;
			case 'delete' : return '#FF8C00'; break;
			default : return '#5cb85c'; break;
		}
	}
	
	
	var getHeaderStyleByHeaderType = function(){
		switch(self.headerType){
			case 'simple' : return 'margin-left:0;text-align:left;background-color:white;font-size:14px;font-weight:normal;color:#333;'; break;
			case 'colored' : return ''; break;
			//case 'standart' : return 'margin-left:0;text-align:left;background-color:white;font-size:14px;font-weight:normal;color:#333;'; break;
			
			default : return 'margin-left:0;text-align:left;background-color:white;font-size:14px;font-weight:normal;color:#333;'; break;
		}
	}
	
	if(parameters.headerType === undefined || typeof(parameters.headerType) != 'string'){
		this.headerType = 'simple';
	} else {
		this.headerType = parameters.headerType;
	}
	
	
	if(parameters.url_from === undefined){
		this.url_from = '/';
	} else {
		this.url_from = parameters.url_from;
	}
	
	if(parameters.url_to === undefined){
		this.url_to = this.url_from;
	} else {
		this.url_to = parameters.url_to;
	}
	
	
	var getRightGET_object = function(get_obj){
		if(get_obj === undefined || typeof(get_obj) != 'object'){
			return null;
		} else {
			var new_get_to_obj = {};
			for(var index_get_to in get_obj){
				if(typeof(index_get_to) == 'string'){
					new_get_to_obj[index_get_to] = get_obj[index_get_to];
				}
			}
		
			if(Object.keys(new_get_to_obj).length){
				return new_get_to_obj;
			} else {
				return null;
			}
		}	
	}
	
	
	if(parameters.get_from === undefined || typeof(parameters.get_from) != 'object'){
		
		this.get_from = null;
		
	} else {
		this.get_from = getRightGET_object(parameters.get_from);
	}
	
	
	
	if(parameters.get_to === undefined  || typeof(parameters.get_to) != 'object'){
		
		this.get_to = this.get_from;
		
	} else {
		this.get_to = getRightGET_object(parameters.get_to);
	}
	
	
	if(parameters.header_color === undefined || typeof(parameters.header_color) != 'string'){
		this.header_color = null;
	} else {
		this.header_color = parameters.header_color;
	}
	
	if(parameters.save_button_color === undefined || typeof(parameters.save_button_color) != 'string'){
		
		this.save_button_color = getSaveButtonColorByActionType();
	} else {
		this.save_button_color = parameters.save_button_color;
	}
	
	
	if(parameters.label_width === undefined || (typeof(parameters.label_width) != 'string' && typeof(parameters.label_width) != 'number')){
		if(parameters.label_width === null){
			this.label_width = null;
		} else {
			this.label_width = '50%';
		}
	} else {
		if(!isNaN(parameters.label_width)){
			this.label_width = parameters.label_width + 'px';
		} else {
			this.label_width = parameters.label_width;
		}
	}
	
	
	if(parameters.label_inline === undefined || typeof(parameters.label_inline) != 'boolean'){
		if(parameters.label_inline === null){
			this.label_inline = null;
		} else {
			this.label_inline = true;
		}
	} else {
		this.label_inline = parameters.label_inline;
	}
	
	
	if(parameters.label_text_align === undefined || typeof(parameters.label_text_align) != 'string'){
		if(parameters.label_text_align === null){
			this.label_text_align = null;
		} else {
			this.label_text_align = 'right';
		}
	} else {
		this.label_text_align = parameters.label_text_align;
	}
	
	
	if(parameters.input_inline === undefined || typeof(parameters.input_inline) != 'boolean'){
		if(parameters.input_inline === null){
			this.input_inline = null;
		} else {
			this.input_inline = true;
		}
	} else {
		this.input_inline = parameters.input_inline;
	}
	
	
	if(parameters.input_width === undefined || (typeof(parameters.input_width) != 'string' && typeof(parameters.input_width) != 'number')){
		if(parameters.input_width === null){
			this.input_width = null;
		} else {
			this.input_width = '30%';
		}
	} else {
		if(!isNaN(parameters.input_width)){
			this.input_width = parameters.input_width + 'px';
		} else {
			this.input_width = parameters.input_width;
		}
	}
	
	
	if(parameters.standartTemplate === undefined || typeof(parameters.standartTemplate) != 'boolean'){
		this.standartTemplate = true;
	} else {
		this.standartTemplate = parameters.standartTemplate;
	}
	
	var convertStyleObjectPartToString = function(styleObj){
		if(typeof(styleObj) == 'string'){
			return styleObj;
		}
		
		if(!typeof(styleObj) == 'object'){
			return null;
		}
		
		var resultStr = '';
		
		for(var i in styleObj){
			if(typeof(i) != 'string'){
				continue;
			}
			
			resultStr += (i + ' : ' + styleObj[i] + ';');
		}
		
		if(resultStr == ''){
			return null;
		}
	}
	
	var self = this;
	
	var convertStyleObjectToString = function(styleObj){
		
		if(typeof(styleObj) == 'string'){
			return styleObj;
		}
		
		if(typeof(styleObj) != 'object'){
			return null;
		}
		
		var resultStr = '';
		
		for(var i in styleObj){
			resultStr += '#' + self.id + ' ' + styleObj[i]['selector'] + '{';
			resultStr += convertStyleObjectPartToString(styleObj[i]['content']);
			resultStr += '}';
		}
		
		return resultStr;
	
	}
	
	if(parameters.totalStyle === undefined || (typeof(parameters.totalStyle) != 'string' && typeof(parameters.totalStyle) != 'object')){
		this.totalStyle = null;
	} else {
		this.totalStyle = parameters.totalStyle;
	}
	
	if(parameters.title === undefined || (typeof(parameters.title) != 'string' && typeof(parameters.title) != 'number')){
		this.title = this.id;
	} else {
		this.title = parameters.title;
	}
	
	if(parameters.width === undefined || (typeof(parameters.width) != 'string' && typeof(parameters.width) != 'number')){
		this.width = '600';
	} else {
	
		this.width = parameters.width;
	}
	
	if(parameters.error_message_open === undefined || typeof(parameters.error_message) != 'string'){
		this.error_message_open = 'Неизвестная ошибка загрузки формы';
	} else {
		this.error_message_open = parameters.error_message_open;
	}
	
	if(parameters.error_message_response === undefined || typeof(parameters.error_message_response) != 'string'){
		if(parameters.error_message_response === null){
			this.error_message_response = null;	
		} else {
			//alert('error_message_response=' + this.error_message_response);
			this.error_message_response = 'Данные формы заполнены не верно';
		}
	} else {
		this.error_message_response = parameters.error_message_response;
	}
	
	
	if(parameters.success_message_response === undefined || typeof(parameters.success_message_response) != 'string'){
		if(parameters.success_message_response === null){
			this.success_message_response = null;	
		} else {
			this.success_message_response = 'Данное действие выполнено';
		}
	} else {
		this.success_message_response = parameters.success_message_response;
	}
	
	
	if(parameters.use_standart_submit === undefined || typeof(parameters.use_standart_submit) != 'boolean'){
		
		this.use_standart_submit = true;
		
	} else {
		this.use_standart_submit = parameters.use_standart_submit;
	}
	
	
	if(parameters.close_button_inline === undefined || typeof(parameters.close_button_inline) != 'boolean'){
		
		this.close_button_inline = true;
		
	} else {
		this.close_button_inline = parameters.close_button_inline;
	}
	
	if(parameters.close_button_color === undefined || typeof(parameters.close_button_color) != 'string'){
		
		this.close_button_color = 'white';//'#dfdfdf';
		
	} else {
		this.close_button_color = parameters.close_button_color;
	}
	
	if(parameters.close_button_text_color === undefined || typeof(parameters.close_button_text_color) != 'string'){
		
		this.close_button_text_color = '#999999';
		
	} else {
		this.close_button_text_color = parameters.close_button_text_color;
	}

	
	if(parameters.close_button_text != false && (parameters.close_button_text === undefined || typeof(parameters.close_button_text) != 'string')){
		this.close_button_text = 'Закрыть';
		
	} else {
		this.close_button_text = parameters.close_button_text;
	}
	
	if(parameters.save_button_inline === undefined || typeof(parameters.save_button_inline) != 'boolean'){
		
		this.save_button_inline = true;
		
	} else {
		this.save_button_inline = parameters.save_button_inline;
	}
	
	
	if(parameters.save_button_text_color === undefined || typeof(parameters.save_button_text_color) != 'string'){
		
		this.save_button_text_color = 'white';
		
	} else {
		this.save_button_text_color = parameters.save_button_text_color;
	}


	if(parameters.save_button_text == false) {
		this.save_button_text = false;

	}else if(parameters.save_button_text === undefined || typeof(parameters.save_button_text) != 'string'){
		
		if(this.title && this.title != '' && typeof(this.title == 'string')){
			this.save_button_text = this.title;
		} else {
			this.save_button_text = 'Сохранить';
		}
		
	} else {
		this.save_button_text = parameters.save_button_text;
	}
	
	if(parameters.afterOpenAction === undefined || typeof(parameters.afterOpenAction) != 'function'){
		this.afterOpenAction = null;
	} else {
		this.afterOpenAction = parameters.afterOpenAction;
	}
	
	
	if(parameters.clientValidation === undefined || typeof(parameters.clientValidation) != 'function'){
		this.clientValidation = null;
	} else {
		this.clientValidation = parameters.clientValidation;
	}
	
	if(parameters.afterResponseSuccess === undefined || typeof(parameters.afterResponseSuccess) != 'function'){
		this.afterResponseSuccess = null;
	} else {
		this.afterResponseSuccess = parameters.afterResponseSuccess;
	}
	
	
	if(parameters.afterResponseError === undefined || typeof(parameters.afterResponseError) != 'function'){
		this.afterResponseError = parameters.afterResponseSuccess;
	} else {
		this.afterResponseError = parameters.afterResponseError;
	}
	
	if(parameters.dataAsquition === undefined || typeof(parameters.dataAsquition) != 'function'){
		this.dataAsquition = null;
	} else {
		this.dataAsquition = parameters.dataAsquition;
	}
	
	if(parameters.auxiliaryObject === undefined || typeof(parameters.auxiliaryObject) != 'object'){
		this.auxiliaryObject = {};
	} else {
		this.auxiliaryObject = parameters.auxiliaryObject;
	}
	
	if(parameters.needRemoveAfterAutomaticalClose === undefined || typeof(parameters.needRemoveAfterAutomaticalClose) != 'boolean'){
		this.needRemoveAfterAutomaticalClose = false;
	} else {
		this.needRemoveAfterAutomaticalClose = parameters.needRemoveAfterAutomaticalClose;
	}
	
	
	this.getModalWindowTemplate = function(){
		var resultStr = '';
		
		
		resultStr += '<div id=' + this.id + ' class="fade modal" role="dialog" tabindex="-1">';
		resultStr += '<style>';
		
		if(this.standartTemplate){
		
			var headerStyle = getHeaderStyleByHeaderType();
			
			if(headerStyle == ''){
				resultStr += '#' + this.id + ' .head_block{';
				resultStr += 'font-size: 14pt;font-weight: 700;height: 42px;padding-top: 15px;text-align: center;vertical-align: middle;white-space: nowrap;';
				if(this.header_color !== null && this.header_color){
					resultStr += 'background-color:' + this.header_color + ';';
					resultStr += 'color:white;';
				}
				resultStr += '} ';
			} else {
				resultStr += '#' + this.id + ' .head_block{';
				resultStr += headerStyle;
				resultStr += '} ';
			}
			
			resultStr += '#' + this.id + ' form label{';
			
			
			if(this.label_width || this.label_width !== null){
				if(!isNaN(this.label_width)){
					resultStr += 'width:' + this.label_width + 'px;'; 	
				} else {
					resultStr += 'width:' + this.label_width + ';';
				}	
			}
			
			if(this.label_inline){
				resultStr += 'display:inline-block;';
			}
			
			
			if(this.label_text_align){
				resultStr += 'text-align:' + this.label_text_align + ';';
			}
			
			resultStr += '}';
			

			// на странице "Информация о рейсе" в окне "Добавление ТС на рейс" это css портит вид формы
			// в принципе формы могут быть с какой угодно версткой, и лучше меньше какого-либо css
			// устанавливать в html-форм

			resultStr += '#' + this.id + ' form input, ' + '#' + this.id + ' form select' + '{';

			//if(this.input_width || this.input_width !== null){
			//	if(!isNaN(this.input_width)){
			//		resultStr += 'width:' + this.input_width + 'px;';
			//	} else {
			//		resultStr += 'width:' + this.input_width + ';';
			//	}
			//}
			
			if(this.input_inline){
				resultStr += 'display:inline-block;';
			}
			resultStr += '}';



			resultStr += '#' + this.id + ' form .button-close{';



			
			if(this.close_button_inline){
				resultStr += 'display:inline-block;';
			}
			
			if(this.close_button_color){
				resultStr += 'background-color:' + this.close_button_color + ';';
			}
			
			if(this.close_button_text_color){
				resultStr += 'color:' + this.close_button_text_color + ';';
			}
			resultStr += '}';
			
			
			
			resultStr += '#' + this.id + ' form .button-submit{';
			if(this.save_button_inline){
				resultStr += 'display:inline-block;';
			}
			if(this.save_button_text_color){
				resultStr += 'color:' + this.save_button_text_color + ';';
			}
			if(this.save_button_color){
				resultStr += 'background-color:' + this.save_button_color + ';';
			}
			resultStr += '}';
			
		}
		
		if(self.headerType){
			var headerStyle = getHeaderStyleByHeaderType();
			resultStr += '#' + this.id + ' .head_block{';
			resultStr += headerStyle;
			resultStr += '}';
		}
		
		
		
		
		if(this.totalStyle){
			resultStr += convertStyleObjectToString(this.totalStyle);
		}
		
		resultStr += '</style>';
		
		var width = this.width;
		
		if(!isNaN(width)){
			width += 'px';
		}
		
		resultStr += '<div class="modal-dialog modal-md" style="width: ' + width + ';"><div class="modal-content"><div class="modal-header head_block"><button type="button" class="close crois_close" data-dismiss="modal" aria-hidden="true">×</button><span class="modal-title">' + this.title + '</span></div><div class="modal-body"><div id="modal-content">Загружаю...</div></div></div></div></div>';
		
		
		
		return resultStr;
		
		
	}
	
	var refresh_mode = false;
	
	var isValidJson = function(json) {
		try {
			JSON.parse(json);
			return true;
		} catch (e) {
			return false;
		}
	}
	
	var getAjaxDuringOpen = function(){
	
		var getStr = '';
		
		if(self.get_from && typeof(self.get_from) == 'object' && Object.keys(self.get_from).length){
			getStr += '?';
			var first = true;
			for(var get_param in self.get_from){
				if(first){
					getStr += get_param + '=' + self.get_from[get_param]; 
					first = false;
				} else {
					getStr += '&' + get_param + '=' + self.get_from[get_param];
				}
			}
		}
	
		$.ajax({
			url: self.url_from + getStr,
			type: 'post',
			data: {},
			contentType: false,
			cache: false,
			processData: false,
			success: function (response) {



				if(typeof(response) == 'object' && response.success !== undefined && (response.html !== undefined || response.form_html !== undefined)){
					
					if(response.success == true) {
						if(response.html !== undefined){
							$('#'+self.id).find('.modal-body').html(response.html);
						} else if(response.form_html !== undefined){
							$('#'+self.id).find('.modal-body').html(response.form_html);
						}
						$('#'+self.id + ' .modal-title').text(response.title);
						if(!refresh_mode){
							$('#'+self.id).removeClass().addClass('fade modal').addClass(response.class).modal('show');
							self.opened = true;
						}
						
						
						if(self.standartTemplate){
							submitAndCloseButtonManager();
						}
						
						bindEventsToAfterClose();
						
						
						
						$('#' + self.id + ' button.button-submit, ' + '#' + self.id +' input[type="submit"],' + '#' + self.id + ' button[type="submit"]').unbind('click').click(function(e){e.preventDefault();sendAjaxToServer();});
						
						if(typeof(self.afterOpenAction) == 'function'){
							self.afterOpenAction(self, response);
						}

					} else {
						alert(self.error_message_open);
					}
				
				} else {
					if(typeof(response) == 'string'){

						$('#' + self.id).find('.modal-body').html(response);
						$('#' + self.id + ' .modal-title').text(self.title);
						

						if(!refresh_mode){
							$('#' + self.id).removeClass().addClass('fade modal').modal('show');
							self.opened = true;
						}
						
						if(self.standartTemplate){
							submitAndCloseButtonManager();
						}
						
						bindEventsToAfterClose();
						$('#' + self.id + ' button.button-submit, ' + '#' + self.id +' input[type="submit"],' + '#' + self.id + ' button[type="submit"]').unbind('click').click(function(e){e.preventDefault();sendAjaxToServer();});
						
						if(typeof(self.afterOpenAction) == 'function'){
							self.afterOpenAction(self, response);
						}
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
	
	var execute_mode = false;
	
	var sendAjaxToServer = function(){
	
		if(self.clientValidation && typeof(self.clientValidation) == 'function'){
			if(!self.clientValidation(self)){
				return;
			}
		}
		
		if(self.dataAsquition && typeof(self.dataAsquition) == 'function'){
			var post_data = self.dataAsquition(self);
		} else {
		
			if(!execute_mode){

				// Славин сборщик данных из данных чекбокса делает массив с данными...
				//var post_data = asquitionPostDataFromForm($('#' + self.id + ' .modal-body form').eq(0));
				var post_data = ($('#' + self.id + ' .modal-body form').eq(0)).serialize();
			} else {
				var post_data = {};
			}
		}
		
		
	
		var getStr = '';
		
		if(self.get_to && typeof(self.get_to) == 'object' && Object.keys(self.get_to).length){
			getStr += '?';
			var first = true;
			for(var get_param in self.get_to){
				if(first){
					getStr += get_param + '=' + self.get_to[get_param]; 
					first = false;
				} else {
					getStr += '&' + get_param + '=' + self.get_to[get_param];
				}
			}
		}


		
		if(!execute_mode){

			//alert('отправляем запрос url=' + self.url_to + getStr);

			$.ajax({
				url: self.url_to + getStr,
				type: 'post',
				data: post_data,
				success: function (response) {

					console.log('response_1:'); console.log(response);

					if(response.error != undefined && response.error.length > 0) {
						alert(response.error);

					}else if(typeof(response) == 'object' && response.success !== undefined){

						if(response.success == true) {
							if(typeof(self.afterResponseSuccess) == 'function'){
								self.afterResponseSuccess(self, response);
							}

							if(self.success_message_response !== null){
								alert(self.success_message_response);
							}

							if(!execute_mode){
								self.close();
							}

						} else {

							$('#' + self.id).find('.modal-body').html(response);
							$('#' + self.id + ' button.button-submit, ' + '#' + self.id +' input[type="submit"],' + '#' + self.id + ' button[type="submit"]').unbind('click').click(function(e){e.preventDefault();sendAjaxToServer();});

							if(typeof(self.afterResponseError) == 'function'){
								self.afterResponseError(self, response);
							}

							if(self.error_message_response !== null){
								alert(self.error_message_response); // 1
							}
						}

					} else {
						if(typeof(response) == 'string'){


							if(response == 'ok') {

								if(typeof(self.afterResponseSuccess) == 'function'){
									self.afterResponseSuccess(self, response);
								}

								if(self.success_message_response !== null){
									alert(self.success_message_response);
								}
								if(!execute_mode){
									self.close();
								}

							}else {

								if(response != 'error'){
									$('#' + self.id).find('.modal-body').html(response);
								}

								if(typeof(self.afterResponseError) == 'function'){
									self.afterResponseError(self, response);
								}

								if(!execute_mode) {

									if (self.standartTemplate) {
										submitAndCloseButtonManager();
									}
									bindEventsToAfterClose();

									$('#' + self.id + ' button.button-submit, ' + '#' + self.id + ' input[type="submit"],' + '#' + self.id + ' button[type="submit"]').unbind('click').click(function (e) {
										e.preventDefault();
										sendAjaxToServer();
									});
								}

								if(self.error_message_response !== null){
									alert(self.error_message_response); // 4
								}
							}
						}
					}
				},
				error: function (data, textStatus, jqXHR) {
					if (textStatus == 'error' && data != undefined) {
						if (void 0 !== data.responseJSON) {
							if (data.responseJSON.message.length > 0) {
								if(typeof(self.afterResponseError) == 'function'){
									self.afterResponseError(self, data.responseJSON);
								}
								alert(data.responseJSON.message);
							}
						} else {
							if (data.responseText.length > 0) {
								if(typeof(self.afterResponseError) == 'function'){
									self.afterResponseError(self, data.responseJSON);
								}
								alert(data.responseText);
							}
						}
					}else {
						handlingAjaxError(data, textStatus, jqXHR);
					}
				}
			});

		} else {

			$.ajax({
				url: self.url_to + getStr,
				type: 'post',
				data: post_data,
				contentType: false,
				cache: false,
				processData: false,
				success: function (response) {

					//console.log('response_2:'); console.log(response);

					if(isValidJson(response)){
						var to_json = JSON.parse(response);

						if(typeof(to_json) == 'object'){
							response = to_json;
						}
					}

					if(typeof(response) == 'object' && response.success !== undefined){

						if(response.success == true) {
							if(typeof(self.afterResponseSuccess) == 'function'){
								self.afterResponseSuccess(self, response);
							}
							if(self.success_message_response !== null){
								alert(self.success_message_response);
							}
							if(!execute_mode){
								self.close();
							}
						} else {

							$('#' + self.id).find('.modal-body').html(response);
							$('#' + self.id + ' button.button-submit, ' + '#' + self.id +' input[type="submit"],' + '#' + self.id + ' button[type="submit"]').unbind('click').click(function(e){e.preventDefault();sendAjaxToServer();});

							if(typeof(self.afterResponseError) == 'function'){
								self.afterResponseError(self, response);
							}

							if(self.error_message_response !== null){
								alert(self.error_message_response); // 5
							}
						}

					} else {
						if(typeof(response) == 'string'){


							if(response == 'ok') {

								if(typeof(self.afterResponseSuccess) == 'function'){
									self.afterResponseSuccess(self, response);
								}

								if(self.success_message_response !== null){
									alert(self.success_message_response);
								}
								if(!execute_mode){
									self.close();
								}


							}else {

								if(response != 'error'){
									$('#' + self.id).find('.modal-body').html(response);
								}

								if(typeof(self.afterResponseError) == 'function'){
									self.afterResponseError(self, response);
								}

								if(!execute_mode){

									if(self.standartTemplate){
										submitAndCloseButtonManager();
									}

									bindEventsToAfterClose();
									$('#' + self.id + ' button.button-submit, ' + '#' + self.id +' input[type="submit"],' + '#' + self.id + ' button[type="submit"]').unbind('click').click(function(e){e.preventDefault();sendAjaxToServer();});
								}

								if(self.error_message_response !== null){
									alert(self.error_message_response); // 6
								}
							}
						}
					}
				},
				error: function (data, textStatus, jqXHR) {
					if (textStatus == 'error' && data != undefined) {
						if (void 0 !== data.responseJSON) {
							if (data.responseJSON.message.length > 0) {
								if(typeof(self.afterResponseError) == 'function'){
									self.afterResponseError(self, data.responseJSON);
								}
								alert(data.responseJSON.message);
							}
						} else {
							if (data.responseText.length > 0) {
								if(typeof(self.afterResponseError) == 'function'){
									self.afterResponseError(self, data.responseJSON);
								}
								alert(data.responseText);
							}
						}

					}else {
						handlingAjaxError(data, textStatus, jqXHR);
					}
				}
			});
		}
	}
	
	
	var bindEventsToAfterClose = function(){
		
		$('#'+self.id).find('.crois_close').bind('click', function(){
			self.close();
		});
		
		$('#'+self.id).find('.button-close').bind('click', function(){
			self.close();
		});
		
		
	}
	
	
	var createEmptyFormGroup = function(){
		
		$('#' + self.id + ' form').append('<div class="row"><div class="col-sm-2 for_close_button"></div><div class="col-sm-2 for_submit_button"></div></div>');
		
	}
	
	var hasSubmitButton = function(){

		if($('#' + self.id + ' form input[type="submit"]' + ',' +'#' + self.id + ' form button[type="submit"]').length){
			return true;
		}
		
		if($('#' + self.id + ' form .button-submit').length){
			return true;
		}
		
		return false;
	}
	
	var hasCloseButton = function(){
		if($('#' + self.id + ' form .button-close').length){
			return true;
		}
		
		if($('#' + self.id + ' form button[data-dismiss="modal"]').length){
			return true;
		}
		
		return false;	
	}
	
	
	var submitAndCloseButtonManager = function(){
		if(self.standartTemplate){
			if(!(hasSubmitButton() || hasCloseButton())){
				createEmptyFormGroup();
				var sac_close = $('#' + self.id + ' form .row .for_close_button');
				var sac_submit = $('#' + self.id + ' form .row .for_submit_button');
			} else {
				if($('#' + self.id + ' form .row .for_close_button').length){
					var sac_close = $('#' + self.id + ' .for_close_button').eq(0);
				} else {
					var sac_close = $('#' + self.id + ' form').eq(0);
				}
				
				if($('#' + self.id + ' form .row .for_submit_button').length){
					var sac_submit = $('#' + self.id + ' .for_submit_button').eq(0);	
				} else {
					var sac_submit = $('#' + self.id + ' form').eq(0);
				}
			}

			if(!hasCloseButton() && self.close_button_text != false){
				sac_close.append('<button class="btn btn-default button-close" type="button" data-dismiss="modal">' + self.close_button_text + '</button>');
			}
			
			if(!hasSubmitButton() && self.save_button_text != false){
				sac_submit.append('<button class="btn btn-primary button-submit" type="submit">' + self.save_button_text + '</button>');
			}
		}
	}
	
	
	
	this.open = function(){
	
		execute_mode = false;
		refresh_mode = false;
	
		if(self.id.indexOf('modal') != -1){
			$('#' + self.id).remove();
		} else {
			self.id = self.getVacantId(self.id);
		}
		
		$('body').append(self.getModalWindowTemplate());
		
		getAjaxDuringOpen();
	}
	
	this.opened = false;
	
	
	
	this.close = function(hide,remove){
	
		if(hide === undefined){
			hide = true;
		}
		
		if(remove === undefined){
			remove = this.needRemoveAfterAutomaticalClose;
		}

		// из-за уничтожения #default-modal или удаления класса модальные окна открывающиеся не через
		//   modalWindow уже не могут открыться.
		//if(hide){
		//	$('#'+self.id).removeClass().modal('hide');
		//}
		//
		

		if(hide){
		
			if(!remove){
				$('#'+self.id).modal('hide');
			} else {
				$('#'+self.id).removeClass().modal('hide');
			}
			
			
			
		}
		
		if(remove){
			$('#'+self.id).remove();
		}
		
		this.opened = false;
	}
	
	this.execute = function(){
		execute_mode = true;
		sendAjaxToServer();
	}
	
	this.refresh = function(){
		execute_mode = false;
		refresh_mode = true;
		getAjaxDuringOpen();
	}
	
	
	
}


var timesOfTrip = function(){

	var start_time = null;
	var mid_time = null;
	var end_time = null;

	var last_start_time = null;
	var last_mid_time = null;
	var last_end_time = null;

	var diff_start_time = null;
	
	var self = this;
	
	var getUTCTime = function(value){
		if(isTimeFormat(value)){
		
			var date = new Date();
			var year = date.getFullYear();
			var month = date.getMonth();
			var day = date.getDate();
			
			var spl = value.split(':');
			
			var hour = parseInt(spl[0]);
			var minute = parseInt(spl[1]);
			
			date.setHours(hour);
			date.setMinutes(minute);
			date.setSeconds(0);
			date.setMilliseconds(0);
			
			return parseInt((date.getTime()/1000/60).toFixed(0));
			 
		} else {
			return null;
		}
	}
	
	var UTCTimeToStr = function(value){
		if(!isNaN(value)){
			var date = new Date();
			date.setTime(value * 1000 * 60);
			//alert(date.getHours()+':'+date.getMinutes());
			var hour = date.getHours();
			
			if(hour<10){
				hour = ''+'0'+hour;
			}

			var minute = date.getMinutes();
			if(minute<10){
				minute = ''+'0'+minute;
			}
			
			return hour+':'+minute;
		} else {
			return 0;
		}
	}
	
	this.set_start_time = function(value, minutes_dif, callback, wrongValueCallback){

		if(isTimeFormat(value)){

			//if(value == start_time){
			//	return;
			//}
			last_start_time = start_time;
			start_time = value;
			
			//if(last_start_time){
			//	diff_start_time = getUTCTime(start_time) - getUTCTime(last_start_time);
			//}
			
			//if(mid_time && isTimeFormat(mid_time)){
			//
			//	if(diff_start_time !== null){
			//		var newMidTimeUTC = getUTCTime(mid_time) + diff_start_time;
			//	} else {
			//		//var newMidTimeUTC = getUTCTime(start_time) + 30;
			//		var newMidTimeUTC = getUTCTime(start_time) + minutes_dif;
			//	}
			//
			//} else {
			//	//var newMidTimeUTC = getUTCTime(start_time) + 30;
			//	var newMidTimeUTC = getUTCTime(start_time) + minutes_dif;
			//}
			var newMidTimeUTC = getUTCTime(start_time) + minutes_dif;
			last_mid_time = mid_time;
			mid_time = UTCTimeToStr(newMidTimeUTC);

			
			//if(end_time && isTimeFormat(end_time)){
			//	if(diff_start_time !== null){
			//		var newEndTimeUTC = getUTCTime(end_time) + diff_start_time;
			//	} else {
			//		//var newEndTimeUTC = getUTCTime(end_time) + 30;
			//		var newEndTimeUTC = getUTCTime(end_time) + minutes_dif;
			//	}
			//} else {
			//	//var newEndTimeUTC = getUTCTime(mid_time) + 30;
			//	var newEndTimeUTC = getUTCTime(mid_time) + minutes_dif;
			//}
			var newEndTimeUTC = getUTCTime(mid_time) + minutes_dif;
			last_end_time = end_time;
			end_time = UTCTimeToStr(newEndTimeUTC);

			if(typeof(callback) == 'function'){
				callback(self);
			}
			
		} else {
			if(typeof(wrongValueCallback) == 'function'){
				wrongValueCallback(self);
			}
		}
	}
	
	this.get_start_time = function(){
		return start_time;
	}
	
	this.set_mid_time = function(value, callback, wrongValueCallback){
		if(isTimeFormat(value)){
			
			if(value == mid_time){
				return;
			}
			if(getUTCTime(value) < getUTCTime(start_time)){
				return;
			}
			last_mid_time = mid_time;
			mid_time = value;

			if(getUTCTime(end_time) - getUTCTime(mid_time) < 0){
				var newEndTimeUTC = getUTCTime(mid_time) + getUTCTime(mid_time) - getUTCTime(start_time);
				last_end_time = end_time;
				end_time = UTCTimeToStr(newEndTimeUTC);
			}
			
			if(end_time === null){
				var newEndTimeUTC = getUTCTime(mid_time) + getUTCTime(mid_time) - getUTCTime(start_time);
				last_end_time = end_time;
				end_time = UTCTimeToStr(newEndTimeUTC);
			}

			if(typeof(callback) == 'function'){
				callback(self);
			}
			
		} else {
			if(typeof(wrongValueCallback) == 'function'){
				wrongValueCallback(self);
			}
		}
	}
	
	this.get_mid_time = function(){
		return mid_time;
	}
	
	
	this.set_end_time = function(value, callback, wrongValueCallback){
		if(isTimeFormat(value)){
		
			if(value == end_time){
				return;
			}
		
			if(getUTCTime(value) < getUTCTime(mid_time)){
				var newEndTimeUTC = getUTCTime(mid_time) + getUTCTime(mid_time) - getUTCTime(start_time);
				last_end_time = end_time;
				end_time = UTCTimeToStr(newEndTimeUTC);
				if(typeof(callback) == 'function'){
					callback(self);
				}
				return;	
			}
		
			last_end_time = end_time;
			end_time = value;
			
			if(typeof(callback) == 'function'){
				callback(self);
			}
			
		} else {
			if(typeof(wrongValueCallback) == 'function'){
				wrongValueCallback(self);
			}
		}
	}
	
	this.get_end_time = function(){
		return end_time;
	}
}


function formTimeValidation(modalObj){
	if(!isTimeFormat($('#' + modalObj.id + ' input[name="Trip[start_time]"]').val(),true)){
		alert('Неправильное время начала сбора');
		return false;
	}

	if(!isTimeFormat($('#' + modalObj.id + ' input[name="Trip[mid_time]"]').val(),true)){
		alert('Неправильное время середины сбора');
		return false;
	}

	if(!isTimeFormat($('#' + modalObj.id + ' input[name="Trip[end_time]"]').val(),true)){
		alert('Неправильное время конца сбора');
		return false;
	}

	return true;
}


function isTimeFormat(time, lead_zero_at_hours){

	if(lead_zero_at_hours === undefined){
		lead_zero_at_hours = true;
	}

	if(!time || time == ''){
		return false;
	}

	if(time.indexOf(':') == -1){
		return false;
	}

	var hour_min = time.split(':');
	if(hour_min.length != 2){
		return false;
	}

	if(lead_zero_at_hours){
		if(isNaN(hour_min[0]) || isNaN(hour_min[1]) || (''+hour_min[0]).length != 2 || (''+hour_min[1]).length != 2){
			return false;
		}
	} else {
		if(isNaN(hour_min[0]) || isNaN(hour_min[1]) || (''+hour_min[0]).length > 2 || (''+hour_min[1]).length != 2 || (''+hour_min[0]).length == 0){
			return false;
		}
	}

	if(hour_min[0] > 24 || hour_min[0] < 0){
		return false;
	}
	if(hour_min[1] > 60 || hour_min[1] < 0){
		return false;
	}

	return true;
}


// функция возвращает дату в формате "dd.mm.yyyy"
function getDate(milliseconds)
{
	var date = new Date(milliseconds);

	var today_day = date.getDate();
	if(today_day < 10) {
		today_day = '0' + today_day;
	}

	var today_month = date.getMonth() + 1;
	if(today_month < 10) {
		today_month = '0' + today_month;
	}

	var today_year = date.getFullYear();

	return today_day + '.' + today_month + '.' + today_year;
}


function getUrlParams(type) {

	var url = location.search;	// строка GET запроса

	if(void 0 === type) {
		type = 'array'
	}

	// Обработка GET-запросов
	var tmp = new Array();    // два вспомогательных
	var tmp2 = new Array();  // массива

	if(type == 'array') {
		var url_params = new Array();
		if (url != '') {
			tmp = (url.substr(1)).split('&');
			for (var i = 0; i < tmp.length; i++) {
				tmp2 = tmp[i].split('=');
				url_params[tmp2[0]] = tmp2[1];
			}
		}
	}else if(type == 'object') {

		var url_params = {};
		if (url != '') {
			tmp = (url.substr(1)).split('&');
			for (var i = 0; i < tmp.length; i++) {
				tmp2 = tmp[i].split('=');
				url_params[tmp2[0]] = tmp2[1];
			}
		}
	}

	return url_params;
}


// Модальное окно "Информация об автомобиле" - нажатие кнопки "Подтвердить"
function transportConfirmedTransportInfoClick(obj, modalObj_plus) {

	if(obj.next().val() == '1'){
		obj.next().val('0');
	} else {
		obj.next().val('1');
	}

	var trip_transport_id = obj.parents('.form-data').attr('trip_transport_id');
	var driver_id = obj.parents('.form-data').attr('driver_id');

	if(driver_id.length == 0) {
		alert('Необходимо установить водителя');

		return false;
	}

	var execute_confirm_change = new modalWindow({
		url_to:'/trip-transport/change-confirm',
		get_to:{
			trip_transport_id: trip_transport_id,
			confirmed: obj.next().val()
		},
		afterResponseSuccess: function(execution, response){
			modalObj_plus.refresh();
			if($('#trip-orders-page').length > 0) { // обновление страницы "Информация о рейсе"
				// updateTripOrdersPage();
			}else if($('#set-of-trips-page').length > 0) {
				// updateSetTripsPage();
			}else if($('#directions-block').length > 0) {
				// updateDirectionsTripBlock();
			}
		},
		afterResponseError: function(execution, response){
			modalObj_plus.refresh();
		},
		success_message_response: null
	});
	execute_confirm_change.execute();
};


// логи действий пользователя
function LogDispatcherAccounting(value) {

	$.ajax({
		url: '/dispatcher-accounting/ajax-create-log-dbl-click',
		type: 'post',
		data: {
			value: value
		},
		success: function (data) {

			if (data.success != true) {
				alert('Ошибка изменения поля');
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

/*
// функция обновления окошка показывающего количество заявок
function updateClientextBlock()
{
	$.ajax({
		url: '/client-ext/ajax-get-clientext-block',
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
			if (textStatus == 'error') {
				if (void 0 !== data.responseJSON) {
					if (data.responseJSON.message.length > 0) {
						alert(data.responseJSON.message);
					}
				} else {
					if (data.responseText.length > 0) {
						alert(data.responseText);
					}
				}
			}
		}
	});
}
*/

$(document).ready(function()
{
	// обновление блока с количеством заявок на всех страницах сайта (кроме тех где Слава переопределил шаблон)
	//setInterval(function() {
	//	updateClientextBlock();
	//}, 10000);

	// обновление блока с чатом
	// setInterval(function() {
	// 	updateChat();
	// }, 60000);

	// выбор даты в верхнем меню - открывается при щелчке на "ДРУГОЙ ДЕНЬ"
	$.datepicker.setDefaults($.datepicker.regional["ru"]);
	$('#another-day').datepicker({
		onSelect: function(dateText, inst) {
			location.href = '/?date='+dateText;
		}
	});

	// Запись на сегодня - открытие модального окна создания заказа на сегодня
	$('body').on('click', '#new-order-today', function() {

		var now = new Date();
		var now_milliseconds = now.getTime();
		var today_date = getDate(now_milliseconds);
		//openModalCreateOrder(today_date);

		var data = {
			date: today_date
		}
		openModalCreateOrder(data);

		return false;
	});
	$('body').on('click', '#new-order-tomorrow', function() {
		var now = new Date();
		var tomorrow_milliseconds = now.getTime() + 86400000;
		var tomorrow_date = getDate(tomorrow_milliseconds);
		//openModalCreateOrder(tomorrow_date);
		var data = {
			date: tomorrow_date
		}
		openModalCreateOrder(data);

		return false;
	});
	$('body').on('click', '#new-order-another-day', function() {
		//openModalCreateOrder();
		var data = {}
		openModalCreateOrder(data);

		return false;
	});
});


// открытие модального окна "привязка транспортного средства к рейсу" на странице "Состав рейса" и "Расстановка"
$(document).on('click', '#add-trip-transport-car, .add_transport_plus, #set-of-trips-page .place', function()
{
	var trip_id = $(this).attr('trip-id');
	var self = this;

	var attachTransport = new modalWindow({
		id: 'default-modal',
		url_from:'/trip-transport/ajax-get-add-cars-form',
		get_from:{
			trip_id: $(self).attr('trip-id')
		},
		url_to:'/trip-transport/ajax-save-cars-form',
		get_to:{
			trip_id: $(self).attr('trip-id')
		},
		width: 680,
		dataAsquition: function(modalWindow)
		{
			var transport_ids = [];
			var driver_ids = [];
			var confirmed = [];
			var tt_id = [];
			var sorts = [];
			var i = 0;

			var sort = $('#' + modalWindow.id + ' .modal-body .trip-transport-row').length;
			$('#' + modalWindow.id + ' .modal-body .trip-transport-row').each(function() {
				var transport_id = $(this).find('*[name="TripTransport[transport_id]"]').val();
				var driver_id = $(this).find('*[name="TripTransport[driver_id]"]').val();
				var confirmed_item = $(this).find('*[name="confirmed[]"]').val();
				var tt_id_item = $(this).find('*[name="tt_id[]"]').val();
				if(transport_id.length > 0 && confirmed_item.length > 0) {
					transport_ids[i] = transport_id;
					driver_ids[i] = driver_id;
					confirmed[i] = confirmed_item;
					tt_id[i] = tt_id_item;
					sorts[i] = sort;
					i++;
					sort--;
				}
			});

			return {
				transport_ids: transport_ids,
				driver_ids: driver_ids,
				confirmed: confirmed,
				tt_id: tt_id,
				sorts: sorts
			}
		},
		afterResponseSuccess : function(mw, response){
			if($('#trip-orders-page').length > 0) { // обновление страницы "Информация о рейсе"
				// updateTripOrdersPage();
			}else if($('#set-of-trips-page').length > 0) {
				// updateSetTripsPage();
			}
		},
		standartTemplate: false,
		title: 'Добавить транспорт к рейсу',
		header_color:'black',
		save_button_text:'Применить',
		success_message_response: null //'Транспорт добавлен к рейсу' - это сообщение зачем показывать?
	});

	attachTransport.open();

	return false;
});



// щелчек на блоке "x заявок"
$(document).on('click', '.clientext-widget', function() {

	$.ajax({
		//url: '/client-ext/ajax-get-client-ext-list',
		url: '/order/ajax-get-client-ext-list',
		type: 'post',
		data: {},
		success: function (response) {
			if(response.success == true) {

				//$('#clientext-modal').css({
				//	width: '0px'
				//});
				//$('#default-modal .modal-dialog').css('width', '600px');
				$('#clientext-modal').find('.modal-body').html(response.html);
				$('#clientext-modal').modal('show');

			}else {
				alert('неустановленная ошибка загрузки списка заявок');
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


// щелчек на одной из заявок в списке заявок открывает форму создания заказа
$(document).on('click', '#clientext-list .clientext', function() {

	//var clientext_id = $(this).attr('clientext-id');
	var order_id = $(this).attr('order-id');
	//openModalCreateOrder(null, null, null, clientext_id);
	//openModalCreateOrder(null, null, order_id);

	var data = {
		order_id: order_id
	}
	openModalCreateOrder(data);
});


$(document).on('click', '.transport-name', function()
{
	var trip_transport_id = $(this).attr('trip_transport_id');

	var transportInfo = new modalWindow({
		url_from: '/trip-transport/show-car-info',
		get_from:{
			trip_transport_id: trip_transport_id
		},
		standartTemplate: false,
		actionType:'submit',

		afterOpenAction:function(modalObj_plus, response){

			$('#'+modalObj_plus.id+' .transport-confirmed-transport-info').unbind('click').bind('click', function(){

				transportConfirmedTransportInfoClick($(this), modalObj_plus);
			});


			$('#'+modalObj_plus.id + ' button.change_driver_on_trip_transport').unbind('click').bind('click', function(){
				if(!$('#'+modalObj_plus.id+' .change_driver').val() || $('#'+modalObj_plus.id+' .change_driver').val() == ''){
					alert('Выберете водителя!');
					return;
				}

				var driver_id = $('#'+modalObj_plus.id+' .change_driver').val();

				var execute_change_driver = new modalWindow({
					url_to:'/trip-transport/change-driver-or-car',
					get_to:{trip_transport_id: trip_transport_id, driver_id:driver_id },
					afterResponseSuccess: function(execution, response){
						var execute_confirm_change = new modalWindow({
							url_to:'/trip-transport/change-confirm',
							get_to:{trip_transport_id: trip_transport_id, confirmed:0 },
							afterResponseSuccess: function(execution, response){
								modalObj_plus.refresh();
							},
							afterResponseError: function(execution, response){
								modalObj_plus.refresh();
							},
							success_message_response:null

						});

						execute_confirm_change.execute();

						//modalObj_plus.refresh();


					},

					afterResponseError: function(execution, response){


						modalObj_plus.refresh();
					},

					success_message_response:'Водитель сменён!',
					error_message_response: 'Не удалось поменять водителя'

				});

				execute_change_driver.execute();
			});


			$('#'+modalObj_plus.id+' .remove_from_trip').unbind('click').bind('click', function(){
				var execute_remove = new modalWindow({
					url_to:'/trip-transport/delete-trip-transport',
					get_to:{trip_transport_id: trip_transport_id },
					afterResponseSuccess: function(execution, response){

						modalObj_plus.close();


					},

					afterResponseError: function(execution, response){


						modalObj_plus.refresh();
					},

					success_message_response:'Этот транспорт снят с рейса',
					error_message_response: 'Не удалось снять этот транспорт с рейса'

				});

				execute_remove.execute();
			});

		},
		title: 'Информация об автомобиле',
		//totalStyle: '.head_block{background-color:white;color:#080808;line-height:30pt;font-size:26px;font-weight:300;margin-left:0;text-align:left;}'
	});
	transportInfo.open();
});


var transport_map = null;
$(document).on('click', '.show-driver-position', function() {

	var trip_transport_id = $(this).attr('trip-transport-id');

	$(this).parents('.modal').find('.button-close').click();

	$.ajax({
		url: '/trip-transport/ajax-get-transport-position?id=' + trip_transport_id,
		type: 'post',
		success: function (response) {

			$('#default-modal').find('.modal-dialog').width('800px');
			$('#default-modal').find('.modal-body').html('<div id="transport-map"></div>').css('padding', '0');

			var title = 'Местоположение машины ' + response.transport_color + ' ' + response.transport_model + ' ' + response.transport_car_reg;
			$('#default-modal .modal-title').html(title);
			$('#default-modal').modal('show');

			$('#default-modal').on('hidden.bs.modal', function(e) {
				$('#default-modal').find('.modal-body').css('padding', '15px');
			});


			ymaps.ready(function(){

				transport_map = new ymaps.Map("transport-map", {
					center: [response.lat, response.long], // показываем центр города где осуществляется посадка
					zoom: 12,
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

				var message = response.transport_name + "<br />" + response.driver_name;
				var placemark = new ymaps.Placemark([response.lat, response.long], {
					hintContent: message,
					//balloonContentHeader: '<div style="width: 100%; background: #FF0000; color: #FFFFFF;">qqq</div>',
					//balloonContentHeader: "balloonContentHeader",
					balloonContent: message
				}, {
					iconLayout: 'islands#circleIcon',
					iconColor: '#1E98FF',
					iconImageSize: [16, 16],
					iconImageOffset: [-8, -8],
					// Определим интерактивную область над картинкой.
					iconShape: {
						type: 'Circle',
						coordinates: [0, 0],
						radius: 8
					}
				});

				transport_map.geoObjects.add(placemark);



				//var placemark = new ymaps.Placemark([response.lat, response.long], {
				//	hintContent: "qqq",
				//	balloonContent: "www",
				//	//iconContent: '12'
				//}, {
				//	//iconLayout: 'default#image',
				//	//iconLayout: 'islands#icon',
				//	//iconLayout: 'islands#dotIcon',
				//	iconLayout: 'islands#circleIcon',
				//	//iconColor: '#1E98FF',
				//	iconColor: color,
				//	//iconImageHref: '/img/map-point.png',
				//	iconImageSize: [16, 16],
				//	iconImageOffset: [-8, -8],
				//	// Определим интерактивную область над картинкой.
				//	iconShape: {
				//		type: 'Circle',
				//		coordinates: [0, 0],
				//		radius: 8
				//	},
				//});

				//var placemark = new ymaps.Placemark([response.lat, response.long], {
				//	hintContent: "qqq",
				//	balloonContent: "qqq"
				//	//data: data,
				//	//iconContent: orders_count
				//	//iconContent: "Диаграмма"
				//}, {
				//	iconLayout: 'default#pieChart',
				//	// Радиус диаграммы в пикселях.
				//	iconPieChartRadius: 15,
				//	// Радиус центральной части макета.
				//	iconPieChartCoreRadius: 9,
				//	// Стиль заливки центральной части.
				//	//    iconPieChartCoreFillStyle: '#ffffff',
				//	// Cтиль линий-разделителей секторов и внешней обводки диаграммы.
				//	//    iconPieChartStrokeStyle: '#ffffff',
				//	// Ширина линий-разделителей секторов и внешней обводки диаграммы.
				//	//    iconPieChartStrokeWidth: 3,
				//	// Максимальная ширина подписи метки.
				//	//    iconPieChartCaptionMaxWidth: 200
				//});
                //
                //
				//console.log('transport_map:'); console.log(transport_map);
				//transport_map.geoObjects.add(placemark);

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

	return false;
});



// открытие формы редактирования пассажиров заказа
$(document).on('click', '.edit-passengers', function()
{
	var mobile_phone = $('#order-client-form #client-mobile_phone').val();

	if(mobile_phone != undefined) {
		mobile_phone = mobile_phone.replace(/\*/g, '');
		if (mobile_phone.length < 15) {
			alert('Введите телефон');
			return false;
		}
	}


	var data = {};
	data.order_id = $(this).attr('order-id');
	data.client_id = $(this).attr('client-id');
	var places_count = parseInt($('#order-client-form').find('*[name="Order[places_count]"]').val());
	if(!isNaN(places_count)) {
		data.places_count = places_count;
	}

	$.ajax({
		url: '/order/ajax-get-passengers-form',
		type: 'post',
		data: data,
		success: function (response) {

			//$('#default-modal').find('.modal-body').html(response.html);
			//$('#default-modal').find('.modal-dialog').width('1000px');
			//$('#default-modal .modal-title').text('Пассажиры заказа id='+data['order_id']);
			//$('#default-modal').modal('show');

			if($('#passengers-modal').length == 0) {
				var html =
					'<div id="passengers-modal" class="fade modal" style="display: none;">'
					+ $('#default-modal').html()
					+ '</div>';
				$('body').append(html);
			}

			$('#passengers-modal').find('.modal-body').html(response.html);
			$('#passengers-modal').find('.modal-dialog').width('1000px');
			$('#passengers-modal .modal-title').text('Пассажиры заказа id='+data['order_id']);
			$('#passengers-modal').modal('show');

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

function findPassportPassenger($passenger_form) {

	var passport_series = $.trim($passenger_form.find('input[name="Passenger[series]"]').val());
	var passport_number = $.trim($passenger_form.find('input[name="Passenger[number]"]').val());
	//console.log('passport_series='+passport_series+' passport_number='+passport_number);

	if(passport_series.length == 4 && passport_number.length == 6) {

		$.ajax({
			url: '/passenger/ajax-get-passenger',
			type: 'post',
			data: {
				passport_series: passport_series,
				passport_number: passport_number
			},
			success: function (response) {

				//console.log('response:'); console.log(response);
				if(response.passenger != null) {

					$passenger_form.find('*[name="Passenger[surname]"]').val(response.passenger.surname);
					$passenger_form.find('*[name="Passenger[name]"]').val(response.passenger.name);
					$passenger_form.find('*[name="Passenger[patronymic]"]').val(response.passenger.patronymic);


					$passenger_form.find('*[name="Passenger[date_of_birth]"]').val(response.passenger.date_of_birth);
					$passenger_form.find('*[name="Passenger[citizenship]"]').val(response.passenger.citizenship);
					$passenger_form.find('*[name="Passenger[gender]"]').val(response.passenger.gender);
					$passenger_form.attr('order-passenger-passenger-id', response.passenger.id)

				}else {
					$passenger_form.removeAttr('order-passenger-passenger-id');
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
}

$(document).on('keyup', '.passengers-form input[name="Passenger[series]"]', function() {
	var $passenger_form = $(this).parents('form');
	findPassportPassenger($passenger_form);

	if($.trim($(this).val()).length == 4) {
		$(this).parents('form').find('input[name="Passenger[number]"]').focus();
	}
});
$(document).on('keyup', '.passengers-form input[name="Passenger[number]"]', function() {
	var $passenger_form = $(this).parents('form');
	findPassportPassenger($passenger_form);

	if($.trim($(this).val()).length == 6) {
		$(this).parents('form').find('input[name="Passenger[surname]"]').focus();
	}
});

$(document).on('change', '.passengers-form input[name="Passenger[child]"]', function() {

	var $form = $(this).parents('form');
	if($(this).is(':checked')) {
		$form.find('input[name="Passenger[series]"]').val('').attr('disabled', 'disabled');
		$form.find('input[name="Passenger[number]"]').val('').attr('disabled', 'disabled');
	}else {
		$form.find('input[name="Passenger[series]"]').removeAttr('disabled');
		$form.find('input[name="Passenger[number]"]').removeAttr('disabled');
	}
});



$(document).on('change', '.passengers-form *[name="Passenger[document_type]"]', function() {

	// series_number_placeholder
	var $option = $(this).find('option:selected');
	var value = $option.attr('value');
	var placeholder = $option.attr('series_number_placeholder');
	var $form = $(this).parents('form');


	$form.find('input[name="Passenger[series_number]"]').attr('placeholder', placeholder);

	if(value == 'foreign_passport') {
		$form.find('*[name="Passenger[citizenship]"]').parents('.form-group').show();
	}else {
		$form.find('*[name="Passenger[citizenship]"]').parents('.form-group').hide();
	}
});



// сохранение формы редактирования пассажиров заказа
$(document).on('click', '.save-passenger-button', function() {

	var $form = $(this).parents('form');

	var Passenger = {
		client_id: $form.find('*[name="Passenger[client_id]"]').val(),
		document_type: $form.find('*[name="Passenger[document_type]"]').val(),
		citizenship: $form.find('*[name="Passenger[citizenship]"]').val(),
		//child: 0 + $form.find('input[name="Passenger[child]"]').is(':checked'),
		//series: $form.find('*[name="Passenger[series]"]').val(),
		//number: $form.find('*[name="Passenger[number]"]').val(),
		series_number: $form.find('*[name="Passenger[series_number]"]').val(),
		//surname: $form.find('*[name="Passenger[surname]"]').val(),
		//name: $form.find('*[name="Passenger[name]"]').val(),
		//patronymic: $form.find('*[name="Passenger[patronymic]"]').val(),
		fio: $form.find('*[name="Passenger[fio]"]').val(),

		date_of_birth: $form.find('*[name="Passenger[date_of_birth]"]').val(),
		gender: $form.find('*[name="Passenger[gender]"]').val()
	};

	var OrderPassenger = {
		id: $form.attr('order-passenger-id'),
		order_id: $form.attr('order-passenger-order-id'),
		passenger_id: $form.attr('order-passenger-passenger-id'),
	};

	if(OrderPassenger.id == undefined) {
		OrderPassenger.id = "";
	}
	if(OrderPassenger.order_id == undefined) {
		OrderPassenger.order_id = "";
	}
	if(OrderPassenger.passenger_id == undefined) {
		OrderPassenger.passenger_id = "";
	}


	var data = {
		Passenger: Passenger,
		OrderPassenger: OrderPassenger
	};


	//data['Passenger[client_id]'] = $form.find('*[name="Passenger[client_id]"]').val();
	//data['Passenger[child]'] = 0 + $form.find('input[name="Passenger[child]"]').is(':checked');
	//data['Passenger[series]'] = $form.find('*[name="Passenger[series]"]').val();
	//data['Passenger[number]'] = $form.find('*[name="Passenger[number]"]').val();
	//data['Passenger[surname]'] = $form.find('*[name="Passenger[surname]"]').val();
	//data['Passenger[name]'] = $form.find('*[name="Passenger[name]"]').val();
	//data['Passenger[patronymic]'] = $form.find('*[name="Passenger[patronymic]"]').val();
	//data['Passenger[date_of_birth]'] = $form.find('*[name="Passenger[date_of_birth]"]').val();
	//data['Passenger[citizenship]'] = $form.find('*[name="Passenger[citizenship]"]').val();
	//data['Passenger[gender]'] = $form.find('*[name="Passenger[gender]"]').val();
    //
	//data['OrderPassenger[id]'] = $form.attr('order-passenger-id');
	//data['OrderPassenger[order_id]'] = $form.attr('order-passenger-order-id');
	//data['OrderPassenger[passenger_id]'] = $form.attr('order-passenger-passenger-id');
    //
	//if(data['OrderPassenger[id]'] == undefined) {
	//	data['OrderPassenger[id]'] = "";
	//}
	//if(data['OrderPassenger[order_id]'] == undefined) {
	//	data['OrderPassenger[order_id]'] = "";
	//}
	//if(data['OrderPassenger[passenger_id]'] == undefined) {
	//	data['OrderPassenger[passenger_id]'] = "";
	//}


	//console.log('data:'); console.log(data);
	$.ajax({
		url: '/order/ajax-save-passenger',
		type: 'post',
		data: data,
		beforeSend: function () {
			//allow_send_order = false;
		},
		success: function (response) {

			//allow_send_order = true;
			if (response.success == true) {

				$form.attr('order-passenger-passenger-id', response.passenger_id);
				$form.attr('order-passenger-id', response.order_passenger_id);
				$form.find('.save-passenger-button').removeClass('btn-success').addClass('btn-primary').text('Переписать');

				// если кол-во сохраненных пассажиров на этом заказе соответсвует кол-ву пассажиров, то
				// иконка открытия формы с пассажирами должна стать зеленой, иначе красной
				var num_places = 0;
				var num_passengers = 0;
				$('.passengers-form form').each(function() {
					num_places++;
					var passenger_id = $(this).attr('order-passenger-passenger-id');
					if(passenger_id > 0) {
						num_passengers++;
					}
				});

				if(num_passengers < num_places) { // не все заполнены места пассажирами
					$('#order-client-form').find('.edit-passengers[order-id="' + data.OrderPassenger.order_id + '"]')
						//.removeClass('text-success')
						.removeClass('text-danger')
						.addClass('text-danger');

					$('#orders-grid').find('.edit-passengers[order-id="' + data.OrderPassenger.order_id + '"]')
						.removeClass('btn-success')
						.removeClass('btn-danger')
						.addClass('btn-danger');

				}else { // все заполнены места
					$('#order-client-form').find('.edit-passengers[order-id="' + data.OrderPassenger.order_id + '"]')
						.removeClass('text-success').removeClass('text-danger')
						//.addClass('text-success')
						.text('Изменить');

					$('#orders-grid').find('.edit-passengers[order-id="' + data.OrderPassenger.order_id + '"]')
						.removeClass('btn-success')
						.removeClass('btn-danger')
						.addClass('btn-success')
				}

				alert('Успешно сохранено');
				$('#default-modal').find('.close').click();

			} else {
				var errors = '';
				for (var field in response.order_passenger_errors) {
					var field_errors = response.order_passenger_errors[field];
					for (var key in field_errors) {
						errors += field_errors[key] + ' ';
					}
				}
				for (var field in response.passenger_errors) {
					var field_errors = response.passenger_errors[field];
					for (var key in field_errors) {
						errors += field_errors[key] + ' ';
					}
				}

				alert(errors);
			}
		},
		error: function (data, textStatus, jqXHR) {

			// allow_send_order = true;
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

				resetOrderFormRadiobuttons();

			}else {
				handlingAjaxError(data, textStatus, jqXHR);
			}
		}
	});

});


/**/
$(document).on('mouseenter', '.call-phone', function() {

	if($(this).next('.phone-tube').length == 0) {
		$(this).after('<div class="phone-tube"><img src="/img/tube.png" /></div>');
	}

	$(this).next('.phone-tube').show();
});

$(document).on('mouseleave', '.call-phone', function() {
	var $obj = $(this);
	setTimeout(function () {
		if($obj.is(':hover') || $obj.next('.phone-tube').is(':hover')) {
			//$obj.next('.phone-tube').show();
		}else {
			$obj.next('.phone-tube').hide();
		}

	}, 200);
});

$(document).on('mouseleave', '.phone-tube', function() {
	var $obj = $(this).prev('.call-phone');
	setTimeout(function () {
		if($obj.is(':hover') || $obj.next('.phone-tube').is(':hover')) {
			//$obj.next('.phone-tube').show();
		}else {
			$obj.next('.phone-tube').hide();
		}

	}, 200);
});
/**/



// клик по трубке всплывающей
$(document).on('click', '.phone-tube', function() {

	var $obj = $(this);
	var phone = $.trim($(this).prev('.call-phone').text());
	//var receiver = $(this).prev('.call-phone').attr('receiver');

	if(phone != '') {
		phone = phone.replace(new RegExp("[^0-9]",'g'),'');
	}

	if(phone.length > 0) {

		$.ajax({
			url: '/call/ajax-make-call',
			type: 'post',
			data: {
				phone: phone
			},
			success: function (response) {
				if (response.success == true) {
					$obj.hide(300);
				}else {
					$obj.hide(300);
					if(response.error != undefined && response.error.length > 0) {
						alert(response.error);
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
});


$(document).on('click', '.call-phone-button', function() {

	if($(this).parents('.missed-call').hasClass('disable')) {
		alert('Линия занята');
		return false;
	}
	var phone = $(this).attr('phone');

	if($(this).parents('#incoming-request-orders-modal').length > 0) {

		var order_id = $(this).parent('.request').attr('order-id');
		var data = {
			order_id: order_id
		}
		openModalCreateOrder(data);


		// логируем действие оператора
		$.ajax({
			url: '/dispatcher-accounting/ajax-create-log-handling-request?order_id='+order_id+'&phone='+phone,
			type: 'post',
			data: {},
			success: function (data) {

				if (data.success != true) {
					alert('Ошибка сохранения действия оператора');
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


	if(phone != '') {
		phone = phone.replace(new RegExp("[^0-9]",'g'),'');
	}

	if(phone.length > 0) {
		$.ajax({
			url: '/call/ajax-make-call',
			type: 'post',
			data: {
				phone: phone,
				caused_by_missed_call_window: true
			},
			success: function (response) {
				if (response.success == true) {
					// ok
				}else {
					if(response.error != undefined && response.error.length > 0) {
						alert(response.error);
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

	return false;
});



function setOperatorStatus(status) {

	$.ajax({
		url: '/call/ajax-set-operator-status?status=' + status,
		type: 'post',
		data: {},
		success: function (response) {
			if (response.success == true) {
				if(status == 'ONLINE') {
					//$('.btn-operator-offline')
					//	.addClass('btn-operator-online')
					//	.removeClass('btn-operator-offline')
					//	.removeClass('blink')
					//	.addClass('btn-default')
					//	.removeClass('btn-danger')
					//	.text('Онлайн');
					alert('Агент со статусом Онлайн');
					location.reload();
				}else if(status == 'OFFLINE') {
					//$('.btn-operator-online')
					//	.addClass('btn-operator-offline')
					//	.removeClass('btn-operator-online')
					//	.addClass('btn-danger')
					//	.addClass('blink')
					//	.removeClass('btn-default')
					//	.text('Оффлайн');
					alert('Агент со статусом Отключен');
					location.reload();
				}
			}else {
					alert('ошибка: ' + response.error);
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

				location.reload();

			}else {
				handlingAjaxError(data, textStatus, jqXHR);
			}
		}
	});
}

$(document).on('click', '.btn-operator-offline', function() {
	if($(this).hasClass('active')) {
		return false;
	}
	setOperatorStatus('ONLINE');

	return false;
});
$(document).on('click', '.btn-operator-online', function() {
	if($(this).hasClass('active')) {
		return false;
	}
	setOperatorStatus('OFFLINE');

	return false;
});

$(document).on('click', '.msg-from-driver .modal-close', function() {

	var $current_modal = $(this).parents('.msg-from-driver');
	var minus_height = parseInt($current_modal.outerHeight(true)) + 5
	var $next_current_modals = $current_modal.nextAll('.msg-from-driver');
	$next_current_modals.each(function() {
		var top = parseInt($(this).offset().top);
		top = top - minus_height;
		$(this).css('top', top + 'px');
	});

	$current_modal.remove();
});

function setAnswerForDriverMessage(chat_id, text) {

	// console.log('setAnswerForDriverMessage chat_id=' + chat_id + ' text=' + text);
	var $msg_block = $('.msg-from-driver[chat_id="' + chat_id + '"]');
	if($msg_block != undefined) {
		$msg_block.find('.answer-block').after('<div class="answer-text">Ответ оператора: ' + text + '</div>');
		$msg_block.find('.answer-block').hide();

	}
}

$(document).on('click', '.msg-from-driver .send-to-driver-answer', function() {

	var chat_id = $(this).parents('.msg-from-driver').attr('chat_id');
	var text = $.trim($(this).parents('.msg-from-driver').find('.answer').val());

	//alert('trip_transport_id='+trip_transport_id+' text='+text);
	if(text == '') {
		return false;
	}

	$.ajax({
		url: '/driver/ajax-send-msg-to-driver?chat_id=' + chat_id + '&text=' + text,
		type: 'post',
		data: {},
		success: function (response) {
			if (response.success == true) {
				alert('Сообщение отправлено');
			}else {
				alert('ошибка: ' + response.error);
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
});