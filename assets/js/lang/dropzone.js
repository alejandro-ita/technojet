Dropzone.options.myAwesomeDropzone = false;
Dropzone.autoDiscover = false; //DESHABILITAMOS EL INICIALIZADOR AUTOMATICO
// Dropzone.autoProcessQueue = false; //DESHABILITAMOS EL INICIALIZADOR AUTOMATICO

// Spanish DROPZONE
$.extend(true, Dropzone.prototype.defaultOptions, {
     dictDefaultMessage: lang('lib_dictDefaultMessage')
    ,dictFallbackMessage: lang('lib_dictFallbackMessage')
    ,dictFallbackText: lang('lib_dictFallbackText')
    ,dictFileTooBig: lang('lib_dictFileTooBig')
    ,dictInvalidFileType: lang('lib_dictInvalidFileType')
    ,dictResponseError: lang('lib_dictResponseError')
    ,dictCancelUpload: lang('lib_dictCancelUpload')
    ,dictUploadCanceled: lang('lib_dictUploadCanceled')
    ,dictCancelUploadConfirmation: lang('lib_dictCancelUploadConfirmation')
    ,dictRemoveFile: '<em class="icon ni ni-cross-circle-fill"></em>'
    ,dictMaxFilesExceeded: lang('lib_dictMaxFilesExceeded')
});