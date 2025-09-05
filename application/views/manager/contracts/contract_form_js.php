<script>
    $(document).ready(function() {
        let config = {
            locale: 'es',
            stepping: 5,
            showClose: true,
            format: 'DD-MM-YYYY',
            useCurrent: false, //Important! See issue #1075
        };
        $('#contract_start').datetimepicker(config).datetimepicker();
        $('#contract_end').datetimepicker(config).datetimepicker();
    });
    app.datepickerLoad = function() {
        $('.datepicker').daterangepicker(app.rangePicker);
    };

    app.categoryChanged = () => {
        if ($('#unionagreementcategory_type').val()) {
            $.get(app.baseUrl + 'scripts/getCategories', {
                "unionAgreementType": $('#unionagreementcategory_type').val()
            }, function(data) {
                if (data.status == 'success') {
                    $('#category').html("<option></option>");
                    data.data.forEach(function(ol) {
                        let option = $("<option>")
                            .val(ol.unionagreementcategory_id)
                            .html(ol.unionagreementcategory_category);
                        // if (unionagreementcategory_id !== null && ol.unionagreementcategory_id == unionagreementcategory_id) {
                        //     option.prop('selected', true);
                        // }
                        $('#category').append(option);
                    });
                    if ($('#category option').length == 2) {
                        $('#category option:eq(1)').prop('selected', true);
                    }
                } else {
                    console.log("ERROR");
                }
            }, 'json').error(function(error) {
                console.log(error)
            });
        } else {
            $('#category').html("<option></option>");
        }
    }
</script>