<script>
    var datatable;
    var url = "<?= site_url('vouchers/datatables?dt=true') ?>";

    apply_filter = function() {
        var urlSend = url + "&datefilter=" + $("#datefilter").val(); //datefilter

        if (datatable) { //datefilter
            datatable.ajax.url(urlSend);
            datatable.ajax.reload();
        }
    };

    app.daterangepickerLoad = function() { //datefilter
        var start = moment().startOf('month');
        var end = moment().endOf('day');
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
        };
        $('#datefilter').daterangepicker(rangePicker);
    };

    app.generateVouchers = function(e) {
        e.preventDefault();
        e.stopImmediatePropagation();

        let dlg = bootbox.dialog({
            title: '<i class="fa fa-spin fa-spinner"></i>  Trabajando',
            message: 'Espere por favor',
            closeButton: false
        });
        var url = '<?= site_url('vouchers/generate') ?>';
        app.ajax(url, [], function(request) {
            dlg.modal('hide');
            if (request.status == 'success') {
                window.location.href = '<?= site_url('vouchers/all') ?>';
            } else {
                bootbox.alert(request.msg);
            }
        });
    };

    app.generateIvaBook = function(e) {
        e.preventDefault();
        e.stopImmediatePropagation();

        window.open("<?= site_url('vouchers/ivaBook?') ?>period=" + $("#datefilter").val(), '_blank');
    };

    $(document).ready(function() {
        app.daterangepickerLoad(); //datefilter
        var urlSend = url + "&datefilter=" + $("#datefilter").val(); //datefilter

        datatable = app.datatable({
            url: urlSend,
            id: "#_list",
            order: [
                [1, "desc"]
            ]
        });

        $('#create_voucher').click(function(e) {
            app.generateVouchers(e);
        });
        $('#create_ivabook').click(function(e) {
            app.generateIvaBook(e);
        });
    });
</script>