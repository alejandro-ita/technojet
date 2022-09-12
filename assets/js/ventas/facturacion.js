jQuery(function($) {
	initDataTable('#tbl-facturas', {
		
		ajax: {
			url: base_url('ventas/ventas/get_facturas'),
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
				_: 'folio', sort: 'id_factura'
		   	}, defaultContent: '', className: 'nk-tb-col'},
			{data: 'estatus_factura', defaultContent: '', className: 'nk-tb-col'},
			{data: 'fecha_elaboracion', defaultContent: '', className: 'nk-tb-col'},
			{data: 'uso_cfdi', defaultContent: '', className: 'nk-tb-col'},
			{data: 'metodo_pago', defaultContent: '', className: 'nk-tb-col'},
			{data: 'forma_pago', defaultContent: '', className: 'nk-tb-col'},
			{data: 'no_pi', defaultContent: '', className: 'nk-tb-col'},
			{data: 'cliente', defaultContent: '', className: 'nk-tb-col'},
			{data: 'subtotal', defaultContent: '', className: 'nk-tb-col'},
			{data: 'descuento', defaultContent: '', className: 'nk-tb-col'},
			{data: 'iva', defaultContent: '', className: 'nk-tb-col'},
			{data: 'total', defaultContent: '', className: 'nk-tb-col'},
			{data: 'moneda', defaultContent: '', className: 'nk-tb-col'},
			{data: 'concepto', defaultContent: '', className: 'nk-tb-col'},
			{data: 'estatus_entrega', defaultContent: '', className: 'nk-tb-col'},
			{data: 'semana', defaultContent: '', className: 'nk-tb-col'},
			{data: 'mes', defaultContent: '', className: 'nk-tb-col'},
			{data: 'anio', defaultContent: '', className: 'nk-tb-col'},
			{data: 'acciones', defaultContent: '', className: 'nk-tb-col nk-tb-col-tools text-right'},
		]
	});

	$('body')

	.on('keyup', '.tools-tbl-facturas #buscar', function(e) {
		IS.init.dataTable['tbl-facturas'].search(this.value).draw();
	})

	.on('click', '.tools-tbl-facturas .facturas_length a', function(e) {
		$(this).closest('ul').find('li').removeClass('active');
		$(this).parent('li').addClass('active');

		IS.init.dataTable['tbl-facturas'].page.len($(this).data('length')).draw();

		e.preventDefault();
	})

	.on('click' , '.tools-tbl-facturas .add-factura', function(e) {
		$(this).tooltip('hide');
		$('#form-filtro').formAjaxSend({
			url: base_url('ventas/ventas/get_modal_add_registro_facturacion'),
			dataType: 'html',
			success: function(modal) {
				$('#content-modals').html(modal);
				initModal('#modal-nueva-factura', {
					onOpenEnd: function() {
						initSelect2('.modal select');
						$('#modal-nueva-factura form').validate();
					}
				});
			}
		});
		e.preventDefault();
	})

	.on('click', '#btn-save-factura', function(e) {
		if ($('#modal-nueva-factura form').valid()) {
			$('#modal-nueva-factura form').formAjaxSend({
				url: base_url('ventas/ventas/process_save_factura'),
				data: {
					//DATA EXTRA
				},
				success: function(response) {
					if(response.success) {
						$('.modal.show').modal('hide');
						//gotoLink(base_url(response.file_path));
						ISToast.fire({icon: response.icon, title: response.msg, customClass: response.icon});
						IS.init.dataTable['tbl-facturas'].ajax.reload(function(jsonData) {
							IS.init.dataTable['tbl-facturas'].columns.adjust().draw();
						});

					} else ISswal.fire({icon: response.icon, title: response.title, text: response.msg, customClass: response.icon});
				}
			})
		}
	})

	.on('click', '#tbl-facturas button#open-modal-update', function(e) {
		$(this).tooltip('hide');
		var tr = $(this).closest('tr');
		$.formAjaxSend({
			url: base_url('ventas/ventas/get_modal_edit_factura'),
			data: tr.data(),
			dataType: 'html',
			success: function(modal) {
				$('#content-modals').html(modal);
				initModal('#modal-editar-factura', {
					onOpenEnd: function() {
						initSelect2('.modal select');
						$('#modal-editar-factura form').validate();
					}
				});
			}
		});
		e.preventDefault();
	})

	.on('click', '#btn-update-factura', function(e) {
		if ($('#modal-editar-factura form').valid()) {
			$('#modal-editar-factura form').formAjaxSend({
				url: base_url('ventas/ventas/process_update_factura'),
				data: {
					//DATOS EXTRAS
				},
				success: function(response) {
					if(response.success) {
						$('.modal.show').modal('hide');
						console.log(response);
						//gotoLink(base_url(response.file_path));
						ISToast.fire({icon: response.icon, title: response.msg, customClass: response.icon});
						IS.init.dataTable['tbl-facturas'].ajax.reload(function(jsonData) {
						    IS.init.dataTable['tbl-facturas'].columns.adjust().draw();
						});
					} else ISswal.fire({icon: response.icon, title: response.title, text: response.msg, customClass: response.icon});
				}
			})
		}
	})

	.on('click', '#tbl-facturas button#remove', function(e) {
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
		    		url: base_url('ventas/ventas/process_remove_factura'),
		    		data: data,
		    		success: function(response) {
		    			if(response.success) {
							ISToast.fire({icon: response.icon, title: response.msg, customClass: response.icon});
							IS.init.dataTable['tbl-facturas'].ajax.reload();

						} else ISswal.fire({icon: response.icon, title: response.title, text: response.msg, customClass: response.icon});
		    		}
		    	});
		    }
		});
	})

	.on('change', '#modal-nueva-factura #subtotal, #modal-editar-factura #subtotal', function(e){
		
		if($("#subtotal").val() != '' && $("#descuento").val() != '' ){
			calculaTotal();
		}
		
	})

	.on('change', '#modal-nueva-factura #descuento, #modal-editar-factura #descuento', function(e){
		
		if($("#subtotal").val() != '' && $("#descuento").val() != '' ){
			calculaTotal();
		}
	})

	function calculaTotal(){
		subtotal = $("#subtotal").val();
		descuento = subtotal * $("#descuento").val() / 100;
		iva = (subtotal - descuento) * 0.16;
		total = (subtotal - descuento) * 1.16;
		$("#iva").val(iva.toFixed(2));
		$("#total").val(total.toFixed(2));
		//console.log("sub: " + subtotal + " desc: " + descuento + " IVA: " + iva + " total: " + total);
	}

});