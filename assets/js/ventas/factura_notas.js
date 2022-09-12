jQuery(function($) {

	initDataTable('#tbl-pi-mostrador', {
		
		ajax: {
			url: base_url('ventas/ventas/get_nc_facturas'),
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
				_: 'folio', sort: 'id_nc_factura'
		   	}, defaultContent: '', className: 'nk-tb-col'},
			{data: 'fecha_pi', defaultContent: '', className: 'nk-tb-col'},
			{data: 'estatus_pi', defaultContent: '', className: 'nk-tb-col'},
			{data: 'cliente', defaultContent: '', className: 'nk-tb-col'},
			{data: 'contacto', defaultContent: '', className: 'nk-tb-col'},
			{data: 'departamento', defaultContent: '', className: 'nk-tb-col'},
			{data: 'uso_cfdi', defaultContent: '', className: 'nk-tb-col'},
			{data: 'forma_pago', defaultContent: '', className: 'nk-tb-col'},
			{data: 'metodo_pago', defaultContent: '', className: 'nk-tb-col'},
			{data: 'fact_remision', defaultContent: '', className: 'nk-tb-col'},
			{data: 'medio', defaultContent: '', className: 'nk-tb-col'},
			{data: 'tipo_producto', defaultContent: '', className: 'nk-tb-col'},
			{data: 'oc', defaultContent: '', className: 'nk-tb-col'},
			{data: 'tipo_entrega', defaultContent: '', className: 'nk-tb-col'},
			{data: 'forma_envio', defaultContent: '', className: 'nk-tb-col'},
			{data: 'motivo_credito', defaultContent: '', className: 'nk-tb-col'},
			{data: 'condiciones', defaultContent: '', className: 'nk-tb-col'},
			{data: 'notas_internas', defaultContent: '', className: 'nk-tb-col'},
			{data: 'notas_facturacion', defaultContent: '', className: 'nk-tb-col'},
			{data: 'tipo_cambio', defaultContent: '', className: 'nk-tb-col'},
			{data: 'email_factura', defaultContent: '', className: 'nk-tb-col'},
			{data: 'acciones', defaultContent: '', className: 'nk-tb-col nk-tb-col-tools text-right'},
		]
	});

	initDataTable('#tbl-nc-factura-consecutivo', {
		
		ajax: {
			url: base_url('ventas/ventas/get_nc_facturas'),
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
				_: 'folio', sort: 'id_nc_factura'
		   	}, defaultContent: '', className: 'nk-tb-col'},
			{data: 'estatus_pi', defaultContent: '', className: 'nk-tb-col'},
			{data: 'fecha_pi', defaultContent: '', className: 'nk-tb-col'},
			{data: 'nota_credito_cfdi', defaultContent: '', className: 'nk-tb-col'},
			{data: 'estatus_nc_cfdi', defaultContent: '', className: 'nk-tb-col'},
			{data: 'fecha_nc_cfdi', defaultContent: '', className: 'nk-tb-col'},
			{data: 'uso_cfdi', defaultContent: '', className: 'nk-tb-col'},
			{data: 'forma_pago', defaultContent: '', className: 'nk-tb-col'},
			{data: 'metodo_pago', defaultContent: '', className: 'nk-tb-col'},
			{data: 'cliente', defaultContent: '', className: 'nk-tb-col'},
			{data: 'razon_social', defaultContent: '', className: 'nk-tb-col'},
			{data: 'contacto', defaultContent: '', className: 'nk-tb-col'},
			{data: 'localidad', defaultContent: '', className: 'nk-tb-col'},
			{data: 'departamento', defaultContent: '', className: 'nk-tb-col'},
			{data: 'fact_remision', defaultContent: '', className: 'nk-tb-col'},
			{data: 'medio', defaultContent: '', className: 'nk-tb-col'},
			{data: 'tipo_producto', defaultContent: '', className: 'nk-tb-col'},
			{data: 'tipo_servicio', defaultContent: '', className: 'nk-tb-col'},
			{data: 'num_poliza', defaultContent: '', className: 'nk-tb-col'},
			{data: 'oc', defaultContent: '', className: 'nk-tb-col'},
			{data: 'fecha_entrega', defaultContent: '', className: 'nk-tb-col'},
			{data: 'tipo_entrega', defaultContent: '', className: 'nk-tb-col'},
			{data: 'forma_envio', defaultContent: '', className: 'nk-tb-col'},
			{data: 'fecha_envio', defaultContent: '', className: 'nk-tb-col'},
			{data: 'remision', defaultContent: '', className: 'nk-tb-col'},
			{data: 'guia', defaultContent: '', className: 'nk-tb-col'},
			{data: 'req', defaultContent: '', className: 'nk-tb-col'},
			{data: 'vale_salida', defaultContent: '', className: 'nk-tb-col'},
			{data: 'estatus', defaultContent: '', className: 'nk-tb-col'},
			{data: 'sub', defaultContent: '', className: 'nk-tb-col'},
			{data: 'desc', defaultContent: '', className: 'nk-tb-col'},
			{data: 'tot', defaultContent: '', className: 'nk-tb-col'},
			{data: 'moneda', defaultContent: '', className: 'nk-tb-col'},
			{data: 'motivo_credito', defaultContent: '', className: 'nk-tb-col'},
			{data: 'notas_internas', defaultContent: '', className: 'nk-tb-col'},
			{data: 'notas_facturacion', defaultContent: '', className: 'nk-tb-col'},
			{data: 'tipo_cambio', defaultContent: '', className: 'nk-tb-col'},	
			{data: 'condiciones', defaultContent: '', className: 'nk-tb-col'},
			{data: 'email_factura', defaultContent: '', className: 'nk-tb-col'},
			{data: 'semana', defaultContent: '', className: 'nk-tb-col'},
			{data: 'mes', defaultContent: '', className: 'nk-tb-col'},
			{data: 'anio', defaultContent: '', className: 'nk-tb-col'},
			{data: 'solvente', defaultContent: '', className: 'nk-tb-col'},
			{data: 'tinta', defaultContent: '', className: 'nk-tb-col'},
			{data: 'solucion_limpieza', defaultContent: '', className: 'nk-tb-col'},
			{data: 'cartucho_solvente', defaultContent: '', className: 'nk-tb-col'},
			{data: 'cartucho_tinta', defaultContent: '', className: 'nk-tb-col'},
			{data: 'ribbon', defaultContent: '', className: 'nk-tb-col'},
			{data: 'aditivos', defaultContent: '', className: 'nk-tb-col'},
			{data: 'etiqueta', defaultContent: '', className: 'nk-tb-col'},
			{data: 'equipo', defaultContent: '', className: 'nk-tb-col'},
			{data: 'equipo_renta', defaultContent: '', className: 'nk-tb-col'},
			{data: 'refaccion', defaultContent: '', className: 'nk-tb-col'},
			{data: 'servicio', defaultContent: '', className: 'nk-tb-col'},
			{data: 'accesorio', defaultContent: '', className: 'nk-tb-col'},
			{data: 'otro', defaultContent: '', className: 'nk-tb-col'},
		]
	});


	$('body')

	.on('keyup', '.tools-tbl-pi #buscar', function(e) {
		IS.init.dataTable['tbl-pi-mostrador'].search(this.value).draw();
	})

	.on('click', '.tools-tbl-pi .pi_length a', function(e) {
		$(this).closest('ul').find('li').removeClass('active');
		$(this).parent('li').addClass('active');

		IS.init.dataTable['tbl-pi-mostrador'].page.len($(this).data('length')).draw();

		e.preventDefault();
	})

	.on('keyup', '.tools-tbl-nc-factura #buscar', function(e) {
		IS.init.dataTable['tbl-nc-factura-consecutivo'].search(this.value).draw();
	})

	.on('click', '.tools-tbl-nc-factura .factura_length a', function(e) {
		$(this).closest('ul').find('li').removeClass('active');
		$(this).parent('li').addClass('active');

		IS.init.dataTable['tbl-nc-factura-consecutivo'].page.len($(this).data('length')).draw();

		e.preventDefault();
	})

	.on('click' , '.tools-tbl-pi .add-pi-factura', function(e) {
		$(this).tooltip('hide');
		$('#form-filtro').formAjaxSend({
			url: base_url('ventas/ventas/get_modal_add_nota_factura'),
			dataType: 'html',
			success: function(modal) {
				$('#content-modals').html(modal);
				initModal('#modal-nuevo-pi-mostrador', {
					onOpenEnd: function() {
						initSelect2('.modal select');
						$('#modal-nuevo-pi-mostrador form').validate();
						init_table_productos_add();
					}
				});
			}
		});
		e.preventDefault();
	})

	.on('click', '#modal-tbl-productos_filter .btn-add', function(e) {
		$.formAjaxSend({
			url: base_url('ventas/ventas/get_modal_add_mostrador_product'),
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
				 id_tipo_producto: $('#modal-add-producto form #id_tipo_prod').val()
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

	.on('click', '#btn-add-producto-pi', function(e) {
		if ($('#modal-add-producto form').valid()) {
			var productoData = $('#modal-add-producto form #id_producto option:selected').data();
			var data = $.extend({}, productoData, {
				id_tipo_producto: $('#modal-add-producto form #id_tipo_prod option:selected').val(),
				tipo_producto: $('#modal-add-producto form #id_tipo_prod option:selected').text(),
				id_unidad_medida: $('#modal-add-producto form #id_unidad_medida option:selected').val(),
				unidad_medida: $('#modal-add-producto form #id_unidad_medida option:selected').text(),
			 	cantidad: $('#modal-add-producto form #cantidad').val(),
				precio_unitario: $('#modal-add-producto form #precio_unitario').val(),
				descuento_pieza: $('#modal-add-producto form #descuento_pieza').val(),
				descuento_total: $('#modal-add-producto form #descuento_total').val(),
				total: $('#modal-add-producto form #total').val(),
				//comision_vendedor: $('#modal-add-producto form #comision_vendedor').val()
			});

			var table 	= IS.init.dataTable['modal-tbl-productos'];
			table.row.add(data).draw()
			$('#modal-add-producto').modal('hide');
		}
		e.preventDefault();
	})

	.on('click', '#btn-save-pi-mostrador', function(e) {
		if ($('#modal-nuevo-pi-mostrador form').valid()) {
			var table = IS.init.dataTable['modal-tbl-productos']
			var rows = table.rows().data();
			if(rows.length) {
				var productos = [];
				table.$('tr').each(function(key, tr) {
					productos.push($(tr).data());
				});
				//console.log(productos);

				$('#modal-nuevo-pi-mostrador form').formAjaxSend({
					url: base_url('ventas/ventas/process_save_nc_factura'),
					data: {
						/*tipo_requisicion: $("#id_tipo_requisicion option:selected").text(),
						vale_entrada: $("#id_vale_entrada option:selected").text(),
						departamento_solicitante: $("#id_departamento_solicitante option:selected").text(),
						almacen_solicitante: $("#id_almacen_solicitante option:selected").text(),
						departamento_encargado_surtir: $("#id_departamento_encargado_surtir option:selected").text(),*/
						productos: productos
					},
					success: function(response) {
						if(response.success) {
							$('.modal.show').modal('hide');
							//gotoLink(base_url(response.file_path));
							ISToast.fire({icon: response.icon, title: response.msg, customClass: response.icon});
							IS.init.dataTable['tbl-pi-mostrador'].ajax.reload(function(jsonData) {
							    IS.init.dataTable['tbl-pi-mostrador'].columns.adjust().draw();
							});

						} else ISswal.fire({icon: response.icon, title: response.title, text: response.msg, customClass: response.icon});
					}
				})

			} else ISswal.fire({icon: 'info', title: '¡Información!', text: 'Agregue al menos un producto', customClass: 'info'});
		}
	})

	.on('click', '#tbl-pi-mostrador button#open-modal-update', function(e) {
		$(this).tooltip('hide');
		var tr = $(this).closest('tr');
		$.formAjaxSend({
			url: base_url('ventas/ventas/get_modal_edit_nc_factura'),
			data: tr.data(),
			dataType: 'html',
			success: function(modal) {
				$('#content-modals').html(modal);
				initModal('#modal-editar-pi-mostrador', {
					onOpenEnd: function() {
						initSelect2('.modal select');
						$('#modal-editar-pi-mostrador form').validate();
						init_table_productos_add(listProductos);
					}
				});
			}
		});
		e.preventDefault();
	})

	.on('click', '#btn-update-pi-mostrador', function(e) {
		if ($('#modal-editar-pi-mostrador form').valid()) {
			var table = IS.init.dataTable['modal-tbl-productos']
			var rows = table.rows().data();
			if(rows.length) {
				var productos = [];
				table.$('tr').each(function(key, tr) {
					productos.push($(tr).data());
				});

				$('#modal-editar-pi-mostrador form').formAjaxSend({
					url: base_url('ventas/ventas/process_update_nc_factura'),
					data: {
						/*tipo_requisicion: $("#id_tipo_requisicion option:selected").text(),
						vale_entrada: $("#id_vale_entrada option:selected").text(),
						departamento_solicitante: $("#id_departamento_solicitante option:selected").text(),
						almacen_solicitante: $("#id_almacen_solicitante option:selected").text(),
						departamento_encargado_surtir: $("#id_departamento_encargado_surtir option:selected").text(),*/
						productos: productos
					},
					success: function(response) {
						if(response.success) {
							$('.modal.show').modal('hide');
							//gotoLink(base_url(response.file_path));
							ISToast.fire({icon: response.icon, title: response.msg, customClass: response.icon});
							IS.init.dataTable['tbl-pi-mostrador'].ajax.reload(function(jsonData) {
							    IS.init.dataTable['tbl-pi-mostrador'].columns.adjust().draw();
							});

						} else ISswal.fire({icon: response.icon, title: response.title, text: response.msg, customClass: response.icon});
					}
				})

			} else ISswal.fire({icon: 'info', title: '¡Información!', text: 'Agregue al menos un producto', customClass: 'info'});
		}
	})

	.on('click', '#modal-nuevo-pi-mostrador #modal-tbl-productos .btn-remove, #modal-editar-pi-mostrador #modal-tbl-productos .btn-remove', function(e) {
		var tr = $(this).closest('tr');
		IS.init.dataTable['modal-tbl-productos'].row(tr).remove().draw();
	})

	.on('click', '#tbl-pi-mostrador button#remove', function(e) {
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
		    		url: base_url('ventas/ventas/process_remove_nc_factura'),
		    		data: data,
		    		success: function(response) {
		    			if(response.success) {
							ISToast.fire({icon: response.icon, title: response.msg, customClass: response.icon});
							IS.init.dataTable['tbl-pi-mostrador'].ajax.reload();

						} else ISswal.fire({icon: response.icon, title: response.title, text: response.msg, customClass: response.icon});
		    		}
		    	});
		    }
		});
	})

	.on('change', '#cantidad', function(e){
		if($("#precio_unitario").val() != '' && $("#descuento_pieza").val() != '' ){
			calculaTotal();
		}
	})

	.on('change', '#precio_unitario', function(e){
		if($("#cantidad").val() != '' && $("#descuento_pieza").val() != '' ){
			calculaTotal();
		}
	})

	.on('change', '#descuento_pieza', function(e){
		if($("#cantidad").val() != '' && $("#precio_unitario").val() != '' ){
			calculaTotal();
		}
	})

	.on('click', '#tbl-pi-mostrador button#build-pdf', function(e) {
		$(this).tooltip('hide');
		var tr = $(this).closest('tr');
		
    	$.formAjaxSend({
    		url: base_url('ventas/ventas/process_build_pdf_nc_factura'),
    		data: tr.data(),
    		success: function(response) {
    			if(response.success) {
					gotoLink(base_url(response.file_path));

				} else ISswal.fire({icon: response.icon, title: response.title, text: response.msg, customClass: response.icon});
    		}
    	});
	})

	function calculaTotal(){
		descuento_total = (($("#precio_unitario").val() * $("#descuento_pieza").val()) / 100) * $("#cantidad").val();
		subtotal = $("#cantidad").val() * $("#precio_unitario").val();
		total = subtotal - descuento_total;
		$("#descuento_total").val(descuento_total.toFixed(2));
		$("#total").val(total.toFixed(2));
		//console.log("sub: " + subtotal + " desc: " + descuento);
	}

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
				{data: 'precio_unitario', defaultContent: '', className: 'nk-tb-col'},
				{data: 'descuento_pieza', defaultContent: '', className: 'nk-tb-col'},
				{data: 'descuento_total', defaultContent: '', className: 'nk-tb-col'},
				{data: 'total', defaultContent: '', className: 'nk-tb-col'},
				//{data: 'comision_vendedor', defaultContent: '', className: 'nk-tb-col'},
				{data: function() {
					return `<button class="btn btn-dim btn-sm text-danger btn-remove py-0 px-1" title="${lang('general_quitar')}">
					        	<em class="icon ni ni-trash"></em>
					        </button>`;
				}, defaultContent: '', className: 'nk-tb-col text-center'}
			]
		});
	}
	

});

