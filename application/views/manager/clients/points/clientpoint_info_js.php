<script>
    app.update = function (e, url) {
        e.stopPropagation();
        e.preventDefault();
        document.addEventListener("click", function (ev) {
            ev.stopPropagation();
            ev.preventDefault();
        }, true);

        bootbox.dialog({
            title: '<i class="fa fa-spin fa-spinner"></i>  Trabajando',
            message: 'Espere por favor',
            closeButton: false
        });
        url = '<?= site_url('clientpoints/update/' . $client_id . '/true') ?>' + (url !== undefined ? '/' + url : '');
        app.ajax(url, [], function (request) {
            window.location.href = '<?= site_url('clientpoints/info/' . $client_id) ?>';
        });
    };

    $(document).ready(function () {
        $('#update').click(function (e) {
            app.update(e);
        });
        $('#update_veraz').click(function (e) {
            app.update(e, '1');
        });
        $('#update_siisa').click(function (e) {
            app.update(e, '2');
        });

        $('#veraz_info').click(function (e) {
            e.stopPropagation();
            e.preventDefault();
            window.open('<?= site_url('clientpoints/xmlVeraz/' . $client_id . '') ?>', '_blank');
        });

        $('#siisa_info').click(function (e) {
            e.stopPropagation();
            e.preventDefault();
            window.open('<?= site_url('clientpoints/xmlSiisa/' . $client_id . '') ?>', '_blank');
        });

        app.ajax('<?= site_url('clientpoints/status/' . $client_id) ?>', [], function (request) {
            if (request != '') {
                if (request.update) {
                    $('#update').click();
                }
            }
        });
    });
</script>
