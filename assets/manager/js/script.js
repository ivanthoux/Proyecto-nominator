var app = app || {};
var Handlebars = Handlebars || {};
app.language = "spanish";
app.sessionAlertPop = false;
app.sessionErrorAttempting = false;
app.login_modal = $(".login-box");
app.reset_form = $("#reset_form");
app.sessionErrorWait = 5000; //original value 5000
app.keepAliveWait = 240000; //original value 240000
var transactions = {};

app.keepAlive = function () {
    $.get(
        app.baseUrl + "manager/keep_alive",
        "",
        function (data) {
            if (data.status == "alive") {
                if (app.sessionErrorAttempting) {
                    app.sessionRestored();
                    app.sessionErrorWait = 5000;
                }
                window.setTimeout(app.keepAlive, app.keepAliveWait);
            } else {
                window.location.href = app.baseUrl + "home";
            }
        },
        "json"
    ).error(function () {
        if (app.sessionErrorAttempting) {
            app.sessionErrorWait = app.sessionErrorWait * 2;
        } else {
            window.setTimeout(app.sessionError, app.sessionErrorWait);
        }
        window.setTimeout(app.keepAlive, app.sessionErrorWait);
    });
};

app.sendNewPassword = function () {
    var password = $('[name="password"').val();
    var password_repeat = $('[name="password_repeat"').val();
    var user_id = $('[name="user_id"').val();
    var reset_hash = $('[name="reset_hash"').val();
    var reset_form = $("#reset_form");

    if ($.trim(password) === "" || $.trim(password_repeat) === "") {
        reset_form.append(
            '<div class="alert alert-danger">Complete su nueva contraseña</div>'
        );
        return;
    }
    if (password !== password_repeat) {
        reset_form.append(
            '<div class="alert alert-danger">Ambas deben coincidir</div>'
        );
        return;
    }

    reset_form.find(".alert").remove();
    $.ajax({
        url: app.baseUrl + "user/set_password",
        data: {
            password: password,
            password_repeat: password_repeat,
            user_id: user_id,
            reset_hash: reset_hash
        },
        type: "POST",
        dataType: "json",
        success: function (data) {
            console.log(data);
            if (data.status && data.status == "success") {
                reset_form.append(
                    '<div class="alert alert-success"> <a href="' +
                    app.baseUrl +
                    "user/set_password" +
                    '"Nueva contraseña guardada <br/> Volvé a la aplicación</a></div>'
                );
            } else {
                reset_form.append(
                    '<div class="alert alert-danger">' + data.errors + "</div>"
                );
            }
        }
    });
};

app.sessionError = function () {
    app.sessionErrorAttempting = true;
    if (!app.sessionAlertPop) {
        app.sessionAlertPop = app.sessionAlertCreate();
        $("body").append(app.sessionAlertPop);
    }
    app.sessionAlertPop.modal("show");
};
app.sessionAlertCreate = function () {
    return $(
        app.popCreate({
            body: "<p>Intentando recuperar la conexión. Espere...</p>",
            title: "Error de conexión",
            isStatic: true,
            footerBtn: "Intentar ahora",
            footerBtnClick: "app.keepAlive()"
        })
    );
};
app.popCreate = function (p) {
    modal =
        '<div class="modal ' +
        (p.primary ? "modal-primary" : "") +
        ' fade" ' +
        (p.isStatic ? 'data-backdrop="static"' : "") +
        ">";
    modal += '<div class="modal-dialog">';
    modal += '<div class="modal-content main">';
    modal += '<div class="modal-header">';
    modal +=
        '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>';
    modal += '<h4 class="modal-title">' + (p.title ? p.title : "") + "</h4>";
    modal += "</div>";
    modal += '<div class="modal-body">' + (p.body ? p.body : "") + "</div>";
    if (p.footerBtn) {
        modal +=
            '<div class="modal-footer"><button type="button" class="btn btn-blue" data-dismiss="modal">Cancelar</button><button type="button" class="btn btn-default" data-dismiss="modal" onclick="' +
            p.footerBtnClick +
            '">' +
            p.footerBtn +
            "</button></div>";
    }
    modal += "</div></div></div>";
    return $(modal);
};
app.sessionRestored = function () {
    app.sessionErrorAttempting = false;
    app.sessionAlertPop.modal("hide");
};

