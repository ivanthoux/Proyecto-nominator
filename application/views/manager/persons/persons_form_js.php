<script>
    $(document).ready(function() {
        let config = {
            locale: 'es',
            stepping: 5,
            showClose: true,
            format: 'DD-MM-YYYY',
            useCurrent: false, //Important! See issue #1075
        };
        $('#person_birth').datetimepicker(config).datetimepicker();
    });
    app.datepickerLoad = function() {
        $('.datepicker').daterangepicker(app.rangePicker);
    };

    app.validateForm = () => {
        $('#submit').val('');
        $('#submit').val(1);
        return ($('#submit').val() !== '');
    }

    app.capital_letter = (obj) => {
        let str = $(obj).val().split(" ");

        for (var i = 0, x = str.length; i < x; i++) {
            str[i] = str[i][0].toUpperCase() + str[i].substr(1);
        }

        $(obj).val(str.join(" "));
    }
</script>