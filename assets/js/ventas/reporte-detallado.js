jQuery(function($) {

	/*initDataTable('#tbl-almacenes-productos-entrada', {
		ajax: {
		 	 url: base_url('ventas/almacenes/get_productos_vales_entrada')
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
			 	_: 'folio', sort: 'id_vale_entrada'
			}, defaultContent: '', className: 'nk-tb-col'}
			,{data: 'referencia_entrada', defaultContent: '', className: 'nk-tb-col'}
			,{data: 'custom_fecha', defaultContent: '', className: 'nk-tb-col'}
			,{data: 'cantidad', defaultContent: '', className: 'nk-tb-col'}
			,{data: 'no_parte', defaultContent: '', className: 'nk-tb-col'}
			,{data: 'tipo_producto', defaultContent: '', className: 'nk-tb-col'}
			,{data: 'unidad_medida', defaultContent: '', className: 'nk-tb-col'}
			,{data: 'concepto_entrada', defaultContent: '', className: 'nk-tb-col'}
			,{data: 'recibio', defaultContent: '', className: 'nk-tb-col'}
			,{data: 'entrego', defaultContent: '', className: 'nk-tb-col'}
			,{data: 'vo_bo', defaultContent: '', className: 'nk-tb-col'}
			,{data: 'estatus', defaultContent: '', className: 'nk-tb-col'}
			,{data: 'acciones', defaultContent: '', className: 'nk-tb-col nk-tb-col-tools text-right'}
		]
		,order: [[0, 'asc']]
		,rowGroup: {
             dataSrc: 'folio'
            ,startRender: function (rows, group) {
            	var data 	= $(rows.nodes()[0]).data();
            	// var colspan = $(rows.nodes()[0]).find('td').length-1;
            	var badge 	= (data.id_vale_estatus=='1')
            		? `<span class="badge bg-success border-success text-white">${data.estatus}</span>`
            		: `<span class="badge bg-danger border-danger text-white">${data.estatus}</span>`;

            	var autorizo = ($('#id_uso').val()=='5') ? `<br>Autorizó: ${data.autorizo}` : '';
                var tr = $('<tr class="nk-tb-item"/>')
                    .append(`<td>No. de Folio: ${data.folio} <br>Estatus: ${badge}</td>`)
                    .append(`<td>Fecha: ${data.custom_fecha}</td>`)
                    .append(`<td colspan="2">Recibió: ${data.recibio} ${autorizo}</td>`)
                    .append(`<td>Entregó: ${data.entrego}</td>`)
                    .append(`<td>Vo Bo: ${data.vo_bo}</td>`)
                    .append(`<td class="nk-tb-col nk-tb-col-tools text-right">
                    	<ul class="nk-tb-actions gx-1">
						    <li class="nk-tb-action-hidden bg-transparent">
						        <button id="build-pdf" class="btn btn-dim btn-sm btn-primary py-0 px-1" title="${lang('vales_generar_pdf')}">
						            <em class="icon ni ni-file-pdf"></em>
						        </button>
						    </li>
						    <li class="nk-tb-action-hidden bg-transparent">
						        <button id="open-modal-update" class="btn btn-dim btn-sm btn-primary py-0 px-1" title="${lang('general_editar')}">
						            <em class="icon ni ni-edit"></em>
						        </button>
						    </li>
						    <li class="nk-tb-action-hidden bg-transparent">
						        <button id="remove" class="btn btn-dim btn-sm btn-danger py-0 px-1" title="${lang('general_delete')}">
						        	<em class="icon ni ni-trash"></em>
						        </button>
						    </li>
						    <li>
						        <button class="btn btn-dim btn-sm py-0 px-1">
						            <em class="icon ni ni-more-h"></em>
						        </button>
						    </li>
						</ul>
                    </td>`);

                return tr.data(data);
            }
        }
	});*/
	

	$('body')

	.on('keyup', '.tools-tbl-productos-entrada #buscar', function(e) {
		IS.init.dataTable['tbl-almacenes-productos-entrada'].search(this.value).draw();
	})

	.on('click', '.tools-tbl-productos-entrada .entrada_length a', function(e) {
		$(this).closest('ul').find('li').removeClass('active');
		$(this).parent('li').addClass('active');

		IS.init.dataTable['tbl-almacenes-productos-entrada'].page.len($(this).data('length')).draw();

		e.preventDefault();
	})

	.on('click', '#tbl-almacenes-productos-entrada button#open-modal-update', function(e) {
		$(this).tooltip('hide');
		var tr = $(this).closest('tr');
		if ($('#form-filtro').valid()) {
			$('#form-filtro').formAjaxSend({
				 url: base_url('ventas/almacenes/get_modal_edit_vale_entrada')
				,data: tr.data()
				,dataType: 'html'
				,success: function(modal) {
					$('#content-modals').html(modal);
					initModal('#modal-editar-vale-entrada', {
						onOpenEnd: function() {
							initSelect2('.modal select');
							$('#modal-editar-vale-entrada form').validate();
							init_tbl_entrada_nuevos_productos(listProductos);
						}
					});
				}
			});
		}
		e.preventDefault();
	})

	.on('click', '#tbl-almacenes-productos-entrada button#remove', function(e) {
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
				data.uso = $("#id_uso option:selected").text()
				data.categoria = $("#id_categoria option:selected").text()
		    	$.formAjaxSend({
		    		url: base_url('ventas/almacenes/process_remove_vale_entrada'),
		    		data: data,
		    		success: function(response) {
		    			if(response.success) {
							ISToast.fire({icon: response.icon, title: response.msg, customClass: response.icon});
							IS.init.dataTable['tbl-almacenes-productos'].ajax.reload();
							IS.init.dataTable['tbl-almacenes-productos-entrada'].ajax.reload(function(jsonData) {
							    IS.init.dataTable['tbl-almacenes-productos-entrada'].columns.adjust().draw();
							});

						} else ISswal.fire({icon: response.icon, title: response.title, text: response.msg, customClass: response.icon});
		    		}
		    	});
		    }
		});
	})

	.on('click', '#tbl-almacenes-productos-entrada button#build-pdf', function(e) {
		var tr = $(this).closest('tr');
		var btn= $(this);
		btn.tooltip('hide');
		
		var data = tr.data();
		data.id_uso = $('#id_uso').val();
    	$.formAjaxSend({
    		url: base_url('ventas/almacenes/process_build_pdf_vale_entrada'),
    		data: data,
    		success: function(response) {
    			if(response.success) {
					gotoLink(base_url(response.file_path));

				} else ISswal.fire({icon: response.icon, title: response.title, text: response.msg, customClass: response.icon});
    		}
    	});
	})

	.on('click', '.tools-tbl-productos-entrada .add-entrada', function(e) {
		//alert('NUEVA COTIZACIÓN');
		$(this).tooltip('hide');
		$('#form-filtro').formAjaxSend({
			 url: base_url('ventas/ventas/get_modal_add_cotizacion')
			,dataType: 'html'
			,success: function(modal) {
				$('#content-modals').html(modal);
				initModal('#modal-nuevo-vale-entrada', {
					onOpenEnd: function() {
						//initSelect2('.modal select');
						//$('#modal-nuevo-vale-entrada form').validate();
						init_tbl_entrada_nuevos_productos();
					}
				});
			}
		});
		e.preventDefault();
	})

	.on('click', '#modal-tbl-entrada-productos_filter .btn-add', function(e) {
		$(this).tooltip('hide');
		$.formAjaxSend({
			 url: base_url('ventas/ventas/get_modal_add_producto_entrada')
			,data: {id_uso: $('select#id_uso').val()}
			,dataType: 'html'
			,success: function(modal) {
				$('#content-modals').append(modal);
				initModal('#modal-add-producto-entrada', {
					 removeOnClose: false
					,backdrop: 'static'
					,keyboard: true
					,onOpenEnd: function() {
						initSelect2('.modal select');
						$('#modal-add-producto-entrada form').validate();
						/*var $select = $('#modal-add-producto-entrada form #id_producto');
						$.each(listProductos, function(key, data) {
							var newOption = $(new Option(data['no_parte'], data['id_producto'], false, false));
							newOption.data(data);
							$select.append(newOption);
						});*/
					}
					,onCloseEnd: function() {
						$('#modal-add-producto-entrada').remove();
					}
				});
			}
		});
	})

	.on('click', 'a[data-dismiss-modal=modal-add-producto-entrada]', function(e) {
    	$('#modal-add-producto-entrada').modal('hide');
    	e.preventDefault();
	})

	.on('change', '#modal-add-producto-entrada form #id_tipo_producto', function(e) {
		if ($('#id_uso').val()!='5') {
			$('#modal-add-producto-entrada form #id_unidad_medida option:not(:first)').remove();
			$('#modal-add-producto-entrada form #id_producto option:not(:first)').remove();
			$.formAjaxSend({
				 url: base_url('ventas/almacenes/get_unidades_medida_productos')
				,data: {
					 id_categoria: $('#id_categoria').val()
					,id_tipo_producto: $(this).val()
				}
				,blockScreen: false
				,success: function(response) {
					var $select = $('#modal-add-producto-entrada form #id_unidad_medida');
					$.each(response, function(key, um) {
						var newOption = $(new Option(um['custom_unidad_medida'], um['id_unidad_medida'], false, false));
						newOption.data(um);
						$select.append(newOption);
					});
				}
			});
		}
	})

	.on('change', '#modal-add-producto-entrada form #id_unidad_medida', function(e) {
		if ($('#id_uso').val()!='5') {
			$('#modal-add-producto-entrada form #id_producto option:not(:first)').remove();
			$('#modal-add-producto-entrada form #descripcion').val('');
			$('#modal-add-producto-entrada form #descripcion').attr('title', '');
			$.formAjaxSend({
				 url: base_url('ventas/almacenes/get_productos_por_tipo')
				,data: {
					 id_categoria: $('#id_categoria').val()
					,id_tipo_producto: $('#modal-add-producto-entrada form #id_tipo_producto').val()
					,id_unidad_medida: $(this).val()
				}
				,blockScreen: false
				,success: function(response) {
					var $select = $('#modal-add-producto-entrada form #id_producto');
					$.each(response, function(key, producto) {
						var newOption = $(new Option(producto['no_parte'], producto['id_producto'], false, false));
						newOption.data(producto);
						$select.append(newOption);
					});
				}
			});
		}
	})

	.on('change', '#modal-add-producto-entrada form #id_producto', function(e) {
		var productoData = $('#modal-add-producto-entrada form #id_producto option:selected').data();
		$('#modal-add-producto-entrada form #descripcion').val(productoData.descripcion);
		$('#modal-add-producto-entrada form #descripcion').attr('title', productoData.descripcion);
	})

	.on('click', '#btn-add-producto-entrada', function(e) {
		if ($('#modal-add-producto-entrada form').valid()) {
			//PRODUCTOS DE ENTRADA ACTIVOS
			if ($('#id_uso').val() == '5') {
				var data = {
					 id_tipo_producto: $('#modal-add-producto-entrada form #id_tipo_producto').val()
					,tipo_producto: $('#modal-add-producto-entrada form #id_tipo_producto option:selected').text()
					,id_unidad_medida: $('#modal-add-producto-entrada form #id_unidad_medida').val()
					,unidad_medida: $('#modal-add-producto-entrada form #id_unidad_medida option:selected').text()
					,no_parte: $('#modal-add-producto-entrada form #no_parte').val()
					,descripcion: $('#modal-add-producto-entrada form #descripcion').val()
				 	,cantidad: $('#modal-add-producto-entrada form #cantidad').val()
					,no_serie: $('#modal-add-producto-entrada form #no_serie').val()
					,estado_producto: $('#modal-add-producto-entrada form #estado_producto').val()
					,costo: $('#modal-add-producto-entrada form #costo').val()
					,id_moneda: $('#modal-add-producto-entrada form #id_moneda').val()
					,moneda: $('#modal-add-producto-entrada form #id_moneda option:selected').text()
				};

			//PRODUCTOS DE ENTRADA GENERAL
			} else {
				var productoData = $('#modal-add-producto-entrada form #id_producto option:selected').data();
				var data = $.extend({}, productoData, {
					 id_tipo_producto: $('#modal-add-producto-entrada form #id_tipo_producto option:selected').val()
					,tipo_producto: $('#modal-add-producto-entrada form #id_tipo_producto option:selected').text()
					,id_unidad_medida: $('#modal-add-producto-entrada form #id_unidad_medida option:selected').val()
					,unidad_medida: $('#modal-add-producto-entrada form #id_unidad_medida option:selected').text()
				 	,cantidad: $('#modal-add-producto-entrada form #cantidad').val()
					,referencia_alfanumerica: $('#modal-add-producto-entrada form #referencia_alfanumerica').val()
					,referencia_entrada: $('#modal-add-producto-entrada form #referencia_entrada').val()
				});
			}

			var table 	= IS.init.dataTable['modal-tbl-entrada-productos'];
			table.row.add(data).draw()
			$('#modal-add-producto-entrada').modal('hide');
		}
		e.preventDefault();
	})

	.on('click', '#btn-save-vale-entrada', function(e) {
		if ($('#modal-nuevo-vale-entrada form').valid()) {
			var table = IS.init.dataTable['modal-tbl-entrada-productos']
			var rows = table.rows().data();
			if(rows.length) {
				var fecha = moment($('#custom_fecha').val(), 'DD/MM/YYYY').format('YYYY-MM-DD');
				var productos = [];
				table.$('tr').each(function(key, tr) {
					productos.push($(tr).data());
				});

				var id_requisicion = $("#id_requisicion").val();
				$('#modal-nuevo-vale-entrada form').formAjaxSend({
					 url: base_url('ventas/almacenes/process_save_productos_entrada')
					,data: {
						 id_uso: $('select#id_uso').val()
						,id_categoria: $('select#id_categoria').val()
						,tipo_entrada: $("#id_ve_tipo_entrada option:selected").text()
						,vale_almacen: $("#id_vale_almacen option:selected").text()
						,requisicion: (id_requisicion ? $("#id_requisicion option:selected").text() : '')
						,uso: $("#id_uso option:selected").text()
						,categoria: $("#id_categoria option:selected").text()
						,fecha: fecha
						,productos: productos
					}
					,success: function(response) {
						if(response.success) {
							$('.modal.show').modal('hide');
							gotoLink(base_url(response.file_path));
							ISToast.fire({icon: response.icon, title: response.msg, customClass: response.icon});
							IS.init.dataTable['tbl-almacenes-productos'].ajax.reload();
							IS.init.dataTable['tbl-almacenes-productos-entrada'].ajax.reload(function(jsonData) {
							    IS.init.dataTable['tbl-almacenes-productos-entrada'].columns.adjust().draw();
							});

						} else ISswal.fire({icon: response.icon, title: response.title, text: response.msg, customClass: response.icon});
					}
				})

			} else ISswal.fire({icon: 'info', title: '¡Información!', text: 'Agregue al menos un producto', customClass: 'info'});
		}
	})

	.on('click', '#btn-actualizar-vale-entrada', function(e) {
		if ($('#modal-editar-vale-entrada form').valid()) {
			var table = IS.init.dataTable['modal-tbl-entrada-productos']
			var rows = table.rows().data();
			if(rows.length) {
				var fecha = moment($('#custom_fecha').val(), 'DD/MM/YYYY').format('YYYY-MM-DD');
				var productos = [];
				table.$('tr').each(function(key, tr) {
					productos.push($(tr).data());
				});

				var id_requisicion = $("#id_requisicion").val();
				$('#modal-editar-vale-entrada form').formAjaxSend({
					 url: base_url('ventas/almacenes/process_update_productos_entrada')
					,data: {
						 id_uso: $('#id_uso').val()
						,tipo_entrada: $("#id_ve_tipo_entrada option:selected").text()
						,vale_almacen: $("#id_vale_almacen option:selected").text()
						,requisicion: (id_requisicion ? $("#id_requisicion option:selected").text() : '')
						,uso: $("#id_uso option:selected").text()
						,categoria: $("#id_categoria option:selected").text()
						,fecha: fecha
						,productos: productos
					}
					,success: function(response) {
						if(response.success) {
							$('.modal.show').modal('hide');
							gotoLink(base_url(response.file_path));
							ISToast.fire({icon: response.icon, title: response.msg, customClass: response.icon});
							IS.init.dataTable['tbl-almacenes-productos'].ajax.reload();
							IS.init.dataTable['tbl-almacenes-productos-entrada'].ajax.reload(function(jsonData) {
							    IS.init.dataTable['tbl-almacenes-productos-entrada'].columns.adjust().draw();
							});

						} else ISswal.fire({icon: response.icon, title: response.title, text: response.msg, customClass: response.icon});
					}
				})

			} else ISswal.fire({icon: 'info', title: '¡Información!', text: 'Agregue al menos un producto', customClass: 'info'});
		}
	})

	.on('click', '#modal-nuevo-vale-entrada #modal-tbl-entrada-productos .btn-remove, #modal-editar-vale-entrada #modal-tbl-entrada-productos .btn-remove', function(e) {
		var tr = $(this).closest('tr');
		IS.init.dataTable['modal-tbl-entrada-productos'].row(tr).remove().draw();
	});

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

});