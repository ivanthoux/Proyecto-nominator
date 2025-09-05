<script>

    var settlementDatatable;
    var settlementUrl = "<?= site_url('settlements/datatables?dt=true') ?>";
    apply_filter = function () {
        var urlSend = settlementUrl;
        <?foreach($filters as $field => $values){?>
          urlSend += "&filter[<?=$field?>]=" + $("#filter_<?=$field?>").val();
        <?}?>
        urlSend += "&datefilter=" + $("#datefilter").val(); //datefilter
        if(settlementDatatable){ //datefilter
          settlementDatatable.ajax.url(urlSend);
          settlementDatatable.ajax.reload();
        }
    };

    app.daterangepickerLoad = function(){ //datefilter
      var start = moment().startOf('month');
      var end = moment();
      let rangePicker = app.rangePicker;
      rangePicker.singleDatePicker = false;
      rangePicker.startDate= start;
      rangePicker.endDate= end;
      rangePicker.ranges= {
         'Hoy': [moment(), moment()],
         'Ayer': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
         'Últimos 7 días': [moment().subtract(6, 'days'), moment()],
         'Últimos 30 días': [moment().subtract(29, 'days'), moment()],
         'Este Mes': [moment().startOf('month'), moment().endOf('month')],
         'Último Mes': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
      }
      $('#datefilter').daterangepicker(rangePicker);
    }

    $(document).ready(function () {
      app.daterangepickerLoad(); //datefilter
      var urlSend = settlementUrl; //datefilter
      urlSend += "&datefilter=" + $("#datefilter").val();//datefilter

      settlementDatatable = app.datatable({
        url: urlSend,
        id: "#settlements_list",
        order: [[0, "desc"]]
      });
    });
</script>
