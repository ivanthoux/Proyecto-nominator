<script>
    var contractDatatable;
    var contractUrl = "<?= site_url('contracts/datatables?dt=true') ?>";
    apply_filter = function() {
        var urlSend = contractUrl;
        <?php if (!empty($filters_super)) : ?>
            <?php foreach ($filters_super as $field => $values) : ?>
                urlSend += "&filter[<?= $field ?>]=" + $("#filter_<?= $field ?>").val();
            <?php endforeach; ?>
        <?php endif ?>
        <?php foreach ($filters as $field => $values) : ?>
            urlSend += "&filter[<?= $field ?>]=" + $("#filter_<?= $field ?>").val();
        <?php endforeach; ?>

        contractDatatable.ajax.url(urlSend);
        contractDatatable.ajax.reload();
    };

    $(document).ready(function() {
        contractDatatable = app.datatable({
            url: contractUrl,
            id: "#contract_list",
        });
    });
</script>