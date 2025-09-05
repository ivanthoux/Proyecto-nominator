<section class="content-header">
    <h1>
        Recibos de sueldo
    </h1>
    <ol class="breadcrumb">
        <li class="active"><a href="<?= site_url('paychecks/all') ?>"><i class="ion ion-person"></i> Recibos de sueldo</a></li>
    </ol>
</section>
<section class="content">
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">Listado</h3>
            <div class="box-tools pull-right">
                <a href="<?= site_url('paychecks/form') ?>" class="btn btn-primary">Nuevo</a>
            </div>
        </div>
        <div class="box-body">
            <table id="paycheck_list" class="table table-bordered responsive nowrap" width="100%">
                <thead>
                    <tr class="top">
                        <th>Empleado</th>
                        <th>Mes liquidado</th>
                        <th>Bruto</th>
                        <th>Neto</th>
                        <th><?= lang('Actions') ?></th>
                    </tr>
                </thead>
                <tfoot>
                    <tr class="top">
                        <th>Empleado</th>
                        <th>Mes liquidado</th>
                        <th>Bruto</th>
                        <th>Neto</th>
                        <th><?= lang('Actions') ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</section>