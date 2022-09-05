jQuery(function($) {
	initDataTable('#tbl-clientes', {
		ajax: {
		 	url: base_url('database/clientes/get_clientes')
		},
		createdRow: function(row, data, index) {
			data.acciones = undefined;
			$(row).addClass('nk-tb-item').data(data);
		},
		columns: [
			{data: 'id_cliente', defaultContent: '', className: 'nk-tb-col'},
			{data: 'cliente', defaultContent: '', className: 'nk-tb-col'},
            {data: 'razon_social', defaultContent: '', className: 'nk-tb-col'},
            {data: 'rfc', defaultContent: '', className: 'nk-tb-col'},
			{data: 'direccion', defaultContent: '', className: 'nk-tb-col'},
			{data: 'municipio', defaultContent: '', className: 'nk-tb-col'},
			{data: 'estado', defaultContent: '', className: 'nk-tb-col'},
			{data: 'telefono', defaultContent: '', className: 'nk-tb-col'},
			{data: 'cp', defaultContent: '', className: 'nk-tb-col'},
			{data: 'contacto', defaultContent: '', className: 'nk-tb-col'},
			{data: 'departamento', defaultContent: '', className: 'nk-tb-col'},
			{data: 'acciones', defaultContent: '', className: 'nk-tb-col nk-tb-col-tools text-right'}
		]
	});

    $('body')

    .on('click', '.tools-tbl-clientes .add-cliente', function(e) {
        $.formAjaxSend({
			 url: base_url('database/clientes/get_modal_new_cliente')
			,dataType: 'html'
			,success: function(modal) {
				$('#content-modals').html(modal);
				initModal('.modal', {
					onOpenEnd: function() {
						$('.modal#new-cliente form').validate();
					}
				});
			}
		});
		e.preventDefault();
	})

    .on('click', '#content-modals #btn-save-cliente', function(e) {
		if ($('.modal#new-cliente form').valid()) {
			$('.modal#new-cliente form').formAjaxSend({
				url: base_url('database/clientes/process_save_cliente'),
				success: function(response) {
					if(response.success) {
						$('.modal.show').modal('hide');
						ISToast.fire({icon: response.icon, title: response.msg, customClass: response.icon});
						IS.init.dataTable['tbl-clientes'].ajax.reload();

					} else ISswal.fire({icon: response.icon, title: response.title, text: response.msg, customClass: response.icon});
				}
			})
		}
	})

    .on('click', '#tbl-clientes #open-modal-update', function(e) {
		var tr = $(this).closest('tr');
		var btn= $(this);
		
		btn.tooltip('hide');
		tr.addClass('selected');
		$.formAjaxSend({
			url: base_url('database/clientes/get_modal_update_cliente'),
			data: tr.data(),
			dataType: 'html',
			success: function(modal) {
				$('#content-modals').html(modal);
				initModal('.modal', {
					onOpenEnd: function() {
						$('.modal#update-cliente form').validate();
					},
					onCloseEnd: function() {
						tr.removeClass('selected');
					}
				});
			}
		})
		e.preventDefault();
	})

    .on('click', '#content-modals #btn-update-cliente', function(e) {
		if ($('.modal#update-cliente form').valid()) {
			var tr = IS.init.dataTable['tbl-clientes'].$('tr.selected');
			$('.modal#update-cliente form').formAjaxSend({
				url: base_url('database/clientes/process_update_cliente'),
				data: {
					id_cliente: tr.data('id_cliente'),
				},
				success: function(response) {
					if(response.success) {
						$('.modal.show').modal('hide');
						ISToast.fire({icon: response.icon, title: response.msg, customClass: response.icon});
						IS.init.dataTable['tbl-clientes'].ajax.reload();

					} else ISswal.fire({icon: response.icon, title: response.title, text: response.msg, customClass: response.icon});
				}
			})
		}
	})

    .on('click', '#tbl-clientes #remove', function(e) {
		var tr = $(this).closest('tr');
		var btn= $(this);
		
		btn.tooltip('hide');
		ISswal.fire({
		    title: lang('general_esta_seguro'),
		    text: lang('general_remove_row'),
		    icon: 'warning',
			showCancelButton: true,
		    confirmButtonText: lang('general_si_hazlo')
		}).then(function(result) {
		    if (result.value) {
				var data = tr.data()
		    	$.formAjaxSend({
		    		url: base_url('database/clientes/process_remove_cliente'),
		    		data: data,
		    		success: function(response) {
		    			if(response.success) {
							ISToast.fire({icon: response.icon, title: response.msg, customClass: response.icon});
							IS.init.dataTable['tbl-clientes'].row(tr).remove().draw();

						} else ISswal.fire({icon: response.icon, title: response.title, text: response.msg, customClass: response.icon});
		    		}
		    	});
		    }
		});
	});
});