<section class="content-header">
  <h1><i class="fa fa-tags"></i> Gasto Recibido <?= !empty($client) ? ' de ' . $client['client_firstname'] . ' ' . $client['client_lastname'] : '' ?></h1>
  <ol class="breadcrumb">
    <li class=""><a href="<?= site_url('expenses/all') ?>"> Gastos</a></li>
  </ol>
</section>
<section class="content package_form">
  <div class="box">
    <div class="box-header with-border">
      <h3 class="box-title"><?= isset($edit) ? 'Editar Gasto' : 'Crear Gasto' ?> <?= !empty($client) ? ' de ' . $client['client_firstname'] . ' ' . $client['client_lastname'] : '' ?></h3>
    </div><!-- /.box-header -->
    <div class="box-body">
      <form class="form" action="" method="post" autocomplete="off">
        <input name="exp_id" type="hidden" value="<?= (isset($edit) && !empty($edit['exp_id'])) ? $edit['exp_id'] : '' ?>" />
        <div class="row">

          <?php if (!empty($client)) : ?>
            <input class="form-control" name="exp_client" type="hidden" value="<?= (!empty($client)) ? $client['client_id'] : '' ?>" />
          <?php endif; ?>

          <div class="col-sm-2">
            <div class="form-group">
              <label>Categoría de Pago</label>
              <select name="exp_category" class="form-control">
                <?php if (in_array($this->session->userdata('user_rol'), ['super', 'administrador'])) : ?>
                  <option <?= (isset($edit) && !empty($edit['exp_type']) && $edit['exp_category'] == 'Combustibles') ? 'selected' : '' ?>>Combustibles</option>
                  <option <?= (isset($edit) && !empty($edit['exp_type']) && $edit['exp_category'] == 'Adelanto de sueldo') ? 'selected' : '' ?>>Adelanto de Sueldo</option>
                  <option <?= (isset($edit) && !empty($edit['exp_type']) && $edit['exp_category'] == 'Premios y Comisiones') ? 'selected' : '' ?>>Premios y Comisiones</option>
                  <option <?= (isset($edit) && !empty($edit['exp_type']) && $edit['exp_category'] == 'Mantenimiento de Instalaciones') ? 'selected' : '' ?>>Mantenimiento de Instalaciones</option>
                  <option <?= (isset($edit) && !empty($edit['exp_type']) && $edit['exp_category'] == 'MANTENIMIENTO DE EDIFICIOS ( SECTOR DEPOSITO, ESTRUCTURA)') ? 'selected' : '' ?>>MANTENIMIENTO DE EDIFICIOS ( SECTOR DEPOSITO, ESTRUCTURA)</option>
                  <option <?= (isset($edit) && !empty($edit['exp_type']) && $edit['exp_category'] == 'MENTENIMIENTO DE VEHICULOS (SECTOR REPARTO)') ? 'selected' : '' ?>>MENTENIMIENTO DE VEHICULOS (SECTOR REPARTO)</option>
                  <option <?= (isset($edit) && !empty($edit['exp_type']) && $edit['exp_category'] == 'VIATICOS Y REFRIGERIOS SECTOR PROMOCION') ? 'selected' : '' ?>>VIATICOS Y REFRIGERIOS SECTOR PROMOCION</option>
                  <option <?= (isset($edit) && !empty($edit['exp_type']) && $edit['exp_category'] == 'VIATICOS Y REFRIGERIOS SECTOR DEPOSITO') ? 'selected' : '' ?>>VIATICOS Y REFRIGERIOS SECTOR DEPOSITO</option>
                  <option <?= (isset($edit) && !empty($edit['exp_type']) && $edit['exp_category'] == 'VIATICOS Y REFRIGERIOS SECTOR ADMINISTRACION') ? 'selected' : '' ?>>VIATICOS Y REFRIGERIOS SECTOR ADMINISTRACION</option>
                  <option <?= (isset($edit) && !empty($edit['exp_type']) && $edit['exp_category'] == 'RETIRO SOCIO MARTIN MAYOL') ? 'selected' : '' ?>>RETIRO SOCIO MARTIN MAYOL</option>
                  <option <?= (isset($edit) && !empty($edit['exp_type']) && $edit['exp_category'] == 'RETIRO SOCIO ANDRES ORIA') ? 'selected' : '' ?>>RETIRO SOCIO ANDRES ORIA</option>
                  <option <?= (isset($edit) && !empty($edit['exp_type']) && $edit['exp_category'] == 'RETIROS NO IMPUTABLES A SOCIOS') ? 'selected' : '' ?>>RETIROS NO IMPUTABLES A SOCIOS</option>
                  <option <?= (isset($edit) && !empty($edit['exp_type']) && $edit['exp_category'] == 'MANTENIMIENTO DE MUEBLES Y UTILES (PROMOCION Y MERCHANDISING)') ? 'selected' : '' ?>>MANTENIMIENTO DE MUEBLES Y UTILES (PROMOCION Y MERCHANDISING)</option>
                  <option <?= (isset($edit) && !empty($edit['exp_type']) && $edit['exp_category'] == 'HONORARIOS VARIOS') ? 'selected' : '' ?>>HONORARIOS VARIOS</option>
                  <option <?= (isset($edit) && !empty($edit['exp_type']) && $edit['exp_category'] == 'FLETES DE TERCEROS') ? 'selected' : '' ?>>FLETES DE TERCEROS</option>
                  <option <?= (isset($edit) && !empty($edit['exp_type']) && $edit['exp_category'] == 'APORTES DE CAPITAL') ? 'selected' : '' ?>>APORTES DE CAPITAL</option>
                  <option <?= (isset($edit) && !empty($edit['exp_type']) && $edit['exp_category'] == 'OBRAS SOCIOS') ? 'selected' : '' ?>>OBRAS SOCIOS</option>
                  <option <?= (isset($edit) && !empty($edit['exp_type']) && $edit['exp_category'] == 'OBRAS TERCEROS') ? 'selected' : '' ?>>OBRAS TERCEROS</option>
                <?php endif; ?>
                <option <?= (isset($edit) && !empty($edit['exp_type']) && $edit['exp_category'] == 'VIATICOS Y REFRIGERIOS SECTOR VENTAS') ? 'selected' : '' ?>>VIATICOS Y REFRIGERIOS SECTOR VENTAS</option>
                <option <?= (isset($edit) && !empty($edit['exp_type']) && $edit['exp_category'] == 'VIATICOS Y REFRIGERIOS SECTOR REPARTO') ? 'selected' : '' ?>>VIATICOS Y REFRIGERIOS SECTOR REPARTO</option>
                <option <?= (isset($edit) && !empty($edit['exp_type']) && $edit['exp_category'] == 'FALTANTES POR DIFERENCIAS EN RENDICIONES') ? 'selected' : '' ?>>FALTANTES POR DIFERENCIAS EN RENDICIONES</option>
                <option <?= (isset($edit) && !empty($edit['exp_type']) && $edit['exp_category'] == 'Otro') ? 'selected' : '' ?>>Otro</option>
              </select>
            </div>
          </div>
          <!-- <div class="col-sm-2">
            <div class="form-group">
              <label>Nombre/Tipo</label>
              <input class="form-control" name="exp_name" type="text" value="<?= (isset($edit) && !empty($edit['exp_name'])) ? $edit['exp_name'] : '' ?>" />
            </div>
          </div> -->
          <div class="col-sm-2">
            <div class="form-group">
              <label>Medio de pago</label>
              <select name="exp_type" class="form-control">
                <option <?= (isset($edit) && !empty($edit['exp_type']) && $edit['exp_type'] == 'Efectivo') ? 'selected' : '' ?>>Efectivo</option>
                <option <?= (isset($edit) && !empty($edit['exp_type']) && $edit['exp_type'] == 'Tarjeta') ? 'selected' : '' ?>>Tarjeta</option>
                <option <?= (isset($edit) && !empty($edit['exp_type']) && $edit['exp_type'] == 'Transferencia') ? 'selected' : '' ?>>Transferencia</option>
                <option <?= (isset($edit) && !empty($edit['exp_type']) && $edit['exp_type'] == 'Cheque') ? 'selected' : '' ?>>Cheque</option>
                <option <?= (isset($edit) && !empty($edit['exp_type']) && $edit['exp_type'] == 'Otro') ? 'selected' : '' ?>>Otro</option>
              </select>
            </div>
          </div>

          <div class="col-sm-2">
            <div class="form-group">
              <label>Monto</label>
              <input class="form-control" name="exp_amount" type="text" value="<?= (isset($edit) && !empty($edit['exp_amount'])) ? $edit['exp_amount'] : '' ?>" />
            </div>
          </div>

          <div class="col-sm-2">
            <div class="form-group">
              <label>Fecha Gasto</label>
              <input class="form-control datepicker" name="exp_date" type="text" value="<?= (isset($edit) && !empty($edit['exp_date'])) ? date('d-m-Y', strtotime($edit['exp_date'])) : '' ?>" />
            </div>
          </div>

        </div>

        <div class="row">
          <div class="col-sm-6">
            <div class="form-group">
              <label>Descripción</label>
              <textarea class="form-control" name="exp_description"><?= (isset($edit) && !empty($edit['exp_description'])) ? $edit['exp_description'] : '' ?></textarea>
            </div>
          </div>
        </div>

        <? if (!empty($errors)) { ?>
          <div class="clearfix">
            <div class="alert alert-danger">
              <?= $errors ?>
            </div>
          </div>
        <? } ?>

        <div class="box-footer">
          <div class="row">
            <div class="col-sm-12">
              <div class="col-sm-6">
                <button class="btn btn-primary"><?= isset($edit) ? lang('Save') : lang('Add') ?></button>
                <a class="btn" href="<?= site_url('expenses/all') ?>"><?= lang('Cancel') ?></a>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</section>