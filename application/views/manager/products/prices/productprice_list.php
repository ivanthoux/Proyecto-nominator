<section class="content-header">
    <h1>
        <i class="fa fa-money"></i> Listado Precios de Productos
    </h1>
    <ol class="breadcrumb">
        <li class="active"><a href="<?= site_url('productprices/all') ?>"><i class="fa fa-house"></i> Precios de Productos</a></li>
    </ol>
</section>

<section class="content">
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">Filtrar Listado </h3>
            <div class="box-tools pull-right">
              <a class="btn btn-primary" href="<?= site_url('productprices/form') ?>"> <span class="fa fa-plus"></span> Nuevo</a>
            </div><!-- /.box-tools -->

        </div><!-- /.box-header -->
        <div class="box-body">
            <table id="product_list" class="table table-bordered  responsive nowrap" width="100%">
                <thead>
                    <tr class="top">
                        <th>Titulo</th>
                        <th><?= lang('Actions') ?></th>
                    </tr>
                </thead>
                <tfoot>
                    <tr class="top">
                      <th>Titulo</th>
                      <th><?= lang('Actions') ?></th>
                    </tr>
                </tfoot>
            </table>
        </div><!-- /.box-body -->
    </div><!-- /.box -->
</section>
