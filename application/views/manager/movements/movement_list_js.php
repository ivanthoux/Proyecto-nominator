<script>
  var movementDatatable;
  var movementUrl = "<?= site_url('movements/datatables?dt=true') ?>";
  apply_filter = function() {
    var urlSend = "<?= site_url('movements/all') ?>";
    urlSend += "/" + $("#datefilter").val().replace(/ /g, '')
    window.location.href = urlSend;
  };

  apply = function() {
    var urlSend = movementUrl;
    <?php foreach ($filters as $field => $values) : ?>
      urlSend += "&filter[<?= $field ?>]=" + $("#filter_<?= $field ?>").val();
    <?php endforeach; ?>
    urlSend += "&datefilter=" + $("#datefilter").val(); //datefilter
    if (movementDatatable) { //datefilter
      movementDatatable.ajax.url(urlSend);
      movementDatatable.ajax.reload();
    }
  }

  app.daterangepickerLoad = function() { //datefilter
    let rangePicker = app.rangePicker;
    rangePicker.singleDatePicker = false;
    rangePicker.ranges = {
      'Hoy': [moment(), moment()],
      'Ayer': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
      'Últimos 7 días': [moment().subtract(6, 'days'), moment()],
      'Últimos 30 días': [moment().subtract(29, 'days'), moment()],
      'Este Mes': [moment().startOf('month'), moment().endOf('month')],
      'Último Mes': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
    };
    $('#datefilter').daterangepicker(rangePicker);
    $('#datefilter').on('apply.daterangepicker', function(ev, picker) {
      apply_filter();
    });
  };

  app.movementsCallback = function(data) {
    $('#income_outcome').html(data.json.income_outcome);
    $('#income').html(data.json.income);
    $('#outcome').html(data.json.outcome);
    $('#income_outcome_no').html(data.json.income_outcome_no);
    // $('#payments').html(data.json.office_debits + " (" + data.json.office_payments + ")");
  };

  app.closeConfirm = function() {
    $("#close-confirm").modal('show');
  };

  app.exportMoves = () => {
    var urlSend = "<?= site_url('movements/export') ?>";
    urlSend += "/" + $("#datefilter").val().replace(/ /g, '')
    urlSend += "?search=" + $('input[type="search"]').val();
    <?php foreach ($filters as $field => $values) : ?>
      urlSend += "&filter[<?= $field ?>]=" + $("#filter_<?= $field ?>").val();
      <?php endforeach; ?>
    window.open(urlSend, '_blank')
  }

  $(document).ready(function() {
    app.daterangepickerLoad(); //datefilter
    var urlSend = movementUrl; //datefilter
    urlSend += "&datefilter=" + $("#datefilter").val().replace(/ /g, ''); //datefilter

    movementDatatable = app.datatable({
      url: urlSend,
      id: "#movements_list",
      pageLength: 50,
      // searching: false,
      order: [
        [1, "desc"]
      ],
      callbackAjax: app.movementsCallback
    });
  });
</script>