<script>
  var clientDatatable;
  var clientUrl = "<?= site_url('clientpacks/datatables?dt=true' . (!empty($client_id) ? '&client=' . $client_id : '')) ?>";

  apply_filter = function() {
    var urlSend = clientUrl;
    <?php foreach ($filters as $field => $values) : ?>
      urlSend += "&filter[<?= $field ?>]=" + $("#filter_<?= $field ?>").val();
    <?php endforeach; ?>

    <?php if (empty($client_id)) : ?>
      urlSend += "&datefilter=" + $("#datefilter").val(); //datefilter
    <?php endif; ?>
    if (clientDatatable) { //datefilter
      clientDatatable.ajax.url(urlSend);
      clientDatatable.ajax.reload();
    }
  };

  app.capitalCallback = function(data) {
    $('#capital').html(data.json.capital + " (" + data.json.capital_numbers + ")");
    // $('#capital_liquid').html(data.json.capital_liquid + " (" + data.json.capital_numbers_liquid + ")");
    // $('#capital_pending').html(data.json.capital_pending + " (" + data.json.capital_numbers_pending + ")");
  };

  app.daterangepickerLoad = function() { //datefilter
    var start = moment().startOf('month');
    var end = moment().endOf('month');
    let rangePicker = app.rangePicker;
    rangePicker.singleDatePicker = false;
    rangePicker.autoUpdateInput = false;
    rangePicker.startDate = start;
    rangePicker.endDate = end;
    rangePicker.ranges = {
      'Hoy': [moment(), moment()],
      'Ayer': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
      'Últimos 7 días': [moment().subtract(6, 'days'), moment()],
      'Últimos 30 días': [moment().subtract(29, 'days'), moment()],
      'Este Mes': [moment().startOf('month'), moment().endOf('month')],
      'Último Mes': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
    };
    $('#datefilter').daterangepicker(rangePicker);
    $('#datefilter').val(rangePicker.startDate.format('DD-MM-YYYY') + ' / ' + rangePicker.endDate.format('DD-MM-YYYY'));
    $('#datefilter').on('apply.daterangepicker', function(ev, picker) {
      $(this).val(picker.startDate.format('DD-MM-YYYY') + ' / ' + picker.endDate.format('DD-MM-YYYY'));
      apply_filter();
    });
  };

  app.openImport = () => {
    app.loadding = bootbox.dialog({
      message: '<p class="text-center mb-0 ml-0 mr-0"><i class="fa fa-spin fa-spinner"></i> Importando datos, espere por favor...</p>',
      closeButton: false
    });
    app.loadding.modal('show');
    app.saveImport();
  };
  app.saveImport = async () => {
    $.ajax({
      url: "<?= site_url('clientpacks/reImport') ?>",
      type: "GET",
      // dataType: "json",
      success: function(data) {
        app.loadding.modal("hide");
        if (data !== '')
          alert(data);
        else
          location.reload();
      },
      error: function(request, errorType, errorMessage) {
        console.log(request);
        alert("ERROR: " + errorType);
        console.log(errorMessage);
        app.loadding.modal("hide");
      }
    });
    // if (await app.checkImport()) {
    //   console.log('SUBIT');
    //   $("#import").modal('hide');
    //   app.loadding = bootbox.dialog({
    //     message: '<p class="text-center mb-0 ml-0 mr-0"><i class="fa fa-spin fa-spinner"></i> Importando datos, espere por favor...</p>',
    //     closeButton: false
    //   });
    //   $("#formImport").submit();
    // }
  };

  app.openExport = () => {
    app.loadding = bootbox.dialog({
      message: '<p class="text-center mb-0 ml-0 mr-0"><i class="fa fa-spin fa-spinner"></i> Exportando datos, espere por favor...</p>',
      closeButton: false
    });
    app.loadding.modal('show');
    app.saveExport();
  };
  app.saveExport = async () => {
    $.ajax({
      url: "<?= site_url('clientpacks/export/4/1') ?>",
      type: "GET",
      // dataType: "json",
      success: function(data) {
        app.loadding.modal("hide");
        if (data !== '')
          alert(data);
        else
          location.reload();
      },
      error: function(request, errorType, errorMessage) {
        console.log(request);
        alert("ERROR: " + errorType);
        console.log(errorMessage);
        app.loadding.modal("hide");
      }
    });
    // if (await app.checkImport()) {
    //   console.log('SUBIT');
    //   $("#import").modal('hide');
    //   app.loadding = bootbox.dialog({
    //     message: '<p class="text-center mb-0 ml-0 mr-0"><i class="fa fa-spin fa-spinner"></i> Importando datos, espere por favor...</p>',
    //     closeButton: false
    //   });
    //   $("#formImport").submit();
    // }
  };

  app.checkImport = async () => {
    if (!window.File || !window.FileReader || !window.FileList || !window.Blob) {
      alert('La API File no esta soportada de manera completa por este navegador.');
      return false;
    }

    var input = document.getElementById('file');
    if (!input) {
      alert("No se encontró el input file");
      return false;
    } else if (!input.files) {
      alert("Este navegador no soporta el control de archivos");
      return false;
    } else if (!input.files[0]) {
      alert("Por favor seleccione un archivo");
      return false;
    } else {
      let file = input.files[0];
      let reader = new FileReader();
      reader.readAsText(file);
      let result = await new Promise((resolve, reject) => {
        reader.onload = function(event) {
          resolve(reader.result)
        }
      });

      try {
        let json = JSON.parse(result);
      } catch (error) {
        alert("Formato incorrecto");
        return false;
      }
    }
    return true;
  };

  $(document).ready(function() {
    app.daterangepickerLoad(); //datefilter
    var urlSend = clientUrl; //datefilter
    <?php if (empty($client_id)) : ?>
      urlSend += "&datefilter=" + $("#datefilter").val(); //datefilter
    <?php endif; ?>
    clientDatatable = app.datatable({
      url: urlSend,
      id: "#client_list",
      order: [
        [1, "desc"]
      ],
      callbackAjax: app.capitalCallback
    });
  });
</script>