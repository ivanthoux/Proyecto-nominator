<script>
    $(document).ready(function() {
        app.datepickerLoad();
        let start = new Date();
        if (start.getDate() <= 20) {
            start.setDate(10);
            start.setMonth(start.getMonth() + 1);
            if (start.getDay() == 0) {
                start.setDate(start.getDate() + 1);
            }
        } else {
            start.setDate(10);
            start.setMonth(start.getMonth() + 2);
            if (start.getDay() == 0) {
                start.setDate(start.getDate() + 1);
            }
        }
        let day = (start.getDate() <= 9 ? '0' + start.getDate() : start.getDate());
        let month = ((start.getMonth() + 1) <= 9 ? '0' + (start.getMonth() + 1) : (start.getMonth() + 1));

        <?php if (isset($edit)) : ?>
            $("#clientpack_start").val('<?= date('d-m-Y H:i', strtotime($edit['clientpack_start'])) ?>');
        <?php else : ?>
            $("#clientpack_start").val(day + '-' + month + '-' + start.getFullYear() + ' 08:00');
        <?php endif; ?>
    });

    app.datepickerLoad = function() {
        $('#clientpack_start').datetimepicker({
            locale: 'es',
            stepping: 5,
            showClose: true,
            format: 'D-MM-YYYY HH:mm',
            disabledHours: [0, 1, 2, 3, 4, 5, 6, 7, 21, 22, 23, 24],
            useCurrent: false, //Important! See issue #1075
        });
    };

    app.sessionsUpdate = function() {
        if ($('#clientpack_package').val() != '') {
            var type = $('#clientpack_package').find('option:selected').data('type');
            // console.log(type);

            var sessions = parseInt($("#clientpack_sessions").val());
            var price = parseFloat($("#clientpack_price").val());

            var commision = (parseFloat($('#clientpack_package').find('option:selected').data('commision') * sessions) / 100) + 1;
            var session_price = Math.round((((price * commision) + parseFloat($('#clientpack_package').find('option:selected').data('expenses'))) / sessions) * 100) / 100;
            var priceFinal = Math.round((session_price * sessions) * 100) / 100;
            $("#clientpack_final").val(priceFinal);
            $("#clientpack_final_hidden").val(priceFinal);
            $("#clientpack_sessions_price").val(session_price);
            $("#clientpack_sessions_price_hidden").val(session_price);
            
            commision = (parseFloat($('#clientpack_package').find('option:selected').data('commision_2') * sessions) / 100) + 1;
            session_price = Math.round((((price * commision) + parseFloat($('#clientpack_package').find('option:selected').data('expenses'))) / sessions) * 100) / 100;
            $("#clientpack_sessions_2_price").val(session_price);
            $("#clientpack_sessions_2_price_hidden").val(session_price);

            app.datePickerRefresh(type * sessions, 'day');
        }
    };

    app.datePickerRefresh = function(times, period) {
        $("#clientpack_end").val($('#clientpack_start').data("DateTimePicker").viewDate().add(times, period).format('DD-MM-YYYY HH:mm'));
        $("#clientpack_start").on("dp.change", function(e) {
            $("#clientpack_end").val(e.date.add(times, period).format('DD-MM-YYYY HH:mm'));
        });
    };

    app.verifyProduct = function() {
        $("#clientpack_verify").val('1');
        $("#clientproduct_form").submit();
    };

    app.confirmSubmit = () => {
        app.loadding = bootbox.dialog({
            message: '<p class="text-center mb-0 ml-0 mr-0"><i class="fa fa-spin fa-spinner"></i> Enviando datos, espere por favor...</p>',
            closeButton: false
        });
        $("#clientproduct_form").submit();
    }

    app.onSubmit = function() {
        if ($("#clientpack_package").val() === "") {
            return;
        }
        app.actionConfirm("#clientproduct_form", 'app.confirmSubmit()');
    };

    app.packchanged = function(el) {
        if ($(el).val() != "") {
            $('#clientpack_package_hidden').val($(el).find('option:selected').val());
            $('#clientpack_sessions').val($(el).find('option:selected').data('sessionsmax'));
            $('#clientpack_price').val($(el).find('option:selected').data('price'));

            let start = new Date();
            switch ($(el).find('option:selected').data('type')) {
                case "Semanal":
                    start.setDate(start.getDate() + 14);
                    break;
                case "Mensual":
                    if (start.getDate() <= 20) {
                        start.setDate(10);
                        start.setMonth(start.getMonth() + 1);
                        if (start.getDay() == 0) {
                            start.setDate(start.getDate() + 1);
                        }
                    } else {
                        start.setDate(10);
                        start.setMonth(start.getMonth() + 2);
                        if (start.getDay() == 0) {
                            start.setDate(start.getDate() + 1);
                        }
                    }
                    break
            }
            let day = (start.getDate() <= 9 ? '0' + start.getDate() : start.getDate());
            let month = ((start.getMonth() + 1) <= 9 ? '0' + (start.getMonth() + 1) : (start.getMonth() + 1));
            $("#clientpack_start").val(day + '-' + month + '-' + start.getFullYear() + ' 08:00');

            app.sessionsUpdate(el);
        }
    };

    <?php
    $packRules = array();
    foreach ($packages as $pack) {
        $pr = array();
        $pr['pack_id'] = $pack['pack_id'];
        foreach ($pack['pack_rules'] as $rules) {
            $r = array();
            foreach ($rules as $key => $rule) {
                $r[$key] = empty($rule) ? null : $rule;
            }
            $pr['rules'][] = $r;
        }
        $packRules[] = $pr;
    }
    echo "var packRules = " . str_replace("\\", "", json_encode($packRules)) . ";\n";
    ?>
</script>