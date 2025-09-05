<script>
    var personDatatable;
    var personUrl = "<?= site_url('personfiles/datatables?dt=true' . (!empty($person_id) ? '&person=' . $person_id : '')) ?>";

    $(document).ready(function () {
        var urlSend = personUrl; //datefilter
        personDatatable = app.datatable({
            url: urlSend,
            id: "#person_file",
            searching: false,
            order: [[1, "desc"]]
        });
    });
</script>
