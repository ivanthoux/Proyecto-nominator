<section class="content-header">
  <h1>
    <i class="fa fa-tasks"></i> Listado de Liquidaciones
  </h1>
  <ol class="breadcrumb">
    <li class="active"><a href="<?= site_url('settlements') ?>"><i class="fa fa-tasks"></i> Liquidaciones</a></li>
  </ol>
</section>

<section class="content">
  <div class="box">
    <div class="box-header with-border">
      <h3 class="box-title">Filtrar Listado </h3>

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
          'sett_user'=>'Asesor');

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
      <table id="settlements_list" class="table table-bordered  responsive nowrap" width="100%">
        <thead>
          <tr class="top">
            <th>Fecha</th>
            <th>Asesor</th>
            <th>Periodo Inicio</th>
            <th>Periodo Fin</th>
            <th>Monto</th>
            <th>Tipo</th>
            <th>Liquido</th>
          </tr>
        </thead>
        <tfoot>
          <tr class="top">
            <th>Fecha</th>
            <th>Asesor</th>
            <th>Periodo Inicio</th>
            <th>Periodo Fin</th>
            <th>Monto</th>
            <th>Tipo</th>
            <th>Liquido</th>
          </tr>
        </tfoot>
      </table>
    </div><!-- /.box-body -->
  </div><!-- /.box -->
</section>
