jQuery(function($) {
	initDataTable('#tbl-reporte-sistemas', {
		ajax: {
		 	 url: base_url('administracion/reporte_sistemas/get_reportes_sistemas')
		}
		,createdRow: function(row, data, index) {
			data.acciones = undefined;
			$(row).addClass('nk-tb-item').data(data);
		}
		,order: [[0, "desc"]]
		,columns: [
			 {data: 'id_reporte_sistema', defaultContent: '', className: 'nk-tb-col'}
			,{data: {
			 	_: 'fecha_custom', sort: 'timestamp'
			 }, defaultContent: '', className: 'nk-tb-col'}
			,{data: 'responsable', defaultContent: '', className: 'nk-tb-col'}
			,{data: 'encargado_elaboracion', defaultContent: '', className: 'nk-tb-col'}
			,{data: 'vo_bo', defaultContent: '', className: 'nk-tb-col'}	
			,{data: 'acciones', defaultContent: '', className: 'nk-tb-col nk-tb-col-tools text-right'}
		]
	})


	$('body')
	.on('click', '.add-reporte', function(e) {
		$.formAjaxSend({
			 url: base_url('administracion/reporte_sistemas/get_modal_nuevo_reporte')
			,dataType: 'html'
			,success: function(modal) {
				$('#content-modals').html(modal);
				initModal('.modal', {
					backdrop: 'static',
					keyboard: false,
					onOpenEnd: function() {
						//https://stackoverflow.com/questions/50626404/jquery-validate-checkbox-at-least-one-for-group-in-a-complex-form
						$('.modal.show form').validate();
						init_tbl_productos_reporte();
					}
				});
			}
		});
		e.preventDefault();
	})

	.on('click', '.modal #btn-guardar-reporte', function() {
		if ($('.modal.show form').valid()) {
			var table = IS.init.dataTable['modal-tbl-productos-reporte-sistemas']
			var rows = table.rows().data();
			var productos = [];
			if(rows.length) {
				table.$('tr').each(function(key, tr) {
					productos.push($(tr).data());
				});
			}
			$('.modal.show form').formAjaxSend({
				 url: base_url('administracion/reporte_sistemas/process_save_nuevo_reporte')
				,data: {productos: productos}
				,success: function(response) {
					if(response.success) {
						$('.modal.show').modal('hide');
						gotoLink(base_url(response.file_path), '_blank', true);
						// ISToast.fire({icon: response.icon, title: response.msg, customClass: response.icon});
						IS.init.dataTable['tbl-reporte-sistemas'].ajax.reload();

					} else ISswal.fire({icon: response.icon, title: response.title, text: response.msg, customClass: response.icon});
				}
			});
		}
	})

	.on('click', '#tbl-reporte-sistemas button#open-modal-update', function(e) {
		$(this).tooltip('hide');
		var tr = $(this).closest('tr');
		$('#form-filtro').formAjaxSend({
			 url: base_url('administracion/reporte_sistemas/get_modal_edit_reporte_sistemas')
			,data: tr.data()
			,dataType: 'html'
			,success: function(modal) {
				$('#content-modals').html(modal);
				initModal('#modal-editar-reporte-sistemas', {
					onOpenEnd: function() {
						$('#modal-editar-reporte-sistemas form').validate();
						init_tbl_productos_reporte(listProductos);
					}
				});
			}
		});
		e.preventDefault();
	})

	.on('click', '#btn-actualizar-reporte-sistemas', function(e) {
		if ($('#modal-editar-reporte-sistemas form').valid()) {
			var table = IS.init.dataTable['modal-tbl-productos-reporte-sistemas']
			var rows = table.rows().data();
			var productos = [];
			if(rows.length) {
				table.$('tr').each(function(key, tr) {
					productos.push($(tr).data());
				});
			}

			$('#modal-editar-reporte-sistemas form').formAjaxSend({
				 url: base_url('administracion/reporte_sistemas/process_update_reporte_sistemas')
				,data: {productos: productos}
				,success: function(response) {
					if(response.success) {
						$('.modal.show').modal('hide');
						gotoLink(base_url(response.file_path), '_blank', true);
						// ISToast.fire({icon: response.icon, title: response.msg, customClass: response.icon});
						IS.init.dataTable['tbl-reporte-sistemas'].ajax.reload();

					} else ISswal.fire({icon: response.icon, title: response.title, text: response.msg, customClass: response.icon});
				}
			});
		}
	})

	.on('click', '#tbl-reporte-sistemas button#remove', function(e) {
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
		    	$.formAjaxSend({
		    		url: base_url('administracion/reporte_sistemas/process_remove_reporte'),
		    		data: tr.data(),
		    		success: function(response) {
		    			if(response.success) {
							ISToast.fire({icon: response.icon, title: response.msg, customClass: response.icon});
							IS.init.dataTable['tbl-reporte-sistemas'].row(tr).remove().draw();

						} else ISswal.fire({icon: response.icon, title: response.title, text: response.msg, customClass: response.icon});
		    		}
		    	});
		    }
		});
	})

	.on('click', '#tbl-reporte-sistemas button#build-pdf', function(e) {
		var tr = $(this).closest('tr');
		var btn= $(this);
		btn.tooltip('hide');
		
    	$.formAjaxSend({
    		url: base_url('administracion/reporte_sistemas/process_build_pdf_reporte_sistemas'),
    		data: tr.data(),
    		success: function(response) {
    			if(response.success) {
					gotoLink(base_url(response.file_path), '_blank', true);

				} else ISswal.fire({icon: response.icon, title: response.title, text: response.msg, customClass: response.icon});
    		}
    	});
	})

	.on('click', '#modal-tbl-productos-reporte-sistemas_filter .btn-add', function(e) {
		$.formAjaxSend({
			 url: base_url('administracion/reporte_sistemas/get_modal_add_producto_reporte')
			,dataType: 'html'
			,success: function(modal) {
				$('#content-modals').append(modal);
				initModal('#modal-add-producto-reporte-sistemas', {
					 removeOnClose: false
					,backdrop: 'static'
					,keyboard: true
					,onOpenEnd: function() {
						initSelect2('.modal select');
						$('#modal-add-producto-reporte-sistemas form').validate();
					}
					,onCloseEnd: function() {
						$('#modal-add-producto-reporte-sistemas').remove();
					}
				});
			}
		});
	})

	.on('click', 'a[data-dismiss-modal=modal-add-producto-reporte-sistemas]', function(e) {
    	$('#modal-add-producto-reporte-sistemas').modal('hide');
    	e.preventDefault();
	})

	.on('click', '#btn-add-producto-reporte-sistemas', function(e) {
		if ($('#modal-add-producto-reporte-sistemas form').valid()) {
			var productoData = $('#modal-add-producto-reporte-sistemas form').serializeObject();
			productoData.unidad_medida = $('#modal-add-producto-reporte-sistemas form #id_unidad_medida option:selected').text();

			var table 	= IS.init.dataTable['modal-tbl-productos-reporte-sistemas'];
			table.row.add(productoData).draw()
			$('#modal-add-producto-reporte-sistemas').modal('hide');
		}
		e.preventDefault();
	})

	.on('click', '#modal-nuevo-reporte #modal-tbl-productos-reporte-sistemas .btn-remove, #modal-editar-reporte-sistemas #modal-tbl-productos-reporte-sistemas .btn-remove', function(e) {
		var tr = $(this).closest('tr');
		IS.init.dataTable['modal-tbl-productos-reporte-sistemas'].row(tr).remove().draw();
	});

	function init_tbl_productos_reporte(data) {
		var tblData = data || [];
		initDataTable('.modal #modal-tbl-productos-reporte-sistemas', {
			 dom: '<"row justify-between g-2" <"col-sm-12 d-flex my-1 justify-content-end" f> ><"datatable-wrap my-1"t>'
			,pageLength: 100
			,data: tblData
			,createdRow: function(row, data, index) {
				data.acciones = undefined;
				$(row).addClass('nk-tb-item').data(data);
			}
			,columns: [
				 {data: 'cantidad', defaultContent: '', className: 'nk-tb-col'}
				,{data: 'no_parte', defaultContent: '', className: 'nk-tb-col'}
				,{data: 'unidad_medida', defaultContent: '', className: 'nk-tb-col'}
				,{data: 'descripcion', defaultContent: '', className: 'nk-tb-col'}
				,{data: 'precio_unitario', defaultContent: '', className: 'nk-tb-col'}
				,{data: 'descuento', defaultContent: '', className: 'nk-tb-col'}
				,{data: 'importe', defaultContent: '', className: 'nk-tb-col'}
				,{data: 'autorizado', defaultContent: '', className: 'nk-tb-col'}
				,{data: function() {
					return `<button class="btn btn-dim btn-sm text-danger btn-remove py-0 px-1" title="${lang('general_quitar')}">
					        	<em class="icon ni ni-trash"></em>
					        </button>`;
				}, defaultContent: '', className: 'nk-tb-col text-center'}
			]
		});
	}
});