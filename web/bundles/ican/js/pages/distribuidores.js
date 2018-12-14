var Distribuidores = function () {

    var oTable;
    var rowDelete = null;
    var puntos = new Array();
    var map, marker, infoWindow = null;

    //Inicializa la tabla
    var initTable = function () {
        MyApp.block('#distribuidor-table-editable');
        var table = $('#distribuidor-table-editable');
        var aoColumns = [
            {
                field: "id",
                title: "#",
                sortable: false, // disable sort for this column
                width: 40,
                textAlign: 'center',
                selector: {class: 'm-checkbox--solid m-checkbox--brand'}
            },
            {
                field: "nombre",
                title: "Nombre",
                width: 200,
                template: function (row) {
                    var output = '<div class="m-card-user m-card-user--sm">\
								<div class="m-card-user__details">\
									<span class="m-card-user__name">' + row.nombre + '</span>\
								</div>\
							</div>';
                    return output;
                }
            },
            {
                field: "comuna",
                title: "Comuna",
                responsive: {visible: 'lg'},
            },
            {
                field: "direccion",
                title: "Dirección",
                responsive: {visible: 'lg'},
            },
            {
                field: "email",
                title: "Correo",
                responsive: {visible: 'lg'},
            },
            {
                field: "telefono",
                title: "Teléfono",
                responsive: {visible: 'lg'},
            },
            {
                field: "estado",
                title: "Estado",
                responsive: {visible: 'lg'},
                width: 60,
                // callback function support for column rendering
                template: function (row) {
                    var status = {
                        1: {'title': 'Activo', 'class': ' m-badge--success'},
                        0: {'title': 'Inactivo', 'class': ' m-badge--danger'}
                    };
                    return '<span class="m-badge ' + status[row.estado].class + ' m-badge--wide">' + status[row.estado].title + '</span>';
                }
            },
            {
                field: "acciones",
                width: 110,
                title: "Acciones",
                sortable: false,
                overflow: 'visible',
                textAlign: 'center'
            }
        ];
        oTable = table.mDatatable({
            // datasource definition
            data: {
                type: 'remote',
                source: {
                    read: {
                        url: 'distribuidor/listarDistribuidor',
                    }
                },
                pageSize: 10,
                saveState: {
                    cookie: true,
                    webstorage: true
                },
                serverPaging: true,
                serverFiltering: true,
                serverSorting: true
            },
            // layout definition
            layout: {
                theme: 'default', // datatable theme
                class: '', // custom wrapper class
                scroll: false, // enable/disable datatable scroll both horizontal and vertical when needed.
                //height: 550, // datatable's body's fixed height
                footer: false // display/hide footer
            },
            // column sorting
            sortable: true,
            pagination: true,
            // columns definition
            columns: aoColumns,
            // toolbar
            toolbar: {
                // toolbar items
                items: {
                    // pagination
                    pagination: {
                        // page size select
                        pageSizeSelect: [10, 25, 30, 50, -1] // display dropdown to select pagination size. -1 is used for "ALl" option
                    }
                }
            },
            //Tanslate
            translate: {
                records: {
                    processing: 'Por favor espere...',
                    noRecords: 'No se existen registros'
                },
                toolbar: {
                    pagination: {
                        items: {
                            info: 'Mostrando {{start}} - {{end}} de {{total}} registros'
                        }
                    }
                }
            }
        });

        //Events
        oTable
            .on('m-datatable--on-ajax-done', function () {
                mApp.unblock('#distribuidor-table-editable');
            })
            .on('m-datatable--on-ajax-fail', function (e, jqXHR) {
                mApp.unblock('#distribuidor-table-editable');
            })
            .on('m-datatable--on-goto-page', function (e, args) {
                MyApp.block('#distribuidor-table-editable');
            })
            .on('m-datatable--on-reloaded', function (e) {
                MyApp.block('#distribuidor-table-editable');
            })
            .on('m-datatable--on-sort', function (e, args) {
                MyApp.block('#distribuidor-table-editable');
            })
            .on('m-datatable--on-check', function (e, args) {
                //eventsWriter('Checkbox active: ' + args.toString());
            })
            .on('m-datatable--on-uncheck', function (e, args) {
                //eventsWriter('Checkbox inactive: ' + args.toString());
            });

        //Busqueda
        var query = oTable.getDataSourceQuery();
        $('#lista-distribuidor .m_form_search').on('keyup', function (e) {
            // shortcode to datatable.getDataSourceParam('query');
            var query = oTable.getDataSourceQuery();
            query.generalSearch = $(this).val().toLowerCase();
            // shortcode to datatable.setDataSourceParam('query', query);
            oTable.setDataSourceQuery(query);
            oTable.load();
        }).val(query.generalSearch);
    };
    //Reset forms
    var resetForms = function () {
        $('#distribuidor-form input').each(function (e) {
            $element = $(this);
            $element.val('');

            $element.data("title", "").removeClass("has-error").tooltip("dispose");
            $element.closest('.form-group').removeClass('has-error').addClass('success');
        });
        $('#distribuidor-form textarea').each(function (e) {
            $element = $(this);
            $element.val('');

            $element.data("title", "").removeClass("has-error").tooltip("dispose");
            $element.closest('.form-group').removeClass('has-error').addClass('success');
        });
        $('#seo-form input, #seo-form textarea').each(function (e) {
            $element = $(this);
            $element.val('');

            $element.data("title", "").removeClass("has-error").tooltip("dispose");
            $element.closest('.form-group').removeClass('has-error').addClass('success');
        });

        $('#estadoactivo').prop('checked', true);

        //Dropzone
        if ($('#my-dropzone').hasClass('dz-started')) {
            $('#my-dropzone').removeClass('dz-started');
        }
        $('.dz-preview').remove();
        realFiles = 0;

        //Limpiar tags
        $('#tags_tagsinput span').each(function (e) {
            $(this).remove();
        });

        var $element = $('.tagsinput');
        $element.tooltip("dispose");
        $element.removeClass('has-error');
        $element.closest('.form-group')
            .removeClass('has-error');

        var $element = $('#processing-dropzone');
        $element.tooltip("dispose")
            .removeClass('has-error')
            .closest('.form-group')
            .removeClass('has-error');

        event_change = false;

        //Mostrar el primer tab
        resetWizard();
    };

    var resetMap = function () {
        map, marker, infoWindow = null;
        $('#map').html('');
        $('#form-group-mapa').removeClass('ng-hide').addClass('ng-hide');
    };

    //Acciones de Google
    //Mostrar la ubicacion en el map
    var initMapa = function () {
        if (isValidUbicacion()) {
            //Reset map
            resetMap();

            //Mostrar map
            $('#form-group-mapa').removeClass('ng-hide');
            var location = new google.maps.LatLng(puntos[0], puntos[1]);
            var mapProp = {
                center: location,
                zoom: 13,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            };

            map = new google.maps.Map(document.getElementById("map"), mapProp);

            infoWindow = new google.maps.InfoWindow();

            eliminarMarker();
            marker = new google.maps.Marker({
                map: map, //el mapa creado en el paso anterior
                position: location, //objeto con latitud y longitud
                draggable: true,
                title: "Mi Ubicación" //que el marcador se pueda arrastrar
            });

            google.maps.event.addListener(marker, 'click', function () {
                openInfoWindow(marker);
            });
            google.maps.event.addListener(marker, 'dragend', function (evt) {
                infoWindow.setOptions({
                    content: '<p>Marker dropped: Current Lat: ' + evt.latLng.lat().toFixed(3) + ' Current Lng: ' + evt.latLng.lng().toFixed(3) + '</p>'
                });
                infoWindow.open(map, marker);
            });
        } else {
            alert('no hay puntos');
        }

        function openInfoWindow(marker) {
            var markerLatLng = marker.getPosition();
            infoWindow.setContent(markerLatLng.lat() + ", " + markerLatLng.lng() + "<br> Arrastre y haz click para actualizar");
            infoWindow.open(map, marker);
        }

        function eliminarMarker() {
            if (marker != null) {
                //Elimino el marker
                marker.setMap(null);
                marker = null;
            }
        }
    };

    //Init autocomplete
    var autocomplete = null;
    var initAutocomplete = function () {
        autocomplete = new google.maps.places.Autocomplete(document.getElementById('direccion'), {types: ['geocode']});
        autocomplete.addListener('place_changed', function () {
            var place = autocomplete.getPlace();
            var geometry = place.geometry;
            var location = geometry.location;
            puntos[0] = location.lat();
            puntos[1] = location.lng();
            initMapa();
        });
    };

    var isValidUbicacion = function () {
        var result = true;
        if (puntos.length == 0) {
            result = false;
        }
        return result;
    };
    //Validacion
    var initForm = function () {
        $("#distribuidor-form").validate({
            rules: {
                nombre: {
                    required: true
                },
                direccion: {
                    required: true
                },
                email: {
                    required: true
                },
                telefono: {
                    required: true
                },
                comuna: {
                    required: true
                }
            },
            messages: {
                nombre: "Este campo es obligatorio",
                direccion: "Este campo es obligatorio",
                telefono: "Este campo es obligatorio",
                comuna: "Este campo es obligatorio",
                email: "Este campo es obligatorio"
            },
            showErrors: function (errorMap, errorList) {
                // Clean up any tooltips for valid elements
                $.each(this.validElements(), function (index, element) {
                    var $element = $(element);

                    $element.data("title", "") // Clear the title - there is no error associated anymore
                        .removeClass("has-error")
                        .tooltip("dispose");

                    $element
                        .closest('.form-group')
                        .removeClass('has-error').addClass('success');
                });

                // Create new tooltips for invalid elements
                $.each(errorList, function (index, error) {
                    var $element = $(error.element);

                    $element.tooltip("dispose") // Destroy any pre-existing tooltip so we can repopulate with new tooltip content
                        .data("title", error.message)
                        .addClass("has-error")
                        .tooltip({
                            placement: 'bottom'
                        }); // Create a new tooltip based on the error messsage we just set in the title

                    $element.closest('.form-group')
                        .removeClass('has-success').addClass('has-error');

                });
            },
        });
    };

    //Nuevo
    var initAccionNuevo = function () {
        $(document).off('click', "#btn-nuevo-distribuidor");
        $(document).on('click', "#btn-nuevo-distribuidor", function (e) {
            btnClickNuevo();
        });

        function btnClickNuevo() {
            resetForms();
            var formTitle = "¿Deseas crear un nuevo distribuidor? Sigue los siguientes pasos:";
            $('#form-distribuidor-title').html(formTitle);
            $('#form-distribuidor').removeClass('m--hide');
            $('#lista-distribuidor').addClass('m--hide');
        };
    };
    //Salvar
    var initAccionSalvar = function () {
        $(document).off('click', "#btn-wizard-finalizar");
        $(document).on('click', "#btn-wizard-finalizar", function (e) {
            btnClickSalvarForm();
        });

        function btnClickSalvarForm() {
            mUtil.scrollTo();
            event_change = false;

            var distribuidor_id = $('#distribuidor_id').val();

            if ($('#distribuidor-form').valid()) {
                var nombre = $('#nombre').val();
                var descripcion = $('#descripcion').val();
                var direccion = $('#direccion').val();
                var comuna = $('#comuna').val();
                var telefono = $('#telefono').val();
                var email = $('#email').val();
                var estado = ($('#estadoactivo').prop('checked')) ? 1 : 0;

                MyApp.block('#form-distribuidor');

                $.ajax({
                    type: "POST",
                    url: "distribuidor/salvarDistribuidor",
                    dataType: "json",
                    data: {
                        'distribuidor_id': distribuidor_id,
                        'nombre': nombre,
                        'descripcion': descripcion,
                        'direccion': direccion,
                        'telefono': telefono,
                        'email': email,
                        'comuna': comuna,
                        'estado': estado
                    },
                    success: function (response) {
                        mApp.unblock('#form-distribuidor');
                        if (response.success) {

                            toastr.success(response.message, "Exito !!!");
                            cerrarForms();
                            oTable.load();
                        } else {
                            toastr.error(response.error, "Error !!!");
                        }
                    },
                    failure: function (response) {
                        mApp.unblock('#form-distribuidor');

                        toastr.error(response.error, "Error !!!");
                    }
                });
            }
        };
    }
    //Cerrar form
    var initAccionCerrar = function () {
        $(document).off('click', ".cerrar-form-distribuidor");
        $(document).on('click', ".cerrar-form-distribuidor", function (e) {
            cerrarForms();
        });
    }
    //Cerrar forms
    var cerrarForms = function () {
        if (!event_change) {
            cerrarFormsConfirmated();
        } else {
            $('#modal-salvar-cambios').modal({
                'show': true
            });
        }
    };
    //Editar
    var initAccionEditar = function () {
        $(document).off('click', "#distribuidor-table-editable a.edit");
        $(document).on('click', "#distribuidor-table-editable a.edit", function (e) {
            e.preventDefault();
            resetForms();


            var distribuidor_id = $(this).data('id');
            $('#distribuidor_id').val(distribuidor_id);
            $('#form-distribuidor').removeClass('m--hide');
            $('#lista-distribuidor').addClass('m--hide');

            editRow(distribuidor_id);
        });

        function editRow(distribuidor_id) {
            MyApp.block('#distribuidor-form');
            $.ajax({
                type: "POST",
                url: "distribuidor/cargarDatos",
                dataType: "json",
                data: {
                    'distribuidor_id': distribuidor_id
                },
                success: function (response) {
                    mApp.unblock('#distribuidor-form');
                    if (response.success) {
                        //Datos distribuidor    

                        var formTitle = "¿Deseas actualizar la distribuidor \"" + response.distribuidor.nombre + "\" ? Sigue los siguientes pasos:";
                        $('#form-distribuidor-title').html(formTitle);

                        $('#nombre').val(response.distribuidor.nombre);
                        $('#titulo').val(response.distribuidor.titulo);
                        $('#direccion').val(response.distribuidor.direccion);
                        $('#telefono').val(response.distribuidor.telefono);
                        $('#email').val(response.distribuidor.email);
                        $('#descripcion').val(response.distribuidor.descripcion)
                        if (!response.distribuidor.estado) {
                            $('#estadoactivo').prop('checked', false);
                            $('#estadoinactivo').prop('checked', true);
                        }
                        $('#comuna').val(response.distribuidor.comuna);
                        $('#comuna').trigger('change');
                        initAutocomplete();
                        $('#form-group-mapa').removeClass('ng-hide');
                        event_change = false;

                    } else {
                        toastr.error(response.error, "Error !!!");
                    }
                },
                failure: function (response) {
                    mApp.unblock('#distribuidor-form');

                    toastr.error(response.error, "Error !!!");
                }
            });

        }
    };
    //Eliminar
    var initAccionEliminar = function () {
        $(document).off('click', "#distribuidor-table-editable a.delete");
        $(document).on('click', "#distribuidor-table-editable a.delete", function (e) {
            e.preventDefault();

            rowDelete = $(this).data('id');
            $('#modal-eliminar').modal({
                'show': true
            });
        });

        $(document).off('click', "#btn-eliminar-distribuidor");
        $(document).on('click', "#btn-eliminar-distribuidor", function (e) {
            btnClickEliminar();
        });

        $(document).off('click', "#btn-delete");
        $(document).on('click', "#btn-delete", function (e) {
            btnClickModalEliminar();
        });

        $(document).off('click', "#btn-delete-selection");
        $(document).on('click', "#btn-delete-selection", function (e) {
            btnClickModalEliminarSeleccion();
        });

        function btnClickEliminar() {
            var ids = '';
            $('.m-datatable__cell--check .m-checkbox--brand > input[type="checkbox"]').each(function () {
                if ($(this).prop('checked')) {
                    var value = $(this).attr('value');
                    if (value != undefined) {
                        ids += value + ',';
                    }
                }
            });

            if (ids != '') {
                $('#modal-eliminar-seleccion').modal({
                    'show': true
                });
            } else {
                toastr.error('Seleccione los elementos a eliminar', "Error !!!");
            }
        };

        function btnClickModalEliminar() {
            var distribuidor_id = rowDelete;

            MyApp.block('#distribuidor-table-editable');

            $.ajax({
                type: "POST",
                url: "distribuidor/eliminarDistribuidor",
                dataType: "json",
                data: {
                    'distribuidor_id': distribuidor_id
                },
                success: function (response) {
                    mApp.unblock('#distribuidor-table-editable');
                    if (response.success) {
                        oTable.load();

                        toastr.success(response.message, "Exito !!!");

                    } else {
                        toastr.error(response.error, "Error !!!");
                    }
                },
                failure: function (response) {
                    mApp.unblock('#distribuidor-table-editable');

                    toastr.error(response.error, "Error !!!");
                }
            });
        };

        function btnClickModalEliminarSeleccion() {
            var ids = '';
            $('.m-datatable__cell--check .m-checkbox--brand > input[type="checkbox"]').each(function () {
                if ($(this).prop('checked')) {
                    var value = $(this).attr('value');
                    if (value != undefined) {
                        ids += value + ',';
                    }
                }
            });

            MyApp.block('#distribuidor-table-editable');

            $.ajax({
                type: "POST",
                url: "distribuidor/eliminarDistribuidores",
                dataType: "json",
                data: {
                    'ids': ids
                },
                success: function (response) {
                    mApp.unblock('#distribuidor-table-editable');
                    if (response.success) {
                        oTable.load();

                        toastr.success(response.message, "Exito !!!");

                    } else {
                        toastr.error(response.error, "Error !!!");
                    }
                },
                failure: function (response) {
                    mApp.unblock('#distribuidor-table-editable');
                    toastr.error(response.error, "Error !!!");
                }
            });
        };
    };

    //Eventos change
    var event_change = false;
    var initAccionChange = function () {
        $(document).off('change', ".event-change");
        $(document).on('change', ".event-change", function (e) {
            event_change = true;
        });

        $(document).off('click', "#btn-save-changes");
        $(document).on('click', "#btn-save-changes", function (e) {
            cerrarFormsConfirmated();
        });
    };
    var cerrarFormsConfirmated = function () {
        resetForms();
        $('#form-distribuidor').addClass('m--hide');
        $('#lista-distribuidor').removeClass('m--hide');
    };


    //Init dropzone
    var myDropZone = null;
    var realFiles = 0;
    var maxFiles = 1;
    var mostrarImagen = function (imagen, size, src) {
        if (imagen != "" && imagen != null) {
            var mockFile = {name: imagen, size: size};

            myDropZone.emit("addedfile", mockFile);
            myDropZone.emit("thumbnail", mockFile, src + imagen);
            myDropZone.emit("complete", mockFile);

            realFiles++;
            $('.dz-preview').addClass('dz-success');
        }
    };

    //initPortlets
    var initPortlets = function () {
        var portlet = new mPortlet('lista-distribuidor');
        portlet.on('afterFullscreenOn', function (portlet) {
            $('.m-portlet').addClass('m-portlet--fullscreen');
        });

        portlet.on('afterFullscreenOff', function (portlet) {
            $('.m-portlet').removeClass('m-portlet--fullscreen');
        });
    }

    var initTagsInput = function () {

        $('.tags').tagsInput({
            width: 'auto',
            defaultText: 'Tag...',
        });

    }

    //Wizard
    var activeTab = 1;
    var totalTabs = 2;
    var mostrarTab = function () {
        setTimeout(function () {
            switch (activeTab) {
                case 1:
                    $('#tab-general').tab('show');
                    break;
                case 2:
                    $('#tab-seo').tab('show');
                    break;
            }
        }, 0);
    }
    var resetWizard = function () {
        activeTab = 1;
        mostrarTab();
    }
    var validWizard = function () {
        var result = true;
        if (activeTab == 1) {

            var imagen = "";
            $('#my-dropzone .dz-preview').each(function (e) {
                imagen = $(this).attr('data-value-imagen');
            });

            if (!$('#distribuidor-form').valid() || imagen == "") {
                result = false;

                if (imagen == "") {
                    var $element = $('#processing-dropzone');
                    $element.tooltip("dispose") // Destroy any pre-existing tooltip so we can repopulate with new tooltip content
                        .data("title", "Es obligatorio seleccionar una imagen")
                        .addClass("has-error")
                        .tooltip({
                            placement: 'top'
                        }); // Create a new tooltip based on the error messsage we just set in the title

                    $element.closest('.form-group')
                        .removeClass('has-success').addClass('has-error');
                }
            }

        }

        return result;
    }

    var initSelects = function () {
        $('#comuna').select2({
            templateResult: function (data) {
                // We only really care if there is an element to pull classes from
                if (!data.element) {
                    return data.text;
                }
                var $element = $(data.element);
                var $wrapper = $('<span></span>');
                $wrapper.addClass($element[0].className);
                $wrapper.text(data.text);
                return $wrapper;
            }
        });
    };

    return {
        //main function to initiate the module
        init: function () {

            initTable();
            initSelects();
            initForm();
            initTagsInput();
            initAutocomplete();

            initAccionNuevo();
            initAccionSalvar();
            initAccionCerrar();

            initAccionEditar();
            initAccionEliminar();

            initAccionChange();

            initPortlets();
        }

    };

}();