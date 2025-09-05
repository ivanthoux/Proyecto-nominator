<section class="content-header">
    <h1>
        Roles de usuarios
    </h1>
    <ol class="breadcrumb">
        <li class="active"><a href="<?= site_url('manager/role') ?>"><i class="ion ion-person"></i> Roles</a></li>
    </ol>
</section>
<section class="content">
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">Listado</h3>
            <div class="box-tools pull-right">
                <a href="<?= site_url('manager/role') ?>" class="btn btn-primary">Nuevo</a>
            </div><!-- /.box-tools -->
        </div><!-- /.box-header -->
        <div class="box-body">
            <table id="role_list" class="table table-bordered  responsive nowrap" width="100%">
                <thead>
                    <tr class="top">
                        <th>Clave</th>
                        <th>Nombre</th>
                        <th><?= lang('Actions') ?></th>
                    </tr>
                </thead>
                <tfoot>
                    <tr class="top">
                        <th>Clave</th>
                        <th>Nombre</th>
                        <th><?= lang('Actions') ?></th>
                    </tr>
                </tfoot>
            </table>
        </div><!-- /.box-body -->
    </div><!-- /.box -->


</section>
