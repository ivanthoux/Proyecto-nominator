<section class="content-header">
  <h1 class="row">
    <div class="col-xs-6 col-sm-9 col-md-9">
      <span class="pull-left"><i class="fa fa-hand-o-right"></i> A cobrar</span>
    </div>
    <div class="col-xs-6 col-sm-3 col-md-3">
      <div class="input-group">
        <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
        <input type="text" id="datefilter" name="datefilter" autocomplete="off" class="form-control rangepicker" value="<?= date('d-m-Y', strtotime($end)) ?>" />
      </div>
    </div>
  </h1>
</section>

<section class="content">
  <div class="row">
    <div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-success">
        <div class="inner">
          <h3 id="periods_not_paid"><?= money_formating($balances['periods_not_paid']) ?></h3>
          <p>Todavía Sin Cobrar</p>
        </div>
        <div class="icon">
          <i class="fa fa-money"></i>
        </div>
      </div>
    </div><!-- ./col -->
    <div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-success">
        <div class="inner">
          <h3 id="periods_paid"><?= money_formating($balances['periods_paid']) ?></h3>
          <p>Ya cobrado</p>
        </div>
        <div class="icon">
          <i class="fa fa-money"></i>
        </div>
      </div>
    </div><!-- ./col -->
    <div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-info">
        <div class="inner">
          <h3 id="periods_total"><?= money_formating($balances['periods_not_paid'] + $balances['periods_paid']) ?></h3>
          <p>Valor Total</p>
        </div>
        <div class="icon">
          <i class="fa fa-folder-open-o"></i>
        </div>
      </div>
    </div><!-- ./col -->

  </div>

  <div class="box">
    <div class="box-header with-border">
      <h3 class="box-title">Cuotas al <?= date('d-m-Y', strtotime($end)) ?></h3>
      <div class="box-tools pull-right">
        <button class="btn btn-primary <?= (empty($road) || $road === 'false') && (empty($client) || $client === 'false')  ? 'disabled' : '' ?>" type="button" <?= (empty($road) || $road === 'false') && (empty($client) || $client === 'false')  ? '' : 'onclick="app.createPayments();"' ?>><i class="fa fa-money"></i> <span class="hidden-xs">Cobrar</span></button>
        <button class="btn btn-warning <?= (empty($road) || $road === 'false') && (empty($client) || $client === 'false') ? 'disabled' : '' ?>" type="button" <?= (empty($road) || $road === 'false') && (empty($client) || $client === 'false')  ? '' : 'onclick="app.createCupon();"' ?>><i class="fa fa-file"></i> <span class="hidden-xs">Cuponera</span></button>
      </div><!-- /.box-tools -->
    </div><!-- /.box-header -->
    <div class="box-body">
      <div class="row">
        <div class="col-sm-3">
          <div class="input-group">
            <span class="input-group-addon"><span class="fa fa-file-text"></span></span>
            <input class="form-control header-live-search" id="search_roadmap" type="text" value="<?= isset($road) && $road !== 'false' ? $road : '' ?>" placeholder="Hoja de Ruta" onchange="app.changeRoad()">
            <span class="input-group-addon" onclick="$('#search_roadmap').val('');apply_date_filter();"><span class="fa fa-remove"></span></span>
          </div>
        </div>
        <div class="col-sm-9">
          <div class="input-group">
            <span class="input-group-addon"><span class="fa fa-user"></span></span>
            <input class="form-control header-live-search" id="search_client" type="text" value="<?= isset($client) && $client ? $client : '' ?>" placeholder="Cliente" onchange="app.changeClient()">
            <span class="input-group-addon" onclick="$('#search_client').val('');apply_date_filter();"><span class="fa fa-remove"></span></span>
          </div>
        </div>
      </div>
      <hr>
      <table id="period_list" class="table table-bordered responsive nowrap" width="100%">
        <thead>
          <tr class="top">
            <th>Hora</th>
            <th>N° Cliente</th>
            <th>Cliente</th>
            <th>Domicilio</th>
            <th>Valor</th>
            <th>Cuota</th>
            <th>Producto</th>
            <th><?= lang('Actions') ?></th>
          </tr>
        </thead>
        <tfoot>
          <tr class="top">
            <th>Hora</th>
            <th>N° Cliente</th>
            <th>Cliente</th>
            <th>Domicilio</th>
            <th>Valor</th>
            <th>Cuota</th>
            <th>Producto</th>
            <th><?= lang('Actions') ?></th>
          </tr>
        </tfoot>
      </table>
    </div><!-- /.box-body -->
  </div><!-- /.box -->
</section>