<section class="content-header">
  <h1 class="row">
    <div class="col-xs-6 col-sm-9 col-md-9">
      <span class="pull-left"><i class="fa fa-hand-o-right"></i> Importar Bancos</span>
    </div>
  </h1>
</section>

<section class="content">
  <form id="form_banks" action="" class="form" method="post" enctype="multipart/form-data" autocomplete="off">
    <div class="box">
      <div class="box-header with-border">
        <div class="col-sm-3 col-xs-9">
          <!-- <label for="banks">Banco</label> -->
          <select class="form-control" name="bank" id="bank">
            <option value="">Seleccione uno ...</option>
            <option value="1" <?= isset($edit) && !empty($edit) && $edit['bank'] == 1 ? 'selected' : '' ?>>ICBC</option>
            <option value="2" <?= isset($edit) && !empty($edit) && $edit['bank'] == 2 ? 'selected' : '' ?>>Santander</option>
          </select>
        </div>
        <div class="col-sm-3 col-xs-9">
          <div class="input-group">
            <input class="form-control" id="file" name="file" type="file">
          </div>
        </div>
        <div class="box-tools pull-right">
          <?php if (isset($edit) && !empty($edit) && empty($errors)) : ?>
            <button class="btn btn-warning"><i class="fa fa-money"></i> <span class="hidden-xs">Guardar</span></button>
            <button class="btn btn-secondary" type="button" onclick="window.location = window.location;"><i class="fa fa-remoce"></i> <span class="hidden-xs">Cancelar</span></button>
          <?php else : ?>
            <button class="btn btn-primary"><i class="fa fa-upload"></i> <span class="hidden-xs">Cargar</span></button>
          <?php endif; ?>
        </div>
        <!-- /.box-tools -->
      </div><!-- /.box-header -->
      <div class="box-body">
        <?php if (!empty($errors)) : ?>
          <!-- <div class="row"> -->
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
          <!-- </div> -->
          <hr>
        <?php endif; ?>
        <table id="period_list" class="table table-bordered responsive nowrap" width="100%">
          <thead>
            <tr class="top">
              <th>Fecha</th>
              <th>N° Operación</th>
              <th>Concepto</th>
              <th>Monto</th>
              <th>Cliente</th>
            </tr>
          </thead>
          <?php if (isset($data)) : ?>
            <tbody>
              <?php foreach ($data as $key => $row) : ?>
                <tr role="row" class="odd">
                  <td>
                    <?= $row[0] ?>
                  </td>
                  <td>
                    <?= $row[1] ?>
                  </td>
                  <td>
                    <?= $row[2] ?>
                  </td>
                  <td>
                    <?= money_formating($row[3]) ?>
                    <input type="hidden" id="row_<?= $key ?>_monto" name="row[<?= $key ?>][monto]" value="<?= $row[3] ?>">
                  </td>
                  <td>
                    <div class="input-group">
                      <span class="input-group-addon"><span class="fa fa-user"></span></span>
                      <input class="form-control header-live-search" id="row_<?= $key ?>_client" type="text" placeholder="Cliente">
                      <input type="hidden" id="row_<?= $key ?>_client_h" name="row[<?= $key ?>][client]">
                      <span class="input-group-addon" onclick="$('#row_<?= $key ?>_client').val('');$('#row_<?= $key ?>_client_h').val('');"><span class="fa fa-remove"></span></span>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          <?php endif; ?>
          <tfoot>
            <tr class="top">
              <th>Fecha</th>
              <th>N° Operación</th>
              <th>Concepto</th>
              <th>Monto</th>
              <th>Cliente</th>
            </tr>
          </tfoot>
        </table>
      </div><!-- /.box-body -->
    </div><!-- /.box -->
  </form>
</section>