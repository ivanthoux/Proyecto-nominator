<script>
    var clientDatatable;
    var clientUrl = "<?= site_url('clientfiles/datatables?dt=true' . (!empty($client_id) ? '&client=' . $client_id : '')) ?>";

    $(document).ready(function () {
        var urlSend = clientUrl; //datefilter
        clientDatatable = app.datatable({
            url: urlSend,
            id: "#client_file",
            searching: false,
            order: [[1, "desc"]]
        });
    });
</script>
