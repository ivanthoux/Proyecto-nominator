<section class="content-header">
  <h1>
    <i class="fa fa-archive"></i> Listado de Cierres
  </h1>
</section>

<section class="content">
  <div class="box">
    <div class="box-header with-border">
      <h3 class="box-title">Filtrar Listado </h3>
      <div class="box-tools pull-right">
      </div><!-- /.box-tools -->

    </div><!-- /.box-header -->
    <div class="box-body">
      <div class="row">
        <div class="col-sm-3">
          <div class="input-group">
            <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
            <input type="text" id="datefilter" name="datefilter" autocomplete="off" class="form-control rangepicker" onchange="apply_filter();" />
          </div>
        </div>

      </div>
      <hr />
      <table id="closings_list" class="table table-bordered responsive nowrap" width="100%">
        <thead>
          <tr class="top">
            <th>Fecha</th>
            <th>Cajero</th>
            <th>Balance</th>
            <th>Efectivo</th>
            <!-- <th>Tarjeta</th> -->
            <th>Cheque</th>
            <th>Transf</th>
            <th>Cta Cte</th>
            <?php if ($this->session->userdata('user_rol_label') == 'Super') : ?>
              <th>Acciones</th>
            <?php else : ?>
              <th>Estado</th>
            <?php endif; ?>
          </tr>
        </thead>
        <tfoot>
          <tr class="top">
            <th>Fecha</th>
            <th>Cajero</th>
            <th>Balance</th>
            <th>Efectivo</th>
            <!-- <th>Tarjeta</th> -->
            <th>Cheque</th>
            <th>Transf</th>
            <th>Cta Cte</th>
            <?php if ($this->session->userdata('user_rol_label') == 'Super') : ?>
              <th>Acciones</th>
            <?php else : ?>
              <th>Estado</th>
            <?php endif; ?>
          </tr>
        </tfoot>
      </table>
    </div><!-- /.box-body -->
  </div><!-- /.box -->
</section>

<div id="receipt" class="hide"></div>