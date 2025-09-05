<script>
    $(document).ready(function() {
        let config = {
            locale: 'es',
            stepping: 5,
            showClose: true,
            format: 'DD-MM-YYYY',
            useCurrent: false, //Important! See issue #1075
        };
        $('#holiday_date').datetimepicker(config).datetimepicker();
    });
    app.datepickerLoad = function() {
        $('.datepicker').daterangepicker(app.rangePicker);
    };
</script>