document.addEventListener("DOMContentLoaded", function() {
    $('body>.content-preloader').fadeOut('slow');
});

/**
 * Lang
 *
 * Fetches a language variable and optionally outputs a form label
 *
 * @param   string  $line       The language line
 * @return  string
 */
function lang(line) {
    if (IS.lang != undefined && IS.lang[line] == undefined) {
        console.warn('Could not find the language line "'+line+'"');
        return 'undefined';
    }

    return IS.lang[line];
}

/**
 * Funcion para obtener la URL del sistema
 * @param subdirectory String un subdirectorio del sitio
 * @return base_url String URL del sitio
 */
function base_url(subdirectory) {
    return IS.site_url + (subdirectory||'');
}

/**
 * Redirecciona a la url recibida
 * @param  {txt} uri recibida
 * @return {void}      
 */
function redirect(url, domain) {
    url = domain ? (domain +'/'+ url) : base_url(url); 
    location.href = url;
}

/**
 * Inicializamos el maxlength de los input
 * @link https://github.com/mimo84/bootstrap-maxlength
 * @param  {txt} uri recibida
 * @return {void}      
 */
function initInputMaxLength(el, options) {
    if (el.constructor === String || el == undefined) {
        var selector = el ? el : IS.autoInit.inputMaxLength;
        var elems = document.querySelectorAll(selector);
    } else elems = el;

    var Opdefault = {
        placement: 'bottom-left-inside',
        alwaysShow: true,
        threshold: 10,
        warningClass: "badge mt-1 badge-success",
        limitReachedClass: "badge mt-1 badge-danger",
        preText: lang('preText'),
        separator: lang('separator'),
        postText: lang('postText'),
        validate: true
    };

    options = options ? options : {};
    $.extend(Opdefault, options);
    $.each(elems, function(index, el) {
        $(this).maxlength(Opdefault);
    });
}

/**
 * Inicializamos el select2 de los select
 * @link https://select2.org/
 * @param  {el} elemento a inicializar
 * @param  {options} Opciones a aplicar en el inicializador
 * @return {void}      
 */
function initSelect2(el, options) {
    var selector = el ? el : IS.autoInit.inputSelect2;
    if (selector.constructor === String) {
        var elems =  $(selector);
    } else var elems = el;

    var Opdefault = {
         selectOnClose: false
        ,minimumResultsForSearch: 5
        ,width: '100%'
    };

    options = options || {};
    $.extend(Opdefault, options);
    $.each(elems, function(index, el) {
        var key = $(el).prop('id');
        IS.init.select2[key] = $(el).select2(Opdefault);
    });
}

function initDropzone(el, options) {
    var selector = el ? el : IS.autoInit.inputDropzone;
    if (selector.constructor === String) {
        var elems =  $(selector);
    } else var elems = el;

    var Opdefault = {
    };

    options = options || {};
    $.extend(Opdefault, options);
    $.each(elems, function(index, el) {
        var key = $(el).prop('id');
        var settings = $.extend({}, Opdefault, $(this).data());
        IS.init.dropzone[key] = $(el).dropzone(settings);
    });
}

/**
 * función para crear una etiqueta <a> y despues hace el evento click.
 * @param  String   url      ruta a enlazar
 * @param  Strinh   target   _blank|_self|_parent|_top
 * @param  Boolean  isDownload Bandera para ver si es un archivo descargable
 * @return String Vooid
 */
function gotoLink(url, target, isDownload) {
    url         = url ? url : base_url();
    target      = target ? target : '_blank';
    var wOpen   = window.open(url);
    var isBlocked = (wOpen == null || typeof(wOpen)=='undefined');
    if (isBlocked) {
        var randomId = Math.floor((Math.random() * 100000));
        var elLink   = $("<a>", {href: url, target: target, id: randomId, text: lang('download_click_aqui')}).prop('outerHTML');
        var msg1     = '<p>'+ lang('download_locked_redirect') +' <br> <br>' + lang('download_click_redirect').str_replace('{click_aqui}', elLink);
        var msg2     = '<p>'+ lang('download_locked_descarga_archivo') +' <br> <br>' + lang('download_click_descarga').str_replace('{click_aqui}', elLink);

        ISswal.fire({
             title: lang('general_alerta')
            ,icon: 'info'
            ,allowOutsideClick: false
            ,allowEscapeKey: false
            ,allowEnterKey: false
            ,showConfirmButton: false
            ,showCloseButton: true
            ,html: isDownload ? msg2 : msg1
        }).then(function(){}, function(){});

        jQuery('#'+ randomId).click(function(){
            swal.close();
        });
    }
}

