<script>
  var closingDatatable;
  var closingUrl = "<?= site_url('closings/datatables?dt=true') ?>";

  apply_filter = function() {
    var urlSend = closingUrl;
    urlSend += "&datefilter=" + $("#datefilter").val(); //datefilter
    if (closingDatatable) {
      closingDatatable.ajax.url(urlSend);
      closingDatatable.ajax.reload();
    }
  };

  app.daterangepickerLoad = function() { //datefilter
    var start = moment().startOf('month');
    var end = moment().add(1, 'days');
    let rangePicker = app.rangePicker;
    rangePicker.singleDatePicker = false;
    rangePicker.startDate = start;
    rangePicker.endDate = end;
    rangePicker.ranges = {
      'Hoy': [moment(), moment().add(1, 'days')],
      'Ayer': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
      'Últimos 7 días': [moment().subtract(6, 'days'), moment().add(1, 'days')],
      'Últimos 30 días': [moment().subtract(29, 'days'), moment().add(1, 'days')],
      'Este Mes': [moment().startOf('month'), moment().endOf('month')],
      'Último Mes': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
    }
    $('#datefilter').daterangepicker(rangePicker);
  }

  $(document).ready(function() {
    app.daterangepickerLoad(); //datefilter
    var urlSend = closingUrl; //datefilter
    urlSend += "&datefilter=" + $("#datefilter").val(); //datefilter
    closingDatatable = app.datatable({
      url: urlSend,
      id: "#closings_list",
      order: [
        [0, "desc"]
      ]
    });

    <?php if ($closing_id) : ?>
      app.receiptOfClosing(<?= $closing_id ?>)
    <?php endif; ?>
  });

  app.receiptOfClosing = function(closeId) {
    $.post(
      '<?= site_url('closings/receipt/') ?>' + closeId, {
        ajax: true
      },
      function(data) {
        $("#receipt").html(data.html)
        printJS({
          printable: 'receipt',
          type: 'html'
        })
        console.log(data)
      },
      'json'
    )
  }
</script>