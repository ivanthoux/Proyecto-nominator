<script>
    var rangePicker = false;

    app.generate = () => {
        let params = {};
        if ($("#pack_id").val() !== '' && $("#pack_id").is(":visible"))
            params.pack = $("#pack_id").val();

        $('input').each((idx, el) => {
            if ($('#' + $(el).attr('id')).is(':visible')) {
                console.log($('#' + $(el).attr('id')).attr('type'));

                if ($('#' + $(el).attr('id')).attr('type') != 'checkbox' &&
                    $('#' + $(el).attr('id')).attr('type') != 'radio') {
                    params[$('#' + $(el).attr('id')).attr('name')] = $('#' + $(el).attr('id')).val();
                } else {
                    params[$('#' + $(el).attr('id')).attr('name')] = $('#' + $(el).attr('id')).is(':checked') ? 1 : "";
                }
            }
        });
        switch ($('#report').val()) {
            case '':
                app.dialog.warring('Debe seleccionar un reporte');
                break
            default:
                window.open(app.baseUrl + 'reports/excel/' + $('#report').val() + '?' + $.param(params));
                break;
        }
    };

    app.reset = () => {
        $('#report').val('');
        $('#pack_id').val('');
        app.chengeOffice();
    };

    app.setFilters = () => {
        // hide or show more filters, change config of filters
        if (!rangePicker)
            rangePicker = app.rangePicker;

        switch ($("#report").val()) {
            case 'vintage':
                rangePicker.singleDatePicker = false;
                rangePicker.ranges = {
                    'Hoy': [moment(), moment()],
                    'Ayer': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Últimos 7 días': [moment().subtract(6, 'days'), moment()],
                    'Últimos 30 días': [moment().subtract(29, 'days'), moment()],
                    'Este Año': [moment().startOf('year'), moment().endOf('month')],
                    'Este Mes': [moment().startOf('month'), moment().endOf('month')],
                    'Último Mes': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                };
                $('#period').daterangepicker(rangePicker);
                $('#period').val('<?= date('d-m-Y', strtotime('first day of january this year')) . ' / ' . date('d-m-Y', strtotime('last day of this month')) ?>');

                $('#periodDiv').show();
                $('#dateDiv').hide();
                $('#packDiv').show();
                $('#otherDiv').hide();
                break;
            case 'deuda_acumulada':
                let config = {
                    locale: 'es',
                    stepping: 5,
                    showClose: true,
                    format: 'DD-MM-YYYY',
                    useCurrent: false, //Important! See issue #1075
                };
                $('#datePicker').datetimepicker(config);
                $('#datePicker').val('<?= date('d-m-Y') ?>');

                $('#otherDiv').html($('<div>')
                    .append(
                        $('<input>')
                        .attr('id', 'short')
                        .attr('name', 'short')
                        .attr('type', 'checkbox')
                        .addClass('form-check-input')
                        .val('1')
                    )
                    .append(
                        $('<label>')
                        .attr('for', 'short')
                        .html('&nbsp;Short Link')
                    )
                );

                $('#periodDiv').hide();
                $('#dateDiv').show();
                $('#packDiv').hide();
                $('#otherDiv').show();
                break;
            case 'clientes_mora':
                $('#periodDiv').hide();
                $('#dateDiv').hide();
                $('#packDiv').hide();
                $('#otherDiv').hide();
                break;
            default:
                rangePicker.singleDatePicker = false;
                rangePicker.ranges = {
                    'Hoy': [moment(), moment()],
                    'Ayer': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Últimos 7 días': [moment().subtract(6, 'days'), moment()],
                    'Últimos 30 días': [moment().subtract(29, 'days'), moment()],
                    'Este Mes': [moment().startOf('month'), moment().endOf('month')],
                    'Último Mes': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                };
                $('#period').daterangepicker(rangePicker);
                $('#period').val('<?= date('d-m-Y', strtotime('first day of this month')) . ' / ' . date('d-m-Y', strtotime('last day of this month')) ?>');

                $('#periodDiv').show();
                $('#dateDiv').hide();
                $('#packDiv').show();
                $('#otherDiv').hide();
                break;
        }
    };

    $(document).ready(() => {
        app.setFilters();
    });
</script>