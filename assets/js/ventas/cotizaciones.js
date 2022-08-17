jQuery(function($) {

	initDataTable('#tbl-cotizaciones', {
		
		ajax: {
			url: base_url('ventas/ventas/get_cotizaciones')
			,data: function(dataFilter) {
				
	    		dataFilter.id_uso = $('select#id_uso').val();
	    		dataFilter.id_categoria = $('select#id_categoria').val();
	    	}
		}
		,createdRow: function(row, data, index) {
			data.acciones = undefined;
			$(row).addClass('nk-tb-item').data(data);
		}
		,columns: [
			{data: {
				_: 'folio', sort: 'id_cotizacion'
		   	}, defaultContent: '', className: 'nk-tb-col'},
			{data: 'razon_social', defaultContent: '', className: 'nk-tb-col'},
			{data: 'estatus_vigencia', defaultContent: '', className: 'nk-tb-col'},
			{data: 'estatus_entrega', defaultContent: '', className: 'nk-tb-col'},
			{data: 'fecha_elaboracion', defaultContent: '', className: 'nk-tb-col'},
			{data: 'atencion', defaultContent: '', className: 'nk-tb-col'},
			{data: 'departamento', defaultContent: '', className: 'nk-tb-col'},
			{data: 'moneda', defaultContent: '', className: 'nk-tb-col'},
			{data: 'condiciones_pago', defaultContent: '', className: 'nk-tb-col'},
			{data: 'vendedor', defaultContent: '', className: 'nk-tb-col'},
			{data: 'acciones', defaultContent: '', className: 'nk-tb-col nk-tb-col-tools text-right'},
		]
	});

	$('body')

	.on('keyup', '.tools-tbl-cotizaciones #buscar', function(e) {
		IS.init.dataTable['tbl-cotizaciones'].search(this.value).draw();
	})

	.on('click', '.tools-tbl-cotizaciones .cotizaciones_length a', function(e) {
		$(this).closest('ul').find('li').removeClass('active');
		$(this).parent('li').addClass('active');

		IS.init.dataTable['tbl-cotizaciones'].page.len($(this).data('length')).draw();

		e.preventDefault();
	})

	.on('click' , '.tools-tbl-cotizaciones .add-cotizacion', function(e) {
		$(this).tooltip('hide');
		$('#form-filtro').formAjaxSend({
			url: base_url('ventas/ventas/get_modal_add_cotizacion'),
			dataType: 'html',
			success: function(modal) {
				$('#content-modals').html(modal);
				initModal('#modal-nueva-cotizacion', {
					onOpenEnd: function() {
						initSelect2('.modal select');
						$('#modal-nueva-cotizacion form').validate();
						init_table_productos_add();
						init_table_notas_add();
					}
				});
			}
		});
		e.preventDefault();
	})

	.on('click', '#modal-tbl-productos_filter .btn-add', function(e) {
		$.formAjaxSend({
			url: base_url('ventas/ventas/get_modal_add_producto_cotizacion'),
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

	.on('click', 'a[data-dismiss-modal=modal-add-nota]', function(e) {
    	$('#modal-add-nota').modal('hide');
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

	.on('click', '#btn-add-producto-cotizacion', function(e) {
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
				precio_unitario: $('#modal-add-producto form #precio_unitario').val(),
				descuento: $('#modal-add-producto form #descuento').val(),
				total: $('#modal-add-producto form #total').val(),
				incluye: $('#modal-add-producto form #incluye').val(),
				comision_vendedor: $('#modal-add-producto form #comision_vendedor').val(),
				opcional: opcional
			});

			var table 	= IS.init.dataTable['modal-tbl-productos'];
			table.row.add(data).draw()
			$('#modal-add-producto').modal('hide');
		}
		e.preventDefault();
	})

	.on('click', '#btn-add-producto-nota', function(e) {
		if ($('#modal-add-nota form').valid()) {
			var data = $.extend({}, {
				nota: $('#modal-add-nota form #nota').val(),
				descripcion: $('#modal-add-nota form #descripcion').val(),
			});

			var table 	= IS.init.dataTable['modal-tbl-notas'];
			table.row.add(data).draw()
			$('#modal-add-nota').modal('hide');
		}
		e.preventDefault();
	})

	.on('click', '#btn-save-cotizacion', function(e) {
		if ($('#modal-nueva-cotizacion form').valid()) {
			var table = IS.init.dataTable['modal-tbl-productos']
			var tableNotas = IS.init.dataTable['modal-tbl-notas']
			var rows = table.rows().data();
			if(rows.length) {
				var productos = [];
				var notas = [];
				table.$('tr').each(function(key, tr) {
					productos.push($(tr).data());
				});

				tableNotas.$('tr').each(function(key, tr) {
					notas.push($(tr).data());
				});

				$('#modal-nueva-cotizacion form').formAjaxSend({
					url: base_url('ventas/ventas/process_save_cotizacion'),
					data: {
						/*tipo_requisicion: $("#id_tipo_requisicion option:selected").text(),
						vale_entrada: $("#id_vale_entrada option:selected").text(),
						departamento_solicitante: $("#id_departamento_solicitante option:selected").text(),
						almacen_solicitante: $("#id_almacen_solicitante option:selected").text(),
						departamento_encargado_surtir: $("#id_departamento_encargado_surtir option:selected").text(),*/
						productos: productos,
						notas: notas
					},
					success: function(response) {
						if(response.success) {
							$('.modal.show').modal('hide');
							//gotoLink(base_url(response.file_path));
							ISToast.fire({icon: response.icon, title: response.msg, customClass: response.icon});
							IS.init.dataTable['tbl-cotizaciones'].ajax.reload(function(jsonData) {
							    IS.init.dataTable['tbl-cotizaciones'].columns.adjust().draw();
							});

						} else ISswal.fire({icon: response.icon, title: response.title, text: response.msg, customClass: response.icon});
					}
				})

			} else ISswal.fire({icon: 'info', title: '¡Información!', text: 'Agregue al menos un producto', customClass: 'info'});
		}
	})

	.on('click', '#tbl-cotizaciones button#open-modal-update', function(e) {
		$(this).tooltip('hide');
		var tr = $(this).closest('tr');
		$.formAjaxSend({
			url: base_url('ventas/ventas/get_modal_edit_cotizacion'),
			data: tr.data(),
			dataType: 'html',
			success: function(modal) {
				$('#content-modals').html(modal);
				initModal('#modal-editar-cotizacion', {
					onOpenEnd: function() {
						initSelect2('.modal select');
						$('#modal-editar-cotizacion form').validate();
						init_table_productos_add(listProductos);
						init_table_notas_add(listNotas);
					}
				});
			}
		});
		e.preventDefault();
	})

	.on('click', '#btn-update-cotizacion', function(e) {
		if ($('#modal-editar-cotizacion form').valid()) {
			var table = IS.init.dataTable['modal-tbl-productos']
			var tableNotas = IS.init.dataTable['modal-tbl-notas']
			var rows = table.rows().data();
			if(rows.length) {
				var productos = [];
				var notas = [];
				table.$('tr').each(function(key, tr) {
					productos.push($(tr).data());
				});

				tableNotas.$('tr').each(function(key, tr) {
					notas.push($(tr).data());
				});

				$('#modal-editar-cotizacion form').formAjaxSend({
					url: base_url('ventas/ventas/process_update_cotizacion'),
					data: {
						/*tipo_requisicion: $("#id_tipo_requisicion option:selected").text(),
						vale_entrada: $("#id_vale_entrada option:selected").text(),
						departamento_solicitante: $("#id_departamento_solicitante option:selected").text(),
						almacen_solicitante: $("#id_almacen_solicitante option:selected").text(),
						departamento_encargado_surtir: $("#id_departamento_encargado_surtir option:selected").text(),*/
						productos: productos,
						notas: notas
					},
					success: function(response) {
						if(response.success) {
							$('.modal.show').modal('hide');
							//gotoLink(base_url(response.file_path));
							ISToast.fire({icon: response.icon, title: response.msg, customClass: response.icon});
							IS.init.dataTable['tbl-cotizaciones'].ajax.reload(function(jsonData) {
							    IS.init.dataTable['tbl-cotizaciones'].columns.adjust().draw();
							});

						} else ISswal.fire({icon: response.icon, title: response.title, text: response.msg, customClass: response.icon});
					}
				})

			} else ISswal.fire({icon: 'info', title: '¡Información!', text: 'Agregue al menos un producto', customClass: 'info'});
		}
	})

	.on('click', '#modal-nueva-cotizacion #modal-tbl-productos .btn-remove, #modal-editar-cotizacion #modal-tbl-productos .btn-remove', function(e) {
		var tr = $(this).closest('tr');
		IS.init.dataTable['modal-tbl-productos'].row(tr).remove().draw();
	})

	.on('click', '#modal-nueva-cotizacion #modal-tbl-notas .btn-remove, #modal-editar-cotizacion #modal-tbl-notas .btn-remove', function(e) {
		var tr = $(this).closest('tr');
		IS.init.dataTable['modal-tbl-notas'].row(tr).remove().draw();
	})

	.on('click', '#tbl-cotizaciones button#remove', function(e) {
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
		    		url: base_url('ventas/ventas/process_remove_cotizacion'),
		    		data: data,
		    		success: function(response) {
		    			if(response.success) {
							ISToast.fire({icon: response.icon, title: response.msg, customClass: response.icon});
							IS.init.dataTable['tbl-cotizaciones'].ajax.reload();

						} else ISswal.fire({icon: response.icon, title: response.title, text: response.msg, customClass: response.icon});
		    		}
		    	});
		    }
		});
	})

	.on('change', '#cantidad', function(e){
		if($("#precio_unitario").val() != '' && $("#descuento").val() != '' ){
			calculaTotal();
		}
	})

	.on('change', '#precio_unitario', function(e){
		if($("#cantidad").val() != '' && $("#descuento").val() != '' ){
			calculaTotal();
		}
	})

	.on('change', '#descuento', function(e){
		if($("#cantidad").val() != '' && $("#precio_unitario").val() != '' ){
			calculaTotal();
		}
	})

	.on('click', '#modal-tbl-notas_filter .btn-add', function(e){
		$.formAjaxSend({
			url: base_url('ventas/ventas/get_modal_add_producto_nota'),
			dataType: 'html',
			success: function(modal) {
				$('#content-modals').append(modal);
				initModal('#modal-add-nota', {
					removeOnClose: false,
					backdrop: 'static',
					keyboard: true,
					onOpenEnd: function() {
						initSelect2('.modal select');
						$('#modal-add-nota form').validate();
					},
					onCloseEnd: function() {
						$('#modal-add-nota').remove();
					}
				});
			}
		});
	})

	function calculaTotal(){
		subtotal = $("#cantidad").val() * $("#precio_unitario").val();
		descuento = subtotal * $("#descuento").val() / 100;
		total = subtotal - descuento;
		$("#total").val(total);
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
				{data: 'descuento', defaultContent: '', className: 'nk-tb-col'},
				{data: 'total', defaultContent: '', className: 'nk-tb-col'},
				{data: 'incluye', defaultContent: '', className: 'nk-tb-col'},
				{data: 'comision_vendedor', defaultContent: '', className: 'nk-tb-col'},
				{data: function() {
					return `<button class="btn btn-dim btn-sm text-danger btn-remove py-0 px-1" title="${lang('general_quitar')}">
					        	<em class="icon ni ni-trash"></em>
					        </button>`;
				}, defaultContent: '', className: 'nk-tb-col text-center'}
			]
		});
	}

	function init_table_notas_add(data) {
		var tblData = data || [];
		initDataTable('.modal #modal-tbl-notas', {
			dom: '<"row justify-between g-2" <"col-sm-12 d-flex my-1 justify-content-end" f> ><"datatable-wrap my-1"t>',
			pageLength: 100,
			data: tblData,
			createdRow: function(row, data, index) {
				data.acciones = undefined;
				$(row).addClass('nk-tb-item').data(data);
			},
			columns: [
				{data: 'nota', defaultContent: '', className: 'nk-tb-col'},
				{data: 'descripcion', defaultContent: '', className: 'nk-tb-col'},
				{data: function() {
					return `<button class="btn btn-dim btn-sm text-danger btn-remove py-0 px-1" title="${lang('general_quitar')}">
					        	<em class="icon ni ni-trash"></em>
					        </button>`;
				}, defaultContent: '', className: 'nk-tb-col text-center'}
			]
		});
	}
	

});