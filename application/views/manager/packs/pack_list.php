<section class="content-header">
    <h1>
        <i class="fa fa-tags"></i> Listado Productos
    </h1>
</section>

<section class="content">
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">Filtrar Listado </h3>
            <div class="box-tools pull-right">
                <a class="btn btn-primary" href="<?= site_url('packs/form') ?>"> <span class="fa fa-plus"></span> Nuevo</a>
            </div><!-- /.box-tools -->

        </div><!-- /.box-header -->
        <div class="box-body">
            <div class="row">
                <?php $filters_label = array('pack_active' => 'Estado'); ?>
                <?php foreach ($filters as $field => $values): ?>
                    <div class="col-sm-3">
                        <select class="form-control filter_field" id="filter_<?= $field ?>" name="<?= $field ?>" onchange="apply_filter();">
                            <option value=""><?= $filters_label[$field] ?></option>
                            <?php foreach ($values as $val) { ?>
                                <option value="<?= $val['value'] ?>"><?= $val['title'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                <?php endforeach; ?>
            </div>
            <hr/>
            <table id="package_list" class="table table-bordered  responsive nowrap" width="100%">
                <thead>
                    <tr class="top">
                        <th>Titulo</th>
                        <th>Cuotas</th>
                        <th>Valor</th>
                        <th>Tasa</th>
                        <th><?= lang('Actions') ?></th>
                    </tr>
                </thead>
                <tfoot>
                    <tr class="top">
                        <th>Titulo</th>
                        <th>Cuotas</th>
                        <th>Valor</th>
                        <th>Tasa</th>
                        <th><?= lang('Actions') ?></th>
                    </tr>
                </tfoot>
            </table>
        </div><!-- /.box-body -->
    </div><!-- /.box -->
</section>
