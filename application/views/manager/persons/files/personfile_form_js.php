<script type="text/javascript">
    app.loading = function() {
        bootbox.dialog({
            title: '<i class="fa fa-spin fa-spinner"></i>  Enviando información',
            message: 'Espere por favor',
            closeButton: false
        });
    }
    $(document).ready(function() {
        <?php
        if (isset($edit) && !empty($edit['personfile_file']) && !empty($edit['personfile_file'])) {
            $preview = (strpos($edit['personfile_file'], '.pdf') !== false) ?
                "<iframe src='" . site_url('resources/persons/' . $edit['personfile_file']) . "' class='file-preview'></iframe>" :
                "<img src='" . site_url('resources/persons/' . $edit['personfile_file']) . "' class='file-preview'>";
        }
        ?>

        var fileinputConfig = {
            maxFileSize: 13000,
            showCaption: false,
            language: 'es',
            maxFileCount: 1,
            autoReplace: true,
            showRemove: false,
            showUpload: false,
            required: true,
            allowedFileTypes: ['image', 'object']
        };
        <?php if (isset($edit) && !empty($edit['personfile_file']) && !empty($edit['personfile_file'])) : ?>
            fileinputConfig.initialPreview = [
                "<?= $preview ?>"
            ];
        <?php endif; ?>
        $("#personfile_file").fileinput(fileinputConfig).on('fileuploaded', function(event, data, previewId, index) {
            $('#personfile_file').val(data.response.name);
        });
    });
</script>