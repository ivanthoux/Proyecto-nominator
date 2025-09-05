<section class="content-header">
  <h1>
    <i class="fa fa-money"></i> Listado de Gastos (Caja Saliente) <?= ($this->session->userdata('user_rol_label') != 'Super') ? '- Sin Cerrar' : ''?>
  </h1>
</section>

<section class="content">
  <div class="box">
    <div class="box-header with-border">
      <h3 class="box-title">Filtrar Listado </h3>
      <div class="box-tools pull-right">
        <a class="btn btn-primary" href="<?= site_url('expenses/form') ?>"> <span class="fa fa-plus"></span> Nuevo</a>
      </div><!-- /.box-tools -->

    </div><!-- /.box-header -->
    <div class="box-body">
      <div class="row">
        <div class="col-sm-3">
          <div class="input-group">
            <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
            <input type="text" id="datefilter" name="datefilter" autocomplete="off" class="form-control rangepicker" onchange="apply_filter();"/>
          </div>
        </div>

        <?
        $filters_label = array(
          'exp_type'=>'Tipo',
          'exp_category'=>'Categoría');

        foreach($filters as $field => $values){?>
          <div class="col-sm-3">
            <select class="form-control filter_field" id="filter_<?=$field?>" name="<?=$field?>" onchange="apply_filter();">
              <option value=""><?= $filters_label[$field]?></option>
              <?
              foreach($values as $val){?>
                <option value="<?=$val['value']?>"><?= $val['title']?></option>
              <? } ?>
            </select>
          </div>
        <? } ?>
      </div>
      <hr/>
      <table id="expenses_list" class="table table-bordered responsive nowrap" width="100%">
        <thead>
          <tr class="top">
            <th>Fecha</th>
            <th>Categoría/Nombre</th>
            <th>Monto</th>
            <th>Tipo</th>
            <th>Registro</th>
            <? if($this->session->userdata('user_rol_label') == 'Super') { ?>
            <th><?= lang('Actions') ?></th>
            <? } ?>
          </tr>
        </thead>
        <tfoot>
          <tr class="top">
            <th>Fecha</th>
            <th>Categoría/Nombre</th>
            <th>Monto</th>
            <th>Tipo</th>
            <th>Registro</th>
            <? if($this->session->userdata('user_rol_label') == 'Super') { ?>
            <th><?= lang('Actions') ?></th>
            <? } ?>
          </tr>
        </tfoot>
      </table>
    </div><!-- /.box-body -->
  </div><!-- /.box -->
</section>
