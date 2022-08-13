jQuery(function ($) {
    initDataTable('#tbl-ventas-clientes', {
        ajax: {
            url: base_url('clientes/clientes/get_clientes')
            , data: function (dataFilter) {
                dataFilter.id_cliente = $('select#id_cliente').val();
            }
        }
        , createdRow: function (row, data, index) {
            data.acciones = undefined;
            $(row).addClass('nk-tb-item').data(data);
        }
        , columns: [
            { data: 'id_cliente', defaultContent: '', className: 'nk-tb-col' }
            , { data: 'nombre', defaultContent: '', className: 'nk-tb-col' }
            , { data: 'razon_social', defaultContent: '', className: 'nk-tb-col' }
            , { data: 'rfc', defaultContent: '', className: 'nk-tb-col' }
            , { data: 'acciones', defaultContent: '', className: 'nk-tb-col nk-tb-col-tools text-right' }
        ]
    });
  
    $('body')

        .on('keyup', '.tools-tbl-clientes #buscar', function (e) {
            IS.init.dataTable['tbl-ventas-clientes'].search(this.value).draw();
        })
        
        .on('change', '.tools-tbl-clientes select#id_categoria', function () {
            IS.init.dataTable['tbl-ventas-clientes'].ajax.reload();
            (parseInt($(this).val()) > 0)
                ? $('.tools-tbl-clientes .add-cliente').elEnable()
                : $('.tools-tbl-clientes .add-cliente').elDisable();
        })

        .on('click', '.tools-tbl-clientes .cliente_length a', function (e) {
            $(this).closest('ul').find('li').removeClass('active');
            $(this).parent('li').addClass('active');

            IS.init.dataTable['tbl-ventas-clientes'].page.len($(this).data('length')).draw();

            e.preventDefault();
        })

        .on('click', '.tools-tbl-clientes .add-cliente ', function (e) {
            $.formAjaxSend({
                url: base_url('clientes/clientes/get_modal_new_cliente')
                , dataType: 'html'
                , success: function (modal) {
                    $('#content-modals').html(modal);
                    initModal('.modal', {
                        onOpenEnd: function () {
                            initSelect2('.modal select', {
                                dropdownParent: $('#content-modals .modal')
                            });
                            $('.modal#new-cliente form').validate();
                        }
                    });
                }
            })
            e.preventDefault();
        })

        .on('click', '#tbl-ventas-clientes #open-modal-update', function (e) {
            var tr = $(this).closest('tr');
            var btn = $(this);

            btn.tooltip('hide');
            tr.addClass('selected');
            $.formAjaxSend({
                url: base_url('clientes/clientes/get_modal_update_cliente')
                , data: tr.data()
                , dataType: 'html'
                , success: function (modal) {
                    $('#content-modals').html(modal);
                    initModal('.modal', {
                        onOpenEnd: function () {
                            initSelect2('.modal select', {
                                dropdownParent: $('#content-modals .modal')
                            });
                            $('.modal#update-cliente form').validate();
                        },
                        onCloseEnd: function () {
                            tr.removeClass('selected');
                        }
                    });
                }
            })
            e.preventDefault();
        })

        .on('click', '#content-modals #btn-save-cliente', function (e) {
            if ($('.modal#new-cliente form').valid()) {
                $('.modal#new-cliente form').formAjaxSend({
                    url: base_url('clientes/clientes/process_save_cliente')
                    , data: {
                        id_categoria: $('select#id_categoria').val()
                        , categoria: $('select#id_categoria option:selected').text()
                    }
                    , success: function (response) {
                        if (response.success) {
                            $('.modal.show').modal('hide');
                            ISToast.fire({ icon: response.icon, title: response.msg, customClass: response.icon });
                            IS.init.dataTable['tbl-ventas-clientes'].ajax.reload();

                        } else ISswal.fire({ icon: response.icon, title: response.title, text: response.msg, customClass: response.icon });
                    }
                })
            }
        })

        .on('click', '#content-modals #btn-update-cliente', function (e) {
            if ($('.modal#update-cliente form').valid()) {
                var tr = IS.init.dataTable['tbl-ventas-clientes'].$('tr.selected');
                $('.modal#update-cliente form').formAjaxSend({
                    url: base_url('clientes/clientes/process_update_cliente')
                    , data: {
                        id_cliente: tr.data('id_cliente')
                        , id_categoria: $('select#id_categoria').val()
                        , categoria: $('select#id_categoria option:selected').text()
                    }
                    , success: function (response) {
                        if (response.success) {
                            $('.modal.show').modal('hide');
                            ISToast.fire({ icon: response.icon, title: response.msg, customClass: response.icon });
                            IS.init.dataTable['tbl-ventas-clientes'].ajax.reload();

                        } else ISswal.fire({ icon: response.icon, title: response.title, text: response.msg, customClass: response.icon });
                    }
                })
            }
        })

        .on('click', '#tbl-ventas-clientes #remove', function (e) {
            var tr = $(this).closest('tr');
            var btn = $(this);

            btn.tooltip('hide');
            ISswal.fire({
                title: lang('general_esta_seguro'),
                text: lang('general_remove_row'),
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: lang('general_si_hazlo')
            }).then(function (result) {
                if (result.value) {
                    var data = tr.data()
                    data.categoria = $('select#id_categoria option:selected').text();
                    $.formAjaxSend({
                        url: base_url('clientes/clientes/process_remove_cliente'),
                        data: data,
                        success: function (response) {
                            if (response.success) {
                                ISToast.fire({ icon: response.icon, title: response.msg, customClass: response.icon });
                                IS.init.dataTable['tbl-ventas-clientes'].row(tr).remove().draw();

                            } else ISswal.fire({ icon: response.icon, title: response.title, text: response.msg, customClass: response.icon });
                        }
                    });
                }
            });
        });
});

