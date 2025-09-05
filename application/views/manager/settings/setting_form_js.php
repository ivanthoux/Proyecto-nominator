<script type="text/javascript">
    $(document).ready(function() {
        $(".textarea").wysihtml5();

        var fileinputConfig = app.fileInputDefaultConfig("logo");
        fileinputConfig = $.extend(fileinputConfig, {
            <?php if (isset($edit) && !empty($edit['setting_data']) && !empty($edit['setting_data']['logo'])) : ?>
                initialPreviewConfig: [{
                    caption: '<?= $edit['setting_data']['logo'] ?>',
                    url: app.baseUrl + "manager/image_delete/logo"
                }],
                initialPreview: [
                    "<img src='<?= site_url('resources/' . $edit['setting_data']['logo']) ?>' class='file-preview-image'>"
                ]
            <?php endif; ?>
        });
        $("#settings_logo").fileinput(fileinputConfig).on('fileuploaded', function(event, data, previewId, index) {
            $('#settings_logo').val(data.response.name);
        }).on('filedeleted', function(event, key) {
            window.location.href = window.location.href;
        });
    });

    app.showCreditSMS = () => {
        $("#credits-sms input").val('');
        $("#credits-sms").modal('show');
    }

    app.addCreditSMS = () => {
        if (isNaN(Number($('#credit').val()))) {
            app.dialog.error('Valor de créditos invalido');
        } else {
            $("#credits-sms").modal('hide');

            $('#credits_sms').val(Number($('#credits_sms').val()) + Number($('#credit').val()));
            $('#setting_data_credits_sms').val(Number($('#setting_data_credits_sms').val()) + Number($('#credit').val()));
        }
    }
</script>