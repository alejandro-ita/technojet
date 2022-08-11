jQuery(function($) {
	initDataTable('#tbl-vendedores', {
		ajax: {
		 	url: base_url('database/vendedores/get_vendedores'),
		 	data: function(dataFilter) {
	    		dataFilter.id_categoria = $('select#id_categoria').val();
	    	}
		},
		createdRow: function(row, data, index) {
			data.acciones = undefined;
			$(row).addClass('nk-tb-item').data(data);
		},
		columns: [
			{data: 'id_vendedor', defaultContent: '', className: 'nk-tb-col'},
			{data: 'vendedor', defaultContent: '', className: 'nk-tb-col'},
            {data: 'departamento', defaultContent: '', className: 'nk-tb-col'},
            {data: 'correo', defaultContent: '', className: 'nk-tb-col'},
            {data: 'comision', defaultContent: '', className: 'nk-tb-col'},
			{data: 'acciones', defaultContent: '', className: 'nk-tb-col nk-tb-col-tools text-right'}
		]
	});

    $('body')

    .on('click', '.tools-tbl-vendedores .add-vendedor', function(e) {
        $.formAjaxSend({
			 url: base_url('database/vendedores/get_modal_new_vendedor')
			,dataType: 'html'
			,success: function(modal) {
				$('#content-modals').html(modal);
				initModal('.modal', {
					onOpenEnd: function() {
						$('.modal#new-vendedor form').validate();
					}
				});
			}
		});
		e.preventDefault();
	})

    .on('click', '#content-modals #btn-save-vendedor', function(e) {
		if ($('.modal#new-vendedor form').valid()) {
			$('.modal#new-vendedor form').formAjaxSend({
				url: base_url('database/vendedores/process_save_vendedor'),
				success: function(response) {
					if(response.success) {
						$('.modal.show').modal('hide');
						ISToast.fire({icon: response.icon, title: response.msg, customClass: response.icon});
						IS.init.dataTable['tbl-vendedores'].ajax.reload();

					} else ISswal.fire({icon: response.icon, title: response.title, text: response.msg, customClass: response.icon});
				}
			})
		}
	})

    .on('click', '#tbl-vendedores #open-modal-update', function(e) {
		var tr = $(this).closest('tr');
		var btn= $(this);
		
		btn.tooltip('hide');
		tr.addClass('selected');
		$.formAjaxSend({
			url: base_url('database/vendedores/get_modal_update_vendedor'),
			data: tr.data(),
			dataType: 'html',
			success: function(modal) {
				$('#content-modals').html(modal);
				initModal('.modal', {
					onOpenEnd: function() {
						$('.modal#update-vendedor form').validate();
					},
					onCloseEnd: function() {
						tr.removeClass('selected');
					}
				});
			}
		})
		e.preventDefault();
	})

    .on('click', '#content-modals #btn-update-vendedor', function(e) {
		if ($('.modal#update-vendedor form').valid()) {
			var tr = IS.init.dataTable['tbl-vendedores'].$('tr.selected');
			$('.modal#update-vendedor form').formAjaxSend({
				url: base_url('database/vendedores/process_update_vendedor'),
				data: {
					id_vendedor: tr.data('id_vendedor'),
				},
				success: function(response) {
					if(response.success) {
						$('.modal.show').modal('hide');
						ISToast.fire({icon: response.icon, title: response.msg, customClass: response.icon});
						IS.init.dataTable['tbl-vendedores'].ajax.reload();

					} else ISswal.fire({icon: response.icon, title: response.title, text: response.msg, customClass: response.icon});
				}
			})
		}
	})

    .on('click', '#tbl-vendedores #remove', function(e) {
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
		    	$.formAjaxSend({
		    		url: base_url('database/vendedores/process_remove_vendedor'),
		    		data: data,
		    		success: function(response) {
		    			if(response.success) {
							ISToast.fire({icon: response.icon, title: response.msg, customClass: response.icon});
							IS.init.dataTable['tbl-vendedores'].row(tr).remove().draw();

						} else ISswal.fire({icon: response.icon, title: response.title, text: response.msg, customClass: response.icon});
		    		}
		    	});
		    }
		});
	});
});