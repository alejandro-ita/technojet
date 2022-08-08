/* Spanish initialisation for the jQuery DataTables plugin. */
$.extend($.fn.DataTable.defaults.oLanguage, {
	sProcessing: lang('lib_sProcessing'),
    sLengthMenu: lang('lib_sLengthMenu'),
	sZeroRecords: lang('lib_sZeroRecords'),
	sEmptyTable: lang('lib_sEmptyTable'),
	sInfo: lang('lib_sInfo'),
	sInfoEmpty: lang('lib_sInfoEmpty'),
	sInfoFiltered: lang('lib_sInfoFiltered'),
	sInfoPostFix: lang('lib_sInfoPostFix'),
	sSearch: lang('lib_sSearch'),
	sSearchPlaceholder: lang('lib_sSearchPlaceholder'),
	sUrl: lang('lib_sUrl'),
	sInfoThousands: lang('lib_sInfoThousands'),
	sLoadingRecords: lang('lib_sLoadingRecords'),
	oPaginate: {
		sFirst: lang('lib_sFirst'),
		sLast: lang('lib_sLast'),
		sPrevious: lang('lib_sPrevious'),
		sNext: lang('lib_sNext')
	},
	oAria: {
		sSortAscending: lang('lib_sSortAscending'),
		sSortDescending: lang('lib_sSortDescending')
	}
});