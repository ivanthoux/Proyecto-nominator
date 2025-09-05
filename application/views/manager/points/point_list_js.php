<script>

    var pointDatatable;
    var pointUrl = "<?= site_url('points/datatables?dt=true') ?>";
    apply_filter = function () {
        var urlSend = pointUrl;
<?php foreach ($filters as $field => $values): ?>
            urlSend += "&filter[<?= $field ?>]=" + $("#filter_<?= $field ?>").val();
<?php endforeach; ?>
        urlSend += "&datefilter=" + $("#datefilter").val().replace(/ /g, ''); //datefilter
        if (pointDatatable) { //datefilter
            pointDatatable.ajax.url(urlSend);
            pointDatatable.ajax.reload();
        }
    };

    app.pointCallback = function (data) {
        if (data.aoData.length > 0) {
            $('#veraz').html(data.aoData[0]._aData.point_veraz.point);
            $('#siisa').html(data.aoData[0]._aData.point_siisa.point);
        } else {
            $('#veraz').html(0);
            $('#siisa').html(0);
        }
    };

    app.daterangepickerLoad = function () { //datefilter
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
        $('#datefilter').on('apply.daterangepicker', function (ev, picker) {
            apply_filter();
        });
    };

    $(document).ready(function () {
        app.daterangepickerLoad(); //datefilter
        var urlSend = pointUrl; //datefilter
        urlSend += "&datefilter=" + $("#datefilter").val().replace(/ /g, ''); //datefilter

        pointDatatable = app.datatable({
            url: urlSend,
            id: "#point_list",
            pageLength: 50,
            order: [[1, "desc"]],
            callbackAjax: app.pointCallback
        });
    });
</script>
