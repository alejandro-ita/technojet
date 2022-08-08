(function($) {
    IS.autoInit = {
        inputMaxLength: '.init-maxlength',
        inputSelect2: '.init-select2',
        inputDropzone: '.init-dropzone',
        modal: '.modal',
        dataTable: '.init-datatable',
        daterangepicker: '.init-daterangepicker',
        tooltip: '.init-tooltip'
    };

    IS.init = {
        select2: {},
        dropzone: {},
        dataTable: {},
        daterangepicker: {}
    };
    
    /*******************************************/
    /****   INIT SWAL ALERT DESING          ****/
    /*******************************************/
    ISswal = swal.mixin({
         // confirmButtonClass: 'btn-success'
        // ,cancelButtonClass: 'btn-danger'
         reverseButtons: true
        ,confirmButtonText: lang('general_acept')
        ,cancelButtonText: lang('general_cancel')
        ,focusCancel: true
        /*,animation: true
        ,customClass: {
            popup: 'animated fadeIn'
        }*/
    });

    /*******************************************/
    /****   INIT SWAL TOATS DESING          ****/
    /*******************************************/
    ISToast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 5000
    });

    /*******************************************/
    /****  JQUERY VALIDATE ERROR RULES      ****/
    /*******************************************/
    $.validator.setDefaults({
        ignore: ".ignore",
        errorPlacement: function(label, element) {
            label.addClass('text-danger');
            
            /**POSICIONAMIENTO DEL ERROR PARA LOS SELECT2 EN UN INPUT GROUP**/
            if ($(element).hasClass('init-select2') &&
                $(element).parent().hasClass('input-group')) {
                label.insertAfter($(element).parent().find('.input-group-append'));

            /**POSICIONAMIENTO DEL ERROR PARA LOS SELECT2**/
            } else if($(element).hasClass('init-select2')) {
                label.insertAfter($(element).next('.select2'));

            } else label.insertAfter(element);
        },
        highlight: function(element, errorClass) {
            $(element).parent().addClass('has-danger')
            $(element).addClass('form-control-danger')
        }
    });

    /*******************************************/
    /****  AUTORESIZE INPUT INLINE          ****/
    /*******************************************/
    if($(IS.autoInit.inputMaxLength).length) {
        initInputMaxLength(IS.autoInit.inputMaxLength);
    }

    /*******************************************/
    /****         INIT SELECT-2             ****/
    /*******************************************/
    if($(IS.autoInit.inputSelect2).length) {
        initSelect2(IS.autoInit.inputSelect2);
    }

    /*******************************************/
    /****         INIT DROPZONE             ****/
    /*******************************************/
    if($(IS.autoInit.inputDropzone).length) {
        initDropzone(IS.autoInit.inputDropzone);
    }

    /*******************************************/
    /****           INIT DATATABLE          ****/
    /*******************************************/
    if($(IS.autoInit.dataTable).length) {
        initDataTable(IS.autoInit.dataTable);
    }

    /*******************************************/
    /****        INIT DATERANGEPICKER       ****/
    /*******************************************/
    if($(IS.autoInit.daterangepicker).length) {
        initDateRangePicker(IS.autoInit.daterangepicker);
    }

    /*******************************************/
    /****           INIT TOOLTIP            ****/
    /*******************************************/
    if($(IS.autoInit.tooltip).length) {
        initTooltip(IS.autoInit.tooltip);
    }

})(jQuery);