/**
 * Generamos el template del option select2 del seller
 * @param  Object data data del seller
 * @return String HTML option select2
 */
function tpl_option_seller(data) {
    var abrev = (data.abrev || $(data.element).data('abrev') || '');
    return $(['<div class="user-card">',
            ((abrev) 
            ?   ['<div class="user-avatar xs user-avatar-xs bg-dim-primary">',
                    '<span class="ucap">'+abrev+'</span>',
                '</div>'].join('')
            : ''),
        '<div class="user-name">',
            '<span class="tb-lead">'+data.text+'</span>',
        '</div>',
    '</div>'].join(''));
}

function initModal(el, options) {
    var options  = options || {};
    var selector = el ? el : IS.autoInit.modal;
    var contentM = options.content || '#content-modals'
    var elems    = (contentM+' ' + selector);

    var Opdefault = {
         backdrop: true
        ,keyboard: true
        ,focus: true
        ,show: true
        ,removeOnClose: true
    };
    $.extend(Opdefault, options);

    //AGREGAMOS LOS EVENTOS AL MODAL
    $(elems)
    //onOpenStart
    .on('show.bs.modal', function(e) {
        if(!$(e.target).hasClass('modal')) return false;
        if(options.onOpenStart != undefined && options.onOpenStart.constructor == Function) options.onOpenStart();
    })
    
    //onOpenEnd
    .on('shown.bs.modal', function(e) {
        if(!$(e.target).hasClass('modal')) return false;
        if(options.onOpenEnd != undefined && options.onOpenEnd.constructor == Function) options.onOpenEnd();
    })
    
    //onCloseStart
    .on('hide.bs.modal', function(e) {
        if(!$(e.target).hasClass('modal')) return false;
        if(options.onCloseStart != undefined && options.onCloseStart.constructor == Function) options.onCloseStart();
    })
    
    //Event onCloseEnd
    .on('hidden.bs.modal', function(e) {
        if(!$(e.target).hasClass('modal')) return false;
        if(options.onCloseEnd != undefined && options.onCloseEnd.constructor == Function) options.onCloseEnd();

        if (Opdefault.removeOnClose) {
            $(contentM).html('');
        }
    });

    //INICIALIZAMOS EL EVENTO
    $(elems).modal(Opdefault);
}

