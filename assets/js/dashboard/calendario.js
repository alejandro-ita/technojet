var calendario;
jQuery(function($) {
	calendario = new FullCalendar.Calendar(document.getElementById("calendario"), {
        timeZone: "UTC",
        locale: 'es',
        initialView: (NioApp.Win.width < NioApp.Break.md) ? "listWeek" : "dayGridMonth",
        themeSystem: "bootstrap",
        headerToolbar: {
            left: 'prev,next today',
		    center: 'title',
		    right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
        },
        height: 800,
        contentHeight: 780,
        aspectRatio: 3,
        editable: !0,
        droppable: false,
        selectable: true,
        // defaultAllDayEventDuration: false,
        views: {
            dayGridMonth: {
                dayMaxEventRows: 2
            }
        },
        direction: NioApp.State.isRTL ? "rtl" : "ltr",
        nowIndicator: true,
        now: moment().format(),
        //OBTENEMOS LOS EVENTOS REGISTRADOS
        events: function(info, successCallback, failureCallback) {
        	$.formAjaxSend({
	        	 url: base_url('technojet/calendario/get_eventos_calendario')
	        	,data: {
	        		fecha_inicio: moment(info.start).add(1, 'd').format('YYYY-MM-DD'),
	        		fecha_fin: moment(info.end).format('YYYY-MM-DD')
	        	}
	        	,success: function(events) {
	        		successCallback(events);
	        	} 
        	});
        }
        //EVENTO AL SELECCIONAR UNA O MAS FECHAS DEL CALENDARIO
		,select: function(info) {
			var fecha_inicio = (info.allDay)
				? moment(info.start).add(1, 'd').format('YYYY-MM-DD')
				: moment(info.start).format('YYYY-MM-DD');
			$.formAjaxSend({
	        	 url: base_url('technojet/calendario/get_modal_new_event')
	        	,data: { fecha_inicio: fecha_inicio, fecha_fin: moment(info.end).format('YYYY-MM-DD')}
				,dataType: 'html'
	        	,success: function(modal) {
	        		$('#content-modals').html(modal);
					initModal('.modal', {
						onOpenEnd: function() {
							$('.modal#modal-add-event form').validate({
								ignore: '#collapseExample:hidden :input'
							});
							initSelect2('.modal select.select2');
						}
					});
	        	} 
        	});
		}
		//EVENTO AL SELECCIONAR UN EVENTO DEL CALENDARIO
		,eventClick: function(info) {
			var dataEncription = info.event._def.extendedProps.dataEncription;
			var readOnly = info.event._def.extendedProps['read-only'];
			console.log(readOnly);
			$.formAjaxSend({
	        	 url: base_url('technojet/calendario/get_modal_update_event')
	        	,data: {id_tarea: info.event.id, dataEncription: dataEncription}
				,dataType: 'html'
	        	,success: function(modal) {
	        		$('#content-modals').html(modal);
					initModal('.modal', {
						onOpenEnd: function() {
							$(info.el).closest('.fc-popover.popover.fc-more-popover').remove();
							if (readOnly)
								$('.modal#modal-update-event form :input:not(.nk-reply-form-editor :input)').elDisable();
							$('.modal#modal-update-event form').validate({
								ignore: '#collapseExample:hidden :input'
							});
							initSelect2('.modal select.select2');
						}
					});
	        	} 
        	});
		}
	});

	calendario.render();

	$('body')
	.on('click', '#content-modals #btn-add-event', function(e) {
		if ($('.modal#modal-add-event form').valid()) {
			$('.modal#modal-add-event form').formAjaxSend({
				 url: base_url('technojet/calendario/process_save_event')
				,success: function(response) {
					if(response.success) {
						($('.modal.show #collapseExample').is(':checked'))
							? calendario.refetchEvents() //ACTUALIZAMOS EL CALENDARIO
							: get_mis_tareas(); //ACTIALIZAMOS LAS TAREAS
						$('.modal.show').modal('hide');
						ISToast.fire({icon: response.icon, title: response.msg, customClass: response.icon});

					} else ISswal.fire({icon: response.icon, title: response.title, text: response.msg, customClass: response.icon});
				}
			});
		}
	})

	.on('click', '#content-modals #btn-update-event', function(e) {
		if ($('.modal#modal-update-event form').valid()) {
			$('.modal#modal-update-event form').formAjaxSend({
				 url: base_url('technojet/calendario/process_update_event')
				,success: function(response) {
					if(response.success) {
						calendario.getEventById(response.event_id).remove();
						($('.modal.show #collapseExample').is(':checked'))
							? calendario.refetchEvents() //ACTUALIZAMOS EL CALENDARIO
							: get_mis_tareas(); //ACTIALIZAMOS LAS TAREAS
						$('.modal.show').modal('hide');
						ISToast.fire({icon: response.icon, title: response.msg, customClass: response.icon});

					} else ISswal.fire({icon: response.icon, title: response.title, text: response.msg, customClass: response.icon});
				}
			});
		}
	})

	.on('click', '#content-modals #btn-remove-event', function(e) {
		ISswal.fire({
		    title: lang('general_esta_seguro'),
		    text: lang('general_remove_row'),
		    icon: 'warning',
			showCancelButton: true,
		    confirmButtonText: lang('general_si_hazlo')
		}).then(function(result) {
		    if (result.value) {
		    	$('.modal#modal-update-event form').formAjaxSend({
		    		url: base_url('technojet/calendario/process_remove_event'),
		    		success: function(response) {
		    			if(response.success) {
							$('.modal.show').modal('hide');
							ISToast.fire({icon: response.icon, title: response.msg, customClass: response.icon});
							calendario.getEventById(response.event.id).remove();
						} else ISswal.fire({icon: response.icon, title: response.title, text: response.msg, customClass: response.icon});
		    		}
		    	});
		    }
		});
		e.preventDefault();
	});
});