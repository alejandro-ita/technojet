jQuery(function($) {

	initDataTable('#tbl-complementos', {
		
		ajax: {
			url: base_url('ventas/ventas/get_complementos'),
			data: function(dataFilter) {
				
	    		//dataFilter.id_uso = $('select#id_uso').val();
	    		//dataFilter.id_categoria = $('select#id_categoria').val();
	    	}
		},
		createdRow: function(row, data, index) {
			data.acciones = undefined;
			$(row).addClass('nk-tb-item').data(data);
		},
		columns: [
			{data: {
				_: 'folio', sort: 'id_complemento'
		   	}, defaultContent: '', className: 'nk-tb-col'},
			{data: 'estatus_complemento', defaultContent: '', className: 'nk-tb-col'},
			{data: 'fecha_complemento', defaultContent: '', className: 'nk-tb-col'},
			{data: 'cliente', defaultContent: '', className: 'nk-tb-col'},
			{data: 'factura', defaultContent: '', className: 'nk-tb-col'},
			{data: 'estatus_factura', defaultContent: '', className: 'nk-tb-col'},
			{data: 'fecha_factura', defaultContent: '', className: 'nk-tb-col'},
			{data: 'fecha_pago', defaultContent: '', className: 'nk-tb-col'},
			{data: 'importe_pago', defaultContent: '', className: 'nk-tb-col'},
			{data: 'importe_restante', defaultContent: '', className: 'nk-tb-col'},
			{data: 'moneda', defaultContent: '', className: 'nk-tb-col'},
			{data: 'num_parcialidad', defaultContent: '', className: 'nk-tb-col'},
			{data: 'observaciones', defaultContent: '', className: 'nk-tb-col'},
			{data: 'semana', defaultContent: '', className: 'nk-tb-col'},
			{data: 'mes', defaultContent: '', className: 'nk-tb-col'},
			{data: 'anio', defaultContent: '', className: 'nk-tb-col'},
			{data: 'acciones', defaultContent: '', className: 'nk-tb-col nk-tb-col-tools text-right'},
		]
	});

	$('body')

	.on('keyup', '.tools-tbl-complementos #buscar', function(e) {
		IS.init.dataTable['tbl-complementos'].search(this.value).draw();
	})

	.on('click', '.tools-tbl-complementos .complementos_length a', function(e) {
		$(this).closest('ul').find('li').removeClass('active');
		$(this).parent('li').addClass('active');

		IS.init.dataTable['tbl-complementos'].page.len($(this).data('length')).draw();

		e.preventDefault();
	})

	.on('click' , '.tools-tbl-complementos .add-complemento', function(e) {
		$(this).tooltip('hide');
		$('#form-filtro').formAjaxSend({
			url: base_url('ventas/ventas/get_modal_add_complementos_pago'),
			dataType: 'html',
			success: function(modal) {
				$('#content-modals').html(modal);
				initModal('#modal-nuevo-complemento', {
					onOpenEnd: function() {
						initSelect2('.modal select');
						$('#modal-nuevo-complemento form').validate();
					}
				});
			}
		});
		e.preventDefault();
	})

	.on('click', '#btn-save-complemento', function(e) {
		if ($('#modal-nuevo-complemento form').valid()) {
			$('#modal-nuevo-complemento form').formAjaxSend({
				url: base_url('ventas/ventas/process_save_complemento'),
				data: {
					//DATA EXTRA
				},
				success: function(response) {
					if(response.success) {
						$('.modal.show').modal('hide');
						//gotoLink(base_url(response.file_path));
						ISToast.fire({icon: response.icon, title: response.msg, customClass: response.icon});
						IS.init.dataTable['tbl-complementos'].ajax.reload(function(jsonData) {
							IS.init.dataTable['tbl-complementos'].columns.adjust().draw();
						});

					} else ISswal.fire({icon: response.icon, title: response.title, text: response.msg, customClass: response.icon});
				}
			})
		}
	})

	.on('click', '#tbl-complementos button#open-modal-update', function(e) {
		$(this).tooltip('hide');
		var tr = $(this).closest('tr');
		$.formAjaxSend({
			url: base_url('ventas/ventas/get_modal_edit_complemento'),
			data: tr.data(),
			dataType: 'html',
			success: function(modal) {
				$('#content-modals').html(modal);
				initModal('#modal-editar-complemento', {
					onOpenEnd: function() {
						initSelect2('.modal select');
						$('#modal-editar-complemento form').validate();
					}
				});
			}
		});
		e.preventDefault();
	})

	.on('click', '#btn-update-complemento', function(e) {
		if ($('#modal-editar-complemento form').valid()) {
			$('#modal-editar-complemento form').formAjaxSend({
				url: base_url('ventas/ventas/process_update_complemento'),
				data: {
					//DATOS EXTRAS
				},
				success: function(response) {
					if(response.success) {
						$('.modal.show').modal('hide');
						console.log(response);
						//gotoLink(base_url(response.file_path));
						ISToast.fire({icon: response.icon, title: response.msg, customClass: response.icon});
						IS.init.dataTable['tbl-complementos'].ajax.reload(function(jsonData) {
						    IS.init.dataTable['tbl-complementos'].columns.adjust().draw();
						});
					} else ISswal.fire({icon: response.icon, title: response.title, text: response.msg, customClass: response.icon});
				}
			})
		}
	})

	.on('click', '#tbl-complementos button#remove', function(e) {
		$(this).tooltip('hide');
		var tr = $(this).closest('tr');
		ISswal.fire({
		    title: lang('general_esta_seguro'),
		    text: lang('general_remove_row'),
		    icon: 'warning',
			showCancelButton: true,
		    confirmButtonText: lang('general_si_hazlo')
		}).then(function(result) {
		    if (result.value) {
		    	var data = tr.data();
		    	//data.id_uso = $('#id_uso').val();
		    	$.formAjaxSend({
		    		url: base_url('ventas/ventas/process_remove_complemento'),
		    		data: data,
		    		success: function(response) {
		    			if(response.success) {
							ISToast.fire({icon: response.icon, title: response.msg, customClass: response.icon});
							IS.init.dataTable['tbl-complementos'].ajax.reload();

						} else ISswal.fire({icon: response.icon, title: response.title, text: response.msg, customClass: response.icon});
		    		}
		    	});
		    }
		});
	})
	
});