app.deleteUserConfirm = function (user) {
    if (!app.deleteUserPop) {
        app.deleteUserPop = $(
            app.popCreate({
                body: "<p>Seguro que quieres dar de baja este usuario?</p>",
                title: "Baja de usuario",
                footerBtn: "Baja",
                footerBtnClick: "app.deleteUserSend('" + user + "')"
            })
        );
        $("body").append(app.deleteUserPop);
    }
    app.deleteUserPop.modal("show");
};
app.deleteUserSend = function (id) {
    window.location = app.baseUrl + "manager/user_remove/" + id;
};
app.activeUserConfirm = function (user) {
    if (!app.deleteUserPop) {
        app.deleteUserPop = $(
            app.popCreate({
                body: "<p>Seguro que quieres activar este usuario?</p>",
                title: "Activar usuario",
                footerBtn: "Activar",
                footerBtnClick: "app.activeUserSend('" + user + "')"
            })
        );
        $("body").append(app.deleteUserPop);
    }
    app.deleteUserPop.modal("show");
};
app.activeUserSend = function (id) {
    window.location = app.baseUrl + "manager/user_active/" + id;
};
app.deletePhotoConfirm = function (photo) {
    if (!app.deleteUserPop) {
        app.deleteUserPop = $(
            app.popCreate({
                body: "<p>Seguro que quieres eliminar esta imagen?</p>",
                title: "Eliminar imagen",
                footerBtn: "Eliminar",
                footerBtnClick: "app.deletePhotoSend('" + photo + "')"
            })
        );
        $("body").append(app.deleteUserPop);
    }
    app.deleteUserPop.modal("show");
};
app.deletePhotoSend = function (id) {
    window.location = app.baseUrl + "manager/image_delete/gallery/" + id;
};

app.desactiveConfirm = function (elemId, objName, url) {
    if (!app.desactivePop) {
        app.desactivePop = $(
            app.popCreate({
                body: "<p>Seguro que quieres darlo de baja?</p>",
                title: "Dar de Baja " + objName,
                footerBtn: "Baja",
                footerBtnClick: "app.deleteConfirmed('" + url + "')"
            })
        );
        $("body").append(app.desactivePop);
    }
    app.desactivePop.modal("show");
};

app.activeConfirm = function (elemId, objName, url) {
    if (!app.activePop) {
        app.activePop = $(
            app.popCreate({
                body: "<p>Seguro que quieres re-activarlo?</p>",
                title: "Re-Activar " + objName,
                footerBtn: "Activar",
                footerBtnClick: "app.deleteConfirmed('" + url + "')"
            })
        );
        $("body").append(app.activePop);
    }
    app.activePop.modal("show");
};

app.deleteConfirm = function (elemId, objName, url, customDescription = '') {
    if (!app.deletePop) {
        app.deletePop = $(
            app.popCreate({
                body: customDescription != '' ? customDescription: "<p>Seguro que quieres eliminarlo?</p>",
                title: "Eliminar " + objName,
                footerBtn: "Eliminar",
                footerBtnClick: "app.deleteConfirmed('" + url + "')"
            })
        );
        $("body").append(app.deletePop);
    }
    app.deletePop.modal("show");
};

app.deleteConfirmed = function (url) {
    window.location = app.baseUrl + url;
};

app.actionConfirm = function (formSubmit, callback) {
    if (!app.actionPop) {
        app.actionPop = $(
            app.popCreate({
                body: "<p>Seguro que quieres confirmar esta acción?</p>",
                title: "Confirmar ",
                footerBtn: "Confirmar",
                footerBtnClick: (callback ? callback : "app.actionConfirmed('" + formSubmit + "')")
            })
        );
        $("body").append(app.actionPop);
    }
    app.actionPop.modal("show");
};

app.actionConfirmed = function (formSubmit) {
    console.log("callback");
    $(formSubmit).submit();
};

app.deleteConfirmAjax = function (elemId, objName, url, params, success, customDescription = '') {
    $(app.deletePopAjax).remove();
    app.deletePopAjax = $(
        app.popCreate({
            body: customDescription == '' ? "<p>Seguro que quieres eliminarlo?</p>": customDescription,
            title: "Eliminar " + objName,
            footerBtn: "Eliminar",
            footerBtnClick:
                "app.deleteConfirmedAjax('" +
                url +
                "', '" +
                params +
                "','" +
                success +
                "')"
        })
    );
    $("body").append(app.deletePopAjax);
    app.deletePopAjax.modal("show");
};

