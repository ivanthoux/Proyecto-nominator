<section class="content-header">
  <h1>
    <i class="fa fa-bars"></i> Listado Productos
  </h1>
  <ol class="breadcrumb">
    <li class="active"><a href="<?= site_url('products') ?>"><i class="fa fa-house"></i> Productos</a></li>
  </ol>
</section>

<section class="content">
  <div class="box">
    <div class="box-header with-border">
      <h3 class="box-title">Filtrar Listado </h3>
      <div class="box-tools pull-right">
        <a class="btn btn-primary" href="<?= site_url('products/form') ?>"> <span class="fa fa-plus"></span> Nuevo</a>
      </div><!-- /.box-tools -->

    </div><!-- /.box-header -->
    <div class="box-body">
      <div class="row">
        <?
        $filters_label = array(
          'prod_category'=>'Categoría',
          'prod_stock'=>'Stock',
          'prod_price'=>'Lista de Precios');

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
      <table id="product_list" class="table table-bordered  responsive nowrap" width="100%">
        <thead>
          <tr class="top">
            <th>Titulo</th>
            <th>Lista de Precio</th>
            <th>Categoría</th>
            <th>Stock</th>
            <th><?= lang('Actions') ?></th>
          </tr>
        </thead>
        <tfoot>
          <tr class="top">
            <th>Titulo</th>
            <th>Lista de Precio</th>
            <th>Categoría</th>
            <th>Stock</th>
            <th><?= lang('Actions') ?></th>
          </tr>
        </tfoot>
      </table>
    </div><!-- /.box-body -->
  </div><!-- /.box -->
</section>
