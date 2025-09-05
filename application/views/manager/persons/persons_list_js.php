<script>
    var clientDatatable;
    var clientUrl = "<?= site_url('persons/datatables?dt=true') ?>";
    apply_filter = function() {
        var urlSend = clientUrl;
        <?php if (!empty($filters_super)) : ?>
            <?php foreach ($filters_super as $field => $values) : ?>
                urlSend += "&filter[<?= $field ?>]=" + $("#filter_<?= $field ?>").val();
            <?php endforeach; ?>
        <?php endif ?>
        <?php foreach ($filters as $field => $values) : ?>
            urlSend += "&filter[<?= $field ?>]=" + $("#filter_<?= $field ?>").val();
        <?php endforeach; ?>

        clientDatatable.ajax.url(urlSend);
        clientDatatable.ajax.reload();
    };

    $(document).ready(function() {
        clientDatatable = app.datatable({
            url: clientUrl,
            id: "#client_list",
        });
    });
</script>