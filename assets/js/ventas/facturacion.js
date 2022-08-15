jQuery(function($) {
	$('body')
	.on('click' , '.add-registro', function(e) {
		$('#form-filtro').formAjaxSend({
			url: base_url('ventas/ventas/get_modal_add_registro_facturacion'),
			dataType: 'html',
			success: function(modal) {
				$('#content-modals').html(modal);
				initModal('#modal-nuevo-registro', {
					onOpenEnd: function() {
						initSelect2('.modal select');
						// $('#modal-add-producto-entrada').validate();
						// init_tbl_entrada_nuevos_productos();
					},
					onCloseEnd: function() {
						$('#modal-nuevo-registro').remove();
						$('.modal-backdrop').remove();
					},
				});
			}
		});
		e.preventDefault();
	})
	.on('click', '.close-registro',function(e){
		$('#modal-nuevo-registro').remove();
		$('.modal-backdrop').remove();
	})
	
	// .on('click', '#modal-tbl-entrada-productos_filter .btn-add', function(e) {

	// 	$('#form-filtro').formAjaxSend({
	// 		url: base_url('ventas/ventas/get_modal_add_factura_product'),
	// 		dataType: 'html',
	// 		success: function(modal) {
	// 			$('#content-modals').append(modal);
	// 			initModal('#modal-add-producto-entrada', {
	// 				onOpenEnd: function() {
	// 					initSelect2('.modal select');
	// 				},
	// 				onCloseEnd: function() {
	// 					$('#modal-add-producto-entrada').remove();
	// 					$('.modal-backdrop').remove();
	// 				},
	// 			});
	// 		}
	// 	});
	// 	e.preventDefault();
	// })
	// .on('click', '#close-factura-product',function(e){
	// 	$('#modal-add-producto-entrada').remove();
	// })

	

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