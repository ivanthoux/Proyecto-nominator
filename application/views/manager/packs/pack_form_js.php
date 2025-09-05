<script>
    var siisaveraz = <?= isset($edit['packpoints']) ? count($edit['packpoints']) : 1 ?>;
    app.packfull = function(el) {
        if ($(el).is(':checked')) {
            $("#pack_sessions").attr('readonly', true);
            $("#pack_sessions").val('');
        } else {
            $("#pack_sessions").attr('readonly', false);
        }
    };

    app.addSiisaVeraz = () => {
        $("#siisaveraz input").val('');
        $("#siisaveraz select").val('');
        $("#siisaveraz #action").val('add');
        $("#siisaveraz").modal('show');
    };
    app.deleteSiisaVeraz = (id) => {
        $("#siisaveraz-list #packpoint_" + id).remove();
        if ($('#siisaveraz-list > div.billable_item').length == 0) {
            $('#siisaveraz_control').val('');
        }
    };
    app.editSiisaVeraz = (id) => {
        $("#siisaveraz input").val('')
        $("#siisaveraz select").val('')
        $("#siisaveraz #action").val(id);

        $('#packpoint_' + id + ' input').each((idx, input) => {
            if ($(input).attr('name').indexOf('packpoint_aut_veraz') !== -1) {
                $("#siisaveraz #packpoint_aut_veraz").val($(input).val());
            }
            if ($(input).attr('name').indexOf('packpoint_min_veraz') !== -1) {
                $("#siisaveraz #packpoint_min_veraz").val($(input).val());
            }
            if ($(input).attr('name').indexOf('packpoint_aut_siisa') !== -1) {
                $("#siisaveraz #packpoint_aut_siisa").val($(input).val());
            }
            if ($(input).attr('name').indexOf('packpoint_min_siisa') !== -1) {
                $("#siisaveraz #packpoint_min_siisa").val($(input).val());
            }
            if ($(input).attr('name').indexOf('packpoint_type') !== -1) {
                $("#siisaveraz #packpoint_type").val($(input).val());
            }
            if ($(input).attr('name').indexOf('packpoint_min_sessions') !== -1) {
                $("#siisaveraz #packpoint_min_sessions").val($(input).val());
            }
            if ($(input).attr('name').indexOf('packpoint_max_sessions') !== -1) {
                $("#siisaveraz #packpoint_max_sessions").val($(input).val());
            }
        });
        $("#siisaveraz").modal('show')
    };
    app.saveSiisaVeraz = () => {
        let action = $("#siisaveraz #action").val();
        let list = $('#siisaveraz-list');
        let amount = siisaveraz;
        let packpoint_aut_veraz = Number($('#siisaveraz #packpoint_aut_veraz').val())
        let packpoint_min_veraz = Number($('#siisaveraz #packpoint_min_veraz').val())
        let packpoint_aut_siisa = Number($('#siisaveraz #packpoint_aut_siisa').val())
        let packpoint_min_siisa = Number($('#siisaveraz #packpoint_min_siisa').val())
        let packpoint_type = $('#siisaveraz #packpoint_type option:selected')
        let packpoint_min_sessions = Number($('#siisaveraz #packpoint_min_sessions').val())
        let packpoint_max_sessions = Number($('#siisaveraz #packpoint_max_sessions').val())

        if (app.checkSiisaVeraz({
                packpoint_aut_veraz: packpoint_aut_veraz,
                packpoint_min_veraz: packpoint_min_veraz,
                packpoint_aut_siisa: packpoint_aut_siisa,
                packpoint_min_siisa: packpoint_min_siisa,
                packpoint_type: packpoint_type,
                packpoint_min_sessions: packpoint_min_sessions,
                packpoint_max_sessions: packpoint_max_sessions
            })) {
            if (action === 'add') {
                list.append($('<div class="list-group-item billable_item position-relative" id="packpoint_-' + amount + '">')
                    .append($('<div class="row">')
                        .append($('<div class="col-sm-1">')
                            .append($('<label>').html(packpoint_aut_veraz))
                            .append($('<input type="hidden" name="packpoints[' + amount + '][packpoint_aut_veraz]" />').val(packpoint_aut_veraz))
                        )
                        .append($('<div class="col-sm-1">')
                            .append($('<label>').html(packpoint_min_veraz))
                            .append($('<input type="hidden" name="packpoints[' + amount + '][packpoint_min_veraz]" />').val(packpoint_min_veraz))
                        )
                        .append($('<div class="col-sm-1">')
                            .append($('<label>').html(packpoint_aut_siisa))
                            .append($('<input type="hidden" name="packpoints[' + amount + '][packpoint_aut_siisa]" />').val(packpoint_aut_siisa))
                        )
                        .append($('<div class="col-sm-1">')
                            .append($('<label>').html(packpoint_min_siisa))
                            .append($('<input type="hidden" name="packpoints[' + amount + '][packpoint_min_siisa]" />').val(packpoint_min_siisa))
                        )
                        .append($('<div class="col-sm-3">')
                            .append($('<label>').html(packpoint_type.html()))
                            .append($('<input type="hidden" name="packpoints[' + amount + '][packpoint_type]" />').val(packpoint_type.val()))
                        )
                        .append($('<div class="col-sm-1">')
                            .append($('<label>').html(packpoint_min_sessions))
                            .append($('<input type="hidden" name="packpoints[' + amount + '][packpoint_min_sessions]" />').val(packpoint_min_sessions))
                        )
                        .append($('<div class="col-sm-1">')
                            .append($('<label>').html(packpoint_max_sessions))
                            .append($('<input type="hidden" name="packpoints[' + amount + '][packpoint_max_sessions]" />').val(packpoint_max_sessions))
                        )
                        .append($('<div class="col-sm-3">')
                            .append($('<a href="#" class="btn btn-warning btn-sm" title="Editar" onclick="app.editSiisaVeraz(-' + amount + ');"><i class="fa fa-edit"></i></a>'))
                            .append('&nbsp;')
                            .append($('<a href="#" class="btn btn-danger btn-sm" title="Eliminar" onclick="app.deleteSiisaVeraz(-' + amount + ');"><i class="fa fa-remove"></i></a>'))
                        )
                    ));
                siisaveraz++;
                $('#siisaveraz_control').val(siisaveraz);
            } else {
                $('#siisaveraz-list #packpoint_' + action + ' input').each((idx, input) => {
                    if ($(input).attr('name').indexOf('packpoint_aut_veraz') !== -1) {
                        $(input).siblings('label').html(packpoint_aut_veraz);
                        $(input).val(packpoint_aut_veraz);
                    }
                    if ($(input).attr('name').indexOf('packpoint_min_veraz') !== -1) {
                        $(input).siblings('label').html(packpoint_min_veraz);
                        $(input).val(packpoint_min_veraz);
                    }
                    if ($(input).attr('name').indexOf('packpoint_aut_siisa') !== -1) {
                        $(input).siblings('label').html(packpoint_aut_siisa);
                        $(input).val(packpoint_aut_siisa);
                    }
                    if ($(input).attr('name').indexOf('packpoint_min_siisa') !== -1) {
                        $(input).siblings('label').html(packpoint_min_siisa);
                        $(input).val(packpoint_min_siisa);
                    }
                    if ($(input).attr('name').indexOf('packpoint_type') !== -1) {
                        $(input).siblings('label').html(packpoint_type.html());
                        $(input).val(packpoint_type.val());
                    }
                    if ($(input).attr('name').indexOf('packpoint_min_sessions') !== -1) {
                        $(input).siblings('label').html(packpoint_min_sessions);
                        $(input).val(packpoint_min_sessions);
                    }
                    if ($(input).attr('name').indexOf('packpoint_max_sessions') !== -1) {
                        $(input).siblings('label').html(packpoint_max_sessions);
                        $(input).val(packpoint_max_sessions);
                    }
                });
            }
            $("#siisaveraz").modal('hide');
        }
    };
    app.checkSiisaVeraz = (data) => {
        let errors = [];
        if (data.packpoint_type.val() === '') {
            errors.push('Tipo de puntuación: debe espesificar una opción');
        }
        if (data.packpoint_min_sessions < 1 || isNaN(data.packpoint_min_sessions)) {
            errors.push('Cuota Mínima: entero mayor a cero(0)');
        }
        if (data.packpoint_max_sessions < 1 || isNaN(data.packpoint_max_sessions)) {
            errors.push('Cuota Máxima: entero mayor a cero(0)');
        }
        if (data.packpoint_min_sessions > data.packpoint_max_sessions) {
            errors.push('Cuota Mínima no puede ser mayor que Cuota Máxima');
        }
        if (errors.length) {
            app.paymentAlert = $(app.popCreate({
                body: '<p><ul><li>' + errors.join('</li><li>') + '</li></ul></p>',
                title: 'Error',
                isStatic: true,
            }));
            $('body').append(app.paymentAlert);
            app.paymentAlert.modal('show');
            return false;
        }
        return true;
    };



    app.addDiscount = () => {
        $("#discount input").val('');
        $("#discount #action").val('add');
        $("#discount").modal('show');
    };
    app.deleteDiscount = (id) => {
        $("#discount-list #packdiscount_" + id).remove();
    };
    app.editDiscount = (id) => {
        $("#discount input").val('');
        $("#discount #action").val(id);

        $('#packdiscount_' + id + ' input').each((idx, input) => {
            if ($(input).attr('name').indexOf('packdiscount_value') !== -1) {
                $("#discount #packdiscount_value").val($(input).val());
            }
            if ($(input).attr('name').indexOf('packdiscount_min_sessions') !== -1) {
                $("#discount #packdiscount_min_sessions").val($(input).val());
            }
            if ($(input).attr('name').indexOf('packdiscount_max_sessions') !== -1) {
                $("#discount #packdiscount_max_sessions").val($(input).val());
            }
        });
        $("#discount").modal('show')
    };
    app.saveDiscount = () => {
        let action = $("#discount #action").val();
        let list = $('#discount-list');
        let amount = siisaveraz;
        let packdiscount_value = Number($('#discount #packdiscount_value').val()).toFixed(2);
        let packdiscount_min_sessions = Number($('#discount #packdiscount_min_sessions').val())
        let packdiscount_max_sessions = Number($('#discount #packdiscount_max_sessions').val())

        if (app.checkDiscount({
                packdiscount_value: packdiscount_value,
                packdiscount_min_sessions: packdiscount_min_sessions,
                packdiscount_max_sessions: packdiscount_max_sessions
            })) {
            if (action === 'add') {
                list.append($('<div class="list-group-item billable_item position-relative" id="packdiscount_-' + amount + '">')
                    .append($('<div class="row">')
                        .append($('<div class="col-sm-2">')
                            .append($('<label>').html(packdiscount_value.replace('.', ',').replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.') + ' %'))
                            .append($('<input type="hidden" name="packdiscounts[' + amount + '][packdiscount_value]" />').val(packdiscount_value))
                        )
                        .append($('<div class="col-sm-2">')
                            .append($('<label>').html(packdiscount_min_sessions))
                            .append($('<input type="hidden" name="packdiscounts[' + amount + '][packdiscount_min_sessions]" />').val(packdiscount_min_sessions))
                        )
                        .append($('<div class="col-sm-2">')
                            .append($('<label>').html(packdiscount_max_sessions))
                            .append($('<input type="hidden" name="packdiscounts[' + amount + '][packdiscount_max_sessions]" />').val(packdiscount_max_sessions))
                        )
                        .append($('<div class="col-sm-6">')
                            .append($('<a href="#" class="btn btn-warning btn-sm" title="Editar" onclick="app.editDiscount(-' + amount + ');"><i class="fa fa-edit"></i></a>'))
                            .append('&nbsp;')
                            .append($('<a href="#" class="btn btn-danger btn-sm" title="Eliminar" onclick="app.deleteDiscount(-' + amount + ');"><i class="fa fa-remove"></i></a>'))
                        )
                    ));
                siisaveraz++;
            } else {
                $('#discount-list #packdiscount_' + action + ' input').each((idx, input) => {
                    if ($(input).attr('name').indexOf('packdiscount_value') !== -1) {
                        $(input).siblings('label').html(packdiscount_value.replace('.', ',').replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.') + ' %');
                        $(input).val(packdiscount_value);
                    }
                    if ($(input).attr('name').indexOf('packdiscount_min_sessions') !== -1) {
                        $(input).siblings('label').html(packdiscount_min_sessions);
                        $(input).val(packdiscount_min_sessions);
                    }
                    if ($(input).attr('name').indexOf('packdiscount_max_sessions') !== -1) {
                        $(input).siblings('label').html(packdiscount_max_sessions);
                        $(input).val(packdiscount_max_sessions);
                    }
                });
            }
            $("#discount").modal('hide');
        }
    };
    app.checkDiscount = (data) => {
        let errors = [];
        if (data.packdiscount_value <= 0 || data.packdiscount_value > 100 || isNaN(data.packdiscount_value)) {
            errors.push('Descuento: numérico mayor a cero (0) y menor o igual a cien (100)');
        }
        if (data.packdiscount_min_sessions < 1 || isNaN(data.packdiscount_min_sessions)) {
            errors.push('Cuota Mínima: entero mayor a cero(0)');
        }
        if (data.packdiscount_max_sessions < 1 || isNaN(data.packdiscount_max_sessions)) {
            errors.push('Cuota Máxima: entero mayor a cero(0)');
        }
        if (data.packdiscount_min_sessions > data.packdiscount_max_sessions) {
            errors.push('Cuota Mínima no puede ser mayor que Cuota Máxima');
        }
        if (errors.length) {
            app.paymentAlert = $(app.popCreate({
                body: '<p><ul><li>' + errors.join('</li><li>') + '</li></ul></p>',
                title: 'Error',
                isStatic: true,
            }));
            $('body').append(app.paymentAlert);
            app.paymentAlert.modal('show');
            return false;
        }
        return true;
    };
</script>