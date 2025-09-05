<div class="row">
    <?php if (can("view_full_person")) : ?>
        <div class="col-lg-4 col-xs-6">
            <!-- small box -->
            <div class="small-box <?= (!empty($person['person_balance']) && $person['person_balance'] < 0) ? 'bg-success' : 'bg-danger' ?>">
                <div class="inner">
                    <h3><?= money_formating((!empty($person['person_balance']) && $person['person_balance'] < 0)) ?></h3>
                    <p>Saldo que le debemos</p>
                </div>
                <div class="icon">
                    <i class="fa <?= (!empty($person['person_balance']) && $person['person_balance'] < 0) ? 'fa-check' : 'fa-bullhorn' ?>"></i>
                </div>
            </div>
        </div><!-- ./col -->
        <?php //if ($person['personbalance']['periods_not_paid'] = 0) : ?>
            <div class="col-lg-4 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3> 0 </h3>
                        <p>Accidentes esta semana</p>
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
    <li class="<?= !empty($active) && $active == 'person_form' ? 'active' : '' ?>"><a href="<?= site_url('persons/form/' . $person['person_id']) ?>">Perfil</a></li>
    <li class="<?= !empty($active) && $active == 'person_file' ? 'active' : '' ?>"><a href="<?= site_url('personfiles/all/' . $person['person_id']) ?>">Documentos</a></li>
    <li class="<?= !empty($active) && $active == 'person_employeeevaluation' ? 'active' : '' ?>"><a href="<?= site_url('employeeevaluations/form/' . $person['person_id']) ?>">Evaluaciones de desempeño</a></li>
    <li class="<?= !empty($active) && $active == 'person_contract' ? 'active' : '' ?>"><a href="<?= site_url('contracts/form/' . $person['person_id']) ?>">Nuevo contrato</a></li>
    <li class="<?= !empty($active) && $active == 'person_paycheck' ? 'active' : '' ?>"><a href="<?= site_url('paychecks/form/' . $person['person_id']) ?>">Recibo de sueldo</a></li>
    <?php if (can("view_full_person")) : ?>
    <?php endif; ?>
</ul>