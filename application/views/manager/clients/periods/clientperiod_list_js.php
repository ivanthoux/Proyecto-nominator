<script>
    var paymentDatatable;
    var paymentUrl = "<?= site_url('clientperiods/datatables?dt=true&client=' . $client_id) ?>";

    apply_filter = function () {
        var urlSend = paymentUrl;
        <?php foreach ($filters as $field => $values): ?>
            urlSend += "&filter[<?= $field ?>]=" + $("#filter_<?= $field ?>").val();
        <?php endforeach; ?>
        urlSend += "&datefilter=" + $("#datefilter").val().replace(/ /g, ''); //datefilter
        if (paymentDatatable) { //datefilter
            paymentDatatable.ajax.url(urlSend);
            paymentDatatable.ajax.reload();
        }
    };

    app.daterangepickerLoad = function () { //datefilter
        var start = moment().subtract(29, 'days');
        var end = moment();
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
		$('#datefilter').on('apply.daterangepicker', function(ev, picker) {
			$(this).val(picker.startDate.format(rangePicker.locale.format) + rangePicker.locale.separator + picker.endDate.format(rangePicker.locale.format));
            apply_filter()
		});
		$('#datefilter').on('cancel.daterangepicker', function(ev, picker) {
			$(this).val('');
		});
    };

    $(document).ready(function () {
        app.daterangepickerLoad(); //datefilter
        var urlSend = paymentUrl; //datefilter

        paymentDatatable = app.datatable({
            url: urlSend,
            id: "#period_list",
            order: [[0, "asc"]]
        });
    });
</script>
