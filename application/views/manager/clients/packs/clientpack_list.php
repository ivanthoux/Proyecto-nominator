<section class="content-header">
  <h1>
    <i class="fa fa-th-large"></i> <?= !empty($client) ? 'Facturas al Cliente - ' . $client['client_firstname'] . ' ' . $client['client_lastname'] : 'Todos los Facturas' ?>
  </h1>
  <ol class="breadcrumb">
    <li class="active"><a href="<?= site_url('clients') ?>"><i class="fa fa-home"></i> Clientes</a></li>
  </ol>
</section>

<section class="content">
  <?php if (!isset($client)) : ?>
    <div class="row">
      <div class="col-lg-4 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-info">
          <div class="inner">
            <h3 id="capital"><?= money_formating(0) . " (0)" ?></h3>
            <p>Capital, <span class="small">sujeto a filtros</span></p>
          </div>
          <div class="icon">
            <i class="fa fa-money" aria-hidden="true"></i>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <?= (!empty($client)) ? $this->load->view('manager/clients/client_menu', array('client' => $client, 'parent' => !empty($parent) ? $parent : false, 'active' => 'client_pack'), true) : ''; ?>
  <?php $active = isset($client) ? $client['client_active'] : 1; ?>
  <?php if (!empty($errors)) : ?>
    <div class="box">
      <div class="box-header with-border">
        <div class="clearfix">
          <div class="alert alert-danger">
            <?php foreach ($errors as $error) : ?>
              <?= $error ?>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>
  <div class="box">
    <div class="box-header with-border">
      <h3 class="box-title">Listado </h3>
      <div class="box-tools pull-right">
        <?php if (!empty($client)) : ?>
          <a class="btn btn-primary <?= $active ? '' : 'hidden'; ?>" href="<?= site_url('clientpacks/form/' . $client_id) ?>"> <span class="fa fa-plus"></span> Nuevo</a>
        <?php else : ?>
          <?php if (in_array($this->session->userdata('user_rol'), ['super', 'administrador'])) : ?>
            <a title="Importar" class="btn btn-primary <?= $active ? '' : 'hidden'; ?>" href="#" onclick="app.openImport();"> <i class="fa fa-plus"></i> <span class="hidden-xs">Importar</span></a>
            <a title="Exportar" class="btn btn-warning <?= $active ? '' : 'hidden'; ?>" href="#" onclick="app.openExport();"> <i class="fa fa-floppy-o"></i> <span class="hidden-xs">Exportar</span></a>
          <?php endif; ?>
        <?php endif; ?>
      </div><!-- /.box-tools -->
    </div><!-- /.box-header -->
    <div class="box-body">
      <div class="row">
        <?php if (empty($client)) : ?>
          <div class="col-sm-4">
            <div class="input-group">
              <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
              <input type="text" id="datefilter" placeholder="Filtrar por fechas" name="datefilter" autocomplete="off" class="form-control rangepicker" onchange="apply_filter();" />
              <span class="input-group-addon" onclick="$('#datefilter').val('');apply_filter();"><span class="fa fa-remove"></span></span>
            </div>
          </div>
        <?php endif; ?>

        <?php $filters_label = array(
          'clientpack_package' => 'Condiciones de Ventas',
          'clientpack_office' => 'Empresa',
          'clientpack_officelocation' => 'Sucursal',
          'clientpack_created_by' => 'Vendedor',
          'clientpack_state' => 'Estado',
          'clientpack_paystate' => 'Estado Cuotas',
        ); ?>
        <!-- <pre><?= print_r($filters, true); ?></pre> -->

        <?php foreach ($filters as $field => $values) : ?>
          <div class="col-sm-2 form-group">
            <select class="form-control filter_field" id="filter_<?= $field ?>" name="<?= $field ?>" onchange="<?= $field === 'clientpack_office' ? "app.chengeOffice()" : "apply_filter();" ?>">
              <option value=""><?= $filters_label[$field] ?></option>
              <?php foreach ($values as $val) { ?>
                <option value="<?= $val['value'] ?>"><?= $val['title'] ?></option>
              <?php } ?>
            </select>
          </div>
        <?php endforeach; ?>
      </div>
      <hr />
      <table id="client_list" class="table table-bordered responsive nowrap" width="100%">
        <thead>
          <tr class="top">
            <th>N° Comprobate</th>
            <th>Creado</th>
            <th>Inicio</th>
            <th>Cuotas</th>
            <th>Restantes</th>
            <th>Valor</th>
            <th>Final</th>
            <th>Vendedor</th>
            <th>ID Cliente</th>
            <th>Razón Social</th>
            <th><?= lang('Actions') ?></th>
          </tr>
        </thead>
        <tfoot>
          <tr class="top">
            <th>N° Comprobate</th>
            <th>Creado</th>
            <th>Inicio</th>
            <th>Cuotas</th>
            <th>Restantes</th>
            <th>Valor</th>
            <th>Final</th>
            <th>Vendedor</th>
            <th>ID Cliente</th>
            <th>Razón Social</th>
            <th><?= lang('Actions') ?></th>
          </tr>
        </tfoot>
      </table>
    </div><!-- /.box-body -->
  </div><!-- /.box -->
</section>

<!-- <div class="modal modal-primary fade" id="import">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
        <h4 class="modal-title">Importar Facturas</h4>
      </div>
      <form id="formImport" method="POST" enctype="multipart/form-data" autocomplete="off">
        <div class="modal-body">
          <input type="hidden" name="action" value="import" />
          <div class=" row">
            <div class="col-sm-4">
              <div class="form-group">
                <label>Archivo</label>
                <input type="file" name="file" id="file">
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">Cancelar</button>
          <button class="btn btn-outline" type="button" onclick="app.saveImport();">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div> -->