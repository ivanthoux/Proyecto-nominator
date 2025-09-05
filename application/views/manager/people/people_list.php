<section class="content-header">
    <h1>
        Personas
    </h1>
    <ol class="breadcrumb">
        <li class="active"><a href="<?= site_url('manager/people') ?>"><i class="fa fa-male"></i> Personas</a></li>
    </ol>
</section>
<section class="content">
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">Listado</h3>
            <div class="box-tools pull-right">
                <a href="<?= site_url('manager/person') ?>" class="btn btn-primary">Nuevo</a>
            </div><!-- /.box-tools -->
        </div><!-- /.box-header -->
        <div class="box-body">
            <table id="people_list" class="table table-bordered  responsive nowrap" width="100%">
                <thead>
                    <tr class="top">
                        <th>Id</th>
                        <th>Nombre</th>
                        <th>Dirección</th>
                        <th>Celular</th>
                        <th>Email</th>
                        <th><?= lang('Actions') ?></th>
                    </tr>
                </thead>
                <tfoot>
                    <tr class="top">
                        <th>Id</th>
                        <th>Nombre</th>
                        <th>Dirección</th>
                        <th>Celular</th>
                        <th>Email</th>
                        <th><?= lang('Actions') ?></th>
                    </tr>
                </tfoot>
            </table>
        </div><!-- /.box-body -->
    </div><!-- /.box -->
</section>
