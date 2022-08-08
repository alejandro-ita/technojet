jQuery(function($) {
	$('#modal-vales-almacen-entrada form').validate();
	$('#modal-vales-estatus-entrada form').validate();

	initDataTable('#tbl-vales-entrada', {
		ajax: {
		 	 url: base_url('database/almacen/get_almacen_vales_entrada')
		 	,data: function(dataFilter) {
	    		dataFilter.id_categoria = $('select#id_categoria').val();
	    		dataFilter.tipo = 'ENTRADA';
	    	}
		}
		,createdRow: function(row, data, index) {
			data.acciones = undefined;
			$(row).addClass('nk-tb-item').data(data);
		}
		,columns: [
			 {data: 'id', defaultContent: '', className: 'nk-tb-col'}
			,{data: 'titulo', defaultContent: '', className: 'nk-tb-col'}
			,{data: 'acciones', defaultContent: '', className: 'nk-tb-col nk-tb-col-tools text-right'}
		]
	});

	$('body')

	.on('keyup', '.tools-tbl-almacen-vales #buscar', function(e) {
		IS.init.dataTable['tbl-vales-entrada'].search(this.value).draw();
	})

	.on('change', '.tools-tbl-almacen-vales select#id_categoria', function() {
		IS.init.dataTable['tbl-vales-entrada'].ajax.reload();
		(parseInt($(this).val())>0)
			? $('.tools-tbl-almacen-vales .add-titulo').elEnable()
			: $('.tools-tbl-almacen-vales .add-titulo').elDisable();
	})

	.on('click', '.tools-tbl-almacen-vales .almacen-vales_length a', function(e) {
		$(this).closest('ul').find('li').removeClass('active');
		$(this).parent('li').addClass('active');

		IS.init.dataTable['tbl-vales-entrada'].page.len($(this).data('length')).draw();

		e.preventDefault();
	})

	.on('click', '.tools-tbl-almacen-vales .add-titulo', function(e) {
		if ($('select#id_categoria').val()=='19') {
			$('#modal-vales-almacen-entrada button').attr('id', 'save-almacen');
			$('#modal-vales-almacen-entrada').modal('show');
		}

		if ($('select#id_categoria').val()=='20') {
			$('#modal-vales-estatus-entrada button').attr('id', 'save-estatus');
			$('#modal-vales-estatus-entrada').modal('show');
		}

		e.preventDefault();
	})

	.on('click', '#tbl-vales-entrada #open-modal-update', function(e) {
		var tr = $(this).closest('tr');
		tr.addClass('selected');
		var id_categoria = tr.data('id_categoria');

		if (id_categoria==19) {
			$('#modal-vales-almacen-entrada button').attr('id', 'update-almacen');
			$('#modal-vales-almacen-entrada input#almacen').val(tr.data('almacen'))
			$('#modal-vales-almacen-entrada').modal('show');
		}

		if (id_categoria==20) {
			$('#modal-vales-estatus-entrada button').attr('id', 'update-estatus');
			$('#modal-vales-estatus-entrada input#estatus').val(tr.data('estatus'))
			$('#modal-vales-estatus-entrada').modal('show');
		}
	})

	.on('click', '#tbl-vales-entrada #remove', function(e) {
		var tr = $(this).closest('tr');
		ISswal.fire({
		    title: lang('general_esta_seguro'),
		    text: lang('general_remove_row'),
		    icon: 'warning',
			showCancelButton: true,
		    confirmButtonText: lang('general_si_hazlo')
		}).then(function(result) {
		    if (result.value) {
				var id_categoria = tr.data('id_categoria');

				if (id_categoria==19) {
					var url = base_url('database/almacen/process_remove_almacen');
				}

				if (id_categoria==20) {
		    		var url = base_url('database/almacen/process_remove_estatus');
				}

				var data = tr.data();
				data.categoria = $('select#id_categoria option:selected').text();
				$.formAjaxSend({
		    		url: url,
		    		data: data,
		    		success: function(response) {
		    			if(response.success) {
							ISToast.fire({icon: response.icon, title: response.msg, customClass: response.icon});
							IS.init.dataTable['tbl-vales-entrada'].row(tr).remove().draw();

						} else ISswal.fire({icon: response.icon, title: response.title, text: response.msg, customClass: response.icon});
		    		}
		    	});
		    }
		});
		e.preventDefault();
	})

	.on('hidden.bs.modal', '#modal-vales-almacen-entrada, #modal-vales-estatus-entrada', function() {
		IS.init.dataTable['tbl-vales-entrada'].$('tr.selected').removeClass('selected');
		$('#modal-vales-almacen-entrada #almacen').val('');
		$('#modal-vales-estatus-entrada #estatus').val('');
	})

	//********CRUD ALMACENES ENTRANTES**************/
	.on('click', '#modal-vales-almacen-entrada #save-almacen', function(e) {
		if($('#modal-vales-almacen-entrada form').valid()) {
			$('#modal-vales-almacen-entrada form').formAjaxSend({
				 url: base_url('database/almacen/process_save_vales_almacen')
				,data: {
					 tipo: 'ENTRADA'
					,categoria: $('select#id_categoria option:selected').text()
				}
				,success: function(response) {
					if(response.success) {
						$('.modal.show').modal('hide');
						ISToast.fire({icon: response.icon, title: response.msg, customClass: response.icon});
						IS.init.dataTable['tbl-vales-entrada'].ajax.reload();

					} else ISswal.fire({icon: response.icon, title: response.title, text: response.msg, customClass: response.icon});
				}
			});
		}
		e.preventDefault();
	})

	.on('click', '#modal-vales-almacen-entrada #update-almacen', function(e) {
		if($('#modal-vales-almacen-entrada form').valid()) {
			var tr = IS.init.dataTable['tbl-vales-entrada'].$('tr.selected');
			$('#modal-vales-almacen-entrada form').formAjaxSend({
				 url: base_url('database/almacen/process_update_vales_almacen')
				,data: {
					 id_vale_almacen: tr.data('id_vale_almacen')
					,tipo: 'ENTRADA'
					,categoria: $('select#id_categoria option:selected').text()
				}
				,success: function(response) {
					if(response.success) {
						$('.modal.show').modal('hide');
						ISToast.fire({icon: response.icon, title: response.msg, customClass: response.icon});
						IS.init.dataTable['tbl-vales-entrada'].ajax.reload();

					} else ISswal.fire({icon: response.icon, title: response.title, text: response.msg, customClass: response.icon});
				}
			});
		}
		e.preventDefault();
	})



	//********CRUD ESTATUS**************/
	.on('click', '#modal-vales-estatus-entrada #save-estatus', function(e) {
		if($('#modal-vales-estatus-entrada form').valid()) {
			$('#modal-vales-estatus-entrada form').formAjaxSend({
				 url: base_url('database/almacen/process_save_vales_estatus')
				,data: {
					 tipo: 'ENTRADA'
					,categoria: $('select#id_categoria option:selected').text()
				}
				,success: function(response) {
					if(response.success) {
						$('.modal.show').modal('hide');
						ISToast.fire({icon: response.icon, title: response.msg, customClass: response.icon});
						IS.init.dataTable['tbl-vales-entrada'].ajax.reload();

					} else ISswal.fire({icon: response.icon, title: response.title, text: response.msg, customClass: response.icon});
				}
			});
		}
		e.preventDefault();
	})

	.on('click', '#modal-vales-estatus-entrada #update-estatus', function(e) {
		if($('#modal-vales-estatus-entrada form').valid()) {
			var tr = IS.init.dataTable['tbl-vales-entrada'].$('tr.selected');
			$('#modal-vales-estatus-entrada form').formAjaxSend({
				 url: base_url('database/almacen/process_update_vales_estatus')
				,data: {
					 id_vale_estatus: tr.data('id_vale_estatus')
					,tipo: 'ENTRADA'
					,categoria: $('select#id_categoria option:selected').text()
				}
				,success: function(response) {
					if(response.success) {
						$('.modal.show').modal('hide');
						ISToast.fire({icon: response.icon, title: response.msg, customClass: response.icon});
						IS.init.dataTable['tbl-vales-entrada'].ajax.reload();

					} else ISswal.fire({icon: response.icon, title: response.title, text: response.msg, customClass: response.icon});
				}
			});
		}
		e.preventDefault();
	})
});