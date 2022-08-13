jQuery(function($) {
	$('#modal-vales-almacen-entrada form').validate();
	$('#modal-vales-estatus-entrada form').validate();
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

	initDataTable('#tbl-vales-entrada-almacen', {
		dom: tblDOM,
		ajax: {
		 	 url: base_url('database/almacen/get_vales_almacen')
		 	,data: function(dataFilter) {
	    		dataFilter.tipo = 'ENTRADA';
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

	initDataTable('#tbl-vales-entrada-estatus', {
		dom: tblDOM,
		ajax: {
		 	 url: base_url('database/almacen/get_vales_estatus')
		 	,data: function(dataFilter) {
	    		dataFilter.tipo = 'ENTRADA';
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

	.on('hidden.bs.modal', '#modal-vales-almacen-entrada, #modal-vales-estatus-entrada', function() {
		IS.init.dataTable['tbl-vales-entrada-almacen'].$('tr.selected').removeClass('selected');
		IS.init.dataTable['tbl-vales-entrada-estatus'].$('tr.selected').removeClass('selected');
		$('#modal-vales-almacen-entrada #almacen').val('');
		$('#modal-vales-estatus-entrada #estatus').val('');
	})

	//********CRUD ALMACENES ENTRANTES**************/
	.on('click', '#tbl-vales-entrada-almacen_filter .btn-add', function() {
		$('#modal-vales-almacen-entrada button').attr('id', 'save-almacen');
		$('#modal-vales-almacen-entrada').modal('show');
	})

	.on('click', '#modal-vales-almacen-entrada #save-almacen', function(e) {
		if($('#modal-vales-almacen-entrada form').valid()) {
			$('#modal-vales-almacen-entrada form').formAjaxSend({
				 url: base_url('database/almacen/process_save_vales_almacen')
				,data: {tipo: 'ENTRADA'}
				,success: function(response) {
					if(response.success) {
						$('.modal.show').modal('hide');
						ISToast.fire({icon: response.icon, title: response.msg, customClass: response.icon});
						IS.init.dataTable['tbl-vales-entrada-almacen'].ajax.reload();

					} else ISswal.fire({icon: response.icon, title: response.title, text: response.msg, customClass: response.icon});
				}
			});
		}
		e.preventDefault();
	})

	.on('click', '#tbl-vales-entrada-almacen #open-modal-update', function() {
		var tr = $(this).closest('tr');
		tr.addClass('selected');
		$('#modal-vales-almacen-entrada button').attr('id', 'update-almacen');
		$('#modal-vales-almacen-entrada input#almacen').val(tr.data('almacen'))
		$('#modal-vales-almacen-entrada').modal('show');
	})

	.on('click', '#modal-vales-almacen-entrada #update-almacen', function(e) {
		if($('#modal-vales-almacen-entrada form').valid()) {
			var tr = IS.init.dataTable['tbl-vales-entrada-almacen'].$('tr.selected');
			$('#modal-vales-almacen-entrada form').formAjaxSend({
				 url: base_url('database/almacen/process_update_vales_almacen')
				,data: {
					 id_vale_almacen: tr.data('id_vale_almacen')
					,tipo: 'ENTRADA'
				}
				,success: function(response) {
					if(response.success) {
						$('.modal.show').modal('hide');
						ISToast.fire({icon: response.icon, title: response.msg, customClass: response.icon});
						IS.init.dataTable['tbl-vales-entrada-almacen'].ajax.reload();

					} else ISswal.fire({icon: response.icon, title: response.title, text: response.msg, customClass: response.icon});
				}
			});
		}
		e.preventDefault();
	})

	.on('click', '#tbl-vales-entrada-almacen #remove', function(e) {
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
							IS.init.dataTable['tbl-vales-entrada-almacen'].row(tr).remove().draw();

						} else ISswal.fire({icon: response.icon, title: response.title, text: response.msg, customClass: response.icon});
		    		}
		    	});
		    }
		});
		e.preventDefault();
	})

	//********CRUD ESTATUS**************/
	.on('click', '#tbl-vales-entrada-estatus_filter .btn-add', function() {
		$('#modal-vales-estatus-entrada button').attr('id', 'save-estatus');
		$('#modal-vales-estatus-entrada').modal('show');
	})

	.on('click', '#modal-vales-estatus-entrada #save-estatus', function(e) {
		if($('#modal-vales-estatus-entrada form').valid()) {
			$('#modal-vales-estatus-entrada form').formAjaxSend({
				 url: base_url('database/almacen/process_save_vales_estatus')
				,data: {tipo: 'ENTRADA'}
				,success: function(response) {
					if(response.success) {
						$('.modal.show').modal('hide');
						ISToast.fire({icon: response.icon, title: response.msg, customClass: response.icon});
						IS.init.dataTable['tbl-vales-entrada-estatus'].ajax.reload();

					} else ISswal.fire({icon: response.icon, title: response.title, text: response.msg, customClass: response.icon});
				}
			});
		}
		e.preventDefault();
	})

	.on('click', '#tbl-vales-entrada-estatus #open-modal-update', function(e) {
		var tr = $(this).closest('tr');
		tr.addClass('selected');
		$('#modal-vales-estatus-entrada button').attr('id', 'update-estatus');
		$('#modal-vales-estatus-entrada input#estatus').val(tr.data('estatus'))
		$('#modal-vales-estatus-entrada').modal('show');
	})

	.on('click', '#modal-vales-estatus-entrada #update-estatus', function(e) {
		if($('#modal-vales-estatus-entrada form').valid()) {
			var tr = IS.init.dataTable['tbl-vales-entrada-estatus'].$('tr.selected');
			$('#modal-vales-estatus-entrada form').formAjaxSend({
				 url: base_url('database/almacen/process_update_vales_estatus')
				,data: {
					 id_vale_estatus: tr.data('id_vale_estatus')
					,tipo: 'ENTRADA'
				}
				,success: function(response) {
					if(response.success) {
						$('.modal.show').modal('hide');
						ISToast.fire({icon: response.icon, title: response.msg, customClass: response.icon});
						IS.init.dataTable['tbl-vales-entrada-estatus'].ajax.reload();

					} else ISswal.fire({icon: response.icon, title: response.title, text: response.msg, customClass: response.icon});
				}
			});
		}
		e.preventDefault();
	})

	.on('click', '#tbl-vales-entrada-estatus #remove', function(e) {
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
							IS.init.dataTable['tbl-vales-entrada-estatus'].row(tr).remove().draw();

						} else ISswal.fire({icon: response.icon, title: response.title, text: response.msg, customClass: response.icon});
		    		}
		    	});
		    }
		});
		e.preventDefault();
	});
});