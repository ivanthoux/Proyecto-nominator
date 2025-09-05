<script>
    var allAdditionals = [
        <?php foreach ($additionals as $additional) : ?>
            <?= json_encode($additional) . ',' ?>
        <?php endforeach; ?>
    ]
    $(document).ready(function() {
        console.log("allAdditionals", allAdditionals);
        let config = {
            locale: 'es',
            stepping: 5,
            showClose: true,
            format: 'DD-MM-YYYY',
            useCurrent: false, //Important! See issue #1075
        };
        $('#paycheck_date').datetimepicker(config).datetimepicker().on('dp.change', function(e) {
            app.monthChanged()
        });
    });
    app.datepickerLoad = function() {
        $('.datepicker').daterangepicker(app.rangePicker);
    };

    app.monthChanged = () => {
        if ($('#paycheck_date').val()) {
            $.get(app.baseUrl + 'scripts/getWorkingDays', {
                "startDateString": $('#paycheck_date').val()
            }, function(data) {
                if (data.status == 'success') {
                    let totalHaber = 0;
                    let totalDiscount = 0;
                    const paycheckBasicAmount = $('#paycheck_basic_diary_amount').val()
                    let daysWorked = data.data[0].DifD;
                    daysWorked = 30;
                    let fullPaycheckAmount = daysWorked * paycheckBasicAmount;
                    totalHaber += fullPaycheckAmount;
                    let greatestPayCheckAmount = daysWorked * paycheckBasicAmount;
                    $('#daysWorked').html(daysWorked)
                    $('#paycheck_basic_days').val(daysWorked)
                    $('#paycheck_basic_amount').val(app.money_format(daysWorked * paycheckBasicAmount, true))
                    $(`[name="paycheck_basic_amount"]`).val(daysWorked * paycheckBasicAmount)
                    allAdditionals.forEach((additional) => {
                        if (additional['additional_remunerative'] == '0') return;
                        if (additional['additional_key'] == 'antiquity') return;
                        const additionalKey = additional['additional_key'];
                        const shouldSubstract = additional['additional_haber'] == '0';
                        const coefficient = additional['additional_coefficient'];
                        const singleValue = (shouldSubstract ? -1 : 1) * greatestPayCheckAmount * coefficient

                        fullPaycheckAmount = fullPaycheckAmount + (shouldSubstract ? -1 : 1) * fullPaycheckAmount * coefficient;

                        if (fullPaycheckAmount > greatestPayCheckAmount) greatestPayCheckAmount = fullPaycheckAmount

                        $(`#paycheck_additional_${additionalKey}_base`).val(app.money_format(greatestPayCheckAmount, true))
                        $(`#paycheck_additional_${additionalKey}_coefficient`).val(app.money_format(coefficient, false))
                        if (shouldSubstract) {
                            totalDiscount += Math.abs(singleValue);
                            $(`#paycheck_additional_${additionalKey}_discount`).val(app.money_format(Math.abs(singleValue), true))
                        } else {
                            totalHaber += singleValue;
                            $(`#paycheck_additional_${additionalKey}_haber`).val(app.money_format(singleValue, true))
                        }
                        $(`[name="paycheck_additional_${additionalKey}_single"]`).val(Math.abs(singleValue))
                    })
                    $(`#paycheck_additional_discount_total`).val(app.money_format(Math.abs(totalDiscount), true))
                    $(`#paycheck_additional_haber_total`).val(app.money_format(totalHaber, true))
                    $(`#paycheck_net`).val(app.money_format(totalHaber - totalDiscount, true))
                    $(`[name="paycheck_bruto"]`).val(Math.abs(totalHaber))
                    $(`[name="paycheck_neto"]`).val(Math.abs(totalHaber - totalDiscount))
                    return;
                } else {
                    console.log("ERROR");
                }
            }, 'json').error(function(error) {
                console.log(error)
            });
        }
    }

    app.money_format = function(amount, dollarSign = false) {
        let formated = Intl.NumberFormat('de-DE', {
            style: "currency",
            currency: "USD"
        }).format(Math.round(amount * 100) / 100);
        formated = formated.replace("$", "");
        if (dollarSign == true)
            formated = `$ ${formated}`;
        return formated;
    }
</script>