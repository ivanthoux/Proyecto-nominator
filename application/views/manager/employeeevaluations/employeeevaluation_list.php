<section class="content-header">
    <h1>
        Evaluaciones de desempeño
    </h1>
    <ol class="breadcrumb">
        <li class="active"><a href="<?= site_url('employeeevaluations/all') ?>"><i class="ion ion-person"></i> Evaluaciones de desempeño</a></li>
    </ol>
</section>
<section class="content">
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">Listado</h3>
            <div class="box-tools pull-right">
                <a href="<?= site_url('employeeevaluations/form') ?>" class="btn btn-primary">Nuevo</a>
            </div>
        </div>
        <div class="box-body">
            <table id="employeeevaluation_list" class="table table-bordered responsive nowrap" width="100%">
                <thead>
                    <tr class="top">
                        <th>Fecha</th>
                        <th>Empleado</th>
                        <th>Desempeño general</th>
                        <th><?= lang('Actions') ?></th>
                    </tr>
                </thead>
                <tfoot>
                    <tr class="top">
                        <th>Fecha</th>
                        <th>Empleado</th>
                        <th>Desempeño general</th>
                        <th><?= lang('Actions') ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</section>