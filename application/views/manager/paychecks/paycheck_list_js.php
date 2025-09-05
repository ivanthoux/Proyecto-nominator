<script>
    var paycheckDatatable;
    var paycheckUrl = "<?= site_url('paychecks/datatables?dt=true') ?>";
    apply_filter = function() {
        var urlSend = paycheckUrl;
        <?php if (!empty($filters_super)) : ?>
            <?php foreach ($filters_super as $field => $values) : ?>
                urlSend += "&filter[<?= $field ?>]=" + $("#filter_<?= $field ?>").val();
            <?php endforeach; ?>
        <?php endif ?>
        <?php foreach ($filters as $field => $values) : ?>
            urlSend += "&filter[<?= $field ?>]=" + $("#filter_<?= $field ?>").val();
        <?php endforeach; ?>

        paycheckDatatable.ajax.url(urlSend);
        paycheckDatatable.ajax.reload();
    };

    $(document).ready(function() {
        paycheckDatatable = app.datatable({
            url: paycheckUrl,
            id: "#paycheck_list",
        });
    });
</script>