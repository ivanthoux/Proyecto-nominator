<section class="content-header">
    <h1>
        Contratos
    </h1>
    <ol class="breadcrumb">
        <li class="active"><a href="<?= site_url('contracts/all') ?>"><i class="ion ion-person"></i> Contratos</a></li>
    </ol>
</section>
<section class="content">
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">Listado</h3>
            <div class="box-tools pull-right">
                <a href="<?= site_url('contracts/form') ?>" class="btn btn-primary">Nuevo</a>
            </div>
        </div>
        <div class="box-body">
            <table id="contract_list" class="table table-bordered responsive nowrap" width="100%">
                <thead>
                    <tr class="top">
                        <th>Empleado</th>
                        <th>Fecha inicio</th>
                        <th>Fecha fin</th>
                        <th>Categoría</th>
                        <th><?= lang('Actions') ?></th>
                    </tr>
                </thead>
                <tfoot>
                    <tr class="top">
                        <th>Empleado</th>
                        <th>Fecha inicio</th>
                        <th>Fecha fin</th>
                        <th>Categoría</th>
                        <th><?= lang('Actions') ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</section>