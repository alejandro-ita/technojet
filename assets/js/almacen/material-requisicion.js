jQuery(function($) {
	initDataTable('#tbl-requisicion-material', {
		ajax: {
		 	 url: base_url('almacen/requisicion_material/get_productos_requisicion_material')
		}
		,createdRow: function(row, data, index) {
			data.acciones = undefined;
			$(row).addClass('nk-tb-item').data(data);
		}
		,columns: [
			{data: {
			 	_: 'folio', sort: 'id_requisicion'
			}, defaultContent: '', className: 'nk-tb-col'}
			,{data: 'fecha_solicitud', defaultContent: '', className: 'nk-tb-col'}
			,{data: 'tipo_requisicion', defaultContent: '', className: 'nk-tb-col'}
			,{data: 'pedido_interno', defaultContent: '', className: 'nk-tb-col'}
			,{data: 'cliente', defaultContent: '', className: 'nk-tb-col'}
			,{data: 'fecha_entrega', defaultContent: '', className: 'nk-tb-col'}
			,{data: 'vale_entrada', defaultContent: '', className: 'nk-tb-col'}
			,{data: 'departamento_solicitante', defaultContent: '', className: 'nk-tb-col'}
			,{data: 'persona_solicitante', defaultContent: '', className: 'nk-tb-col'}
			,{data: 'almacen_solicitante', defaultContent: '', className: 'nk-tb-col'}
			,{data: 'encargado_almacen', defaultContent: '', className: 'nk-tb-col'}
			,{data: 'tipo_entrega', defaultContent: '', className: 'nk-tb-col'}
			,{data: 'departamento_encargado_surtir', defaultContent: '', className: 'nk-tb-col'}
			,{data: 'observaciones', defaultContent: '', className: 'nk-tb-col'}
			,{data: 'nombre_almacen', defaultContent: '', className: 'nk-tb-col'}
			,{data: 'nombre_compras', defaultContent: '', className: 'nk-tb-col'}
			,{data: 'nombre_autorizacion', defaultContent: '', className: 'nk-tb-col'}
			,{data: 'estatus_requisicion', defaultContent: '', className: 'nk-tb-col'}
			,{data: 'acciones', defaultContent: '', className: 'nk-tb-col nk-tb-col-tools text-right'}
		]
		,order: [[0, 'asc']]
	});

	$('body')

	.on('keyup', '.tools-tbl-requisicion #buscar', function(e) {
		IS.init.dataTable['tbl-requisicion-material'].search(this.value).draw();
	})

	.on('click', '.tools-tbl-requisicion .requisicion_length a', function(e) {
		$(this).closest('ul').find('li').removeClass('active');
		$(this).parent('li').addClass('active');

		IS.init.dataTable['tbl-requisicion-material'].page.len($(this).data('length')).draw();

		e.preventDefault();
	})

	.on('click', '#tbl-requisicion-material button#open-modal-update', function(e) {
		$(this).tooltip('hide');
		var tr = $(this).closest('tr');
		$.formAjaxSend({
			 url: base_url('almacen/requisicion_material/get_modal_edit_requisicion')
			,data: tr.data()
			,dataType: 'html'
			,success: function(modal) {
				$('#content-modals').html(modal);
				initModal('#modal-editar-vale-requisicion', {
					onOpenEnd: function() {
						initSelect2('.modal select');
						$('#modal-editar-vale-requisicion form').validate();
						init_tbl_productos_requisicion(listProductos);
					}
				});
			}
		});
		e.preventDefault();
	})

	.on('click', '#tbl-requisicion-material button#remove', function(e) {
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
		    	data.id_uso = $('#id_uso').val();
		    	$.formAjaxSend({
		    		url: base_url('almacen/requisicion_material/process_remove_requisicion'),
		    		data: data,
		    		success: function(response) {
		    			if(response.success) {
							ISToast.fire({icon: response.icon, title: response.msg, customClass: response.icon});
							IS.init.dataTable['tbl-requisicion-material'].ajax.reload();

						} else ISswal.fire({icon: response.icon, title: response.title, text: response.msg, customClass: response.icon});
		    		}
		    	});
		    }
		});
	})

	.on('click', '#tbl-requisicion-material button#build-pdf', function(e) {
		$(this).tooltip('hide');
		var tr = $(this).closest('tr');
		
    	$.formAjaxSend({
    		url: base_url('almacen/requisicion_material/process_build_pdf_requsicion'),
    		data: tr.data(),
    		success: function(response) {
    			if(response.success) {
					gotoLink(base_url(response.file_path));

				} else ISswal.fire({icon: response.icon, title: response.title, text: response.msg, customClass: response.icon});
    		}
    	});
	})

	.on('click', '.tools-tbl-requisicion .add-requisicion', function(e) {
		$(this).tooltip('hide');
		$('#form-filtro').formAjaxSend({
			 url: base_url('almacen/requisicion_material/get_modal_add_requisicion')
			,dataType: 'html'
			,success: function(modal) {
				$('#content-modals').html(modal);
				initModal('#modal-nueva-vale-requisicion', {
					onOpenEnd: function() {
						initSelect2('.modal select');
						$('#modal-nueva-vale-requisicion form').validate();
						init_tbl_productos_requisicion();
					}
				});
			}
		});
		e.preventDefault();
	})

	.on('click', '#modal-tbl-productos-requisicion_filter .btn-add', function(e) {
		$.formAjaxSend({
			 url: base_url('almacen/requisicion_material/get_modal_add_producto_requisicion')
			,dataType: 'html'
			,success: function(modal) {
				$('#content-modals').append(modal);
				initModal('#modal-add-producto-requisicion', {
					 removeOnClose: false
					,backdrop: 'static'
					,keyboard: true
					,onOpenEnd: function() {
						initSelect2('.modal select');
						$('#modal-add-producto-requisicion form').validate();
					}
					,onCloseEnd: function() {
						$('#modal-add-producto-requisicion').remove();
					}
				});
			}
		});
	})

	.on('click', 'a[data-dismiss-modal=modal-add-producto-requisicion]', function(e) {
    	$('#modal-add-producto-requisicion').modal('hide');
    	e.preventDefault();
	})

	.on('change', '#modal-add-producto-requisicion form #id_tipo_producto', function(e) {
		$('#modal-add-producto-requisicion form #id_unidad_medida option:not(:first)').remove();
		$('#modal-add-producto-requisicion form #id_producto option:not(:first)').remove();
		$.formAjaxSend({
			 url: base_url('almacen/almacenes/get_unidades_medida_productos')
			,data: {id_tipo_producto: $(this).val()}
			,blockScreen: false
			,success: function(response) {
				var $select = $('#modal-add-producto-requisicion form #id_unidad_medida');
				$.each(response, function(key, um) {
					var newOption = $(new Option(um['custom_unidad_medida'], um['id_unidad_medida'], false, false));
					newOption.data(um);
					$select.append(newOption);
				});
			}
		});
	})

	.on('change', '#modal-add-producto-requisicion form #id_unidad_medida', function(e) {
		$('#modal-add-producto-requisicion form #id_producto option:not(:first)').remove();
		$('#modal-add-producto-requisicion form #descripcion').val('');
		$('#modal-add-producto-requisicion form #descripcion').attr('title', '');
		$.formAjaxSend({
			 url: base_url('almacen/almacenes/get_productos_por_tipo')
			,data: {
				 id_tipo_producto: $('#modal-add-producto-requisicion form #id_tipo_producto').val()
				,id_unidad_medida: $(this).val()
			}
			,blockScreen: false
			,success: function(response) {
				var $select = $('#modal-add-producto-requisicion form #id_producto');
				$.each(response, function(key, producto) {
					var newOption = $(new Option(producto['no_parte'], producto['id_producto'], false, false));
					newOption.data(producto);
					$select.append(newOption);
				});
			}
		});
	})

	.on('change', '#modal-add-producto-requisicion form #id_producto', function(e) {
		var productoData = $('#modal-add-producto-requisicion form #id_producto option:selected').data();
		$('#modal-add-producto-requisicion form #descripcion').val(productoData.descripcion);
		$('#modal-add-producto-requisicion form #descripcion').attr('title', productoData.descripcion);
	})

	.on('click', '#btn-add-producto-requisicion', function(e) {
		if ($('#modal-add-producto-requisicion form').valid()) {
			var productoData = $('#modal-add-producto-requisicion form #id_producto option:selected').data();
			var data = $.extend({}, productoData, {
				 id_tipo_producto: $('#modal-add-producto-requisicion form #id_tipo_producto option:selected').val()
				,tipo_producto: $('#modal-add-producto-requisicion form #id_tipo_producto option:selected').text()
				,id_unidad_medida: $('#modal-add-producto-requisicion form #id_unidad_medida option:selected').val()
				,unidad_medida: $('#modal-add-producto-requisicion form #id_unidad_medida option:selected').text()
			 	,cantidad: $('#modal-add-producto-requisicion form #cantidad').val()
			});

			var table 	= IS.init.dataTable['modal-tbl-productos-requisicion'];
			table.row.add(data).draw()
			$('#modal-add-producto-requisicion').modal('hide');
		}
		e.preventDefault();
	})

	.on('click', '#btn-save-vale-requisicion', function(e) {
		if ($('#modal-nueva-vale-requisicion form').valid()) {
			var table = IS.init.dataTable['modal-tbl-productos-requisicion']
			var rows = table.rows().data();
			if(rows.length) {
				var productos = [];
				table.$('tr').each(function(key, tr) {
					productos.push($(tr).data());
				});

				$('#modal-nueva-vale-requisicion form').formAjaxSend({
					 url: base_url('almacen/requisicion_material/process_save_productos_requisicion')
					,data: {
						 tipo_requisicion: $("#id_tipo_requisicion option:selected").text()
						,vale_entrada: $("#id_vale_entrada option:selected").text()
						,departamento_solicitante: $("#id_departamento_solicitante option:selected").text()
						,almacen_solicitante: $("#id_almacen_solicitante option:selected").text()
						,departamento_encargado_surtir: $("#id_departamento_encargado_surtir option:selected").text()
						,productos: productos
					}
					,success: function(response) {
						if(response.success) {
							$('.modal.show').modal('hide');
							gotoLink(base_url(response.file_path));
							ISToast.fire({icon: response.icon, title: response.msg, customClass: response.icon});
							IS.init.dataTable['tbl-requisicion-material'].ajax.reload(function(jsonData) {
							    IS.init.dataTable['tbl-requisicion-material'].columns.adjust().draw();
							});

						} else ISswal.fire({icon: response.icon, title: response.title, text: response.msg, customClass: response.icon});
					}
				})

			} else ISswal.fire({icon: 'info', title: '¡Información!', text: 'Agregue al menos un producto', customClass: 'info'});
		}
	})

	.on('click', '#btn-actualizar-vale-requisicion', function(e) {
		if ($('#modal-editar-vale-requisicion form').valid()) {
			var table = IS.init.dataTable['modal-tbl-productos-requisicion']
			var rows = table.rows().data();
			if(rows.length) {
				var productos = [];
				table.$('tr').each(function(key, tr) {
					productos.push($(tr).data());
				});

				$('#modal-editar-vale-requisicion form').formAjaxSend({
					 url: base_url('almacen/requisicion_material/process_update_productos_requisicion')
					,data: {
						 tipo_requisicion: $("#id_tipo_requisicion option:selected").text()
						,vale_entrada: $("#id_vale_entrada option:selected").text()
						,departamento_solicitante: $("#id_departamento_solicitante option:selected").text()
						,almacen_solicitante: $("#id_almacen_solicitante option:selected").text()
						,departamento_encargado_surtir: $("#id_departamento_encargado_surtir option:selected").text()
						,productos: productos
					}
					,success: function(response) {
						if(response.success) {
							$('.modal.show').modal('hide');
							gotoLink(base_url(response.file_path));
							ISToast.fire({icon: response.icon, title: response.msg, customClass: response.icon});
							IS.init.dataTable['tbl-requisicion-material'].ajax.reload(function(jsonData) {
							    IS.init.dataTable['tbl-requisicion-material'].columns.adjust().draw();
							});

						} else ISswal.fire({icon: response.icon, title: response.title, text: response.msg, customClass: response.icon});
					}
				})

			} else ISswal.fire({icon: 'info', title: '¡Información!', text: 'Agregue al menos un producto', customClass: 'info'});
		}
	})

	.on('click', '#modal-nueva-vale-requisicion #modal-tbl-productos-requisicion .btn-remove, #modal-editar-vale-requisicion #modal-tbl-productos-requisicion .btn-remove', function(e) {
		var tr = $(this).closest('tr');
		IS.init.dataTable['modal-tbl-productos-requisicion'].row(tr).remove().draw();
	});

	function init_tbl_productos_requisicion(data) {
		var tblData = data || [];
		initDataTable('.modal #modal-tbl-productos-requisicion', {
			 dom: '<"row justify-between g-2" <"col-sm-12 d-flex my-1 justify-content-end" f> ><"datatable-wrap my-1"t>'
			,pageLength: 100
			,data: tblData
			,createdRow: function(row, data, index) {
				data.acciones = undefined;
				$(row).addClass('nk-tb-item').data(data);
			}
			,columns: [
				 {data: 'tipo_producto', defaultContent: '', className: 'nk-tb-col'}
				,{data: 'unidad_medida', defaultContent: '', className: 'nk-tb-col'}
				,{data: 'no_parte', defaultContent: '', className: 'nk-tb-col'}
				,{data: 'descripcion', defaultContent: '', className: 'nk-tb-col'}
				,{data: 'cantidad', defaultContent: '', className: 'nk-tb-col'}
				,{data: function() {
					return `<button class="btn btn-dim btn-sm text-danger btn-remove py-0 px-1" title="${lang('general_quitar')}">
					        	<em class="icon ni ni-trash"></em>
					        </button>`;
				}, defaultContent: '', className: 'nk-tb-col text-center'}
			]
		});
	}
});