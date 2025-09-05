<script>
  var paymentDatatable;
  var paymentUrl = "<?= site_url('clientperiods/datatables?dt=true' . (!empty($getpaid) ? '&getpaid=1' : '')) ?>";

  apply_filter = function() {
    var urlSend = paymentUrl;
    urlSend += "&datefilter=" + $("#datefilter").val(); //datefilter
    <?php if (isset($road) && $road !== 'false') : ?>
      urlSend += "&filter[clientpack_roadmap]=" + '<?= $road ?>';
    <?php endif; ?>
    <?php if (isset($client) && $client !== 'false') : ?>
      urlSend += "&filter[clientpack_client]=" + '<?= $client ?>';
    <?php endif; ?>
    if (paymentDatatable) { //datefilter
      paymentDatatable.ajax.url(urlSend);
      paymentDatatable.ajax.reload();
    }
  };

  apply_date_filter = function() {
    // console.log(encodeURI($('#search_client').val()))
    var urlSend = "<?= site_url('clientperiods/getpaid') ?>";
    urlSend += "/" + $("#datefilter").val().replace(/ /g, '') +
    ($('#search_roadmap').val() ? "/" + $('#search_roadmap').val() : '/false') +
    ($('#search_client').val() ? "/" + escape($('#search_client').val()) : '') +
    "<?= (!empty($getpaid) ? '?getpaid=1' : '') ?>"; //datefilter
    // console.log(urlSend)
    window.location.href = urlSend;
  }

  app.createCupon = () => {
    var urlSend = "<?= site_url('clientperiods/coupon') . (!empty($getpaid) ? '?getpaid=1' : '') ?>";
    <?php if (isset($road) && $road !== 'false') : ?>
      urlSend += "&filter[clientpack_roadmap]=" + '<?= $road ?>';
    <?php endif; ?>
    <?php if (isset($client) && $client !== 'false') : ?>
      urlSend += "&filter[clientpack_client]=" + '<?= $client ?>';
    <?php endif; ?>
    if ($("#period_list_filter input").val())
      urlSend += "&filter[search]=" + $("#period_list_filter input").val();
    urlSend += "&datefilter=" + $("#datefilter").val(); //datefilter
    window.location = urlSend;
  };

  app.createPayments = () => {
    var urlSend = "<?= site_url('clientpayments/form/0/0/0/1') . (!empty($getpaid) ? '?getpaid=1' : '') ?>";
    <?php if (isset($road) && $road !== 'false') : ?>
      urlSend += "&filter[clientpack_roadmap]=" + '<?= $road ?>';
    <?php endif; ?>
    <?php if (isset($client) && $client !== 'false') : ?>
      urlSend += "&filter[clientpack_client]=" + '<?= urlencode($client) ?>';
    <?php endif; ?>
    if ($("#period_list_filter input").val())
      urlSend += "&filter[search]=" + $("#period_list_filter input").val();
    window.location = urlSend;
  };

  app.daterangepickerLoad = function() { //datefilter
    let rangePicker = {
      locale: 'es',
      stepping: 5,
      showClose: true,
      format: 'DD-MM-YYYY',
      useCurrent: false, //Important! See issue #1075
    };
    rangePicker.minDate = new Date();
    $('#datefilter').datetimepicker(rangePicker);

    $('#datefilter').on("dp.change", function(e) {
      apply_date_filter();
    });
  }

  app.changeRoad = () => {
    if (!$('#search_roadmap').val())
      apply_date_filter();
  }
  app.changeClient = () => {
    if (!$('#search_client').val())
      apply_date_filter();
  }

  $(document).ready(function() {
    app.daterangepickerLoad(); //datefilter
    var urlSend = paymentUrl; //datefilter
    urlSend += "&datefilter=" + $("#datefilter").val(); //datefilter
    <?php if (isset($road) && $road !== 'false') : ?>
      urlSend += "&filter[clientpack_roadmap]=" + '<?= $road ?>';
    <?php endif; ?>
    <?php if (isset($client) && $client !== 'false') : ?>
      urlSend += "&filter[clientpack_client]=" + '<?= $client ?>';
    <?php endif; ?>

    paymentDatatable = app.datatable({
      url: urlSend,
      id: "#period_list",
      order: [
        [0, "asc"]
      ],
      callbackAjax: (data) => {
        $("#periods_not_paid").html(data.json.balances.periods_not_paid_f);
        $("#periods_paid").html(data.json.balances.periods_paid_f);
        $("#periods_total").html(data.json.balances.periods_total);
      }
    });

    $("#search_roadmap").typeahead({
      delay: 500,
      source: function(query, process) {
        return $.get(
          app.baseUrl + "clientpacks/getRoadmaps", {
            search: {
              value: query
            }
          },
          function(data) {
            return process(data);
          },
          "json"
        );
      },
      afterSelect: function(selected) {
        apply_date_filter();
      },
    });

    $("#search_client").typeahead({
      delay: 500,
      source: function(query, process) {
        if ($("#search_roadmap").val())
          return $.get(
            app.baseUrl + "clientpacks/getClientsRoad/" + $("#search_roadmap").val(), {
              search: {
                value: query
              }
            },
            function(data) {
              return process(data);
            },
            "json"
          );
        else
          return $.get(
            app.baseUrl + "clients/getSearch", {
              search: {
                value: query
              }
            },
            function(data) {
              return process(data);
            },
            "json"
          );
        return [];
      },
      afterSelect: function(selected) {
        apply_date_filter();
      },
    });
  });
</script>