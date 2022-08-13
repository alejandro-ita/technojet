jQuery(function($) {
	$('#modal-vales-almacen-salida form').validate();
	$('#modal-vales-estatus-salida form').validate();
	var tblDOM = `
			<"row justify-between g-2"
	        	<"col-5 col-sm-4" <"datatable-filter" l>>
				<"col-7 col-sm-8 d-flex justify-content-end"f>
	        >
			<"datatable-wrap my-3"t>
			<"row align-items-center"
				<"col-5 col-sm-12 col-md-6 text-left text-md-left"i>
				<"col-7 col-sm-12 col-md-6"p>
			>`;

	initDataTable('#tbl-vales-salida-almacen', {
		dom: tblDOM,
		ajax: {
		 	 url: base_url('database/almacen/get_vales_almacen')
		 	,data: function(dataFilter) {
	    		dataFilter.tipo = 'SALIDA';
	    	}
		}
		,language: {
			sLengthMenu: "<div class='form-control-select'> _MENU_ </div>"
		}
		,createdRow: function(row, data, index) {
			data.acciones = undefined;
			$(row).addClass('nk-tb-item').data(data);
		}
		,columns: [
			 {data: 'id_vale_almacen', defaultContent: '', className: 'nk-tb-col'}
			,{data: 'almacen', defaultContent: '', className: 'nk-tb-col'}
			,{data: 'acciones', defaultContent: '', className: 'nk-tb-col nk-tb-col-tools text-right'}
		]
	});

	initDataTable('#tbl-vales-salida-estatus', {
		dom: tblDOM,
		ajax: {
		 	 url: base_url('database/almacen/get_vales_estatus')
		 	,data: function(dataFilter) {
	    		dataFilter.tipo = 'SALIDA';
	    	}
		}
		,language: {
			sLengthMenu: "<div class='form-control-select'> _MENU_ </div>"
		}
		,createdRow: function(row, data, index) {
			data.acciones = undefined;
			$(row).addClass('nk-tb-item').data(data);
		}
		,columns: [
			 {data: 'id_vale_estatus', defaultContent: '', className: 'nk-tb-col'}
			,{data: 'estatus', defaultContent: '', className: 'nk-tb-col'}
			,{data: 'acciones', defaultContent: '', className: 'nk-tb-col nk-tb-col-tools text-right'}
		]
	});

	$('body')

	.on('hidden.bs.modal', '#modal-vales-almacen-salida, #modal-vales-estatus-salida', function() {
		IS.init.dataTable['tbl-vales-salida-almacen'].$('tr.selected').removeClass('selected');
		IS.init.dataTable['tbl-vales-salida-estatus'].$('tr.selected').removeClass('selected');
		$('#modal-vales-almacen-salida #almacen').val('');
		$('#modal-vales-estatus-salida #estatus').val('');
	})

	//********CRUD ALMACENES SALIENTES**************/
	.on('click', '#tbl-vales-salida-almacen_filter .btn-add', function() {
		$('#modal-vales-almacen-salida button').attr('id', 'save-almacen');
		$('#modal-vales-almacen-salida').modal('show');
	})

	.on('click', '#modal-vales-almacen-salida #save-almacen', function(e) {
		if($('#modal-vales-almacen-salida form').valid()) {
			$('#modal-vales-almacen-salida form').formAjaxSend({
				 url: base_url('database/almacen/process_save_vales_almacen')
				,data: {tipo: 'SALIDA'}
				,success: function(response) {
					if(response.success) {
						$('.modal.show').modal('hide');
						ISToast.fire({icon: response.icon, title: response.msg, customClass: response.icon});
						IS.init.dataTable['tbl-vales-salida-almacen'].ajax.reload();

					} else ISswal.fire({icon: response.icon, title: response.title, text: response.msg, customClass: response.icon});
				}
			});
		}
		e.preventDefault();
	})

	.on('click', '#tbl-vales-salida-almacen #open-modal-update', function() {
		var tr = $(this).closest('tr');
		tr.addClass('selected');
		$('#modal-vales-almacen-salida button').attr('id', 'update-almacen');
		$('#modal-vales-almacen-salida input#almacen').val(tr.data('almacen'))
		$('#modal-vales-almacen-salida').modal('show');
	})

	.on('click', '#modal-vales-almacen-salida #update-almacen', function(e) {
		if($('#modal-vales-almacen-salida form').valid()) {
			var tr = IS.init.dataTable['tbl-vales-salida-almacen'].$('tr.selected');
			$('#modal-vales-almacen-salida form').formAjaxSend({
				 url: base_url('database/almacen/process_update_vales_almacen')
				,data: {
					 id_vale_almacen: tr.data('id_vale_almacen')
					,tipo: 'SALIDA'
				}
				,success: function(response) {
					if(response.success) {
						$('.modal.show').modal('hide');
						ISToast.fire({icon: response.icon, title: response.msg, customClass: response.icon});
						IS.init.dataTable['tbl-vales-salida-almacen'].ajax.reload();

					} else ISswal.fire({icon: response.icon, title: response.title, text: response.msg, customClass: response.icon});
				}
			});
		}
		e.preventDefault();
	})

	.on('click', '#tbl-vales-salida-almacen #remove', function(e) {
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
		    		url: base_url('database/almacen/process_remove_almacen'),
		    		data: tr.data(),
		    		success: function(response) {
		    			if(response.success) {
							ISToast.fire({icon: response.icon, title: response.msg, customClass: response.icon});
							IS.init.dataTable['tbl-vales-salida-almacen'].row(tr).remove().draw();

						} else ISswal.fire({icon: response.icon, title: response.title, text: response.msg, customClass: response.icon});
		    		}
		    	});
		    }
		});
		e.preventDefault();
	})

	//********CRUD ESTATUS**************/
	.on('click', '#tbl-vales-salida-estatus_filter .btn-add', function() {
		$('#modal-vales-estatus-salida button').attr('id', 'save-estatus');
		$('#modal-vales-estatus-salida').modal('show');
	})

	.on('click', '#modal-vales-estatus-salida #save-estatus', function(e) {
		if($('#modal-vales-estatus-salida form').valid()) {
			$('#modal-vales-estatus-salida form').formAjaxSend({
				 url: base_url('database/almacen/process_save_vales_estatus')
				,data: {tipo: 'SALIDA'}
				,success: function(response) {
					if(response.success) {
						$('#modal-vales-estatus-salida #estatus').val('');
						$('.modal.show').modal('hide');
						ISToast.fire({icon: response.icon, title: response.msg, customClass: response.icon});
						IS.init.dataTable['tbl-vales-salida-estatus'].ajax.reload();

					} else ISswal.fire({icon: response.icon, title: response.title, text: response.msg, customClass: response.icon});
				}
			});
		}
		e.preventDefault();
	})

	.on('click', '#tbl-vales-salida-estatus #open-modal-update', function(e) {
		var tr = $(this).closest('tr');
		tr.addClass('selected');
		$('#modal-vales-estatus-salida button').attr('id', 'update-estatus');
		$('#modal-vales-estatus-salida input#estatus').val(tr.data('estatus'))
		$('#modal-vales-estatus-salida').modal('show');
	})

	.on('click', '#modal-vales-estatus-salida #update-estatus', function(e) {
		if($('#modal-vales-estatus-salida form').valid()) {
			var tr = IS.init.dataTable['tbl-vales-salida-estatus'].$('tr.selected');
			$('#modal-vales-estatus-salida form').formAjaxSend({
				 url: base_url('database/almacen/process_update_vales_estatus')
				,data: {
					 id_vale_estatus: tr.data('id_vale_estatus')
					,tipo: 'salida'
				}
				,success: function(response) {
					if(response.success) {
						$('.modal.show').modal('hide');
						ISToast.fire({icon: response.icon, title: response.msg, customClass: response.icon});
						IS.init.dataTable['tbl-vales-salida-estatus'].ajax.reload();

					} else ISswal.fire({icon: response.icon, title: response.title, text: response.msg, customClass: response.icon});
				}
			});
		}
		e.preventDefault();
	})

	.on('click', '#tbl-vales-salida-estatus #remove', function(e) {
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
		    		url: base_url('database/almacen/process_remove_estatus'),
		    		data: tr.data(),
		    		success: function(response) {
		    			if(response.success) {
							ISToast.fire({icon: response.icon, title: response.msg, customClass: response.icon});
							IS.init.dataTable['tbl-vales-salida-estatus'].row(tr).remove().draw();

						} else ISswal.fire({icon: response.icon, title: response.title, text: response.msg, customClass: response.icon});
		    		}
		    	});
		    }
		});
		e.preventDefault();
	});

});