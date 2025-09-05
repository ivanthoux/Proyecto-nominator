<section class="content-header">
  <h1><i class="fa fa-money"></i> Lista Precio de Producto</h1>
  <ol class="breadcrumb">
    <li class=""><a href="<?= site_url('productprices/all') ?>"> Precios de Productos</a></li>
  </ol>
</section>
<section class="content product_form">
  <div class="box">
    <div class="box-header with-border">
      <h3 class="box-title"><?= isset($edit) ? 'Editar Categoría' : 'Crear Categoría' ?></h3>
    </div><!-- /.box-header -->
    <div class="box-body">
      <form class="form" action="" method="post" autocomplete="off">
        <input name="prodprice_id" type="hidden" value="<?= (isset($edit) && !empty($edit['prodprice_id']) ) ? $edit['prodprice_id'] : '' ?>"/>
        <div class="row">
          <div class="col-sm-4">
            <div class="form-group">
              <label>Nombre de lista</label>
              <input class="form-control" name="prodprice_title" type="text" value="<?= (isset($edit) && !empty($edit['prodprice_title']) ) ? $edit['prodprice_title'] : '' ?>"/>
            </div>
          </div>
          <div class="col-sm-4">
            <div class="form-group">
              <label>% Ganancia</label>
              <div class="input-group">
                <span class="input-group-addon">%</span>
                <input class="form-control" name="prodprice_coef" type="text" value="<?= (isset($edit) && !empty($edit['prodprice_coef']) && $edit['prodprice_coef'] > 0 ) ? $edit['prodprice_coef'] : '' ?>"/>
              </div>
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
                <a class="btn" href="<?= site_url('productprices/all') ?>"><?= lang('Cancel') ?></a>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</section>