app.deleteConfirmedAjax = function (url, params, success) {
    app.ajax(url, params, function (data) {
        if (data != "") {
            var msg = "";
            for (var idx in data.errors) {
                msg += data.errors[idx];
            }
            app.dialog.error(msg);
        } else {
            window.location.href = success;
        }
    });
};

app.dialog = {
    warring: function (message, callback) {
        var config = {
            title: "<i class='fa fa-exclamation-circle'></i> Alerta",
            message: message,
            className: "warring-box",
            buttons: {
                close: {
                    /**
                     * @required String
                     * this button's label
                     */
                    label: "Cerrar",
                    /**
                     * @optional String
                     * an additional class to apply to the button
                     */
                    className: "btn-default pull-left",
                    /**
                     * @optional Function
                     * the callback to invoke when this button is clicked
                     */
                    callback: function () {
                        dialog.modal("hide");
                    }
                }
            }
        };
        if (callback) {
            config.buttons.edit = {
                /**
                 * @required String
                 * this button's label
                 */
                label: "Continuar",
                /**
                 * @optional String
                 * an additional class to apply to the button
                 */
                className: "btn-primary pull-right",
                /**
                 * @optional Function
                 * the callback to invoke when this button is clicked
                 */
                callback: function () {
                    if (callback) {
                        callback();
                    }
                }
            }
        }
        var dialog = bootbox.dialog(config);
    },
    error: function (message) {
        var dialog = bootbox.dialog({
            title: "<i class='fa fa-exclamation-circle'></i> Error",
            message: message,
            className: "error-box",
            buttons: {
                success: {
                    /**
                     * @required String
                     * this button's label
                     */
                    label: "Cerrar",
                    /**
                     * @optional String
                     * an additional class to apply to the button
                     */
                    className: "btn-default pull-left",
                    /**
                     * @optional Function
                     * the callback to invoke when this button is clicked
                     */
                    callback: function () {
                        dialog.modal("hide");
                    }
                }
            }
        });
    },
    success: function (title, message, callback) {
        var dialog = bootbox.dialog({
            title: "<i class='fa fa-check'></i> " + title,
            message: message,
            className: "success-box",
            buttons: {
                success: {
                    /**
                     * @required String
                     * this button's label
                     */
                    label: "Cerrar",
                    /**
                     * @optional String
                     * an additional class to apply to the button
                     */
                    className: "btn-default pull-left",
                    /**
                     * @optional Function
                     * the callback to invoke when this button is clicked
                     */
                    callback: function () {
                        dialog.modal("hide");
                        if (callback) {
                            callback();
                        }
                    }
                }
            }
        });
    }
};

app.rangePicker = {
    singleDatePicker: true,
    showDropdowns: true,
    autoApply: true,
    locale: {
        format: "DD-MM-YYYY",
        separator: " / ",
        applyLabel: "Aceptar",
        cancelLabel: "Cancelar",
        fromLabel: "Desde",
        toLabel: "Hasta",
        customRangeLabel: "Manual",
        daysOfWeek: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa"],
        monthNames: [
            "Enero",
            "Febrero",
            "Marzo",
            "Abril",
            "Mayo",
            "Junio",
            "Julio",
            "Agosto",
            "Septiembre",
            "Octubre",
            "Noviembre",
            "Diciembre"
        ],
        firstDay: 1
    }
};

