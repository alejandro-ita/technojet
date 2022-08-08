jQuery(function($) {
	init_DateRangePicker();
	initDataTable('#tbl-registro-actividades', {
		ajax: {
		 	 url: base_url('administracion/Registro_actividades/get_registro_actividades')
		 	,data: function(data) {
	    		data.startDate = $('#dateRange').data('daterangepicker').startDate.format('YYYY-MM-DD');
				data.endDate = $('#dateRange').data('daterangepicker').endDate.format('YYYY-MM-DD');
	    	}
		}
		,createdRow: function(row, data, index) {
			data.acciones = undefined;
			$(row).addClass('nk-tb-item').data(data);
		}
		,order: [[0, "desc"]]
		,columns: [
			 {data: {
			 	_: 'timestamp_custom', sort: 'timestamp'
			 }, defaultContent: '', className: 'nk-tb-col'}
			,{data: 'usuario_custom', defaultContent: '', className: 'nk-tb-col'}
			,{data: 'id_registro', defaultContent: '', className: 'nk-tb-col'}
			,{data: 'actividad', defaultContent: '', className: 'nk-tb-col'}
			,{data: 'browser', defaultContent: '', className: 'nk-tb-col'}
			,{data: 'ip', defaultContent: '', className: 'nk-tb-col'}
			,{data: 'acciones', defaultContent: '', className: 'nk-tb-col nk-tb-col-tools text-right'}
		]
	})

	.on('click', '.ver-mas', function() {
		var tr = $(this).closest('tr');
			
		var data = $.extend({}, tr.data());
		data.descripcion = JSON.parse(data.data_change);
		$.formAjaxSend({
			 url: base_url('administracion/Registro_actividades/get_modal_registro_actividad')
			,data: data
			,dataType: 'html'
			,success: function(modal) {
				$('#content-modals').html(modal);
				initModal('.modal', {
					onOpenEnd: function() {
						if ($('.modal code#before').length) {
							$('.modal #before').html(prettyPrintJSON($('.modal code#before').data('olddata')));
						}
						
						if ($('.modal code#after').length) {
							$('.modal #after').html(prettyPrintJSON($('.modal code#after').data('newdata')));
						}
					}
				});
			}
		});
	});

});

function init_DateRangePicker() {
	initDateRangePicker('#dateRange', {
		opens: 'left',
		minYear: 2022,
    	maxYear: parseInt(moment().format('YYYY'), 10),
    	maxDate: moment().format(get_config('momentJSdateFormat'))
	});
}

var library = {
	json: {
		replacer: function(match, pIndent, pKey, pVal, pEnd) {
			var key = '<span class=json-key>';
			var val = '<span class=json-value>';
	      	var str = '<span class=json-string>';
	      	var r = pIndent || '';
	      	if (pKey)
	         	r = r + key + pKey.replace(/[": ]/g, '') + '</span>: ';
	      	if (pVal)
	         	r = r + (pVal[0] == '"' ? str : val) + pVal + '</span>';
	      	return r + (pEnd || '');
	    },
		prettyPrint: function(obj) {
	    	var jsonLine = /^( *)("[\w]+": )?("[^"]*"|[\w.+-]*)?([,[{])?$/mg;
	      	return JSON.stringify(obj, null, 3)
	        	.replace(/&/g, '&amp;').replace(/\\"/g, '&quot;')
	        	.replace(/</g, '&lt;').replace(/>/g, '&gt;')
	        	.replace(jsonLine, library.json.replacer);
	    }
	}
};