function initDataTable(el, options) {
    el = el || IS.autoInit.dataTable;
    options = options || {};
    var initComplete = (options.initComplete != undefined && options.initComplete.constructor == Function) && options.initComplete || function() {};

    var className = '';
    var classDragable= (options.dragableX ? 'table-responsive tblDragScrollX' : '');
    var Opdefault = {
        /*dom: '<"row justify-between g-2"<"col-7 col-sm-4 text-left"f>'+
            '<"col-5 col-sm-8 text-right"<"datatable-filter"<"d-flex justify-content-end g-2"l>>>>'+
            '<"datatable-wrap my-3"t><"row align-items-center"<"col-7 col-sm-12 col-md-6"p><"col-5 col-sm-12 col-md-6 text-left text-md-right"i>>',*/
         dom: '<"datatable-wrap my-3"t><"row align-items-center"<"col-5 col-sm-12 col-md-6 text-left text-md-left"i><"col-7 col-sm-12 col-md-6"p>>'
        ,ajax: null
        ,responsive: true
        ,searching:  true
        ,scrollX:    false
        ,details:    true
        ,pageLength: 10
        ,lengthMenu: [
          [10, 25, 50, -1],
          [10, 25, 50, lang('general_todos')]
        ]
        ,bFilter: false
        ,ordering: true
        ,processing: true
        ,btnDownload: false
        ,btnAdd: false
        ,btnColVis: false
        ,addCounter: false
        ,pagingType: 'full_numbers'
    };

    var settings = $.extend({}, Opdefault, options, {
        fnPreDrawCallback: function(oSettings) {
            $(oSettings.nTableWrapper).find('.init-tooltip').tooltip('hide');
        }
        ,fnDrawCallback: function(oSettings) {
            initTooltip($(oSettings.nTableWrapper).find('.init-tooltip'));
        }
        ,initComplete: function(settings, json) {
            var config       = settings.oInit;

            //AGREGAMOS EL CONTADOR DE LOS REGISTROS DE LA TABLA
            if(config.addCounter) {
                var instance = settings.sInstance;
                var table = IS.init.dataTable[instance];

                table.on('order.dt search.dt', function () {
                    table.column(0, { search: 'applied', order: 'applied' }).nodes().each(function (cell, i) {
                        cell.innerHTML = i + 1;
                    });
                }).draw();
            }

            if (options.dragableX) {
                elDragScroll('.tblDragScrollX');
            }

            initComplete(settings, json);
        }
    });
    
    if (settings.ajax !== null) {
        settings.ajax.method = 'post';
        if(settings.serverSide) {
            
        } else {
            settings.ajax.dataSrc = '';
        }
    }
    $(el).each(function(key, ele) {
        $(ele).find('caption').remove();
        $(ele).find('thead').removeClass('d-none');

        // var table   = NioApp.DataTable(ele, settings);
        var table   = $(ele).DataTable(settings);
        var id      = table.table().node().id;
        IS.init.dataTable[id]   = table;
        add_DT_Buttons(IS.init.dataTable[id]);
    });

    return $(el);
}

/**
 * @docs https://datatables.net/forums/discussion/35580/how-can-you-place-export-buttons-in-another-location-on-your-page
 * @param object dataTable inicializado
 * @param botones nuevos a agregar
 */
function add_DT_Buttons(dataTable, buttons) {
    var DTSettings  = dataTable.init();
    var tableID     = dataTable.table().node().id;
    var buttons     = buttons || [];

    $.extend(!0, $.fn.DataTable.Buttons.defaults, {
        dom: {
            button: {
                className: "btn"
            },
            collection: {
                /*tag: "div",*/
                className: "dropdown-menu dropdown-menu-sm-right",
                /*button: {
                    tag: "a",
                    className: "dt-button dropdown-item",
                    active: "active",
                    disabled: "disabled"
                }*/
            }
        }
    });

    if (DTSettings.btnAdd) {
        buttons.push({
            text: '<em class="icon ni ni-plus font-medium-2"></em>', 
            className: 'btn btn-icon btn-sm btn-success btn-add ml-2 p-1 init-tooltip',
            titleAttr: lang('general_nuevo')
        });
    }

    if (DTSettings.btnDownload) {
        buttons.push({
            text: '<i class="ft-download-cloud font-medium-2"></i>', 
            className: 'btn btn-sm btn-warning btn-download init-tooltip',
            titleAttr: lang('general_descargar')
        });
    }

    if (buttons) {
        new $.fn.dataTable.Buttons(dataTable, {buttons: buttons});
        dataTable.buttons(0, null).container().appendTo('#'+tableID+'_filter');
    }

    if (DTSettings.btnColVis) {
        var btnColVis = [{
            extend: 'colvis',
            columns: ':not(.noVis)',
            text: '<em class="icon ni ni-caret-down"></em>',
            titleAttr: lang('lib_mostrar'),
            className: 'btn btn-sm bg-transparent text-white'
        }];

        new $.fn.dataTable.Buttons(dataTable, {buttons: btnColVis});
        dataTable.buttons(1, null).container().appendTo('#'+tableID+' thead .content-colVis');
    }

}

