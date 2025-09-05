<script>
    $(document).ready(function () {
        $('#packrule_rule').change(function () {
            var data_type = $(this).find('option:selected').data('type');
            $("#rule_type").val(data_type);

            $("#packrule_value").prop('disabled', (data_type == '' || data_type == '3'));
            $("input:radio").prop('disabled', (data_type == '' || data_type != '3'));

            $("#packrule_value").val('');
            $("input[name=packrule_boolean]").prop("checked", false);
        });
    });
</script>
