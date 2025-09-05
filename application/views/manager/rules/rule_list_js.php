<script>
    var ruleDatatable;
    var ruleUrl = "<?= site_url('rules/datatables?dt=true') ?>";
    apply_filter = function () {
        var urlSend = ruleUrl;
        <?php foreach ($filters as $field => $values): ?>
            urlSend += "&filter[<?= $field ?>]=" + $("#filter_<?= $field ?>").val();
        <?php endforeach; ?>
        ruleDatatable.ajax.url(urlSend);
        ruleDatatable.ajax.reload();
    };

    $(document).ready(function () {
        ruleDatatable = app.datatable({
            url: ruleUrl,
            id: "#rule_list",
            order: [[0, "asc"]]
        });
    });
</script>
