jQuery(function($) {
	initDataTable('#tbl-ventas-productos', {
		ajax: {
		 	 url: base_url('database/ventas/get_productos')
		 	,data: function(dataFilter) {
	    		dataFilter.id_categoria = $('select#id_categoria').val();
	    	}
		}
		,createdRow: function(row, data, index) {
			data.acciones = undefined;
			$(row).addClass('nk-tb-item').data(data);
		}
		,columns: [
			 {data: 'id_producto', defaultContent: '', className: 'nk-tb-col'}
			,{data: 'categoria', defaultContent: '', className: 'nk-tb-col'}
			,{data: 'no_parte', defaultContent: '', className: 'nk-tb-col'}
			,{data: 'tipo_producto', defaultContent: '', className: 'nk-tb-col'}
			,{data: 'custom_unidad_medida', defaultContent: '', className: 'nk-tb-col'}
			,{data: 'descripcion', defaultContent: '', className: 'nk-tb-col'}
			,{data: 'precio_inventario', defaultContent: '', className: 'nk-tb-col'}
			,{data: 'custom_moneda', defaultContent: '', className: 'nk-tb-col'}
			,{data: 'stock_min', defaultContent: '', className: 'nk-tb-col'}
			,{data: 'stock_max', defaultContent: '', className: 'nk-tb-col'}
			,{data: 'piezas_iniciales', defaultContent: '', className: 'nk-tb-col'}
			,{data: 'acciones', defaultContent: '', className: 'nk-tb-col nk-tb-col-tools text-right'}
		]
	});

	$('body')

	.on('keyup', '.tools-tbl-productos #buscar', function(e) {
		IS.init.dataTable['tbl-ventas-productos'].search(this.value).draw();
	})

	.on('change', '.tools-tbl-productos select#id_categoria', function() {
		IS.init.dataTable['tbl-ventas-productos'].ajax.reload();
		(parseInt($(this).val())>0)
			? $('.tools-tbl-productos .add-producto').elEnable()
			: $('.tools-tbl-productos .add-producto').elDisable();
	})

	.on('click', '.tools-tbl-productos .producto_length a', function(e) {
		$(this).closest('ul').find('li').removeClass('active');
		$(this).parent('li').addClass('active');

		IS.init.dataTable['tbl-ventas-productos'].page.len($(this).data('length')).draw();

		e.preventDefault();
	})

	.on('click', '.tools-tbl-productos .add-producto', function(e) {
		$.formAjaxSend({
			 url: base_url('database/ventas/get_modal_new_producto')
			,dataType: 'html'
			,success: function(modal) {
				$('#content-modals').html(modal);
				initModal('.modal', {
					onOpenEnd: function() {
						initSelect2('.modal select', {
							dropdownParent: $('#content-modals .modal')
						});
						$('.modal#new-producto form').validate();
					}
				});
			}
		})
		e.preventDefault();
	})

	.on('click', '#tbl-ventas-productos #open-modal-update', function(e) {
		var tr = $(this).closest('tr');
		var btn= $(this);
		
		btn.tooltip('hide');
		tr.addClass('selected');
		$.formAjaxSend({
			 url: base_url('database/ventas/get_modal_update_producto')
			,data: tr.data()
			,dataType: 'html'
			,success: function(modal) {
				$('#content-modals').html(modal);
				initModal('.modal', {
					onOpenEnd: function() {
						initSelect2('.modal select', {
							dropdownParent: $('#content-modals .modal')
						});
						$('.modal#update-producto form').validate();
					},
					onCloseEnd: function() {
						tr.removeClass('selected');
					}
				});
			}
		})
		e.preventDefault();
	})

	.on('click', '#content-modals #btn-save-producto', function(e) {
		if ($('.modal#new-producto form').valid()) {
			$('.modal#new-producto form').formAjaxSend({
				 url: base_url('database/ventas/process_save_producto')
				,data: {
					 id_categoria: $('select#id_categoria').val()
					,categoria: $('select#id_categoria option:selected').text()
				}
				,success: function(response) {
					if(response.success) {
						$('.modal.show').modal('hide');
						ISToast.fire({icon: response.icon, title: response.msg, customClass: response.icon});
						IS.init.dataTable['tbl-ventas-productos'].ajax.reload();

					} else ISswal.fire({icon: response.icon, title: response.title, text: response.msg, customClass: response.icon});
				}
			})
		}
	})

	.on('click', '#content-modals #btn-update-producto', function(e) {
		if ($('.modal#update-producto form').valid()) {
			var tr = IS.init.dataTable['tbl-ventas-productos'].$('tr.selected');
			$('.modal#update-producto form').formAjaxSend({
				 url: base_url('database/ventas/process_update_producto')
				,data: {
					 id_producto: tr.data('id_producto')
					,id_categoria: $('select#id_categoria').val()
					,categoria: $('select#id_categoria option:selected').text()
				}
				,success: function(response) {
					if(response.success) {
						$('.modal.show').modal('hide');
						ISToast.fire({icon: response.icon, title: response.msg, customClass: response.icon});
						IS.init.dataTable['tbl-ventas-productos'].ajax.reload();

					} else ISswal.fire({icon: response.icon, title: response.title, text: response.msg, customClass: response.icon});
				}
			})
		}
	})

	.on('click', '#tbl-ventas-productos #remove', function(e) {
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
		    		url: base_url('database/ventas/process_remove_producto'),
		    		data: data,
		    		success: function(response) {
		    			if(response.success) {
							ISToast.fire({icon: response.icon, title: response.msg, customClass: response.icon});
							IS.init.dataTable['tbl-ventas-productos'].row(tr).remove().draw();

						} else ISswal.fire({icon: response.icon, title: response.title, text: response.msg, customClass: response.icon});
		    		}
		    	});
		    }
		});
	});
});