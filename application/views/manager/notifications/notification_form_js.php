<script>
    let notificationDetails = <?= !empty($notificationDetails) ? json_encode($notificationDetails) : '[]' ?>;
    var notificationDatatable
    $(document).ready(function() {
        notificationDetails = notificationDetails.map((notificationDetail, index) => ({
            ...notificationDetail,
            id: index+1
        }))
        clientDatatable = $("#detail_list").DataTable({
            data: notificationDetails,
            lengthChange: false,
            pageLength: 10,
            language: {
                url: "//cdn.datatables.net/plug-ins/1.10.9/i18n/Spanish.json"
            },
            columns: [{
                title: '#',
                data: 'id',
                width: "5%",
            }, {
                title: 'Detalle',
                data: 'notificationdetail_observation',
                width: "95%",
            }]
        })
    });
</script>