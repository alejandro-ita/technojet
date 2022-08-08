(function ( $ ) {
	$.fn.extend({
		/**
		 * Función principal para realizar el envió de los datos via ajax a partir de un formulario
		 */
		formAjaxSend: function(options) {
			options 		= options || {};
			var dataExtra 	= options.data || {};
			options.data 	= undefined;
			
			//DATOS QUE SE OBTIENE DEL FORMULARIO
			var inputNotserializeObject = ':not(.encript-md5):not(.send-ignore):not([type=password])';
			var $form 		= $(this);
			var form_url 	= $form.prop('action'),
				formData 	= (typeof this == 'object') ? $form.find(inputNotserializeObject).serializeObject() : {},
				btnSubmit 	= (typeof this == 'object') ? $form.find('[type="submit"], .submit') : $('');

			if(typeof this == 'object') {
				//Se busca las contraseñas a encriptar
				$(this).find('.encript-md5, [type=password]').each(function(key, el) {
					formData[$(this).prop('name') || 'input'+key] = $.md5($(this).val());
				});
			}

			var settings = {
		         url: form_url					//ruta a enviar los datos
		        ,method: 'post'					//forma de enviar los datos del formulario.
		        ,data: formData 				//datos a enviar ya sea un objeto o array, si no se recibe trata de obtener del form. example {data1: valor1, data2: valor2}
				,dataType: 'json' 				//dataType tipo de datos que se espera recibir del lado del servidor. xml | json | script | html
				,async: true
				,cache: true
				,contentType: 'application/x-www-form-urlencoded; charset=UTF-8'
				,processData: true
        		,blockScreen: true
        		,rmBSonComplete: true			//ocultar el loading en la pantalla
        		,isPromise: (typeof this == 'object')
				,beforeSend: function(jqXHR) {} //función a realizar antes de procesar la petición al servidor
				,success: function(response) {} //script a ejecutar despues de procesar la petición al servidor
				,error: function(jqXHR) {} 		//función a realizar al generar un error en la petición al servidor
				,complete: function(jqXHR) {} 	//función a realizar al completar la petición al servidor
		    };
		    
		    $.extend(true, settings, options, {data: dataExtra});

			//SI NO SE HA DEFINIDO UNA RUTA DEL EVENTO AJAX, SE MANDA UNA ALERTA.
			if (!settings.url) {
				ISswal.fire(lang('general_error'), lang('general_route_not_defined'), 'error');
				return false;
			}

			var promesa = $.ajax({
				url: settings.url
				,method: settings.method
				,data: settings.data
				,dataType: settings.dataType
				,async: settings.async
				,cache: settings.cache
				,contentType: settings.contentType
				,processData: settings.processData
				,beforeSend: function(jqXHR, obj) {
					settings.blockScreen && $('body>.content-preloader').fadeIn('slow');

					//DESACTIVAMOS EL BOTÓN DEL SUBMIT
					btnSubmit.elDisable();
					settings.beforeSend(jqXHR, obj);
				}
				,success: function(response, textStatus, jqXHR) {
					settings.success(response);
				}
				,error: function(jqXHR, textStatus, errorThrown) {
					// console.log(jqXHR);				
					settings.error(jqXHR);
				}
				,statusCode: {
				    0: function() {
				    	ISswal.fire('info', lang('error_0_mensaje'), 'error');
				    },
				    301: function() { //Moved Permanently
				    	ISswal.fire(lang('general_error'), lang('error_301_mensaje'), 'error');
				    },
				    400: function() { //Bad Request
				    	ISswal.fire(lang('general_error'), lang('error_400_mensaje'), 'error');
				    },
		            401: function() { //Unauthorized
		            	ISswal.fire({
						  	 text: lang('error_401_mensaje')
						  	,type: 'info'
						  	,button: lang('general_acept')
				 		}).then(function() {
	 						location.reload();
				 		});
		            },
				    404: function() { //Not Found
				      ISswal.fire(lang('general_error'), lang('error_404_mensaje'), 'error');
				    },
				    500: function() { //Internal Server Error
				      ISswal.fire(lang('general_error'), lang('error_500_mensaje'), 'error');
				    }
				}
				,complete: function(jqXHR) {
					if (settings.blockScreen && settings.rmBSonComplete) 
						$('body>.content-preloader').fadeOut('slow');

					//ACTIVAMOS EL BOTÓN DEL SUBMIT
					btnSubmit.elEnable();
					settings.complete(jqXHR);

				}
			});

			return settings.isPromise ? promesa : this;
		}

		/**
		 * jQuery serializeObject
		 * @copyright 2014, David G. Hong <davidhong.code@gmail.com>
		 * @link https://github.com/hongymagic/jQuery.serializeObject
		 * @license MIT
		 * @version 2.0.3
		 */
		,serializeObject: function() {
			"use strict";
			var a = {};
			$.each(this.serializeArray(), function(b, c) {
				var d = a[c.name];
				"undefined"!= typeof d&&d!==null ? $.isArray(d) ? d.push(c.value) : a[c.name] = [d,c.value] : a[c.name] = c.value

			});

			//BY IS, PARA LOS INPUT SELECT[MULTIPLE] VALOR SEPARARLO POR (,)
			var form = this;
			$.each(a, function(key, val) {
				if (form.find('[name="'+key+'"]').is('select[multiple]') && $.isArray(a[key])) a[key] =  a[key].join(',');
			});

			return a;
		}
		,elDisable: function() {
			return $(this).addClass('disabled').prop('disabled', true).attr('disabled', 'disabled');
		}
		,elEnable: function() {
			return $(this).removeClass('disabled').prop('disabled', false).removeAttr('disabled');
		}
		/**
		 * Aplicacion de animación a un elemento
		 * Inspirado en https://github.com/daneden/animate.css
		 * @param  Object   Opciones para la animación
		 * @example
		 * $('el').animateCSS(animationName, callbackComplete);
		 * OR
		 * $('el').animateCSS({
		 * 		 animation: 'bounce'
		 * 		,animationStart: function() {}
		 * 		,animationEnd: function() {}
		 * });
		 *
		 */
		,animateCSS: function(options) {
			if ('string' == typeof options) {
				options = {
					 animation: arguments[0]
					,animationEnd: arguments[1] || function() {}
				}
			}

			var settings = {
				 el: this
				,animation: 'bounce'
				,animationStart: function() {}
				,animationEnd: function() {}
			}

			$.extend(settings, options);
	        var element = '.this-apply-animation';
	        settings.el.addClass(element.replace('.', ''));

		    const node = document.querySelector(element);
		    node.classList.add('animated', settings.animation);
			
		    function handleAnimationStart() {
		        node.removeEventListener('webkitAnimationEnd', handleAnimationStart); //para Chrome, Safari y Opera
		        node.removeEventListener('animationend', handleAnimationStart);

		        settings.animationStart();
		    }

		    function handleAnimationEnd() {
		        node.classList.remove('animated', 'this-apply-animation', settings.animation);
		        node.removeEventListener('webkitAnimationEnd', handleAnimationEnd); //para Chrome, Safari y Opera
		        node.removeEventListener('animationend', handleAnimationEnd);

		        settings.animationEnd();
		    }

		    //EVENTO AL INICIAR LA ANIMACIÓN
    		node.addEventListener("webkitAnimationStart", handleAnimationStart); //para Chrome, Safari y Opera
    		node.addEventListener('animationstart', handleAnimationStart);

			//EVENTO AL FINALIZAR LA ANIMACIÓN
    		node.addEventListener("webkitAnimationEnd", handleAnimationEnd); //para Chrome, Safari y Opera
    		node.addEventListener('animationend', handleAnimationEnd);
			
			return this;
		}
	});
	
	//SHORT ACCESS FUNCTION
	$.formAjaxSend = $.fn.formAjaxSend;
	$.loadContentData = $.fn.loadContentData;
	$.updateInlineFields = $.fn.updateInlineFields;
}(jQuery));
