jQuery(function($) {
	$('body').on('select2:select', 'select.option-new', function(e) {
		var $select = $(this).closest('select');
		if ($(this).val()=='new') {
			ISswal.fire({
	            title: lang('productos_nuevo_consumible'),
	            input: "text",
	        }).then(function(result) {
	        	if(result.value.trim()) {
	        		var optionValue = result.value.trim().toUpperCase();
	        		var exist = $select.find('option').filter(function () {
	        						return this.text.toUpperCase()==optionValue;
	        					}).attr('value');

	        		if (exist==undefined) {
	        			var newOption = new Option(optionValue, 'addValue::'+optionValue, true, true);
					    $select.append(newOption).trigger('change');

	        		} else $select.val(exist).trigger('change');
	        	}
	        });
		}
		e.preventDefault();
	});
});