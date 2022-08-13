jQuery(function($) {
	$('#modal-vales-almacen-salida form').validate();
	$('#modal-vales-estatus-salida form').validate();

	initDataTable('#tbl-vales-salida', {
		ajax: {
		 	 url: base_url('database/almacen/get_almacen_vales_salida')
		 	,data: function(dataFilter) {
	    		dataFilter.id_categoria = $('select#id_categoria').val();
	    		dataFilter.tipo = 'SALIDA';
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
		IS.init.dataTable['tbl-vales-salida'].search(this.value).draw();
	})

	.on('change', '.tools-tbl-almacen-vales select#id_categoria', function() {
		IS.init.dataTable['tbl-vales-salida'].ajax.reload();
		(parseInt($(this).val())>0)
			? $('.tools-tbl-almacen-vales .add-titulo').elEnable()
			: $('.tools-tbl-almacen-vales .add-titulo').elDisable();
	})

	.on('click', '.tools-tbl-almacen-vales .almacen-vales_length a', function(e) {
		$(this).closest('ul').find('li').removeClass('active');
		$(this).parent('li').addClass('active');

		IS.init.dataTable['tbl-vales-salida'].page.len($(this).data('length')).draw();

		e.preventDefault();
	})

	.on('click', '.tools-tbl-almacen-vales .add-titulo', function(e) {
		if ($('select#id_categoria').val()=='21') {
			$('#modal-vales-almacen-salida button').attr('id', 'save-almacen');
			$('#modal-vales-almacen-salida').modal('show');
		}

		if ($('select#id_categoria').val()=='22') {
			$('#modal-vales-estatus-salida button').attr('id', 'save-estatus');
			$('#modal-vales-estatus-salida').modal('show');
		}

		e.preventDefault();
	})

	.on('click', '#tbl-vales-salida #open-modal-update', function(e) {
		var tr = $(this).closest('tr');
		tr.addClass('selected');
		var id_categoria = tr.data('id_categoria');

		if (id_categoria==21) {
			$('#modal-vales-almacen-salida button').attr('id', 'update-almacen');
			$('#modal-vales-almacen-salida input#almacen').val(tr.data('almacen'))
			$('#modal-vales-almacen-salida').modal('show');
		}

		if (id_categoria==22) {
			$('#modal-vales-estatus-salida button').attr('id', 'update-estatus');
			$('#modal-vales-estatus-salida input#estatus').val(tr.data('estatus'))
			$('#modal-vales-estatus-salida').modal('show');
		}
	})

	.on('click', '#tbl-vales-salida #remove', function(e) {
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

				if (id_categoria==21) {
					var url = base_url('database/almacen/process_remove_almacen');
				}

				if (id_categoria==22) {
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
							IS.init.dataTable['tbl-vales-salida'].row(tr).remove().draw();

						} else ISswal.fire({icon: response.icon, title: response.title, text: response.msg, customClass: response.icon});
		    		}
		    	});
		    }
		});
		e.preventDefault();
	})

	.on('hidden.bs.modal', '#modal-vales-almacen-salida, #modal-vales-estatus-salida', function() {
		IS.init.dataTable['tbl-vales-salida'].$('tr.selected').removeClass('selected');
		$('#modal-vales-almacen-salida #almacen').val('');
		$('#modal-vales-estatus-salida #estatus').val('');
	})

	//********CRUD ALMACENES ENTRANTES**************/
	.on('click', '#modal-vales-almacen-salida #save-almacen', function(e) {
		if($('#modal-vales-almacen-salida form').valid()) {
			$('#modal-vales-almacen-salida form').formAjaxSend({
				 url: base_url('database/almacen/process_save_vales_almacen')
				,data: {
					 tipo: 'SALIDA'
					,categoria: $('select#id_categoria option:selected').text()
				}
				,success: function(response) {
					if(response.success) {
						$('.modal.show').modal('hide');
						ISToast.fire({icon: response.icon, title: response.msg, customClass: response.icon});
						//IS.init.dataTable['tbl-vales-salida-almacen'].ajax.reload();
						IS.init.dataTable['tbl-vales-salida'].ajax.reload();

					} else ISswal.fire({icon: response.icon, title: response.title, text: response.msg, customClass: response.icon});
				}
			});
		}
		e.preventDefault();
	})

	.on('click', '#modal-vales-almacen-salida #update-almacen', function(e) {
		if($('#modal-vales-almacen-salida form').valid()) {
			var tr = IS.init.dataTable['tbl-vales-salida'].$('tr.selected');
			$('#modal-vales-almacen-salida form').formAjaxSend({
				 url: base_url('database/almacen/process_update_vales_almacen')
				,data: {
					 id_vale_almacen: tr.data('id_vale_almacen')
					,tipo: 'SALIDA'
					,categoria: $('select#id_categoria option:selected').text()
				}
				,success: function(response) {
					if(response.success) {
						$('.modal.show').modal('hide');
						ISToast.fire({icon: response.icon, title: response.msg, customClass: response.icon});
						IS.init.dataTable['tbl-vales-salida'].ajax.reload();

					} else ISswal.fire({icon: response.icon, title: response.title, text: response.msg, customClass: response.icon});
				}
			});
		}
		e.preventDefault();
	})



	//********CRUD ESTATUS**************/
	.on('click', '#modal-vales-estatus-salida #save-estatus', function(e) {
		if($('#modal-vales-estatus-salida form').valid()) {
			$('#modal-vales-estatus-salida form').formAjaxSend({
				 url: base_url('database/almacen/process_save_vales_estatus')
				,data: {
					 tipo: 'SALIDA'
					,categoria: $('select#id_categoria option:selected').text()
				}
				,success: function(response) {
					if(response.success) {
						$('.modal.show').modal('hide');
						ISToast.fire({icon: response.icon, title: response.msg, customClass: response.icon});
						IS.init.dataTable['tbl-vales-salida'].ajax.reload();

					} else ISswal.fire({icon: response.icon, title: response.title, text: response.msg, customClass: response.icon});
				}
			});
		}
		e.preventDefault();
	})

	.on('click', '#modal-vales-estatus-salida #update-estatus', function(e) {
		if($('#modal-vales-estatus-salida form').valid()) {
			var tr = IS.init.dataTable['tbl-vales-salida'].$('tr.selected');
			$('#modal-vales-estatus-salida form').formAjaxSend({
				 url: base_url('database/almacen/process_update_vales_estatus')
				,data: {
					 id_vale_estatus: tr.data('id_vale_estatus')
					,tipo: 'SALIDA'
					,categoria: $('select#id_categoria option:selected').text()
				}
				,success: function(response) {
					if(response.success) {
						$('.modal.show').modal('hide');
						ISToast.fire({icon: response.icon, title: response.msg, customClass: response.icon});
						IS.init.dataTable['tbl-vales-salida'].ajax.reload();

					} else ISswal.fire({icon: response.icon, title: response.title, text: response.msg, customClass: response.icon});
				}
			});
		}
		e.preventDefault();
	})
});