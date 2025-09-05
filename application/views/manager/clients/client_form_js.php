<script>
    $(document).ready(function() {
        $('.edit').click(function(e) {
            e.stopPropagation();

            let input = $(this).parents('.control-group').find('input');
            let phones = ['client_phone', 'client_mobile', 'client_ref1_phone', 'client_ref2_phone', 'client_ref3_phone'];
            if (phones.includes(input.attr('id'))) {
                let number = input.val()
                    .replace(/\s/g, '')
                    .replace(/-/g, '');

                $('#' + input.attr('id')).val(Number(number));
                $('#' + input.attr('id')).mask('00 0000-0000')
            }

            $(input).removeClass('hidden');
            app.focusPhone(input);

            // $(this).parents('.control-group').find('.input-group-btn').removeClass('hidden');
            $(this).parents('.control-group').find('.editable').addClass('hidden');
            $(this).addClass('hidden');
        });
        <?php if (isset($edit)) : ?>
            app.changeRegion(<?= $edit['client_city'] ?>);

            app.changeSuns(<?= $edit['client_childrens'] ?>);
            $('#client_childrens').val(<?= $edit['client_childrens'] ?>);
            <?php if (empty($edit['client_mobile_validate'])) : ?>
                // $('#client_mobile_validate').siblings('.control-group').find('.edit').click();
            <?php endif; ?>
            <?php if (empty($edit['client_ref1_phone_validate'])) : ?>
                // $('#client_ref1_phone_validate').siblings('.control-group').find('.edit').click();
            <?php endif; ?>
        <?php endif; ?>
    });
    app.changeSuns = (childs) => {
        $('#client_childrens').val('');
        if (childs >= 1) {
            $('#childrens').removeClass('hidden');
        } else {
            $('#childrens').addClass('hidden');
        }
    };
    app.datepickerLoad = function() {
        $('.datepicker').daterangepicker(app.rangePicker);
    };

    app.changeRegion = function(client_city = null) {
        console.log(client_city);
        if ($('#region_id').val()) {
            $.get(app.baseUrl + 'scripts/getCities/' + $('#region_id').val(), '', function(data) {
                if (data.status == 'success') {
                    $('#client_city').html("<option value=''>Localidades</option>");

                    data.data.forEach(function(city) {
                        let option = $("<option>")
                            .val(city.id)
                            .html(city.name);
                        if (client_city !== null && city.id == client_city) {
                            option.prop('selected', true);
                        }
                        $('#client_city').append(option);
                    });
                    if ($('#client_city option').length == 2) {
                        $('#client_city option:eq(1)').prop('selected', true);
                    }
                } else {
                    console.log("ERROR");
                }
            }, 'json').error(function(error) {
                console.log(error)
            });
        } else {
            $('#client_city').html("<option value=''>Localidades</option>");
        }
    };

    var btnValidate;
    var idValidate;
    var checkValidate = 0;
    app.getCode = (btn, id, type = 'confirm') => {
        if ($('#' + id + '_validate').val() === '') {
            let number = $('#' + id).val()
                .replace(/\s/g, '')
                .replace(/-/g, '');

            if (!isNaN(Number(number)) && number.length == 10) {
                btnValidate = btn;
                idValidate = id;
                checkValidate = <?= isset($checkvalidate) ? $checkvalidate : 12 ?>;

                if (type === 'confirm') {
                    confirm = bootbox.dialog({
                        title: "<i class='fa fa-question-circle'></i> Consulta",
                        message: "¿Desea Volver a enviar el código de validación?",
                        className: "warring-box",
                        buttons: {
                            close: {
                                label: "No",
                                className: "btn-default pull-left",
                                callback: function() {
                                    confirm.modal("hide");
                                    app.loadding = bootbox.dialog({
                                        message: '<p class="text-center mb-0 ml-0 mr-0"><i class="fa fa-spin fa-spinner"></i> Enviando SMS, espere por favor...</p>',
                                        closeButton: false
                                    });
                                    app.loadding.on('shown.bs.modal', function(e) {
                                        app.ajax('<?= site_url('clients/getCodeValidate/') ?>' + number, [], function(request) {
                                            if (request.status === 'success') {
                                                app.loadding.modal("hide");

                                                $("#code_confirm input").val('');
                                                $('#code_id').val(request.id);
                                                $("#code_confirm").modal('show');
                                            } else {
                                                app.loadding.modal("hide");
                                                app.dialog.error('No se encontro envío alguno para ese número');
                                            }
                                        });
                                    });
                                }
                            },
                            edit: {
                                label: "Si",
                                className: "btn-primary pull-right",
                                callback: function() {
                                    sendSMS();
                                }
                            }
                        }
                    });
                } else {
                    sendSMS();
                }
            } else {
                app.dialog.warring('Formato de número invalido (00 0000-0000)');
            }

            function sendSMS() {
                app.loadding = bootbox.dialog({
                    message: '<p class="text-center mb-0 ml-0 mr-0"><i class="fa fa-spin fa-spinner"></i> Enviando SMS, espere por favor...</p>',
                    closeButton: false
                });
                app.loadding.on('shown.bs.modal', function(e) {
                    app.ajax('<?= site_url('clients/setCodeValidate/') ?>' + number + '/' + type, [], function(request) {
                        if (request.status === 'success') {
                            if (type === 'confirm') {
                                app.loadding.modal("hide");

                                $("#code_confirm input").val('');
                                $('#code_id').val(request.id);
                                $("#code_confirm").modal('show');
                            } else {
                                app.checkResponse(request.id);
                            }
                        } else {
                            app.loadding.modal("hide");
                            app.dialog.error(request.message);
                        }
                    });
                });
            }
        }
    };

    app.checkResponse = (id) => {
        if (checkValidate > 0) {
            app.ajax('<?= site_url('clients/checkStatusSMS/') ?>' + id, [], function(request) {
                if (request.status === 'success') {
                    if (["1", "4"].includes(request.state)) {
                        $('#' + idValidate + '_validate').val(id);

                        app.focusPhone($('#' + idValidate)[0]);

                        $(btnValidate).removeClass('btn-danger');
                        $(btnValidate).addClass('bg-olive');

                        $(btnValidate).find('i').addClass('fa-check');
                        $(btnValidate).find('i').removeClass('fa-exclamation-triangle');

                        app.loadding.modal("hide");
                    } else if (request.state == null) {
                        checkValidate--;
                        setTimeout(() => {
                            app.checkResponse(id);
                        }, 5000);
                    } else {
                        checkValidate = 0;
                        app.loadding.modal("hide");
                        app.dialog.warring('El número no puedo ser validado, por favor intente con otro número');
                    }
                } else {
                    checkValidate = 0;
                    app.loadding.modal("hide");
                    app.dialog.warring('El número no puedo ser validado, por favor intente con otro número');
                }
            });
        } else {
            app.loadding.modal("hide");
            app.dialog.warring('El número no puedo ser validado, por favor intente con otro número');
        }
    }

    app.focusPhone = (obj) => {
        $(obj).data('val', $(obj).val());
        $(obj).data('validate', $('#' + $(obj).attr('id') + '_validate').val());
    }

    app.changePhone = (obj) => {
        var prev = $(obj).data('val');
        var current = $(obj).val();

        if (prev !== current) {
            $('#' + $(obj).attr('id') + '_validate').val('');

            $(obj).siblings('.input-group-btn').find('button').addClass('btn-danger');
            $(obj).siblings('.input-group-btn').find('button').removeClass('bg-olive');

            $(obj).siblings('.input-group-btn').find('button').find('i').addClass('fa-exclamation-triangle');
            $(obj).siblings('.input-group-btn').find('button').find('i').removeClass('fa-check');
        } else {
            $('#' + $(obj).attr('id') + '_validate').val($(obj).data('validate'));

            $(obj).siblings('.input-group-btn').find('button').addClass('bg-olive');
            $(obj).siblings('.input-group-btn').find('button').removeClass('btn-danger');

            $(obj).siblings('.input-group-btn').find('button').find('i').addClass('fa-check');
            $(obj).siblings('.input-group-btn').find('button').find('i').removeClass('fa-exclamation-triangle');
        }
    }

    app.validateCode = () => {
        if ($('#code').val().trim()) {
            app.ajax('<?= site_url('clients/validateCodePhone/') ?>' + $('#code_id').val() + '/' + $('#code').val() + '/<?= !empty($edit) ? $edit['client_id'] : 0 ?>', [], function(request) {
                if (request.status === 'success') {
                    $('#' + idValidate + '_validate').val($('#code_id').val());

                    app.focusPhone($('#' + idValidate)[0]);

                    $(btnValidate).removeClass('btn-danger');
                    $(btnValidate).addClass('bg-olive');

                    $(btnValidate).find('i').addClass('fa-check');
                    $(btnValidate).find('i').removeClass('fa-exclamation-triangle');

                    $("#code_confirm").modal('hide');
                } else {
                    app.dialog.warring('El código ingresado no es correcto, intentelo nuevamente');
                }
            });
        }
    }

    app.validateForm = () => {
        $('#submit').val('');
        // if ($('#submit').val() === '' && $('#client_mobile_validate').val() === '') {
        //     app.dialog.error('El número de celular debe ser validado');
        // } else {
        $('#submit').val(1);
        // }
        return ($('#submit').val() !== '');
    }

    app.capital_letter = (obj) => {
        let str = $(obj).val().split(" ");

        for (var i = 0, x = str.length; i < x; i++) {
            str[i] = str[i][0].toUpperCase() + str[i].substr(1);
        }

        $(obj).val(str.join(" "));
    }
</script>