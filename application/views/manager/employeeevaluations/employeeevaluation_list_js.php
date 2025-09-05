<script>
    var employeeEvaluationDatatable;
    var employeeEvaluationUrl = "<?= site_url('employeeevaluations/datatables?dt=true') ?>";
    apply_filter = function() {
        var urlSend = employeeEvaluationUrl;
        <?php if (!empty($filters_super)) : ?>
            <?php foreach ($filters_super as $field => $values) : ?>
                urlSend += "&filter[<?= $field ?>]=" + $("#filter_<?= $field ?>").val();
            <?php endforeach; ?>
        <?php endif ?>
        <?php foreach ($filters as $field => $values) : ?>
            urlSend += "&filter[<?= $field ?>]=" + $("#filter_<?= $field ?>").val();
        <?php endforeach; ?>

        employeeEvaluationDatatable.ajax.url(urlSend);
        employeeEvaluationDatatable.ajax.reload();
    };

    $(document).ready(function() {
        employeeEvaluationDatatable = app.datatable({
            url: employeeEvaluationUrl,
            id: "#employeeevaluation_list",
        });
    });
</script>