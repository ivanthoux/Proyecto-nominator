<section class="content-header">
  <h1><i class="fa fa-tags"></i> Pago <?= !empty($client) ? ' de ' . $client['client_firstname'] . ' ' . $client['client_lastname'] : (!empty($office) ? 'de ' . $office['office_name'] . ' periodo ' . date('d-m-Y', strtotime($closing['officeclosing_start'])) . ' - ' . date('d-m-Y', strtotime($closing['officeclosing_end'])) : '') ?></h1>
  <ol class="breadcrumb">
    <li class=""><a href="<?= empty($client) && empty($office) ? site_url('payments/all') : !empty($client) ? site_url('clientperiods/all') : site_url('officeclosings/all') ?>"><i class="fa fa-home"></i> <?= empty($client) && empty($office) ? 'Pagos' : !empty($client) ? 'Clientes' : 'Cierres' ?></a></li>
  </ol>
</section>
<section class="content package_form">
  <?php if (!empty($client)) : ?>
    <?= $this->load->view('manager/clients/client_menu', array('client' => $client, 'parent' => !empty($parent) ? $parent : false, 'active' => 'client_payment'), TRUE); ?>
  <?php endif; ?>
  <div class="box">
    <div class="box-header with-border">
      <h3 class="box-title"><?= isset($edit) ? 'Editar Pago' : 'Crear Pago' ?> <?= !empty($client) ? ' de ' . $client['client_firstname'] . ' ' . $client['client_lastname'] : '' ?></h3>
    </div><!-- /.box-header -->
    <div class="box-body">
      <form class="form" action="" method="post" autocomplete="off">
        <input name="pay_id" id="pay_id" type="hidden" value="<?= (isset($edit) && !empty($edit['pay_id'])) ? $edit['pay_id'] : '' ?>" />

        <?php if (empty($client) && empty($office)) : ?>
          <div class="row">
            <div class="col-sm-6">
              <div class="alert alert-warning">
                Si esta por registrar un pago de un Cliente, dirigirse primero a la seccion de <b><a href="<?= site_url('clients') ?>">CLIENTES</a></b> para buscarlo y asignarle el pago correspondiente.
              </div>
            </div>
          </div>
        <?php else : ?>
          <?php if (!empty($periods)) : ?>
            <div class="row">
              <div class="col-sm-6">
                <ul class="list-group">
                  <?php foreach ($periods as $per) : ?>
                    <li class="list-group-item active d-flex justify-content-between align-items-center">
                      <div class="custom-control custom-checkbox">
                        <label class="custom-control-label" for="clientperiod[<?= $per['clientperiod_id'] ?>]">
                          <?php if (empty($edit)) : ?>
                            <input type="checkbox" onclick="app.refreshPayment()" class="custom-control-input payment-period" data-amount="<?= $per['clientperiod_amount'] ?>" data-date="<?= $per['clientperiod_date'] ?>" data-daytask="<?= $per['pack_daytask'] ?>" data-iva="<?= $per['pack_iva'] ?>" value="<?= $per['clientperiod_amount'] ?>" name="clientperiod[<?= $per['clientperiod_id'] ?>]" id="_<?= $per['clientperiod_id'] ?>">
                            <input type="hidden" value="0" name="clientperiod[<?= $per['clientperiod_id'] ?>][task]" id="_<?= $per['clientperiod_id'] ?>_task">
                            <input type="hidden" value="<?= $per['clientperiod_amount'] ?>" name="clientperiod[<?= $per['clientperiod_id'] ?>][period_amount]">
                          <?php endif; ?>
                          <?= $per['clientpack_title'] . (!empty($per['clientpack_type']) && $per['clientpack_type'] == "Mensual Interés" ? ' - Capital ' . money_formating($per['clientpack_onlyinterest_balance']) : ' - Cuota ' . $per['clientperiod_packperiod']) . ' - Fecha: ' . date('d-m-Y', strtotime($per['clientperiod_date'])) ?>
                        </label>
                        <span class="pull-right"><?= money_formating($per['clientperiod_amount']) ?></span>
                      </div>
                    </li>
                  <?php endforeach; ?>
                </ul>
              </div>
            </div>
          <?php endif; ?>
        <?php endif; ?>

        <?php if (!empty($edit) && !empty($edit['pay_presented'])) : ?>
          <div class="row">
            <div class="col-sm-6">
              <div class="alert alert-warning">
                Este pago ya fue contabilizado en un cierre de caja del dia <?= date('d-m-Y', strtotime($edit['pay_presented_at'])) ?>
                <br /> Las modificaciones en el <b>valor</b> no se verán reflejadas en el cierre de ese dia ni en el de hoy.
              </div>
            </div>
          </div>
        <?php endif; ?>

        <div class="row">
          <div class="col-sm-3">
            <label>Fecha Pago</label>
            <div class="input-group">
              <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
              <input class="form-control" name="pay_date" id="pay_date" type="text" value="<?= (isset($edit) && !empty($edit['pay_date'])) ? date('d-m-Y', strtotime($edit['pay_date'])) : date('d-m-Y') ?>" />
            </div>
          </div>
        </div>

        <div class="row">

          <?php if (!empty($clientperiod)) : ?>
            <input class="form-control" name="pay_clientperiod" type="hidden" value="<?= (!empty($clientperiod)) ? $clientperiod : '' ?>" />
          <?php endif; ?>

          <div class="col-sm-3">
            <div class="form-group">
              <label>Medio de pago</label>
              <select name="pay_type" class="form-control">
                <?php if (in_array($this->session->userdata('user_rol'), ['super', 'administrador'])) : ?>
                  <option <?= (isset($edit) && !empty($edit['pay_type']) && $edit['pay_type'] == 'Efectivo') ? 'selected' : '' ?>>Efectivo</option>
                  <option <?= (isset($edit) && !empty($edit['pay_type']) && $edit['pay_type'] == 'Transferencia') ? 'selected' : '' ?>>Transferencia</option>
                  <option <?= (isset($edit) && !empty($edit['pay_type']) && $edit['pay_type'] == 'BANCO CORRIENTES - PAGOS.LINEA.nominator') ? 'selected' : '' ?>>BANCO CORRIENTES - PAGOS.LINEA.nominator</option>
                  <option <?= (isset($edit) && !empty($edit['pay_type']) && $edit['pay_type'] == 'BANCO NACION - PAGOS.LINEA.NACION') ? 'selected' : '' ?>>BANCO NACION - PAGOS.LINEA.NACION</option>
                  <option <?= (isset($edit) && !empty($edit['pay_type']) && $edit['pay_type'] == 'MERCADO PAGO - PAGOS.LINEA.MP') ? 'selected' : '' ?>>MERCADO PAGO - PAGOS.LINEA.MP</option>
                  <option <?= (isset($edit) && !empty($edit['pay_type']) && $edit['pay_type'] == 'BANCO MACRO - PAGOS.nominator.LINEA') ? 'selected' : '' ?>>BANCO MACRO - PAGOS.nominator.LINEA</option>
                  <option <?= (isset($edit) && !empty($edit['pay_type']) && $edit['pay_type'] == 'Cheque') ? 'selected' : '' ?>>Cheque</option>
                <?php endif; ?>
                <option <?= (isset($edit) && !empty($edit['pay_type']) && $edit['pay_type'] == 'Sobrante por Rendiciones') ? 'selected' : '' ?>>Sobrante por Rendiciones</option>
                <option <?= (isset($edit) && !empty($edit['pay_type']) && $edit['pay_type'] == 'Otro') ? 'selected' : '' ?>>Otro</option>
              </select>
            </div>
          </div>

          <div class="col-sm-3">
            <div class="form-group">
              <label>Monto</label>
              <input required="" class="form-control" name="pay_amount" id="pay_amount" type="text" value="<?= isset($edit) && !empty($edit['pay_amount']) ? $edit['pay_amount'] : (empty($office) ? '' : $closing['officeclosing_topay']) ?>" />
              <input class="form-control" name="pay_daytask" id="pay_daytask" type="hidden" value="<?= isset($edit) && !empty($edit['pay_daytask']) ? $edit['pay_daytask'] : '' ?>" />
            </div>

          </div>
        </div>

        <div class="row">
          <div class="col-sm-6">
            <div class="form-group">
              <label>Descripción</label>
              <textarea class="form-control" name="pay_description"><?= (isset($edit) && !empty($edit['pay_description'])) ? $edit['pay_description'] : '' ?></textarea>
            </div>
          </div>
        </div>

        <?php if (!empty($errors)) : ?>
          <div class="clearfix">
            <div class="alert alert-danger">
              <?php if (gettype($errors) == 'array') : ?>
                <?php foreach ($errors as $error) : ?>
                  <?= $error ?>
                <?php endforeach; ?>
              <?php else : ?>
                <?= $errors ?>
              <?php endif; ?>
            </div>
          </div>
        <?php endif; ?>

        <div class="box-footer">
          <div class="row">
            <div class="col-sm-12">
              <div class="col-sm-6">
                <button class="btn btn-primary"><?= isset($edit) ? lang('Save') : lang('Add') ?></button>
                <a class="btn" href="<?= empty($client) && empty($office) ? site_url('payments/all') : (!empty($client) ? site_url('clientperiods/all/') . $client['client_id'] : site_url('officeclosings/all')) ?>"><?= lang('Cancel') ?></a>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</section>