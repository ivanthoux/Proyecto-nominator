<script>
    apply_filter = function() {
        app.loadding = bootbox.dialog({
            message: '<p class="text-center mb-0 ml-0 mr-0"><i class="fa fa-spin fa-spinner"></i> Generando Tablero, espere por favor...</p>',
            closeButton: false
        });
        var urlSend = app.baseUrl + 'manager/dashboard';
        urlSend += "/" + $("#datefilter").val().replace(/ /g, ''); //datefilter

        console.log('filterapply');
        window.location.href = urlSend+'/true';
    };

    app.daterangepickerLoad = function() { //datefilter
        let rangePicker = app.rangePicker;
        rangePicker.singleDatePicker = false;
        rangePicker.autoApply = true;
        rangePicker.ranges = {
            'Hoy': [moment(), moment()],
            'Ayer': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Esta semana': [moment().startOf('week'), moment()],
            'Este Mes': [moment().startOf('month'), moment().endOf('month')]
        }
        $('#datefilter').daterangepicker(rangePicker);

        $('#datefilter').on('apply.daterangepicker', function(ev, picker) {
            apply_filter();
        });
    }

    $(document).ready(function() {
        app.daterangepickerLoad(); //datefilter
        $(".knob").knob();
        <?php if (!$charge) : ?>
            apply_filter();
        <?php endif; ?>
    });
</script>