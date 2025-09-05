<div class="row">
    <?php if (can("view_full_client")) : ?>
        <div class="col-lg-4 col-xs-6">
            <!-- small box -->
            <div class="small-box <?= (!empty($client['client_balance']) && $client['client_balance'] < 0) ? 'bg-success' : 'bg-danger' ?>">
                <div class="inner">
                    <h3><?= money_formating($client['client_balance']) ?></h3>
                    <p>Saldo</p>
                </div>
                <div class="icon">
                    <i class="fa <?= (!empty($client['client_balance']) && $client['client_balance'] < 0) ? 'fa-check' : 'fa-bullhorn' ?>"></i>
                </div>
            </div>
        </div><!-- ./col -->
        <?php //if ($client['clientbalance']['periods_not_paid'] = 0) : ?>
            <div class="col-lg-4 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3><?= ($client['clientbalance']['periods_not_paid']) ?></h3>
                        <p>Cuotas restantes</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-info"></i>
                    </div>
                </div>
                </a>
            </div><!-- ./col -->
        <?php //endif; ?>
    <?php endif; ?>
</div>
<ul class="nav nav-tabs" role="tablist">
    <li class="<?= !empty($active) && $active == 'client_form' ? 'active' : '' ?>"><a href="<?= site_url('clients/form/' . $client['client_id']) ?>">Perfil</a></li>
    <li class="<?= !empty($active) && $active == 'client_file' ? 'active' : '' ?>"><a href="<?= site_url('clientfiles/all/' . $client['client_id']) ?>">Documentos</a></li>
    <li class="<?= !empty($active) && $active == 'client_pack' ? 'active' : '' ?>"><a href="<?= site_url('clientpacks/all/' . $client['client_id']) ?>">Facturas</a></li>
    <li class="<?= !empty($active) && $active == 'client_period' ? 'active' : '' ?>"><a href="<?= site_url('clientperiods/all/' . $client['client_id']) ?>">Cuotas</a></li>
    <li class="<?= !empty($active) && $active == 'client_payment' ? 'active' : '' ?>"><a href="<?= site_url('clientpayments/all/' . $client['client_id']) ?>">Pagos</a></li>
    <?php if (can("view_full_client")) : ?>
    <?php endif; ?>
</ul>