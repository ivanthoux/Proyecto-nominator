<section class="content-header">
    <h1>
        Adicionales
    </h1>
    <ol class="breadcrumb">
        <li class="active"><a href="<?= site_url('manager/additionals') ?>"><i class="ion ion-person"></i> Adicionales</a></li>
    </ol>
</section>
<section class="content">
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">Listado</h3>
            <div class="box-tools pull-right">
                <a href="<?= site_url('manager/additional') ?>" class="btn btn-primary">Nuevo</a>
            </div>
        </div>
        <div class="box-body">
            <table id="additional_list" class="table table-bordered  responsive nowrap" width="100%">
                <thead>
                    <tr class="top">
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Coeficiente</th>
                        <th>Remunerativo</th>
                        <th><?= lang('Actions') ?></th>
                    </tr>
                </thead>
                <tfoot>
                    <tr class="top">
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Coeficiente</th>
                        <th>Remunerativo</th>
                        <th><?= lang('Actions') ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</section>
