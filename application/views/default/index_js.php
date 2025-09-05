<script>
    $(document).ready(() => {
        let pay = 0.0;
        app.title = (id) => {
            $('#a_pagar_' + id).html($('#values_' + id).val());
            pay += parseFloat($('#values_' + id).data('value'));
            $('#pay_mp').val(Math.round((pay) * 100) / 100);
        };

        <?php if (isset($client)) : ?>
            <?php foreach ($packs as $pack) : ?>
                app.title(<?= $pack['clientpack']['clientpack_id'] ?>);
            <?php endforeach; ?>
        <?php endif; ?>
    });
</script>