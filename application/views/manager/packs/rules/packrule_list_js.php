<script>
    var packDatatable;
    var packUrl = "<?= site_url('packrules/datatables?dt=true&pack_id=' . $pack_id) ?>";
    apply_filter = function () {
        var urlSend = packUrl;
        <?php foreach ($filters as $field => $values): ?>
            urlSend += "&filter[<?= $field ?>]=" + $("#filter_<?= $field ?>").val();
        <?php endforeach; ?>

        packDatatable.ajax.url(urlSend);
        packDatatable.ajax.reload();
    };

    $(document).ready(function () {
        packDatatable = app.datatable({
            url: packUrl,
            id: "#packrules_list",
        });
    });
</script>