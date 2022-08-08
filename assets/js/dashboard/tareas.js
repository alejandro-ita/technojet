jQuery(function($) {
	if ($('.content-mis-tareas').length)
		get_mis_tareas();
	if ($('#tbl-mis-tareas').length)
		get_all_task();

	$('body')
	.on('click', 'a.add-task', function(e) {
		$.formAjaxSend({
			 url: base_url('technojet/tareas/get_modal_new_task')
			,dataType: 'html'
			,success: function(modal) {
				$('#content-modals').html(modal);
				initModal('.modal', {
					onOpenEnd: function() {
						$('.modal#modal-add-task form').validate({
							ignore: '#collapseExample:hidden :input'
						});
						initSelect2('.modal select.select2');
					}
				});
			}
		});
		e.preventDefault();
	})

	.on('click', '#content-modals #btn-add-task', function(e) {
		if ($('.modal#modal-add-task form').valid()) {
			$('.modal#modal-add-task form').formAjaxSend({
				 url: base_url('technojet/tareas/process_save_task')
				,success: function(response) {
					if(response.success) {
						if ($('.modal.show #collapseExample').is(':checked')) 
							calendario.refetchEvents();
						get_mis_tareas();
						ISToast.fire({icon: response.icon, title: response.msg, customClass: response.icon});
						$('.modal.show').modal('hide');

					} else ISswal.fire({icon: response.icon, title: response.title, text: response.msg, customClass: response.icon});
				}
			});
		}
	})

	.on('click', '.rm-task', function(e) {
		var dataEncription = $(this).closest('.task-data').data('dataencription');
		var task = ($('.content-mis-tareas').length)
			? $(this).closest('.card-inner.card-inner-md')
			: $(this).closest('tr');
		ISswal.fire({
		    title: lang('general_esta_seguro'),
		    text: lang('general_remove_row'),
		    icon: 'warning',
			showCancelButton: true,
		    confirmButtonText: lang('general_si_hazlo')
		}).then(function(result) {
		    if (result.value) {
		    	$.formAjaxSend({
		    		url: base_url('technojet/tareas/process_remove_task'),
		    		data: {dataEncription: dataEncription},
		    		success: function(response) {
		    			if(response.success) {
							ISToast.fire({icon: response.icon, title: response.msg, customClass: response.icon});
							($('.content-mis-tareas').length)
								? task.remove()
								: IS.init.dataTable['tbl-mis-tareas'].row(task).remove().draw();
						} else ISswal.fire({icon: response.icon, title: response.title, text: response.msg, customClass: response.icon});
		    		}
		    	});
		    }
		});
		e.preventDefault();
	})

	.on('click', '.add-comment', function(e) {
		var dataEncription = $(this).closest('.task-data').data('dataencription');
		$.formAjaxSend({
			 url: base_url('technojet/tareas/get_modal_add_comment')
		    ,data: {dataEncription: dataEncription}
			,dataType: 'html'
			,success: function(modal) {
				$('#content-modals').html(modal);
				initModal('.modal', {
					onOpenEnd: function() {
						$('.modal#modal-add-comment form').validate();
					}
				});
			}
		});
		e.preventDefault();
	})

	.on('click', '.view-task', function(e) {
		var dataEncription = $(this).closest('.task-data').data('dataencription');
		$.formAjaxSend({
			 url: base_url('technojet/tareas/get_modal_view_task')
		    ,data: {dataEncription: dataEncription}
			,dataType: 'html'
			,success: function(modal) {
				$('#content-modals').html(modal);
				initModal('.modal', {
					onOpenEnd: function() {
						initSelect2('.modal select.select2');
						$('.modal #tab-tarea #btn-update-task').remove();
						$('.modal#modal-update-event form :input:not(.nk-reply-form-editor :input)').elDisable();
					}
				});
			}
		});
		e.preventDefault();
	})

	.on('click', '.update-task', function(e) {
		var dataEncription = $(this).closest('.task-data').data('dataencription');
		$.formAjaxSend({
			 url: base_url('technojet/tareas/get_modal_update_task')
		    ,data: {dataEncription: dataEncription}
			,dataType: 'html'
			,success: function(modal) {
				$('#content-modals').html(modal);
				initModal('.modal', {
					onOpenEnd: function() {
						$('.modal#modal-update-task form').validate({
							ignore: '#collapseExample:hidden :input'
						});
						initSelect2('.modal select.select2');
					}
				});
			}
		});
		e.preventDefault();
	})

	.on('click', '#content-modals #btn-update-task', function(e) {
		if ($('.modal#modal-update-task form').valid()) {
			$('.modal#modal-update-task form').formAjaxSend({
				 url: base_url('technojet/tareas/process_update_task')
				,success: function(response) {
					if(response.success) {
						if ($('.content-mis-tareas').length) {
							get_mis_tareas();
							if ($('.modal.show #collapseExample').is(':checked'))
								calendario.refetchEvents();

						} else IS.init.dataTable['tbl-mis-tareas'].ajax.reload();

						$('.modal.show').modal('hide');
						ISToast.fire({icon: response.icon, title: response.msg, customClass: response.icon});

					} else ISswal.fire({icon: response.icon, title: response.title, text: response.msg, customClass: response.icon});
				}
			});
		}
	})

	.on('click', '#content-modals #btn-add-comment', function(e) {
		if ($('.modal#modal-add-comment form').valid()) {
			$('.modal#modal-add-comment form').formAjaxSend({
				 url: base_url('technojet/tareas/process_save_comment')
				,success: function(response) {
					if(response.success) {
						$('.modal.show').modal('hide');
						ISToast.fire({icon: response.icon, title: response.msg, customClass: response.icon});

					} else ISswal.fire({icon: response.icon, title: response.title, text: response.msg, customClass: response.icon});
				}
			});
		}
	})

	.on('click', '.modal .nav-tabs a:not(.loaded)', function (event) {
		$(this).addClass('loaded');
		$.formAjaxSend({
			 url: base_url('technojet/tareas/get_comment_task')
			,data: {dataEncription: $('.modal input[name=dataEncription]').val()}
			,dataType: 'html'
			,success:function(comments) {
				$('.modal .nk-chat-panel').html(comments);
				new SimpleBar($('.nk-chat-panel')[0]);
			}
		});
	})

	.on('input', '.modal .nk-msg #message', function(event) {
		if ($('.modal .nk-msg #message').val().trim().length>0) {
			$('.modal .nk-msg .btn-save-comment').prop('disabled', false);
		} else $('.modal .nk-msg .btn-save-comment').prop('disabled', true);
	})

	.on('click', '.modal .nk-msg .btn-save-comment', function(e) {
		var comment = $('.modal .nk-msg #message').val().trim();
		if (comment.length>0) {
			$.formAjaxSend({
				 url: base_url('technojet/tareas/process_save_comment')
				,data: {
					 comentario: comment
					,dataEncription: $('.modal input[name=dataEncription]').val()
					,returnDaigalog: 1
				}
				,success: function(response) {
					if(response.success) {
						$('.modal .content-comment').append(response.dialog);
						$('.modal .nk-msg #message').val('');
						$('.modal .nk-msg #message').trigger('input');
						ISToast.fire({icon: response.icon, title: response.msg, customClass: response.icon});

					} else ISswal.fire({icon: response.icon, title: response.title, text: response.msg, customClass: response.icon});
				}
			});
		}
	})

	.on('click', '.modal .content-comment .rm-comment', function(e) {
		var dialog = $(this).closest('.nk-reply-item');
		$.formAjaxSend({
			 url: base_url('technojet/tareas/process_rm_comment')
			,data: {dataEncription: dialog.data('dataencription')}
			,success: function(response) {
				if(response.success) {
					ISToast.fire({icon: response.icon, title: response.msg, customClass: response.icon});
					dialog.remove();

				} else ISswal.fire({icon: response.icon, title: response.title, text: response.msg, customClass: response.icon});
			}
		});
		e.preventDefault()
	})

	.on('keyup', '.tools-tbl-mis-tareas #buscar', function(e) {
		IS.init.dataTable['tbl-mis-tareas'].search(this.value).draw();
	})

	.on('click', '.tools-tbl-mis-tareas .mis-tareas_length a', function(e) {
		$(this).closest('ul').find('li').removeClass('active');
		$(this).parent('li').addClass('active');

		IS.init.dataTable['tbl-mis-tareas'].page.len($(this).data('length')).draw();

		e.preventDefault();
	})

	.on('click', '#reload-tbl-task', function(e) {
		IS.init.dataTable['tbl-mis-tareas'].ajax.reload();
		e.preventDefault();
	})

	.on('click', '.modal .show-tab-tarea', function(e) {
		$('.modal a[href="#tab-tarea"]').trigger('click');
		e.preventDefault();
	});

	function get_all_task() {
		initDataTable('#tbl-mis-tareas', {
			 dom: '<"row justify-between g-2"><"my-3"t><"row align-items-center"<"col-5 col-sm-12 col-md-6 text-left text-md-left"i><"col-7 col-sm-12 col-md-6"p>>'
			,responsive: false
			,ajax: {
				 url: base_url('technojet/tareas/get_all_task')
				,data: function(data) {
					data.id_estatus 	= $('#filter-id_estatus').val().join(',');
					data.id_prioridad 	= $('#filter-id_prioridad').val();
					data.fecha_inicio 	= $('#filter-fecha_inicio').val() || undefined;
					data.fecha_fin 		= $('#filter-fecha_fin').val() || undefined;
				}
			}
			,createdRow: function(row, data, index) {
				$(row).addClass('nk-tb-item');
			}
			,columns: [
				{data: function(data) {
					return `<strong>Tar-${data.id_tarea} ${data.titulo}</strong> <span class="sub-text">${data.descripcion_corto}</span>`;
				}, defaultContent: '', className: 'nk-tb-col'}
				,{data: 'custom_responsable', defaultContent: '', className: 'nk-tb-col tb-col-mb'}
				,{data: 'custom_participantes', defaultContent: '', className: 'nk-tb-col tb-col-lg'}
				// ,{data: 'custom_fecha_inicio', defaultContent: '', className: 'nk-tb-col tb-col-mb'}
				// ,{data: 'custom_fecha_fin', defaultContent: '', className: 'nk-tb-col tb-col-mb'}
				,{data: 'custom_prioridad', defaultContent: '', className: 'nk-tb-col tb-col-mb'}
				,{data: 'custom_estatus', defaultContent: '', className: 'nk-tb-col tb-col-mb'}
				,{data: 'acciones', defaultContent: '', className: 'nk-tb-col nk-tb-col-tools'}
			]
		});
	}
});

function get_mis_tareas() {
	$.formAjaxSend({
		 url: base_url('technojet/tareas/get_mis_tareas')
		,dataType: 'html'
		,success: function(response) {
			$('.content-mis-tareas').html(response);
			new SimpleBar($('.content-mis-tareas')[0]);
		}
	});
}