app.datatable = function (config) {
    var _config = {
        id: "",
        url: window.location.href + "_datatables",
        filter: {
            placeholder: "Filtrar..."
        },
        searching: true,
        processing: true,
        dataSrc: function (response) {
            return response.data;
        }
    };
    _config = $.extend(_config, config);

    var list;
    var filterAdded = false;

    dataLoadingStart = function (type) {
        $(_config.id).css("opacity", "0.5");
    };
    dataLoadingEnd = function (type) {
        $(_config.id).css("opacity", "1");
        if (_config.callbackAjax !== undefined) {
            _config.callbackAjax(type);
        }
    };

    list = $(_config.id).DataTable({
        processing: true,
        serverSide: true,
        language: {
            // loadingRecords: '&nbsp;',
            // processing: 'Loading...',
            processing: "<span class='mb-1 mt-1 mr-1 ml-1'><i class='fa fa-spin fa-spinner'></i> Cargando información ...</span>",
            sLengthMenu: "_MENU_",
            sZeroRecords: "No se encontraron resultados",
            sEmptyTable: "Ningún dato disponible en esta tabla",
            sInfo: "_START_ al _END_ de _TOTAL_",
            sInfoEmpty: "0 de 0",
            sInfoFiltered: "(filtrado de un total de _MAX_)",
            sInfoPostFix: "",
            sSearch: "",
            sUrl: "",
            sInfoThousands: ",",
            loadingRecords: "&nbsp;",
            oPaginate: {
                sFirst: "<span class='fa fa-angle-double-left'></span>",
                sLast: "<span class='fa fa-angle-double-right'></span>",
                sNext: "<span class='fa fa-chevron-right'></span>",
                sPrevious: "<span class='fa fa-chevron-left'></span>"
            },
            oAria: {
                sSortAscending:
                    ": Activar para ordenar la columna de manera ascendente",
                sSortDescending:
                    ": Activar para ordenar la columna de manera descendente"
            }
        },
        fnPreDrawCallback: function () {
            dataLoadingStart();
        },
        fnDrawCallback: function (a) {
            dataLoadingEnd(a);
            if (!filterAdded) {
                filterAdded = true;
                $(_config.id + "_filter")
                    .find("input")
                    .attr("placeholder", _config.filter.placeholder);
            }
        },
        ajax: {
            url: _config.url,
            method: "post",
            dataSrc: _config.dataSrc
        },
        order: _config.order,
        searching: _config.searching,
        pageLength: _config.pageLength || 10
    });

    list.on("draw", function () {
        if (typeof _config.onDraw === "function") {
            _config.onDraw();
        }
    });

    return list;
};

app.ajax = function (url, params, callback) {
    $.ajax({
        url: url,
        data: params,
        type: "POST",
        dataType: "json",
        success: function (data) {
            eval(callback(data));
        },
        error: function (request, errorType, errorMessage) {
            eval(callback(request, errorType, errorMessage));
        }
    });
};

app.fileInputDefaultConfig = function (uploadUrl, deleteUrl) {
    return {
        uploadUrl: app.baseUrl + "manager/image_upload/" + uploadUrl,
        maxFileSize: 13000,
        showCaption: false,
        language: "es",
        maxFileCount: 1,
        autoReplace: true,
        allowedFileTypes: ["image"],
        deleteUrl: app.baseUrl + "manager/image_delete/" + deleteUrl
    };
};

app.appointmentFormLoaded = false;
app.appointmentFormLoad = function (start, end) {
    if (!app.appointmentFormLoaded) {
        app.appointmentFormLoaded = true;
        $("#appoint_start").datetimepicker({
            locale: "es",
            stepping: 15,
            showClose: true,
            // allowInputToggle: true,
            format: "D-MM-YYYY HH:mm",
            disabledHours: [0, 1, 2, 3, 4, 5, 6, 7, 21, 22, 23, 24],
            useCurrent: false, //Important! See issue #1075
            defaultDate: moment()
                .startOf("hour")
                .add(1, "hour")
        });
        $("#appoint_end").datetimepicker({
            locale: "es",
            stepping: 15,
            showClose: true,
            // allowInputToggle: true,
            format: "D-MM-YYYY HH:mm",
            useCurrent: false, //Important! See issue #1075
            disabledHours: [0, 1, 2, 3, 4, 5, 6, 7, 21, 22, 23, 24],
            defaultDate: moment()
                .startOf("hour")
                .add(90, "minutes")
        });
        $("#appoint_start").on("dp.change", function (e) {
            app.appointmentClientSelected();
            $("#appoint_end")
                .data("DateTimePicker")
                .minDate(e.date.add(15, "minutes"));
            $("#appoint_end")
                .data("DateTimePicker")
                .date(e.date.add(15, "minutes"));
        });
    }
    if (start) {
        $("#appoint_start")
            .data("DateTimePicker")
            .date(start);
    }
    if (end) {
        $("#appoint_end")
            .data("DateTimePicker")
            .date(end);
    }
};

