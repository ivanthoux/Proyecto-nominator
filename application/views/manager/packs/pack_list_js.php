<script>
    var packageDatatable;
    var packageUrl = "<?= site_url('packs/datatables?dt=true') ?>";
    apply_filter = function () {
        var urlSend = packageUrl;
        <?php foreach ($filters as $field => $values): ?>
            urlSend += "&filter[<?= $field ?>]=" + $("#filter_<?= $field ?>").val();
        <?php endforeach; ?>
        packageDatatable.ajax.url(urlSend);
        packageDatatable.ajax.reload();
    };

    $(document).ready(function () {
        packageDatatable = app.datatable({
            url: packageUrl,
            id: "#package_list",
            order: [[0, "asc"]]
        });
    });
</script>