/*jQuery(function($) {
	$('body')
	.on('click' , '.add-factura', function(e) {
		$('#form-filtro').formAjaxSend({
			url: base_url('ventas/ventas/get_modal_add_factura_notas'),
			dataType: 'html',
			success: function(modal) {
				$('#content-modals').html(modal);
				initModal('#modal-entrada-factura-notas', {
					onOpenEnd: function() {
						initSelect2('.modal select');
						$('#modal-add-producto-entrada').validate();
						init_tbl_entrada_nuevos_productos();
					},
					onCloseEnd: function() {
						$('#modal-add-producto-entrada').remove();
						$('.modal-backdrop').remove();
					},
				});
			}
		});
		e.preventDefault();
	})
	
	.on('click', '#modal-tbl-entrada-productos_filter .btn-add', function(e) {

		$('#form-filtro').formAjaxSend({
			url: base_url('ventas/ventas/get_modal_add_factura_notas_product'),
			dataType: 'html',
			success: function(modal) {
				$('#content-modals').append(modal);
				initModal('#modal-add-producto-entrada', {
					onOpenEnd: function() {
						initSelect2('.modal select');
					},
					onCloseEnd: function() {
						$('#modal-add-producto-entrada').remove();
						$('.modal-backdrop').remove();
					},
				});
			}
		});
		e.preventDefault();
	})
	.on('click', '#close-factura-product',function(e){
		$('#modal-add-producto-entrada').remove();
	})

	

	function init_tbl_entrada_nuevos_productos(data) {
		var tblData = data || [];

		//PRODUCTOS DE ENTRADA ACTIVOS
		if ($('#id_uso').val() == '5') {
			var columns = [
				 {data: 'tipo_producto', defaultContent: '', className: 'nk-tb-col'}
				,{data: 'unidad_medida', defaultContent: '', className: 'nk-tb-col'}
				,{data: 'no_parte', defaultContent: '', className: 'nk-tb-col'}
				,{data: 'descripcion', defaultContent: '', className: 'nk-tb-col'}
				,{data: 'cantidad', defaultContent: '', className: 'nk-tb-col'}
				,{data: 'no_serie', defaultContent: '', className: 'nk-tb-col'}
				,{data: 'estado_producto', defaultContent: '', className: 'nk-tb-col'}
				,{data: 'costo', defaultContent: '', className: 'nk-tb-col'}
				,{data: 'moneda', defaultContent: '', className: 'nk-tb-col'}
				,{data: function() {
					return `<button class="btn btn-dim btn-sm text-danger btn-remove py-0 px-1" title="${lang('general_quitar')}">
					        	<em class="icon ni ni-trash"></em>
					        </button>`;
				}, defaultContent: '', className: 'nk-tb-col text-center'}
			];

		} else {
			var columns = [
				 {data: 'tipo_producto', defaultContent: '', className: 'nk-tb-col'}
				,{data: 'unidad_medida', defaultContent: '', className: 'nk-tb-col'}
				,{data: 'no_parte', defaultContent: '', className: 'nk-tb-col'}
				,{data: 'descripcion', defaultContent: '', className: 'nk-tb-col'}
				,{data: 'cantidad', defaultContent: '', className: 'nk-tb-col'}
				,{data: 'referencia_alfanumerica', defaultContent: '', className: 'nk-tb-col'}
				,{data: 'referencia_entrada', defaultContent: '', className: 'nk-tb-col'}
				,{data: function() {
					return `<button class="btn btn-dim btn-sm text-danger btn-remove py-0 px-1" title="${lang('general_quitar')}">
					        	<em class="icon ni ni-trash"></em>
					        </button>`;
				}, defaultContent: '', className: 'nk-tb-col text-center'}
			];
		}

		initDataTable('.modal #modal-tbl-entrada-productos', {
			 dom: '<"row justify-between g-2" <"col-sm-12 d-flex my-1 justify-content-end" f> ><"datatable-wrap my-1"t>'
			,pageLength: 100
			,data: tblData
			,createdRow: function(row, data, index) {
				data.acciones = undefined;
				$(row).addClass('nk-tb-item').data(data);
			}
			,columns: columns
		});
	}



});*/