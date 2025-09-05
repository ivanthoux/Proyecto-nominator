<section class="content-header">
    <h1>
        <i class="fa fa-users"></i> Usuarios
    </h1>
</section>
<section class="content">
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">Listado</h3>
            <div class="box-tools pull-right">
                <a href="<?= site_url('manager/user') ?>" class="btn btn-primary">Nuevo</a>
            </div><!-- /.box-tools -->
        </div><!-- /.box-header -->
        <div class="box-body">
            <div class="row">
                <?php $filters_label = array('user_office' => 'Oficinas'); ?>

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
            <table id="user_list" class="table table-bordered responsive nowrap" width="100%">
                <thead>
                    <tr class="top">
                        <th><?= lang('Name') ?></th>
                        <th><?= lang('Email') ?></th>
                        <th>Rol</th>
                        <th>Clave Nueva</th>
                        <th>Activo</th>
                        <th><?= lang('Actions') ?></th>
                    </tr>
                </thead>
                <tfoot>
                    <tr class="top">
                        <th><?= lang('Name') ?></th>
                        <th><?= lang('Email') ?></th>
                        <th>Rol</th>
                        <th>Clave Nueva</th>
                        <th>Activo</th>
                        <th><?= lang('Actions') ?></th>
                    </tr>
                </tfoot>
            </table>
        </div><!-- /.box-body -->
    </div><!-- /.box -->


</section>
