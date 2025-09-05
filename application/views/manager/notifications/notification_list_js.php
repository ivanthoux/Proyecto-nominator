<script>
    var notificationDatatable;
    var clientUrl = "<?= site_url('notifications/datatables?dt=true') ?>";
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

        notificationDatatable.ajax.url(urlSend);
        notificationDatatable.ajax.reload();
    };

    $(document).ready(function() {
        notificationDatatable = app.datatable({
            url: clientUrl,
            id: "#notification_list",
        });
    });
</script>