app.appointmentClientSelected = function () {
    if ($("#appoint_client").val() !== "") {
        let lastselected = $("#appoint_clientpack").val();
        $("#appoint_clientpack").html("");
        $("#appoint_error").html("");
        $("#appoint_sessions_left").html("");
        $.ajax({
            url:
                app.baseUrl +
                "clientpacks/appoint?client=" +
                $("#appoint_client").val() +
                "&appointmentdate=" +
                $("#appoint_start").val(),
            method: "GET",
            dataType: "json",
            success: function (data) {
                if (data && data.data && data.data.length > 0) {
                    $("#save_event").attr("disabled", false);
                    for (let service = 0; service < data.data.length; service++) {
                        $("#appoint_clientpack").append(
                            '<option data-left="' +
                            data.data[service]["clientpack_sessions_left"] +
                            '" data-balance="' +
                            (data.data[service]["balance"]
                                ? data.data[service]["balance"]
                                : "") +
                            '" data-fullopen="' +
                            data.data[service]["pack_fullopen"] +
                            '" value="' +
                            data.data[service]["clientpack_id"] +
                            '" ' +
                            (lastselected === data.data[service]["clientpack_id"]
                                ? "selected"
                                : "") +
                            ">" +
                            data.data[service]["clientpack_title"] +
                            (data.data[service]["attached"]
                                ? " - " + data.data[service]["attached"]
                                : "") +
                            "</option"
                        );
                    }
                    app.appointmentServiceSelected();
                } else {
                    $("#appoint_error").html(
                        '<div class="alert alert-danger">Este cliente no posee servicios/packs ACTIVOS en estas fechas, modifique las fechas o registre el servicio/pack al cliente</div>'
                    );
                    $("#save_event").attr("disabled", true);
                }
            },
            error: function () {
                console.log("there was an error while fetching events!");
            }
        });
    }
};

app.appointmentServiceSelected = function () {
    let optionSelected = $("#appoint_clientpack").find("option:selected");
    if ($("#appoint_client").val() !== "") {
        if ($("#appoint_clientpack").val() !== "") {
            if (optionSelected.data("fullopen")) {
                $("#appoint_sessions_left").html(
                    '<div class="alert alert-info">Paquete LIBRE</div>'
                );
            } else {
                let left = parseInt(optionSelected.data("left"));
                if (left > 0) {
                    $("#appoint_sessions_left").html(
                        '<div class="alert alert-info">Restan ' + left + " sesiones</div>"
                    );
                } else {
                    $("#appoint_sessions_left").html(
                        '<div class="alert alert-warning">Ya no restan sesiones</div>'
                    );
                }
            }
            let balance = optionSelected.data("balance");
            if (balance !== "") {
                $("#appoint_sessions_left .alert").append(
                    '<br/> <h4><span class="label label-' +
                    (parseFloat(balance) <= 0 ? "info" : "danger") +
                    '">Saldo $ ' +
                    balance +
                    "</span></h4>"
                );
            }
        } else {
            $("#appoint_sessions_left").html(
                '<div class="alert alert-warning">Debes seleccionar un servicio/pack</div>'
            );
        }
    }
};

app.eventFormSubmit = function () {
    $("#appoint_error").html("");
    $("#save_event").attr("disabled", true);
    $.ajax({
        url: app.baseUrl + "scheduler/form/" + $("#appoint_id").val(),
        method: "POST",
        data: $("#eventForm").serialize(),
        dataType: "json",
        success: function (data) {
            $("#save_event").attr("disabled", false);
            if (data && data.status && data.status === "success") {
                if ($("#appoint_id").val() !== "") {
                    window.location.href = app.baseUrl + "scheduler";
                } else {
                    $("#calendar").fullCalendar("refetchEvents");
                    $("#form-appointment").modal("hide");
                }
            } else {
                $("#appoint_error").html(
                    '<div class="alert alert-danger">Hubo un error guardando el evento, comunicarse con soporte</div>'
                );
            }
        },
        error: function () {
            $("#save_event").attr("disabled", false);
            console.log("there was an error while fetching events!");
        }
    });
};

app.getHolydays = (year) => {
    let holydays;
    $.ajax({
        async: false,
        url: 'https://nolaborables.com.ar/api/v2/feriados/' + year + '?formato=mensual'
    }).done(data => {
        holydays = data;
    });
    return holydays;
};

$(document).ready(function () {
    var datatableobjs = $(".datatable_noconfig");
    if (datatableobjs.length) {
        datatableobjs.DataTable({
            language: {
                url: "//cdn.datatables.net/plug-ins/1.10.9/i18n/Spanish.json"
            }
        });
    }
    $(".datepicker").daterangepicker(app.rangePicker);
});