function initDateRangePicker(el, options) {
    var selector = el ? el : IS.autoInit.daterangepicker;
    if (selector.constructor === String) {
        var elems =  $(selector);
    } else var elems = el;
    var Opdefault = {
        autoApply: true,
        locale: {
            firstDay: 1,
            format: get_config('momentJSdateFormat'),
            separator: ' - ',
            applyLabel: lang('general_acept'),
            cancelLabel: lang('general_cancel'),
            fromLabel: lang('general_del'),
            toLabel: lang('general_al'),
            customRangeLabel: "Custom",
            weekLabel: "W",
            daysOfWeek: [
                lang('general_short_dia_0'),
                lang('general_short_dia_1'),
                lang('general_short_dia_2'),
                lang('general_short_dia_3'),
                lang('general_short_dia_4'),
                lang('general_short_dia_5'),
                lang('general_short_dia_6')
            ],
            monthNames: [
                lang('general_mes1'),
                lang('general_mes2'),
                lang('general_mes3'),
                lang('general_mes4'),
                lang('general_mes5'),
                lang('general_mes6'),
                lang('general_mes7'),
                lang('general_mes8'),
                lang('general_mes9'),
                lang('general_mes10'),
                lang('general_mes11'),
                lang('general_mes12')
            ]
        }
    };

    options = options || {};
    $.extend(Opdefault, options);
    $.each(elems, function(index, el) {
        var key = $(el).prop('id');
        var settings = $.extend({}, Opdefault, $(this).data());
        IS.init.daterangepicker[key] = $(el).daterangepicker(settings);
    });
}

/**
 * Returns the specified config item
 *
 * @param   string
 * @return  mixed
 */
function get_config(item) {
    if (IS.sysConfig[item] == undefined) {
        console.warn('Could not find the config line "'+item+'"');
        return 'undefined';
    }

    return IS.sysConfig[item];
}

function initTooltip(el, options) {
    if (el.constructor === String) {
        var selector = el ? el : IS.autoInit.tooltip;
        var elems = document.querySelectorAll(selector);
    } else elems = el;

    var Opdefault = {
        placement: 'top',
        html: true
    };

    options = options || {};
    $.extend(Opdefault, options);

    $(elems).each(function(index, el) {
        var settings = $.extend({}, Opdefault, options, $(this).data());
        $(this).tooltip(settings);
    });
}

/**
 * Cargamos el option seller
 */
function loadOptionsSellers(select) {
    var select = $(select);
    $.formAjaxSend({
        url: base_url('settings/get_sellers_list'),
        blockScreen: false,
        success: function(response) {
            $(response).each(function(index, data) {
                //Create a DOM Option
                if (!select.find("option[value='" + data.user_id + "']").length) {
                    var newOption = new Option(data.full_name, data.user_id, false, false);
                    select.append(newOption);
                }
            });
        }
    });
}

/**
 * Validación del input select[seller_id]
 * @return String Value of seller_id
 */
function validSeller(showAlert) {
    showAlert = (typeof showAlert != 'undefined') ? showAlert : true;
    return new Promise((resolve, reject) => {
        var inputSeller = $('#seller_id');

        if ($('#seller_id').length && !$('#seller_id').val()) {
            if (showAlert) {
                ISToast.fire({type: 'warning', title: lang('mess_sellect_seller'), customClass: 'warning'});
            }
            reject('SELLER_NOT_SELECTED'); 
        } else {
            var seller_id = ($('#seller_id').length ? $('#seller_id').val() : undefined);
            resolve(seller_id);
        }
    });
}

function prettyPrintJSON(jsonData) {
    var jsonReplacer = function(match, pIndent, pKey, pVal, pEnd) {
        var key = '<span class=json-key>';
        var val = '<span class=json-value>';
        var str = '<span class=json-string>';
        var r = pIndent || '';
        
        if (pKey)
            r = r + key + pKey.replace(/[": ]/g, '') + '</span>: ';
        
        if (pVal)
            r = r + (pVal[0] == '"' ? str : val) + pVal + '</span>';
        
        return r + (pEnd || '');
    }

    var jsonLine = /^( *)("[\w]+": )?("[^"]*"|[\w.+-]*)?([,[{])?$/mg;
    return JSON.stringify(jsonData, null, 3)
        .replace(/&/g, '&amp;').replace(/\\"/g, '&quot;')
        .replace(/</g, '&lt;').replace(/>/g, '&gt;')
        .replace(jsonLine, jsonReplacer);
}