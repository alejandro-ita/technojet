jQuery(function($) {
	initDataTable('#tbl-ventas-pi-facturas', {
		ajax: {
		 	url: base_url('database/ventas/get_catalog_pedidos_internos_factura'),
		 	data: function(dataFilter) {
	    		dataFilter.id_categoria = $('select#id_categoria').val();
	    	}
		},
		createdRow: function(row, data, index) {
			data.acciones = undefined;
			$(row).addClass('nk-tb-item').data(data);
		},
		columns: [
			{data: 'id_ventas_cotizacion', defaultContent: '', className: 'nk-tb-col'},
			{data: 'categoria', defaultContent: '', className: 'nk-tb-col'},
			{data: 'c_cotizacion', defaultContent: '', className: 'nk-tb-col'},
			{data: 'acciones', defaultContent: '', className: 'nk-tb-col nk-tb-col-tools text-right'}
		]
	});

	$('body')

	.on('keyup', '.tools-tbl-pedidos-internos-factura #buscar', function(e) {
		IS.init.dataTable['tbl-ventas-pi-facturas'].search(this.value).draw();
	})

	.on('change', '.tools-tbl-pedidos-internos-factura select#id_categoria_factura', function() {
		IS.init.dataTable['tbl-ventas-pi-facturas'].ajax.reload();
		(parseInt($(this).val())>0)
			? $('.tools-tbl-pedidos-internos-factura .add-c-factura').elEnable()
			: $('.tools-tbl-pedidos-internos-factura .add-c-factura').elDisable();
	})

	.on('click', '.tools-tbl-pedidos-internos-factura .pedidos_internos_length a', function(e) {
		$(this).closest('ul').find('li').removeClass('active');
		$(this).parent('li').addClass('active');

		IS.init.dataTable['tbl-ventas-pi-facturas'].page.len($(this).data('length')).draw();

		e.preventDefault();
	})

	.on('click', '.tools-tbl-pedidos-internos-factura .add-c-factura', function(e) {
		$.formAjaxSend({
			 url: base_url('database/ventas/get_modal_new_pi')
			,dataType: 'html'
			,success: function(modal) {
				$('#content-modals').html(modal);
				initModal('.modal', {
					onOpenEnd: function() {
						$('.modal#new-pi form').validate();
					}
				});
			}
		});
		e.preventDefault();
	})

	.on('click', '#tbl-ventas-pi-facturas #open-modal-update', function(e) {
		var tr = $(this).closest('tr');
		var btn= $(this);
		
		btn.tooltip('hide');
		tr.addClass('selected');
		$.formAjaxSend({
			url: base_url('database/ventas/get_modal_update_pi'),
			data: tr.data(),
			dataType: 'html',
			success: function(modal) {
				$('#content-modals').html(modal);
				initModal('.modal', {
					onOpenEnd: function() {
						$('.modal#update-requisicion form').validate();
					},
					onCloseEnd: function() {
						tr.removeClass('selected');
					}
				});
			}
		})
		e.preventDefault();
	})

	.on('click', '#content-modals #btn-save-pi', function(e) {
		if ($('.modal#new-pi form').valid()) {
			$('.modal#new-pi form').formAjaxSend({
				url: base_url('database/ventas/process_save_c_cotizacion'),
				data: {
					id_categoria: $('select#id_categoria_factura').val(),
					categoria: $('select#id_categoria_factura option:selected').text()
				},
				success: function(response) {
					if(response.success) {
						$('.modal.show').modal('hide');
						ISToast.fire({icon: response.icon, title: response.msg, customClass: response.icon});
						IS.init.dataTable['tbl-ventas-pi-facturas'].ajax.reload();

					} else ISswal.fire({icon: response.icon, title: response.title, text: response.msg, customClass: response.icon});
				}
			})
		}
	})

	.on('click', '#content-modals #btn-update-pi', function(e) {
		if ($('.modal#update-pi form').valid()) {
			var tr = IS.init.dataTable['tbl-ventas-pi-facturas'].$('tr.selected');
			$('.modal#update-pi form').formAjaxSend({
				 url: base_url('database/ventas/process_update_c_cotizacion')
				,data: {
					id_ventas_cotizacion: tr.data('id_ventas_cotizacion'),
					id_categoria: $('select#id_categoria').val(),
					categoria: $('select#id_categoria option:selected').text()
				}
				,success: function(response) {
					if(response.success) {
						$('.modal.show').modal('hide');
						ISToast.fire({icon: response.icon, title: response.msg, customClass: response.icon});
						IS.init.dataTable['tbl-ventas-pi-facturas'].ajax.reload();

					} else ISswal.fire({icon: response.icon, title: response.title, text: response.msg, customClass: response.icon});
				}
			})
		}
	})

	.on('click', '#tbl-ventas-pi-facturas #remove', function(e) {
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
				data.categoria = $('select#id_categoria option:selected').text();
		    	$.formAjaxSend({
		    		url: base_url('database/ventas/process_remove_c_cotizacion'),
		    		data: data,
		    		success: function(response) {
		    			if(response.success) {
							ISToast.fire({icon: response.icon, title: response.msg, customClass: response.icon});
							IS.init.dataTable['tbl-ventas-pedidos-internos'].row(tr).remove().draw();

						} else ISswal.fire({icon: response.icon, title: response.title, text: response.msg, customClass: response.icon});
		    		}
		    	});
		    }
		});
	});
});