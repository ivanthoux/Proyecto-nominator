var app = app || {};
app.login_modal = $(".login-box");
app.reset_form = $(".login-box");
app.employer_register_form = $(".register-box");

app.login = function() {
  var email = app.login_modal.find('[name="email"]').val();
  var pass = app.login_modal.find('[name="password"]').val();

  if ($.trim(email) === "" || $.trim(pass) === "") {
    app.dialog.error("Por favor complete los campos de email y contraseña");
    return;
  }

  app.login_modal.find(".login-box-body").remove(".alert");
  $.ajax({
    url: app.baseUrl + "user/get_login",
    data: {
      email: email,
      password: pass,
      createcookie: app.login_modal.find('[name="createcookie"]').is(":checked")
        ? 1
        : 0
    },
    type: "POST",
    dataType: "json",
    success: function(data) {
      if (data.status && data.status == "success") {
        window.location.href = app.baseUrl + "manager";
      } else {
        if (data.errors) {
          app.dialog.error(data.errors);
        } else {
          app.dialog.error("Algo salió mal");
        }
      }
    }
  });
};
app.signup = function() {
  var firstname = app.login_modal.find('[name="firstname"]').val();
  var lastname = app.login_modal.find('[name="lastname"]').val();
  var account = app.login_modal.find('[name="account"]').val();
  var email = app.login_modal.find('[name="email"]').val();
  var pass = app.login_modal.find('[name="password"]').val();
  var pass_repeat = app.login_modal.find('[name="password_repeat"]').val();

  if ($.trim(email) === "" || $.trim(pass) === "") {
    app.dialog.error("Por favor complete los campos de email y contraseña");
    return;
  }
  if ($.trim(firstname) === "" || $.trim(lastname) === "") {
    app.dialog.error("Por favor complete los campos de nombre y apellido");
    return;
  }

  app.login_modal.find(".login-box-body").remove(".alert");
  $.ajax({
    url: app.baseUrl + "user/get_signed",
    data: {
      email: email,
      password: pass,
      password_repeat: pass_repeat,
      account: account,
      firstname: firstname,
      lastname: lastname
    },
    type: "POST",
    dataType: "json",
    success: function(data) {
      if (data.status && data.status == "success") {
        window.location.href = app.baseUrl + "manager";
      } else {
        if (data.errors) {
          app.dialog.error(data.errors);
        } else {
          app.dialog.error("Algo salió mal");
        }
      }
    }
  });
};

app.resetPassword = function() {
  var email = app.reset_form.find('[name="email"]').val();
  if ($.trim(email) === "") {
    app.dialog.error("Completar su email correctamente");
    return;
  }
  app.reset_form.find(".alert").remove();
  $.ajax({
    url: app.baseUrl + "user/get_password_reset",
    data: { email: email },
    type: "POST",
    dataType: "json",
    success: function(data) {
      if (data.status && data.status == "success") {
        app.reset_form.append(
          '<div class="alert alert-success">Fue enviado un email a su correo</div>'
        );
      } else {
        if (data.errors) {
          app.dialog.error(data.errors);
        } else {
          app.dialog.error("Algo salió mal");
        }
      }
    }
  });
};

app.sendNewPassword = function() {
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
    success: function(data) {
      console.log(data);
      if (data.status && data.status == "success") {
        reset_form.append(
          '<div class="alert alert-success"> <a href="' +
            app.baseUrl +
            "user/login" +
            '">Nueva contraseña guardada <br/> Volvé a la aplicación</a></div>'
        );
      } else {
        reset_form.append(
          '<div class="alert alert-danger">' + data.errors + "</div>"
        );
      }
    }
  });
};

app.btnLoadint = {
  show: function() {
    $("#sender-btn").hide();
    $("#loading-btn").show();
  },
  hide: function() {
    $("#sender-btn").show();
    $("#loading-btn").hide();
  }
};

app.dialog = {
  warring: function(message, callback) {
    var dialog = bootbox.dialog({
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
          callback: function() {
            dialog.modal("hide");
          }
        },
        edit: {
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
          callback: function() {
            if (callback) {
              callback();
            }
          }
        }
      }
    });
  },
  error: function(message, callback) {
    var dialog = bootbox.dialog({
      title: "<i class='fa fa-exclamation-circle'></i> Alerta",
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
          callback: function() {
            dialog.modal("hide");
            callback();
          }
        }
      }
    });
  },
  success: function(title, message, callback) {
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
          callback: function() {
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

$.fn.serializeObject = function() {
  var o = {};
  var a = this.serializeArray();
  $.each(a, function() {
    if (o[this.name] !== undefined) {
      if (!o[this.name].push) {
        o[this.name] = [o[this.name]];
      }
      o[this.name].push(this.value || "");
    } else {
      o[this.name] = this.value || "";
    }
  });
  return o;
};

app.rangePicker = {
  locale: {
    format: "DD/MM/YYYY",
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

$(document).ready(function() {});
