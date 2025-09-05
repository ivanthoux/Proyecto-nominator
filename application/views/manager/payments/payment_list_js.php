<script>
    var paymentDatatable;
    var paymentUrl = "<?= site_url('payments/datatables?dt=true') ?>";
    apply_filter = function() {
        apply();
    };

    apply = function() {
        var urlSend = paymentUrl;
        <?php foreach ($filters as $field => $values) { ?>
            urlSend += "&filter[<?= $field ?>]=" + $("#filter_<?= $field ?>").val();
        <?php } ?>
        urlSend += "&datefilter=" + $("#datefilter").val(); //datefilter
        if (paymentDatatable) { //datefilter
            paymentDatatable.ajax.url(urlSend);
            paymentDatatable.ajax.reload();
        }
    }

    app.daterangepickerLoad = function() { //datefilter
        var start = moment().startOf('month');
        var end = moment();
        let rangePicker = app.rangePicker;
        rangePicker.singleDatePicker = false;
        rangePicker.startDate = start;
        rangePicker.endDate = end;
        rangePicker.ranges = {
            'Hoy': [moment(), moment()],
            'Ayer': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Últimos 7 días': [moment().subtract(6, 'days'), moment()],
            'Últimos 30 días': [moment().subtract(29, 'days'), moment()],
            'Este Mes': [moment().startOf('month'), moment().endOf('month')],
            'Último Mes': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
        $('#datefilter').daterangepicker(rangePicker);
    }

    $(document).ready(function() {
        app.daterangepickerLoad(); //datefilter
        var urlSend = paymentUrl; //datefilter
        urlSend += "&datefilter=" + $("#datefilter").val(); //datefilter

        paymentDatatable = app.datatable({
            url: urlSend,
            id: "#payments_list",
            order: [
                [0, "desc"]
            ]
        });
    });
</script>