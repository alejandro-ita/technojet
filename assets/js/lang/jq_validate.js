(function( factory ) {
	if ( typeof define === "function" && define.amd ) {
		define( ["jquery", "../jquery.validate"], factory );
	} else if (typeof module === "object" && module.exports) {
		module.exports = factory( require( "jquery" ) );
	} else {
		factory( jQuery );
	}
}(function( $ ) {

	/*
	 * Translated default messages for the jQuery validation plugin.
	 * Locale: ES (Spanish; Español)
	 */
	var ico_error = '<i class="fas fa-info-circle"></i> ';
	$.extend( $.validator.messages, {
		required: ico_error + lang('validate_required'),
		remote: ico_error + lang('validate_remote'),
		email: ico_error + lang('validate_email'),
		url: ico_error + lang('validate_url'),
		date: ico_error + lang('validate_date'),
		dateISO: ico_error + lang('validate_dateISO'),
		number: ico_error + lang('validate_number'),
		digits: ico_error + lang('validate_digits'),
		creditcard: ico_error + lang('validate_creditcard'),
		equalTo: ico_error + lang('validate_equalTo'),
		extension: ico_error + lang('validate_extension'),
		maxlength: $.validator.format( ico_error + lang('validate_maxlength') ),
		minlength: $.validator.format( ico_error + lang('validate_minlength') ),
		rangelength: $.validator.format( ico_error + lang('validate_rangelength') ),
		range: $.validator.format( ico_error + lang('validate_range') ),
		max: $.validator.format( ico_error + lang('validate_max') ),
		min: $.validator.format( ico_error + lang('validate_min') ),
		nifES: ico_error + lang('validate_nifES'),
		nieES: ico_error + lang('validate_nieES'),
		cifES: ico_error + lang('validate_cifES')
	} );
	return $;
}));

/**
* Return true if the field value matches the given format RegExp
*
* @example $.validator.methods.pattern("AR1004",element,/^AR\d{4}$/)
* @result true
*
* @example $.validator.methods.pattern("BR1004",element,/^AR\d{4}$/)
* @result false
*
* @name $.validator.methods.pattern
* @type Boolean
* @cat Plugins/Validate/Methods
*/
$.validator.addMethod( "pattern", function( value, element, param ) {
	if ( this.optional( element ) ) {
		return true;
	}
	if ( typeof param === "string" ) {
		param = new RegExp( "^(?:" + param + ")$" );
	}
	return param.test( value );
}, "Invalid format." );