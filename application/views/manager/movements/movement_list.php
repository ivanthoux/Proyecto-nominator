<section class="content-header">
  <h1>
    <i class="fa fa-exchange"></i> Movimientos de Caja
  </h1>
</section>

<section class="content">
  <div class="row">

    <div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-info">
        <div class="inner">
          <h3 id="income_outcome"></h3>
          <p>Balance en RANGO FECHA</p>
        </div>
        <div class="icon">
          <i class="fa fa-calendar"></i>
        </div>
      </div>
    </div><!-- ./col -->
    <div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-success">
        <div class="inner">
          <h3 id="income"></h3>
          <p>Ingresos en RANGO FECHA</p>
        </div>
        <div class="icon">
          <i class="fa fa-calendar"></i>
        </div>
      </div>
    </div><!-- ./col -->
    <div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-warning">
        <div class="inner">
          <h3 id="outcome"></h3>
          <p>Gastos en RANGO FECHA</p>
        </div>
        <div class="icon">
          <i class="fa fa-calendar"></i>
        </div>
      </div>
    </div><!-- ./col -->
    <div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-danger">
        <div class="inner">
          <h3 id="income_outcome_no"></h3>
          <p>Por cerrar GLOBAL</p>
        </div>
        <div class="icon">
          <i class="fa fa-globe"></i>
        </div>
      </div>
    </div><!-- ./col -->

  </div>

  <div class="box">
    <div class="box-header with-border">
      <h3 class="box-title">Listado </h3>
      <div class="box-tools pull-right">
        <?php if (can('view_closingmoves')) : ?>
          <a class="btn btn-primary" onclick="app.closeConfirm()">Cerrar Caja</a>
          <a class="btn bg-olive" onclick="app.exportMoves()"><i class="fa fa-floppy-o"></i> <span class="hidden-xs">Exportar</span></a>
        <?php endif; ?>
      </div><!-- /.box-tools -->

    </div><!-- /.box-header -->
    <div class="box-body">
      <div class="row">
        <div class="col-sm-5">
          <div class="input-group">
            <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
            <input type="text" id="datefilter" name="datefilter" autocomplete="off" class="form-control rangepicker" value="<?= date('d-m-Y', strtotime($start)) . ' / ' . date('d-m-Y', strtotime($end)) ?>" />
          </div>
        </div>
      </div>
      <hr />
      <div class="row">

        <?php $filters_label = array('created_by' => 'Operador'); ?>

        <?php foreach ($filters as $field => $values) : ?>
          <?php if (!empty($filters_label[$field])) : ?>
            <div class="col-sm-2">
              <select class="form-control filter_field" id="filter_<?= $field ?>" name="<?= $field ?>" onchange="apply();">
                <option value=""><?= $filters_label[$field] ?></option>
                <?php foreach ($values as $val) : ?>
                  <option value="<?= $val['value'] ?>"><?= $val['title'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>
      <hr />
      <table id="movements_list" class="table table-bordered responsive nowrap" width="100%">
        <thead>
          <tr class="top">
            <th>Cliente/Detalle</th>
            <th>Fecha</th>
            <th>Valor</th>
            <th>Tipo</th>
            <th>Recibió</th>
            <th>Descripción</th>
            <th><?= lang('Actions') ?></th>
          </tr>
        </thead>
        <tfoot>
          <tr class="top">
            <th>Cliente/Detalle</th>
            <th>Fecha</th>
            <th>Valor</th>
            <th>Tipo</th>
            <th>Recibió</th>
            <th>Descripción</th>
            <th><?= lang('Actions') ?></th>
          </tr>
        </tfoot>
      </table>
    </div><!-- /.box-body -->
  </div><!-- /.box -->
</section>

<div class="modal modal-primary fade" id="close-confirm">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
        <h4 class="modal-title">Confirmar Cierre de Caja</h4>
      </div>
      <form action="<?= site_url('movements/close/' . date('d-m-Y', strtotime($start)) . '/' . date('d-m-Y', strtotime($end))) ?>" method="post">
        <div class="modal-body">
          <div class="row">
            <div class="col-sm-6">
              <h4>Balance Total</h4>
            </div>
            <div class="col-sm-6">
              <h4 class="text-right"><?= money_formating($balances_not_closed['income'] - $balances_not_closed['outcome']) ?></h4>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-6">
              <h4>Efectivo Presente</h4>
            </div>
            <div class="col-sm-6">
              <h4 class="text-right"><?= money_formating($balances_not_closed['income_cash'] - $balances_not_closed['outcome_cash']) ?></h4>
            </div>
          </div>
          <!-- <div class="row">
            <div class="col-sm-5">
              <h4>Tarjeta cupones</h4>
            </div>
            <div class="col-sm-5">
              <h4 class="text-right"><?= money_formating($balances_not_closed['income_card']) ?></h4>
            </div>
          </div> -->
          <div class="row">
            <div class="col-sm-6">
              <h4>Cheques</h4>
            </div>
            <div class="col-sm-6">
              <h4 class="text-right"><?= money_formating($balances_not_closed['income_check']) ?></h4>
            </div>
          </div>
          <textarea readonly rows="3" class="form-control" style="resize: none;">
<?php foreach ($unpresented_check_payments as $check_payment):?>
<?= ($check_payment['pay_bank_name'] . ' (Cant.: ' . $check_payment['pay_count_bank']. ')') . ": " . money_formating($check_payment['pay_amount_by_bank'])."\n"?>
<?php endforeach; ?>
            </textarea>
          <div class="row">
            <div class="col-sm-6">
              <h4>Transferencias</h4>
            </div>
            <div class="col-sm-6">
              <h4 class="text-right"><?= money_formating($balances_not_closed['income_transfer']) ?></h4>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-6">
              <h4>Cuenta Corriente</h4>
            </div>
            <div class="col-sm-6">
              <h4 class="text-right"><?= money_formating($balances_not_closed['income_cc'], true, false) ?></h4>
            </div>
          </div>
          <textarea name="obs" rows="10" class="form-control" style="resize: none;">
DETALLE Valores Efectivo
    Billetes 1000:
    Billetes 500:
    Billetes 200:
    Billetes 100:
    Billetes 50:
    Billetes 20:
    Billetes 10:
    Notas:
          </textarea>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-outline">Confirmar</button>
        </div>
      </form>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div>