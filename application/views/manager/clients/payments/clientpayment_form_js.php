<script>
  $(document).ready(function() {
    <?php if ((!empty($client) && !$client['client_ctrl']) || !empty($clients) && !$clients['client_ctrl']) : ?>
      app.controlClient = $(
        app.popCreate({
          body: "<?= (!empty($client) ? "El Cliente no posee " . $client['client_ctrl_msg'] : "Hay clientes que no poseen " . $clients['client_ctrl_msg']) . "<br>Verificar los Datos y volver a intentarlo</p>" ?>",
          title: "Alerta",
          footerBtn: "Editar",
          footerBtnClick: "window.location = '<?= site_url((!empty($client) ? 'clients/form/' . $client['client_id'] : 'clients/all')) ?>'"
        })
      );
      $("body").append(app.controlClient);
      if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
        app.controlClient.modal("show");
      }
    <?php endif; ?>

    app.datepickerLoad();
    app.mapLoad();
    app.resizeCanvas();
    $(window).keydown(function(event) {
      if (event.keyCode == 13) {
        event.preventDefault();
        return false;
      }
    });

    // Update all the clientperiod_daytask
    updateAllThePeriodsDaytask()
    app.refreshTotalClientPeriodAmountSelected()
    $("#pay_date").on("dp.change", function(e) {
      updateAllThePeriodsDaytask()
    });
  });

  const updateAllThePeriodsDaytask = () => {
    allClientPeriods.forEach((clientperiodId) => {
      app.refresh(`_${clientperiodId}`, false);
    })
  }
  var holydays = [];
  var select = false;
  var selectedClient;
  var clients = [<?= implode(", ", array_keys($clientperiodsByClient)) ?>];
  var allClientPeriods = [
    <?php foreach ($periods as $period) : ?>
      <?= $period['clientperiod_id'] . ',' ?>
    <?php endforeach; ?>
  ]

  app.setSimplifiedPayment = () => {
    let lastSimplePaymentState = $(`#isSimplePayment`).val();
    let isSimplePayment = lastSimplePaymentState == '0';
    $(`#isSimplePayment`).val(isSimplePayment ? '1' : '0');
    if (!isSimplePayment) {
      $(`.payment_instruction`).each((function(index, element) {
        $(element).show(500)
      }))
      $(`.simplified_payment_alert`).each((function(index, element) {
        $(element).hide(500)
      }))
      $(`.payment_details`).each((function(index, element) {
        $(element).show(500)
      }))
      app.refreshTotalAmountPaid();
    } else {
      $(`.payment_instruction`).each((function(index, element) {
        $(element).hide(500)
      }))
      $(`.simplified_payment_alert`).each((function(index, element) {
        $(element).show(500)
      }))
      $(`.payment_details`).each((function(index, element) {
        $(element).hide(500)
      }))
      $(`#total_amount_to_pay`).val($(`#total_selected_client_period_amount`).val());
      app.updateBtn();
    }
    $(".simplePayment i").toggleClass("fa-check-square-o", isSimplePayment);
    $(".simplePayment i").toggleClass("fa-square-o", !isSimplePayment);
  }

  app.selectAll = () => {
    app.loadding = bootbox.dialog({
      message: '<p class="text-center mb-0 ml-0 mr-0"><i class="fa fa-spin fa-spinner"></i> Calculando mora, espere por favor...</p>',
      closeButton: false
    });
    app.loadding.on('shown.bs.modal', function(e) {
      $(".payment-period").each((idx, elm) => {
        let alreadyChecked = $(elm).prop('checked');
        let mustSelectAll = $('#selectOrUnselectAllClientPeriods i').hasClass('fa-check-square-o')
        $(elm).prop('checked', mustSelectAll);
        if (mustSelectAll && !alreadyChecked)
          app.refresh($(elm).prop('id'), true);
        if (!mustSelectAll && alreadyChecked)
          app.refresh($(elm).prop('id'), true);
      });
      app.updateBtn();
      app.refreshTotalClientPeriodAmountSelected();
      app.loadding.modal("hide");
      // Show every client.
      if ($('#selectOrUnselectAllClientPeriods i').hasClass('fa-check-square-o'))
        $('.collapse').collapse('hide');
      else
        $('.collapse').collapse('show');
    });
  }

  app.datepickerLoad = function() {
    $('.pay_date').each(function(idx, elm) {
      let config = {
        locale: 'es',
        stepping: 5,
        showClose: true,
        format: 'DD-MM-YYYY',
        useCurrent: false, //Important! See issue #1075
      };
      <?php if ($this->session->userdata('user_rol') !== 'super') : ?>
        let minDate = new Date();
        config.minDate = new Date(minDate.setDate(minDate.getDate() - 3));
        let maxDate = new Date();
        config.maxDate = maxDate;
      <?php endif; ?>
      $(elm).datetimepicker(config);
      if ($(elm).attr('id').includes('expiration_date'))
        return;
      let id = $(elm).attr('id').split('_');
      // console.log(id);
    })
  }

  app.updateBtn = () => {
    select = $("input[type=checkbox]:checked").length == $("input[type=checkbox]").length;

    $('#submitter').toggleClass('disabled', $("input[type=checkbox]:checked").length == 0);
    clients.forEach((client) => {
      let clientPaymentsSubmited = $(`input[name*="client[${client}][paymentDetail]"]`).length
      let clientClientPeriodsSelected = $(`input[type=checkbox][name*="client[${client}][clientperiod]"]:checked`).length;
      // Validate that all the clients with at least one introduced payment have at least one clientperiod selected.
      let clientHasAtLeastOnePayment = clientPaymentsSubmited >= 1;
      let clientHasNoClientPeriodSelected = clientClientPeriodsSelected == 0;
      if (clientHasAtLeastOnePayment && clientHasNoClientPeriodSelected)
        $('#submitter').toggleClass('disabled', true);
      // Validate that all the clients with at least one introduced clientperiod have at least one payment submitted.
      let clientHasAtLeastOneClientPeriodSelected = clientClientPeriodsSelected >= 1;
      let clientHasNoPaymentsSubmitted = clientPaymentsSubmited == 0;
      let isSimplePayment = $(`#isSimplePayment`).val() != '0';
      if (clientHasAtLeastOneClientPeriodSelected && clientHasNoPaymentsSubmitted && !isSimplePayment) {
        $('#submitter').toggleClass('disabled', true);
      }
    })


    $("#selectOrUnselectAllClientPeriods i").toggleClass("fa-check-square-o", !select);
    $("#selectOrUnselectAllClientPeriods i").toggleClass("fa-square-o", select);
    $("#selectOrUnselectAllClientPeriods span").html((select ? "Deseleccionar" : "Seleccionar") + " todas");
  }

  app.refreshPayment = (id, sum) => {
    app.loadding = bootbox.dialog({
      message: '<p class="text-center mb-0 ml-0 mr-0"><i class="fa fa-spin fa-spinner"></i> Calculando mora, espere por favor...</p>',
      closeButton: false
    });
    app.loadding.on('shown.bs.modal', function(e) {
      app.refresh(id, sum); //Actualizar mora
      app.updateBtn(); //Verificar validez de formulario entero (Al menos un pago por cada deuda documentada)
      app.refreshTotalClientPeriodAmountSelected();
      app.loadding.modal("hide");
    });
  }
  var banks = JSON.parse('<?= json_encode($banks) ?>');
  banks = banks.map((bank) => {
    return {
      id: bank.bank_id,
      name: bank.bank_name,
      cod: bank.bank_cod
    };
  });
  app.refresh = (clientPeriodId, mustSum) => {
    var days = 0;
    var task = 0;
    let taskVal = 0;
    let pay_amount = 0;

    var startDay = new Date($("#" + clientPeriodId).data('date2'));
    startDay.setHours(0, 0, 0, 0);
    var endDay = $('#pay_date').data("DateTimePicker").viewDate()._d;
    endDay.setHours(0, 0, 0, 0);
    days = app.getDays(startDay, endDay);

    //here
    let amount = Math.round($("#" + clientPeriodId).data('amount') * 100) / 100;
    let discount = Math.round($("#" + clientPeriodId).data('discount') * 100) / 100;
    let discountVal = 0;
    let interest = Math.round($("#" + clientPeriodId).data('interest') * 100) / 100;

    let daytask = Math.round($("#" + clientPeriodId).data('daytask') * 10000) / 10000;
    if (days > 0) {
      task = ((daytask * days) / 100);
      taskVal = Math.round((amount * task) * 100) / 100;
      pay_amount = Math.round((amount + taskVal) * 100) / 100;
    } else {
      taskVal = 0;
      // console.log("unckeck")
      discountVal = Math.round((interest * (discount / 100)) * 100) / 100;
      $("#" + clientPeriodId + '_pay_discount').val(discountVal);
      $("#" + clientPeriodId + '_pay_discount_h').val(discountVal);

      pay_amount = Math.round((amount - discountVal) * 100) / 100;
    }
    $('#clientperiod' + clientPeriodId + "_daytask").html(`<b>$ ${app.money_format(taskVal)}</b>`);
    $('#clientperiod' + clientPeriodId + "_total").html(`<b>$ ${app.money_format(pay_amount)}</b>`);
    let clientId = $("#" + clientPeriodId).data('clientid');
    let clientperiod_amount_tot = Math.round(app.getFloat($(`#client_${clientId}_clientperiod_amount_tot`).val()) * 100) / 100;
    let isClientPeriodChecked = $("#" + clientPeriodId).prop("checked");
    $('#' + clientPeriodId + "_task").val(taskVal);
    if (mustSum && isClientPeriodChecked) {
      $(`#client_${clientId}_clientperiod_amount_tot`).val(app.money_format(Math.round((clientperiod_amount_tot + pay_amount) * 100) / 100, true));
    }
    if (mustSum && !isClientPeriodChecked) {
      $(`#client_${clientId}_clientperiod_amount_tot`).val(app.money_format(Math.round((clientperiod_amount_tot - pay_amount) * 100) / 100, true));
    }
  }

  app.getDays = function(dDate1, dDate2) {
    let dDate = new Date(dDate1.getTime());
    let days = 0;
    // console.log(dDate, dDate1, dDate2)

    function service() {
      if (dDate >= dDate2) {
        dDate = new Date(dDate1.getTime());
        return next();
      }

      let found = false;
      holydays.map(row => {
        if (row.year == dDate.getFullYear()) {
          found = true;
        }
      });
      if (!found) {
        dDate.setDate(dDate.getDate() + 1);
        return service();
      } else {
        dDate.setDate(dDate.getDate() + 1);
        return service();
      }
    }

    function next() {
      if (dDate >= dDate2)
        return days;
      // if (dDate.getDay() != 0 && dDate.getDay() != 6) {
      let found = false;
      holydays.map(row => {
        if (row.year == dDate.getFullYear()) {
          if (row.data[dDate.getMonth()].hasOwnProperty(dDate.getDate())) {
            found = true;
          }
        }
      });
      if (!found)
        days++;
      // }

      dDate.setDate(dDate.getDate() + 1);
      return next();
    }

    return service();
  }

  app.saveOldVal = function(id) {
    let discount = Math.round($("#" + id + '_pay_discount').val() * 100) / 100;
    let old = Math.round($("#" + id + '_pay_amount').val() * 100) / 100;
    console.log(id, old, discount);
    $("#" + id + '_pay_amount').data('old', (old + discount));
  }
  app.changeBank = () => {};
  app.changePay = function(id) {
    let prev = Math.round($("#" + id + '_pay_amount').data('old') * 100) / 100;
    let current = Math.round($("#" + id + '_pay_amount').val() * 100) / 100;
    let discount = Math.round($("#" + id + '_pay_discount').val() * 100) / 100;
    // current = (current < discount ? 0 : current);
    console.log(prev, current, discount);
  }

  app.resizeCanvas = function() {
    var canvas = document.getElementById("signature");

    if (canvas) {
      app.signaturePad = new SignaturePad(canvas);

      $('#clear').on('click', function() {
        app.signaturePad.clear();
        app.signaturePad.backgroundColor = 'rgb(255, 255, 255)';
      });
      // When zoomed out to less than 100%, for some very strange reason,
      // some browsers report devicePixelRatio as less than 1
      // and only part of the canvas is cleared then.
      var ratio = Math.max(window.devicePixelRatio || 1, 1);
      canvas.width = canvas.offsetWidth * ratio;
      canvas.height = canvas.offsetHeight * ratio;
      canvas.getContext("2d").scale(ratio, ratio);
    }
  }

  app.allow_submit = false;
  app.mapLoad = function() {
    if (!$('#pay_id').val().length) {
      // Solo al crearse un nuevo pago registramos firma y localizacion
      $('#submitter').click(function(e) {
        if (!app.allow_submit && !$('#submitter').hasClass("disabled")) {
          e.preventDefault();

          if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
            if (app.signaturePad.isEmpty()) {
              return alert("Proporcione una firma primero por favor.");
            }
          }

          if (navigator.geolocation) {
            $(this).prop('disabled', true);
            //$(this).css('pointer-events', 'none');

            navigator.geolocation.getCurrentPosition(function(position) {
              $('#pay_lat').val(position.coords.latitude);
              $('#pay_lng').val(position.coords.longitude);
              if (!app.signaturePad.isEmpty()) {
                $('#sign').val(app.signaturePad.toDataURL('image/png'));
              }
              //app.allow_submit = true;
              if (app.validateExtraFields()) {
                $('form#payment').submit()
              }
            }, function() {
              $(this).prop('disabled', false);
              return alert("Error en obtener Geolocation");
            });
          } else {
            $(this).prop('disabled', false);
            return alert("Navegador no soporta Geolocation");
          }
        } else if ($('#submitter').hasClass("disabled")) {
          e.preventDefault();
        }
      });
    }

    $('#clear').click();

    if ($('#gmap_div').is('*')) {
      setTimeout(function() {
        app.showMap($('#pay_lat').val(), $('#pay_lng').val())
      }, 500);
    }
  }

  app.showMap = function(lat, lng) {
    let map_options = {
      mapTypeControl: false,
      streetViewControl: false,
      fullscreenControl: false
    };

    var loc = {
      lat: parseFloat(lat),
      lng: parseFloat(lng)
    };

    var map = new google.maps.Map(document.getElementById('gmap_div'), map_options);
    let marker = new google.maps.Marker({
      position: loc,
      map: map,
      title: 'Dirección pago'
    });

    map.setZoom(15);
    map.setCenter(loc);
  }

  var is_numeric = /^\d+$/;

  app.validatePayment = () => {
    let pay_type = $("#pay_type").val();
    let errorMessage = '';
    let pay_fields = [];
    let pay_amount = $('#pay_amount').val().replace(/\s+/g, '');
    $('#pay_amount').val(pay_amount);

    //if (!is_numeric.test(pay_amount)) {
    if (/^((\d{1,3})(.\d{3})*(\,\d*)?|\d+(\,\d*)?)$/i.test(pay_amount) == false) {
      errorMessage += "<li>El monto no es un número válido.</li>"
    }
    if (pay_amount <= 0) {
      errorMessage += "<li>El monto debe ser mayor a 0</li>"
    }
    if (pay_type == 'Efectivo' && $(`input[type='hidden'][value='Efectivo'][name*='client[${selectedClient}][paymentDetail]']`).length > 0) {
      errorMessage += "<li>Solo puede haber un monto de pago efectivo.</li>"
    }
    if (pay_type == 'Cheque') {
      let current_check_number = $("#payment_number").val();
      let current_bank_cod = $("#payment_bank_cod").val();
      let current_bank_name = $("#payment_bank").val();
      $("input[type='hidden'][value*='Cheque']").each(function(index) {
        let check_number = $(this).parent().find("input[type='hidden'][name*='pay_number']").val();
        let bank_cod = $(this).parent().find("input[type='hidden'][name*='pay_bank_cod']").val();
        if (current_bank_cod == bank_cod && check_number == current_check_number) {
          errorMessage += `<li>Ya existe un cheque con el número ${current_check_number} del banco ${current_bank_name}.</li>`
        }
      });
    }
    switch (pay_type) {
      case "Cheque":
        pay_fields = ['bank_cod', 'expiration_date', 'clearing', 'number', 'cuit'];
        break;
      case "MERCADO PAGO - PAGOS.LINEA.MP":
        pay_fields = ['transaction_number'];
        break;
    }
    pay_fields.forEach((field) => {
      let value = $('#payment_' + field).val();
      switch (field) {
        case "clearing":
          if (value == '' || !is_numeric.test(value)) {
            errorMessage += "<li>" + $('#payment_' + field).attr('title') + "</li>"
          }
          break;
        case "number":
          if (value == '' || !is_numeric.test(value)) {
            errorMessage += "<li>" + $('#payment_' + field).attr('title') + "</li>"
          }
          break;
        case "cuit":
          if (value.length != 11 || !is_numeric.test(value) || !app.isValidCuit(value)) {
            errorMessage += "<li>" + $('#payment_' + field).attr('title') + "</li>"
          }
          break;
        default:
          if (value == '') {
            errorMessage += "<li>" + $('#payment_' + field).attr('title') + "</li>";
          }
          break;
      }
    });
    if (errorMessage != '') {
      errorMessage = "<ul>" + errorMessage + "</ul>";
      app.dialog.warring(errorMessage);
      return false;
    }
    return true
  }

  app.validateExtraFields = () => {
    let checkedPeriods = $('form#payment').find('input:checked');
    let errorMessage = '';
    checkedPeriods.each(function(index, checkedPeriod) {
      let id = $(this).attr('id'); //_id
      let pay_type = $("#pay_type" + id).data('value');
      let pay_fields = [];
      let current_period_error = '';
      switch (pay_type) {
        case "Cheque":
          pay_fields = ['bank_cod', 'expiration_date', 'clearing', 'number', 'cuit'];
          break;
        case "MERCADO PAGO - PAGOS.LINEA.MP":
          pay_fields = ['transaction_number'];
          break;
      }
      pay_fields.forEach((field) => {
        let value = $("#" + id + '_payment_' + field).val();
        switch (field) {
          case "clearing":
            if (value == '' || !is_numeric.test(value)) {
              current_period_error += "<li>" + $("#" + id + '_payment_' + field).attr('title') + "</li>"
            }
            break;
          case "number":
            if (value == '' || !is_numeric.test(value)) {
              current_period_error += "<li>" + $("#" + id + '_payment_' + field).attr('title') + "</li>"
            }
            break;
          case "cuit":
            if (value.length != 11 || !is_numeric.test(value) || !app.isValidCuit(value)) {
              current_period_error += "<li>" + $("#" + id + '_payment_' + field).attr('title') + "</li>"
            }
            break;
          default:
            if (value == '') {
              current_period_error += "<li>" + $("#" + id + '_payment_' + field).attr('title') + "</li>";
            }
            break;
        }
      });
      if (current_period_error != '') {
        current_period_error = $(this).parent().text() + "<br>" + "<ul>" + current_period_error + "</ul>";
        errorMessage += current_period_error;
      }
    });
    if (errorMessage != '') {
      $('#submitter').prop('disabled', false);
      app.dialog.warring(errorMessage);
      return false;
    }
    return true;
  }

  app.fillCheckNumber = function(id) {
    let text = $('#' + id).val() + "";
    while (text.length < 8) {
      text = "0" + text;
    }
    $('#' + id).val(text);
  }

  var clientsPaymentNumbers = [];
  app.addPayment = function() {
    if ($("#pay_type").val() == null) return;
    if (clientsPaymentNumbers[selectedClient] == null) clientsPaymentNumbers[selectedClient] = 0;
    let currentPaymentNumber = clientsPaymentNumbers[selectedClient]++;
    let currentPaymentType = $("#pay_type").val();
    if (!app.validatePayment()) {
      return;
    }
    let pay_det_extra_data = app.createExtraDataFields(currentPaymentNumber);
    let description = app.createDescription();
    let ammount = app.getFloat($("#pay_amount").val());
    $(`
      <div class="list-group-item billable_item position-relative" id="client_${selectedClient}_payment_${currentPaymentNumber}">
        <input type="hidden" name="client[${selectedClient}][paymentDetail][${currentPaymentNumber}][pay_order]" value="${currentPaymentNumber}" />
        <input type="hidden" name="client[${selectedClient}][paymentDetail][${currentPaymentNumber}][pay_type]" value="${currentPaymentType}" />
        <input type="hidden" class="client_${selectedClient}_pay_det_amount" name="client[${selectedClient}][paymentDetail][${currentPaymentNumber}][pay_amount]" value="${ammount}" />
        ${pay_det_extra_data}
        <div class="row">
          <div class="col-sm-6">
            <label>${description}</label>
          </div>
          <div class="col-sm-2 text-right">
            <label>${app.money_format(ammount, true)}</label>
          </div>
          <div class="col-sm-1">
          </div>
          <div class="col-sm-2">
            <button type="button" class="btn btn-danger" onclick="app.removePayment(${selectedClient}, ${currentPaymentNumber})"><span>×</span></button>
          </div>
        </div>
      </div>
    `).hide().appendTo(`#client_${selectedClient}_payments`).fadeIn(500);
    $("#add-payment").modal('hide');
    app.refreshTotalAmountPaid();
    app.updateBtn()
  };

  app.refreshTotalAmountPaid = function() {
    let total_monto = 0;
    $(`.client_${selectedClient}_pay_det_amount`).each(function() {
      total_monto += parseFloat($(this).val());
    });
    total_monto = Math.round(total_monto * 100) / 100;
    $(`#client_${selectedClient}_pay_amount_tot`).val(app.money_format(total_monto, true));

    let totalAmountPaid = 0;
    clients.forEach((client) => {
      $(`.client_${client}_pay_det_amount`).each(function() {
        totalAmountPaid += parseFloat($(this).val());
      });
    })
    $(`#total_amount_to_pay`).val(app.money_format(totalAmountPaid, true));
  }

  app.createDescription = function() {
    let pay_type = $(`#pay_type`).val();
    let description = pay_type;
    switch (pay_type) {
      case "Cheque":
        description += ` - ${$('#payment_bank').val()} - n° ${$('#payment_number').val()} - Exp: ${$('#payment_expiration_date').val()} - Clearing ${$('#payment_clearing').val()} - Cuit ${$('#payment_cuit').val()}`;
        break;
      case "MERCADO PAGO - PAGOS.LINEA.MP":
        description += ` - Trans. n° - ${$('#payment_transaction_number').val()}`;
        break;
    }
    return description;
  }

  app.createExtraDataFields = function(currentPaymentNumber) {
    extra_data_fields = [];
    let pay_type = $("#pay_type").val();
    switch (pay_type) {
      case "Cheque":
        extra_data_fields = ['bank_cod', 'expiration_date', 'clearing', 'number', 'cuit'];
        break;
      case "MERCADO PAGO - PAGOS.LINEA.MP":
        extra_data_fields = ['transaction_number'];
        break;
    }
    pay_det_extra_data = '';
    extra_data_fields.forEach((field) => {
      let value = $(`#payment_${field}`).val();
      pay_det_extra_data += `<input type="hidden" name="client[${selectedClient}][paymentDetail][${currentPaymentNumber}][pay_${field}]" value="${value}" />`
    });
    return pay_det_extra_data;
  }

  app.removePayment = function(clientId, paymentId) {
    selectedClient = clientId;
    $(`#client_${clientId}_payment_${paymentId}`).fadeOut("normal", function() {
      $(this).remove();
      app.refreshTotalAmountPaid();
    });
  };

  app.showPaymentModal = function(clientId) {
    $("#add-payment").modal('show');
    selectedClient = clientId;
    app.refreshModal();
  };

  app.refreshModal = () => {
    $('#pay_amount').val('0');
    extra_data_fields = ['bank', 'bank_cod', 'expiration_date', 'clearing', 'number', 'cuit', 'transaction_number'];
    //Always hide all the extra data fields and delete everything inside inputs
    extra_data_fields.forEach((field) => {
      //let div = $("#" + id + '_div_payment_'+field); if (!!div) div.hide();
      $('#div_payment_' + field).hide();
      $('#payment_' + field).val('');
      $('#payment_' + field).removeAttr('readonly');
      $('#payment_' + field).attr('disabled', 'disabled');
    });
    let pay_fields = [];
    let pay_type = $("#pay_type").val();
    switch (pay_type) {
      case "Cheque":
        pay_fields = ['bank', 'bank_cod', 'expiration_date', 'clearing', 'number', 'cuit'];
        $('#payment_bank').typeahead({
          delay: 500,
          source: banks,
          afterSelect: function(selected) {
            $('#payment_bank_cod').val(selected.cod);
            $('#payment_bank').attr('readonly', 'readonly');
          },
        });
        break;
      case "MERCADO PAGO - PAGOS.LINEA.MP":
        pay_fields = ['transaction_number'];
        break;
    }
    pay_fields.forEach((field) => {
      $('#div_payment_' + field).show();
      $('#payment_' + field).removeAttr('disabled');
    });
    //$("#payment_expiration_date").datetimepicker({});
  }

  app.money_format = function(amount, dollarSign = false) {
    let formated = Intl.NumberFormat('de-DE', {
      style: "currency",
      currency: "USD"
    }).format(Math.round(amount * 100) / 100);
    formated = formated.replace("$", "");
    if (dollarSign == true)
      formated = `$ ${formated}`;
    return formated;
  }

  /* 
   * app.getFloat(money_val)
   * Given a formatted currency string (example: $ ###.###,## or $ 123.456,12) returns the equivalent parsed float.
   */
  app.getFloat = function(money_val) {
    let stripped_string = money_val.replaceAll('$', '').replaceAll('.', '').replaceAll(',', '.');
    return parseFloat(stripped_string);
  }

  app.isValidCuit = function(cuit) {
    if (cuit.length != 11) return false;

    let rv = false;
    let resultado = 0;
    let cuit_nro = cuit.replace("-", "");
    const codes = "6789456789";
    let verificador = parseInt(cuit_nro[cuit_nro.length - 1]);
    let x = 0;

    while (x < 10) {
      let digitoValidador = parseInt(codes.substring(x, x + 1));
      if (isNaN(digitoValidador)) digitoValidador = 0;
      let digito = parseInt(cuit_nro.substring(x, x + 1));
      if (isNaN(digito)) digito = 0;
      let digitoValidacion = digitoValidador * digito;
      resultado += digitoValidacion;
      x++;
    }
    resultado = resultado % 11;
    rv = (resultado == verificador);
    return rv;
  }
  app.refreshTotalClientPeriodAmountSelected = function() {
    let totalClientPeriodsAmount = 0;
    let totalSelectedClientPeriodsAmount = 0;
    $(".payment-period").each((idx, elm) => {
      let isChecked = $(elm).prop('checked');
      let clientperiodAmount = 0;
      let taskAmount = 0;
      let clientPeriodId = $(elm).attr('id');
      clientperiodAmount = parseFloat($(elm).val())
      taskAmount = parseFloat($('#' + clientPeriodId + "_task").val());
      totalClientPeriodsAmount += (clientperiodAmount + taskAmount)
      if (isChecked) {
        totalSelectedClientPeriodsAmount += (clientperiodAmount + taskAmount)
      }
    });
    $(`#total_selected_client_period_amount`).val(app.money_format(Math.round((totalSelectedClientPeriodsAmount) * 100) / 100, true));

    // Update the total amount to pay of all client periods
    $(`#total_client_periods_amount`).val((app.money_format(Math.round((totalClientPeriodsAmount) * 100) / 100, true)));

    let isSimplePayment = $(`#isSimplePayment`).val() != '0';
    if (isSimplePayment) {
      $(`#total_amount_to_pay`).val($(`#total_selected_client_period_amount`).val());
    }
  }
</script>

<script src="https://cdn.jsdelivr.net/npm/signature_pad@2.3.2/dist/signature_pad.min.js"></script>