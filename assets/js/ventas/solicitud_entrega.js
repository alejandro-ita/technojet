jQuery(function($) {

	initDataTable('#tbl-solicitudes-entrega', {
		
		ajax: {
			url: base_url('ventas/ventas/get_solicitudes_entrada'),
			data: function(dataFilter) {
				
	    		dataFilter.id_uso = $('select#id_uso').val();
	    		dataFilter.id_categoria = $('select#id_categoria').val();
	    	}
		},
		createdRow: function(row, data, index) {
			data.acciones = undefined;
			$(row).addClass('nk-tb-item').data(data);
		},
		columns: [
			{data: {
				_: 'folio', sort: 'id_solicitud'
		   	}, defaultContent: '', className: 'nk-tb-col'},
			{data: 'fecha_solicitud', defaultContent: '', className: 'nk-tb-col'},
			{data: 'estatus', defaultContent: '', className: 'nk-tb-col'},
			{data: 'departamento', defaultContent: '', className: 'nk-tb-col'},
			{data: 'tipo_envio', defaultContent: '', className: 'nk-tb-col'},
			{data: 'consignado', defaultContent: '', className: 'nk-tb-col'},
			{data: 'pi_nc_oc', defaultContent: '', className: 'nk-tb-col'},
			{data: 'paqueteria', defaultContent: '', className: 'nk-tb-col'},
			{data: 'cliente', defaultContent: '', className: 'nk-tb-col'},
			{data: 'contacto', defaultContent: '', className: 'nk-tb-col'},
			{data: 'condicion_entrega', defaultContent: '', className: 'nk-tb-col'},
			{data: 'direccion', defaultContent: '', className: 'nk-tb-col'},
			{data: 'dep_solicitante', defaultContent: '', className: 'nk-tb-col'},
			{data: 'almacen_saliente', defaultContent: '', className: 'nk-tb-col'},
			{data: 'forma_pago', defaultContent: '', className: 'nk-tb-col'},
			{data: 'acciones', defaultContent: '', className: 'nk-tb-col nk-tb-col-tools text-right'},
		]
	});

	initDataTable('#tpl-tbl-solicitud-entrega-consecutivo', {
		
		ajax: {
			url: base_url('ventas/ventas/get_solicitudes_entrada'),
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
				_: 'folio', sort: 'id_solicitud'
		   	}, defaultContent: '', className: 'nk-tb-col'},
			{data: 'estatus', defaultContent: '', className: 'nk-tb-col'},
			{data: 'fecha_solicitud', defaultContent: '', className: 'nk-tb-col'},
			{data: 'forma_envio', defaultContent: '', className: 'nk-tb-col'},
			{data: 'tipo_envio', defaultContent: '', className: 'nk-tb-col'},
			{data: 'pi_nc_oc', defaultContent: '', className: 'nk-tb-col'},
			{data: 'cliente', defaultContent: '', className: 'nk-tb-col'},
			{data: 'razon_social', defaultContent: '', className: 'nk-tb-col'},
			{data: 'lugar_entrega', defaultContent: '', className: 'nk-tb-col'},
			{data: 'contacto', defaultContent: '', className: 'nk-tb-col'},
			{data: 'departamento', defaultContent: '', className: 'nk-tb-col'},
			{data: 'consignado', defaultContent: '', className: 'nk-tb-col'},
			{data: 'paqueteria', defaultContent: '', className: 'nk-tb-col'},
			{data: 'condicion_entrega', defaultContent: '', className: 'nk-tb-col'},
			{data: 'dep_solicitante', defaultContent: '', className: 'nk-tb-col'},
			{data: 'almacen_saliente', defaultContent: '', className: 'nk-tb-col'},
			{data: 'observaciones', defaultContent: '', className: 'nk-tb-col'},
			{data: 'status', defaultContent: '', className: 'nk-tb-col'},
			{data: 'fecha_envio', defaultContent: '', className: 'nk-tb-col'},
			{data: 'fecha_entrega', defaultContent: '', className: 'nk-tb-col'},
			{data: 'semana', defaultContent: '', className: 'nk-tb-col'},
			{data: 'mes', defaultContent: '', className: 'nk-tb-col'},
			{data: 'anio', defaultContent: '', className: 'nk-tb-col'},
		]
	});

	$('body')

	.on('keyup', '.tools-tbl-solicitudes-entrega #buscar', function(e) {
		IS.init.dataTable['tbl-solicitudes-entrega'].search(this.value).draw();
	})

	.on('click', '.tools-tbl-solicitudes-entrega .solicitudes_length a', function(e) {
		$(this).closest('ul').find('li').removeClass('active');
		$(this).parent('li').addClass('active');

		IS.init.dataTable['tbl-solicitudes-entrega'].page.len($(this).data('length')).draw();

		e.preventDefault();
	})

	.on('keyup', '.tools-tbl-solicitud-consecutivo #buscar', function(e) {
		IS.init.dataTable['tpl-tbl-solicitud-entrega-consecutivo'].search(this.value).draw();
	})

	.on('click', '.tools-tbl-solicitud-consecutivo .solicitudes_length a', function(e) {
		$(this).closest('ul').find('li').removeClass('active');
		$(this).parent('li').addClass('active');

		IS.init.dataTable['tpl-tbl-solicitud-entrega-consecutivo'].page.len($(this).data('length')).draw();

		e.preventDefault();
	})

	.on('click' , '.tools-tbl-solicitudes-entrega .add-solicitud-entrega', function(e) {
		$(this).tooltip('hide');
		$('#form-filtro').formAjaxSend({
			url: base_url('ventas/ventas/get_modal_add_solicitud'),
			dataType: 'html',
			success: function(modal) {
				$('#content-modals').html(modal);
				initModal('#modal-nueva-solicitud', {
					onOpenEnd: function() {
						initSelect2('.modal select');
						$('#modal-nueva-solicitud form').validate();
						init_table_productos_add();
					}
				});
			}
		});
		e.preventDefault();
	})

	.on('click', '#modal-tbl-productos_filter .btn-add', function(e) {
		$.formAjaxSend({
			url: base_url('ventas/ventas/get_modal_add_solicitud_product'),
			dataType: 'html',
			success: function(modal) {
				$('#content-modals').append(modal);
				initModal('#modal-add-producto', {
					removeOnClose: false,
					backdrop: 'static',
					keyboard: true,
					onOpenEnd: function() {
						initSelect2('.modal select');
						$('#modal-add-producto form').validate();
					},
					onCloseEnd: function() {
						$('#modal-add-producto').remove();
					}
				});
			}
		});
	})

	.on('click', 'a[data-dismiss-modal=modal-add-producto]', function(e) {
    	$('#modal-add-producto').modal('hide');
    	e.preventDefault();
	})

	.on('change', '#modal-add-producto form #id_tipo_prod', function(e) {
		$('#modal-add-producto form #id_unidad_medida option:not(:first)').remove();
		$('#modal-add-producto form #id_producto option:not(:first)').remove();
		$.formAjaxSend({
			url: base_url('ventas/ventas/get_unidades_medida_productos'),
			data: {id_tipo_producto: $(this).val()},
			blockScreen: false,
			success: function(response) {
				var $select = $('#modal-add-producto form #id_unidad_medida');
				$.each(response, function(key, um) {
					var newOption = $(new Option(um['custom_unidad_medida'], um['id_unidad_medida'], false, false));
					newOption.data(um);
					$select.append(newOption);
				});
			}
		});
	})

	.on('change', '#modal-add-producto form #id_unidad_medida', function(e) {
		$('#modal-add-producto form #id_producto option:not(:first)').remove();
		$('#modal-add-producto form #descripcion').val('');
		$('#modal-add-producto form #descripcion').attr('title', '');
		$.formAjaxSend({
			 url: base_url('ventas/ventas/get_productos_por_tipo')
			,data: {
				 id_tipo_producto: $('#modal-add-producto form #id_tipo_producto').val()
				,id_unidad_medida: $(this).val()
			}
			,blockScreen: false
			,success: function(response) {
				var $select = $('#modal-add-producto form #id_producto');
				$.each(response, function(key, producto) {
					var newOption = $(new Option(producto['no_parte'], producto['id_producto'], false, false));
					newOption.data(producto);
					$select.append(newOption);
				});
			}
		});
	})

	.on('change', '#modal-add-producto form #id_producto', function(e) {
		var productoData = $('#modal-add-producto form #id_producto option:selected').data();
		$('#modal-add-producto form #descripcion').val(productoData.descripcion);
		$('#modal-add-producto form #descripcion').attr('title', productoData.descripcion);
	})

	.on('click', '#btn-add-producto', function(e) {
		if ($('#modal-add-producto form').valid()) {
			var opcional = 0;
			if( $('#opcional').is(':checked') ) {
				opcional = 1;
			}

			var productoData = $('#modal-add-producto form #id_producto option:selected').data();
			var data = $.extend({}, productoData, {
				id_tipo_producto: $('#modal-add-producto form #id_tipo_prod option:selected').val(),
				tipo_producto: $('#modal-add-producto form #id_tipo_prod option:selected').text(),
				id_unidad_medida: $('#modal-add-producto form #id_unidad_medida option:selected').val(),
				unidad_medida: $('#modal-add-producto form #id_unidad_medida option:selected').text(),
			 	cantidad: $('#modal-add-producto form #cantidad').val(),
			});

			var table 	= IS.init.dataTable['modal-tbl-productos'];
			table.row.add(data).draw()
			$('#modal-add-producto').modal('hide');
		}
		e.preventDefault();
	})

	.on('click', '#btn-save-solicitud', function(e) {
		if ($('#modal-nueva-solicitud form').valid()) {
			var table = IS.init.dataTable['modal-tbl-productos']
			var rows = table.rows().data();
			if(rows.length) {
				var productos = [];
				table.$('tr').each(function(key, tr) {
					productos.push($(tr).data());
				});

				$('#modal-nueva-solicitud form').formAjaxSend({
					url: base_url('ventas/ventas/process_save_solicitud_entrega'),
					data: {
						productos: productos
					},
					success: function(response) {
						if(response.success) {
							$('.modal.show').modal('hide');
							//gotoLink(base_url(response.file_path));
							ISToast.fire({icon: response.icon, title: response.msg, customClass: response.icon});
							IS.init.dataTable['tbl-solicitudes-entrega'].ajax.reload(function(jsonData) {
							    IS.init.dataTable['tbl-solicitudes-entrega'].columns.adjust().draw();
							});

						} else ISswal.fire({icon: response.icon, title: response.title, text: response.msg, customClass: response.icon});
					}
				})

			} else ISswal.fire({icon: 'info', title: '¡Información!', text: 'Agregue al menos un producto', customClass: 'info'});
		}
	})

	.on('click', '#tbl-solicitudes-entrega button#open-modal-update', function(e) {
		$(this).tooltip('hide');
		var tr = $(this).closest('tr');
		$.formAjaxSend({
			url: base_url('ventas/ventas/get_modal_edit_solicitud_entrega'),
			data: tr.data(),
			dataType: 'html',
			success: function(modal) {
				$('#content-modals').html(modal);
				initModal('#modal-editar-solicitud', {
					onOpenEnd: function() {
						initSelect2('.modal select');
						$('#modal-editar-solicitud form').validate();
						init_table_productos_add(listProductos);
					}
				});
			}
		});
		e.preventDefault();
	})

	.on('click', '#btn-update-solicitud', function(e) {
		if ($('#modal-editar-solicitud form').valid()) {
			var table = IS.init.dataTable['modal-tbl-productos']
			var rows = table.rows().data();
			if(rows.length) {
				var productos = [];
				table.$('tr').each(function(key, tr) {
					productos.push($(tr).data());
				});

				$('#modal-editar-solicitud form').formAjaxSend({
					url: base_url('ventas/ventas/process_update_solicitud_entrega'),
					data: {
						productos: productos
					},
					success: function(response) {
						if(response.success) {
							$('.modal.show').modal('hide');
							//gotoLink(base_url(response.file_path));
							ISToast.fire({icon: response.icon, title: response.msg, customClass: response.icon});
							IS.init.dataTable['tbl-solicitudes-entrega'].ajax.reload(function(jsonData) {
							    IS.init.dataTable['tbl-solicitudes-entrega'].columns.adjust().draw();
							});

						} else ISswal.fire({icon: response.icon, title: response.title, text: response.msg, customClass: response.icon});
					}
				})

			} else ISswal.fire({icon: 'info', title: '¡Información!', text: 'Agregue al menos un producto', customClass: 'info'});
		}
	})

	.on('click', '#modal-nueva-solicitud #modal-tbl-productos .btn-remove, #modal-editar-solicitud #modal-tbl-productos .btn-remove', function(e) {
		var tr = $(this).closest('tr');
		IS.init.dataTable['modal-tbl-productos'].row(tr).remove().draw();
	})

	.on('click', '#tbl-solicitudes-entrega button#remove', function(e) {
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
		    		url: base_url('ventas/ventas/process_remove_solicitud_entrega'),
		    		data: data,
		    		success: function(response) {
		    			if(response.success) {
							ISToast.fire({icon: response.icon, title: response.msg, customClass: response.icon});
							IS.init.dataTable['tbl-solicitudes-entrega'].ajax.reload();

						} else ISswal.fire({icon: response.icon, title: response.title, text: response.msg, customClass: response.icon});
		    		}
		    	});
		    }
		});
	})

	.on('click', '#tbl-solicitudes-entrega button#build-pdf', function(e) {
		$(this).tooltip('hide');
		var tr = $(this).closest('tr');
		
    	$.formAjaxSend({
    		url: base_url('ventas/ventas/process_build_pdf_solicitud_entrega'),
    		data: tr.data(),
    		success: function(response) {
    			if(response.success) {
					gotoLink(base_url(response.file_path));

				} else ISswal.fire({icon: response.icon, title: response.title, text: response.msg, customClass: response.icon});
    		}
    	});
	})

	function init_table_productos_add(data) {
		var tblData = data || [];
		initDataTable('.modal #modal-tbl-productos', {
			dom: '<"row justify-between g-2" <"col-sm-12 d-flex my-1 justify-content-end" f> ><"datatable-wrap my-1"t>',
			pageLength: 100,
			data: tblData,
			createdRow: function(row, data, index) {
				data.acciones = undefined;
				$(row).addClass('nk-tb-item').data(data);
			},
			columns: [
				{data: 'tipo_producto', defaultContent: '', className: 'nk-tb-col'},
				{data: 'unidad_medida', defaultContent: '', className: 'nk-tb-col'},
				{data: 'no_parte', defaultContent: '', className: 'nk-tb-col'},
				{data: 'descripcion', defaultContent: '', className: 'nk-tb-col'},
				{data: 'cantidad', defaultContent: '', className: 'nk-tb-col'},
				{data: function() {
					return `<button class="btn btn-dim btn-sm text-danger btn-remove py-0 px-1" title="${lang('general_quitar')}">
					        	<em class="icon ni ni-trash"></em>
					        </button>`;
				}, defaultContent: '', className: 'nk-tb-col text-center'}
			]
		});
	}

});