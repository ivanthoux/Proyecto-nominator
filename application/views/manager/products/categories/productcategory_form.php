<section class="content-header">
  <h1><i class="fa fa-folder-open"></i> Categoría de Producto</h1>
  <ol class="breadcrumb">
    <li class=""><a href="<?= site_url('productcategories/all') ?>"> categorías de Productos</a></li>
  </ol>
</section>
<section class="content product_form">
  <div class="box">
    <div class="box-header with-border">
      <h3 class="box-title"><?= isset($edit) ? 'Editar Categoría' : 'Crear Categoría' ?></h3>
    </div><!-- /.box-header -->
    <div class="box-body">
      <form class="form" action="" method="post" autocomplete="off">
        <input name="prodcat_id" type="hidden" value="<?= (isset($edit) && !empty($edit['prodcat_id']) ) ? $edit['prodcat_id'] : '' ?>"/>
        <div class="row">
          <div class="col-sm-4">
            <div class="form-group">
              <label>Titulo</label>
              <input class="form-control" name="prodcat_title" type="text" value="<?= (isset($edit) && !empty($edit['prodcat_title']) ) ? $edit['prodcat_title'] : '' ?>"/>
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
                <a class="btn" href="<?= site_url('productcategories/all') ?>"><?= lang('Cancel') ?></a>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</section>
