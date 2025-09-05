<script>

    var expenseDatatable;
    var expenseUrl = "<?= site_url('expenses/datatables?dt=true') ?>";

    apply_filter = function () {
        var urlSend = expenseUrl;
        <?foreach($filters as $field => $values){?>
          urlSend += "&filter[<?=$field?>]=" + $("#filter_<?=$field?>").val();
        <?}?>
        urlSend += "&datefilter=" + $("#datefilter").val(); //datefilter
        if(expenseDatatable){
          expenseDatatable.ajax.url(urlSend);
          expenseDatatable.ajax.reload();
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
         'Hoy': [moment(), moment().endOf('day')],
         'Ayer': [moment().subtract(1, 'days'), moment().subtract(1, 'days').endOf('day')],
         'Últimos 7 días': [moment().subtract(6, 'days'), moment().endOf('day')],
         'Últimos 30 días': [moment().subtract(29, 'days'), moment().endOf('day')],
         'Este Mes': [moment().startOf('month'), moment().endOf('month').endOf('day')],
         'Último Mes': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
      }
      $('#datefilter').daterangepicker(rangePicker);
    }

    $(document).ready(function () {
      app.daterangepickerLoad(); //datefilter
      var urlSend = expenseUrl; //datefilter
      urlSend += "&datefilter=" + $("#datefilter").val();//datefilter
      expenseDatatable = app.datatable({
          url: urlSend,
          id: "#expenses_list",
          order: [[0, "desc"]]
      });
    });
</script>
