var Servicios = function () {

    var servicio_id;

    //Reset forms
    var resetForms = function () {
        $('#servicio-form input, #servicio-form textarea').each(function (e) {
            $element = $(this);

            if ($(this).attr('id') != "servicio_id") {
                $element.val('');

                $element.data("title", "").removeClass("has-error").tooltip("dispose");
                $element.closest('.form-group').removeClass('has-error').addClass('success');
            }
        });

        $('#descripcion').summernote('code', '');

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
    };

    //Validacion
    var initForm = function () {
        $("#servicio-form").validate({
            rules: {
                titulo: {
                    required: true
                }
            },
            messages: {
                titulo: "Este campo es obligatorio"
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

    //Salvar
    var initAccionSalvar = function () {
        $(document).off('click', "#btn-salvar-servicio");
        $(document).on('click', "#btn-salvar-servicio", function (e) {
            btnClickSalvarForm();
        });

        function btnClickSalvarForm() {
            mUtil.scrollTo();

            var descripcion = $('#descripcion').summernote('code');
            var tags = $('#tags').val();

            if ($('#servicio-form').valid() && descripcion !== "" && tags != "") {

                var servicio_id = $('#servicio_id').val();
                var titulo = $('#titulo').val();

                var imagenes = "";
                $('#my-dropzone .dz-preview').each(function (e) {
                    imagenes += $(this).attr('data-value-imagen') + ',';
                });

                MyApp.block('#form-servicio');

                $.ajax({
                    type: "POST",
                    url: "servicio/salvarServicio",
                    dataType: "json",
                    data: {
                        'servicio_id': servicio_id,
                        'titulo': titulo,
                        'descripcion': descripcion,
                        'tags': tags,
                        'imagenes': imagenes
                    },
                    success: function (response) {
                        mApp.unblock('#form-servicio');
                        if (response.success) {

                            toastr.success(response.message, "Exito !!!");

                            servicio_id = response.servicio_id;
                            $("#servicio_id").val(servicio_id);
                        } else {
                            toastr.error(response.error, "Error !!!");
                        }
                    },
                    failure: function (response) {
                        mApp.unblock('#form-servicio');

                        toastr.error(response.error, "Error !!!");
                    }
                });

            } else {
                if (tags == "") {
                    var $element = $('.tagsinput');
                    $element.tooltip("dispose") // Destroy any pre-existing tooltip so we can repopulate with new tooltip content
                        .data("title", "Este campo es obligatorio")
                        .addClass("has-error")
                        .tooltip({
                            placement: 'top'
                        }); // Create a new tooltip based on the error messsage we just set in the title

                    $element.closest('.form-group')
                        .removeClass('has-success').addClass('has-error');
                }
                if (descripcion == "") {
                    var $element = $('.note-editor');
                    $element.tooltip("dispose") // Destroy any pre-existing tooltip so we can repopulate with new tooltip content
                        .data("title", "Este campo es obligatorio")
                        .addClass("has-error")
                        .tooltip({
                            placement: 'top'
                        }); // Create a new tooltip based on the error messsage we just set in the title

                    $element.closest('.form-group')
                        .removeClass('has-success').addClass('has-error');
                }
            }
        };
    }
    var editRow = function (servicio_id) {
        resetForms();

        MyApp.block('#servicio-form');

        $.ajax({
            type: "POST",
            url: "servicio/cargarDatos",
            dataType: "json",
            data: {
                'servicio_id': servicio_id
            },
            success: function (response) {
                mApp.unblock('#servicio-form');
                if (response.success) {
                    //Datos marca

                    $('#titulo').val(response.servicio.titulo);
                    $('#descripcion').summernote('code', response.servicio.descripcion);

                    var tags = response.servicio.tags;
                    if (tags != "" && tags != null) {
                        tags = tags.split(',');
                        for (var i = 0; i < tags.length; i++)
                            $('#tags').addTag(tags[i]);
                    }

                    var images = response.servicio.imagenes;
                    for (var i = 0; i < images.length; i++) {
                        mostrarImagen(images[i].imagen[0], images[i].imagen[1], images[i].imagen[2]);
                    }

                } else {
                    toastr.error(response.error, "Error !!!");
                }
            },
            failure: function (response) {
                mApp.unblock('#servicio-form');

                toastr.error(response.error, "Error !!!");
            }
        });

    }

    //Init dropzone
    var myDropZone = null;
    var realFiles = 0;
    var initDropZone = function () {
        // Prevent Dropzone from auto discovering this element:
        Dropzone.options.myDropzone = false;
        Dropzone.autoDiscover = false;

        myDropZone = new Dropzone("div#my-dropzone", {
            thumbnailWidth: 1131,
            thumbnailHeight: 780,
            parallelUploads: 1,
            maxFilesize: 10, // MB
            paramName: "foto", // The name that will be used to transfer the file
            dictCancelUploadConfirmation: "¿Estas seguro que quieres cancelar la subida del archivo?",
            dictRemoveFileConfirmation: "¿Estas seguro que quieres eliminar el archivo?",
            dictDefaultMessage: "Arrastra tu imagen o haz click aquí",
            addRemoveLinks: true,
            dictRemoveFile: "Eliminar",
            dictCancelUpload: "Cancelar",
            url: "servicio/salvarImagen",
            accept: function (file, done) {
                realFiles++;
                if (!file.type.match(/image.*/)) {
                    toastr.error('Por favor, verifique que el archivo seleccionado sea una imagen', "Error !!!");
                    //Eliminar
                    eliminarImagen(file);

                } else {
                    done();
                }
            },
            success: function (file, response) {

                if (file.width != '1169' || file.height != '487') {
                    toastr.error('Las dimensiones de la imagen deben ser 1169 x 487', "Error !!!");
                    myDropZone.removeFile(file);
                } else {

                    $(file.previewElement).attr('data-value-imagen', response.name);
                    if (file.previewElement) {
                        return file.previewElement.classList.add("dz-success");
                    }

                    event_change = true;
                }

            },
            removedfile: function (file) {
                var imagen = $(file.previewElement).attr('data-value-imagen');

                MyApp.block('#processing-dropzone');

                $.ajax({
                    type: "POST",
                    url: "servicio/eliminarImagen",
                    dataType: "json",
                    data: {
                        'imagen': imagen
                    },
                    success: function (response) {
                        mApp.unblock('#processing-dropzone');
                        if (response.success) {
                            eliminarImagen(file);
                            return;

                        } else {
                            toastr.error(response.error, "Error !!!");
                            return;
                        }
                    },
                    failure: function (response) {
                        mApp.unblock('#processing-dropzone');

                        toastr.error(response.error, "Error !!!");
                        return;
                    }
                });
            },
            thumbnail: function (file, dataUrl) {
                var thumbnailElement, _i, _len, _ref, _results;
                if (file.previewElement) {
                    file.previewElement.classList.remove("dz-file-preview");
                    file.previewElement.classList.add("dz-image-preview");
                    _ref = file.previewElement.querySelectorAll("[data-dz-thumbnail]");
                    _results = [];
                    for (_i = 0, _len = _ref.length; _i < _len; _i++) {
                        thumbnailElement = _ref[_i];
                        thumbnailElement.alt = file.name;
                        _results.push(thumbnailElement.src = dataUrl);
                    }
                    $(file.previewElement).attr('data-value-imagen', file.name);
                    return _results;
                }
            },
        });
        Dropzone.confirm = function (question, accepted, rejected) {
            // Ask the question, and call accepted() or rejected() accordingly.
            // CAREFUL: rejected might not be defined. Do nothing in that case.
            $('#confirm-question').html(question);

            $('#modal-confirm').modal({
                show: true
            });


            $(document).off('click', "#btn-confirm-aceptar");
            $(document).on('click', '#btn-confirm-aceptar', function () {
                accepted();
            });
            $(document).off('click', "#btn-confirm-cancelar");
            $(document).on('click', '#btn-confirm-cancelar', function () {
                //rejected();
            });
        }

        function eliminarImagen(file) {
            var _ref;
            if (file.previewElement) {
                if ((_ref = file.previewElement) != null) {
                    _ref.parentNode.removeChild(file.previewElement);
                }
            }
            realFiles--;
            event_change = true;
            if (realFiles < 1) {
                if ($('#my-dropzone').hasClass('dz-started'))
                    $('#my-dropzone').removeClass('dz-started');
            }

        }
    };
    var mostrarImagen = function (imagen, size, src) {
        if (imagen != "" && imagen != null) {
            var mockFile = {name: imagen, size: size};

            myDropZone.emit("addedfile", mockFile);
            myDropZone.emit("thumbnail", mockFile, src + imagen);
            myDropZone.emit("complete", mockFile);

            realFiles++;
            $('my-dropzone .dz-preview').addClass('dz-success');
        }
    };

    var initTagsInput = function () {

        $('.tags').tagsInput({
            width: 'auto',
            defaultText: 'Tag...',
        });

    }

    return {
        //main function to initiate the module
        init: function () {

            initForm();
            initDropZone();
            initTagsInput();

            initAccionSalvar();

            servicio_id = $("#servicio_id").val();
            if (servicio_id != "") {
                editRow(servicio_id);
            }

        }

    };

}();