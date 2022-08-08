jQuery(function($) {
	initDataTable('#tbl-almacen-requisiciones', {
		ajax: {
		 	 url: base_url('database/almacen/get_requisiciones')
		 	,data: function(dataFilter) {
	    		dataFilter.id_categoria = $('select#id_categoria').val();
	    	}
		}
		,createdRow: function(row, data, index) {
			data.acciones = undefined;
			$(row).addClass('nk-tb-item').data(data);
		}
		,columns: [
			 {data: 'id_almacen_requisicion', defaultContent: '', className: 'nk-tb-col'}
			,{data: 'categoria', defaultContent: '', className: 'nk-tb-col'}
			,{data: 'requisicion', defaultContent: '', className: 'nk-tb-col'}
			,{data: 'acciones', defaultContent: '', className: 'nk-tb-col nk-tb-col-tools text-right'}
		]
	});

	$('body')

	.on('keyup', '.tools-tbl-requisiciones #buscar', function(e) {
		IS.init.dataTable['tbl-almacen-requisiciones'].search(this.value).draw();
	})

	.on('change', '.tools-tbl-requisiciones select#id_categoria', function() {
		IS.init.dataTable['tbl-almacen-requisiciones'].ajax.reload();
		(parseInt($(this).val())>0)
			? $('.tools-tbl-requisiciones .add-requisicion').elEnable()
			: $('.tools-tbl-requisiciones .add-requisicion').elDisable();
	})

	.on('click', '.tools-tbl-requisiciones .requisiciones_length a', function(e) {
		$(this).closest('ul').find('li').removeClass('active');
		$(this).parent('li').addClass('active');

		IS.init.dataTable['tbl-almacen-requisiciones'].page.len($(this).data('length')).draw();

		e.preventDefault();
	})

	.on('click', '.tools-tbl-requisiciones .add-requisicion', function(e) {
		$.formAjaxSend({
			 url: base_url('database/almacen/get_modal_new_requisicion')
			,dataType: 'html'
			,success: function(modal) {
				$('#content-modals').html(modal);
				initModal('.modal', {
					onOpenEnd: function() {
						$('.modal#new-requisicion form').validate();
					}
				});
			}
		});
		e.preventDefault();
	})

	.on('click', '#tbl-almacen-requisiciones #open-modal-update', function(e) {
		var tr = $(this).closest('tr');
		var btn= $(this);
		
		btn.tooltip('hide');
		tr.addClass('selected');
		$.formAjaxSend({
			 url: base_url('database/almacen/get_modal_update_requisicion')
			,data: tr.data()
			,dataType: 'html'
			,success: function(modal) {
				$('#content-modals').html(modal);
				initModal('.modal', {
					onOpenEnd: function() {
						$('.modal#update-requisicion form').validate();
					},
					onCloseEnd: function() {
						tr.removeClass('selected');
					}
				});
			}
		})
		e.preventDefault();
	})

	.on('click', '#content-modals #btn-save-requisicion', function(e) {
		if ($('.modal#new-requisicion form').valid()) {
			$('.modal#new-requisicion form').formAjaxSend({
				 url: base_url('database/almacen/process_save_requisicion')
				,data: {
					 id_categoria: $('select#id_categoria').val()
					,categoria: $('select#id_categoria option:selected').text()
				}
				,success: function(response) {
					if(response.success) {
						$('.modal.show').modal('hide');
						ISToast.fire({icon: response.icon, title: response.msg, customClass: response.icon});
						IS.init.dataTable['tbl-almacen-requisiciones'].ajax.reload();

					} else ISswal.fire({icon: response.icon, title: response.title, text: response.msg, customClass: response.icon});
				}
			})
		}
	})

	.on('click', '#content-modals #btn-update-requisicion', function(e) {
		if ($('.modal#update-requisicion form').valid()) {
			var tr = IS.init.dataTable['tbl-almacen-requisiciones'].$('tr.selected');
			$('.modal#update-requisicion form').formAjaxSend({
				 url: base_url('database/almacen/process_update_requisicion')
				,data: {
					 id_almacen_requisicion: tr.data('id_almacen_requisicion')
					,id_categoria: $('select#id_categoria').val()
					,categoria: $('select#id_categoria option:selected').text()
				}
				,success: function(response) {
					if(response.success) {
						$('.modal.show').modal('hide');
						ISToast.fire({icon: response.icon, title: response.msg, customClass: response.icon});
						IS.init.dataTable['tbl-almacen-requisiciones'].ajax.reload();

					} else ISswal.fire({icon: response.icon, title: response.title, text: response.msg, customClass: response.icon});
				}
			})
		}
	})

	.on('click', '#tbl-almacen-requisiciones #remove', function(e) {
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
		    		url: base_url('database/almacen/process_remove_requisicion'),
		    		data: data,
		    		success: function(response) {
		    			if(response.success) {
							ISToast.fire({icon: response.icon, title: response.msg, customClass: response.icon});
							IS.init.dataTable['tbl-almacen-requisiciones'].row(tr).remove().draw();

						} else ISswal.fire({icon: response.icon, title: response.title, text: response.msg, customClass: response.icon});
		    		}
		    	});
		    }
		});
	});
});