jQuery(function($) {
	//LOGIN SYSTEM
	$("form.form-login").validate({
	 	 ignore:'.ignore'
	 	,focusCleanup: false
	 	,submitHandler: function(form) {
	 		$(form).formAjaxSend({
			 	 url: base_url('login/auth')
	 			,success: function(response) {
	 				var reloadpage = $('button').data('reloadpage');
	 				if (response.success) {
	 					if (reloadpage == 1) {
	 						location.reload();
	 					} else redirect(response.redirect);//ACCESO CORRECTO PRIMER INGRESO
	 				//ERROR AUTENTICACION
	 				} else ISswal.fire({title: response.title, text: response.msg, icon: response.icon});
	 			}
	 		});
	 	}
	});

	//RESET PASSWORD
	$('form.reset-password').validate({
	 	 ignore:'.ignore'
	 	,rules: {
	 		password: {pattern: /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[.$@$!%*?&])/},
		    'password-repeat': {equalTo: "#password"}
  		}
  		,messages: {password: { pattern: lang('login_password_message') } }
	 	,submitHandler: function(form) {
	 		$(form).formAjaxSend({
	 			url: base_url('process_change_password'),
	 			success: function(response) {
	 				if (response.success) {
						$('form.reset-password :input').val('');
						ISToast.fire({
							 icon: response.icon
							,title: response.msg
							,customClass: 'success'
							,onClose: function () { redirect(''); }
						});
	 				} else ISswal.fire({title: response.title, text: response.msg, icon: response.icon});
	 			}
	 			,complete: function(jqXHR) {
	 				if (jqXHR.status == 200 && jqXHR.responseJSON.success) $('[type="submit"]').elDisable();
	 			}
	 		});
	 	}
	});

	//FORGOT PASSWORD
	/*$('form.forgot-password').validate({
		focusCleanup: false,
		submitHandler: function(form) {
			$(form).formAjaxSend({
				url: base_url('send_recover_password'),
	 			success: function(response) {
	 				if (response.success) {
						$('form.forgot-password :input').val('');
						ISToast.fire({
							 type: response.type
							,title: response.msg
							,customClass: 'success'
							,timer: 10000
							,onClose: function () { redirect('login'); }
						});
	 				} else ISswal.fire({title: response.title, text: response.msg, type: response.type});
	 			},
	 			complete: function(jqXHR) {
	 				if (jqXHR.status == 200 && jqXHR.responseJSON.success) $('[type="submit"], input').elDisable();
	 			}
	 		});
		}
	});*/
});

