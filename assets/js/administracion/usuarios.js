jQuery(function($) {
	initDataTable('#tbl-usuarios', {
		ajax: {
		 	 url: base_url('administracion/usuarios/get_usuarios')
		 	,data: function(dataFilter) {
	    		dataFilter.id_categoria = $('select#id_categoria').val();
	    	}
		}
		,createdRow: function(row, data, index) {
			data.acciones = undefined;
			$(row).addClass('nk-tb-item').data(data);
		}
		,columns: [
			 {data: 'id_usuario', defaultContent: '', className: 'nk-tb-col'}
			,{data: 'nombre_completo', defaultContent: '', className: 'nk-tb-col'}
			,{data: 'email', defaultContent: '', className: 'nk-tb-col'}
			,{data: 'perfil', defaultContent: '', className: 'nk-tb-col'}
			,{data: 'custom_last_login', defaultContent: '', className: 'nk-tb-col'}
			,{data: 'acciones', defaultContent: '', className: 'nk-tb-col nk-tb-col-tools text-right'}
		]
	});

	$('body')

	.on('keyup', '.tools-tbl-usuarios #buscar', function(e) {
		IS.init.dataTable['tbl-usuarios'].search(this.value).draw();
	})

	.on('click', '.tools-tbl-usuarios .usuarios_length a', function(e) {
		$(this).closest('ul').find('li').removeClass('active');
		$(this).parent('li').addClass('active');

		IS.init.dataTable['tbl-usuarios'].page.len($(this).data('length')).draw();

		e.preventDefault();
	})

	.on('click', '.tools-tbl-usuarios .add-user', function(e) {
		$.formAjaxSend({
			 url: base_url('administracion/usuarios/get_modal_new_usuario')
			,dataType: 'html'
			,success: function(modal) {
				$('#content-modals').html(modal);
				initModal('.modal', {
					onOpenEnd: function() {
						NioApp.Select2.init('.modal .select2');
						$('.modal#new-user form').validate();
						$('.modal#new-user #user-access').jstree({
					        'plugins': ['checkbox'],
					        'core': {
					            'data': JSON.parse(userAccess),
					            'themes': {
					                'name': 'proton',
					                'responsive': true
					            }
					        }
					    });
					}
				});
			}
		});
		e.preventDefault();
	})

	.on('click', '#tbl-usuarios #open-modal-update', function(e) {
		var tr = $(this).closest('tr');
		var btn= $(this);
		
		btn.tooltip('hide');
		tr.addClass('selected');
		$.formAjaxSend({
			 url: base_url('administracion/usuarios/get_modal_update_usuario')
			,data: tr.data()
			,dataType: 'html'
			,success: function(modal) {
				$('#content-modals').html(modal);
				initModal('.modal', {
					onOpenEnd: function() {
						NioApp.Select2.init('.modal .select2');
						$('.modal#update-user form').validate();
						$('.modal#update-user #user-access').jstree({
					        'plugins': ['checkbox'],
					        'core': {
					            'data': JSON.parse(userAccess),
					            'themes': {
					                'name': 'proton',
					                'responsive': true
					            }
					        }
					    });
					},
					onCloseEnd: function() {
						tr.removeClass('selected');
					}
				});
			}
		})
		e.preventDefault();
	})

	.on('click', '#content-modals #btn-save-user', function(e) {
		if ($('.modal#new-user form').valid()) {
			$('.modal#new-user form').formAjaxSend({
				 url: base_url('administracion/usuarios/process_save_user')
				,data: {ids_menu: get_menu_ids()}
				,success: function(response) {
					if(response.success) {
						$('.modal.show').modal('hide');
						ISToast.fire({icon: response.icon, title: response.msg, customClass: response.icon});
						IS.init.dataTable['tbl-usuarios'].ajax.reload();

					} else ISswal.fire({icon: response.icon, title: response.title, text: response.msg, customClass: response.icon});
				}
			})
		}
	})

	.on('click', '#content-modals #btn-update-user', function(e) {
		if ($('.modal#update-user form').valid()) {
			var tr = IS.init.dataTable['tbl-usuarios'].$('tr.selected');
			$('.modal#update-user form').formAjaxSend({
				 url: base_url('administracion/usuarios/process_update_user')
				,data: {id_usuario:tr.data('id_usuario'), ids_menu: get_menu_ids()}
				,success: function(response) {
					if(response.success) {
						$('.modal.show').modal('hide');
						ISToast.fire({icon: response.icon, title: response.msg, customClass: response.icon});
						IS.init.dataTable['tbl-usuarios'].ajax.reload();

					} else ISswal.fire({icon: response.icon, title: response.title, text: response.msg, customClass: response.icon});
				}
			})
		}
	})

	.on('click', '#tbl-usuarios #remove', function(e) {
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
		    	$.formAjaxSend({
		    		url: base_url('administracion/usuarios/process_remove_user'),
		    		data: tr.data(),
		    		success: function(response) {
		    			if(response.success) {
							ISToast.fire({icon: response.icon, title: response.msg, customClass: response.icon});
							IS.init.dataTable['tbl-usuarios'].row(tr).remove().draw();

						} else ISswal.fire({icon: response.icon, title: response.title, text: response.msg, customClass: response.icon});
		    		}
		    	});
		    }
		});
	});

	function get_menu_ids() {
		var ids_menu = [];
		$.each($("#user-access").jstree("get_checked", true), function (key, li) { 
		    ids_menu.push(li['li_attr']['data-id']);
		    //OBTENEMOS EL ID DE LOS PADRES
		    $.each(li.parents, function(key, id) {
		    	if (id!='#' && $('#'+id).length && $('#'+id).data('id')) {
		    		ids_menu.push($('#'+id).data('id'));
		    	}
		    });
		});

		return ids_menu.filter(function(itm, i, a) {
		    return i == a.indexOf(itm);
		});
	}
});