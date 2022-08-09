jQuery(function($) {
	$('#form-filtro').validate({
		ignore: '.col-sm-12.d-none select'
	});
	initDataTable('#tbl-almacenes-productos', {
		
		ajax: {
			url: base_url('ventas/almacenes/get_productos_almacenes')
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
			 {data: 'no_parte', defaultContent: '', className: 'nk-tb-col'}
			,{data: 'tipo_producto', defaultContent: '', className: 'nk-tb-col'}
			,{data: 'unidad_medida', defaultContent: '', className: 'nk-tb-col'}
			,{data: 'descripcion', defaultContent: '', className: 'nk-tb-col'}
			,{data: function(data) {
				return ($('select#id_uso').val()==2? 0 : data.piezas_iniciales);
			}, defaultContent: '', className: 'nk-tb-col'}
			,{data: 'entradas', defaultContent: '', className: 'nk-tb-col'}
			,{data: 'salidas', defaultContent: '', className: 'nk-tb-col'}
			,{data: 'total_piezas', defaultContent: '', className: 'nk-tb-col'}
			,{data: 'costo', defaultContent: '', className: 'nk-tb-col'}
			,{data: 'moneda', defaultContent: '', className: 'nk-tb-col'}
			,{data: 'acciones', defaultContent: '', className: 'nk-tb-col nk-tb-col-tools text-right'}
		]
	});

	$('body')

	.on('keyup', '.tools-tbl-almacen #buscar', function(e) {
		IS.init.dataTable['tbl-almacenes-productos'].search(this.value).draw();
	})

	.on('click', '.tools-tbl-almacen .almacen_length a', function(e) {
		$(this).closest('ul').find('li').removeClass('active');
		$(this).parent('li').addClass('active');

		IS.init.dataTable['tbl-almacenes-productos'].page.len($(this).data('length')).draw();

		e.preventDefault();
	})

	.on('change', '#id_uso', function(e) {
		var id_uso = $('#id_uso').val();
		var $select = $('#id_categoria');
		$select.find('option:not(:first)').remove();
		if (id_uso != '5') { //NO ES ACTIVOS
			$select.closest('.col-sm-12').removeClass('d-none');
			var title = lang('almacenes_total_piezas');
			$.formAjaxSend({
				 url: base_url('technojet/catalogos/get_almacenes_categorias')
				,data: {id_uso: $('#id_uso').val()}
				,blockScreen: false
				,success: function(response) {
					$.each(response, function(key, data) {
						var newOption = new Option(data['categoria'], data['id_categoria'], false, false);
						$select.append(newOption);
					});
				}
			});
		} else {
			$select.closest('.col-sm-12').addClass('d-none');
			var title = lang('almacenes_total_piezas_activos');
		}

		$(IS.init.dataTable['tbl-almacenes-productos'].column(7).header()).text(title);
	})

	.on('click' , '.add-entrada', function(e) {
		$('#form-filtro').formAjaxSend({
			url: base_url('ventas/ventas/get_modal_add_cotizacion'),
			dataType: 'html',
			success: function(modal) {
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

	.on('click', 'button#load-productos', function(e) {
		if ($('#form-filtro').valid()) {
			IS.init.dataTable['tbl-almacenes-productos'].ajax.reload();
			IS.init.dataTable['tbl-almacenes-productos-entrada'].ajax.reload(function(jsonData) {
			    IS.init.dataTable['tbl-almacenes-productos-entrada'].columns.adjust().draw();
			});
			IS.init.dataTable['tbl-almacenes-productos-salida'].ajax.reload(function(jsonData) {
			    IS.init.dataTable['tbl-almacenes-productos-salida'].columns.adjust().draw();
			});
		}
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


});