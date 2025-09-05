<section class="content-header">
  <h1><i class="fa fa-bars"></i> Producto</h1>
  <ol class="breadcrumb">
    <li class=""><a href="<?= site_url('products/all') ?>"> Productos</a></li>
  </ol>
</section>
<section class="content product_form">
  <div class="box">
    <div class="box-header with-border">
      <h3 class="box-title"><?= isset($edit) ? 'Editar Producto' : 'Crear Producto' ?></h3>
    </div><!-- /.box-header -->
    <div class="box-body">
      <form class="form" action="" method="post" autocomplete="off">
        <input name="prod_id" type="hidden" value="<?= (isset($edit) && !empty($edit['prod_id']) ) ? $edit['prod_id'] : '' ?>"/>
        <div class="row">
          <div class="col-sm-3">
            <div class="form-group">
              <label>Titulo</label>
              <input class="form-control" name="prod_title" type="text" value="<?= (isset($edit) && !empty($edit['prod_title']) ) ? $edit['prod_title'] : '' ?>"/>
            </div>
          </div>
          <div class="col-sm-2">
            <div class="form-group">
              <label>Categoría</label>
              <select name="prod_category" class="form-control">
                <option value="">Elegir</option>
                <?if(!empty($prod_categories)){
                  foreach($prod_categories as $cat){
                    ?>
                    <option <?= (isset($edit) && !empty($edit['prod_category']) && $edit['prod_category'] == $cat['prodcat_id']) ? 'selected' : ''?> value="<?=$cat['prodcat_id']?>"><?=$cat['prodcat_title']?></option>
                  <? }
                } ?>
              </select>
            </div>
          </div>
          <div class="col-sm-2">
            <div class="form-group">
              <label>Lista de Precios</label>
              <select name="prod_price" class="form-control">
                <option value="">Elegir</option>
                <?if(!empty($prod_prices)){
                  foreach($prod_prices as $price){
                    ?>
                    <option <?= (isset($edit) && !empty($edit['prod_price']) && $edit['prod_price'] == $price['prodprice_id']) ? 'selected' : ''?> value="<?=$price['prodprice_id']?>"><?=$price['prodprice_title']?></option>
                  <? }
                } ?>
              </select>
            </div>
          </div>
          <div class="col-sm-2">
            <div class="form-group">
              <label>Costo</label>
              <input class="form-control" name="prod_cost" type="text" value="<?= (isset($edit) && !empty($edit['prod_cost']) ) ? $edit['prod_cost'] : '' ?>"/>
            </div>
          </div>

          <div class="col-sm-2">
            <div class="form-group">
              <label>Stock</label>
              <input class="form-control" name="prod_stock" type="text" value="<?= (isset($edit) && !empty($edit['prod_stock']) ) ? $edit['prod_stock'] : '' ?>"/>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-sm-6">
            <div class="form-group">
              <label>Descripción</label>
              <textarea class="form-control" name="prod_description"><?= (isset($edit) && !empty($edit['prod_description']) ) ? $edit['prod_description'] : '' ?></textarea>
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
                <a class="btn" href="<?= site_url('products/all') ?>"><?= lang('Cancel') ?></a>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</section>
