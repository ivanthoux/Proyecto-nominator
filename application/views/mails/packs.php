<html>
    <body>
        <?php $state = $clientpack['clientpack_state'] == '1' ? "Pendiente" : ($clientpack['clientpack_state'] == '2' ? "Autorizado" : "Rechazado"); ?>
        <h1><?= "Producto " . $state ?></h1> 
        <p><?= lang('Enviado') ?> : <?= date('d-m-Y H:i', strtotime($clientpack['clientpack_created_at'])) ?></p>
        <?php if (!empty($office)): ?>
            <p><?= "Vendedor: " . $user['user_firstname'] . ", " . $user['user_lastname'] . " - (" . $office['office_name'] . ")" ?></p>
        <?php endif; ?>
        <p><?= $client['client_lastname'] . ', ' . $client['client_firstname'] ?>, posee <b><?= $state ?></b> un cr&eacute;dito <b><?= $pack['pack_name'] ?></b> por un valor de <b><?= money_formating($clientpack['clientpack_price']) ?></b></p>
    </body>
</html>