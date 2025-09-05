<section class="content-header">
    <h1 class="row">
        <span class="pull-left"><i class="fa fa-dashboard"></i> Tablero</span>
        <div class="col-sm-3">
            <div class="input-group">
                <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                <input type="text" id="datefilter" name="datefilter" autocomplete="off" class="form-control rangepicker" value="<?= date('d-m-Y', strtotime($start)) . ' / ' . date('d-m-Y', strtotime($end)) ?>" />
            </div>
        </div>
    </h1>
</section>

<!-- <pre><?= print_r($clientpacks, true) ?></pre> -->
<?php if ($charge) : ?>
    <section class="content">
        <div class="row">
            <div class="col-sm-6">
                <h3 class="box-title">Balances en el filtro de fechas</h3>
                <div class="row">
                    <div class="col-xs-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3><?= money_formating($clientpacks['budget_final']) ?></h3>
                                <p>Total financiado</p>
                                <h3><?= money_formating($clientpacks['budget_real']) ?></h3>
                                <p>Capital</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-folder-open-o"></i>
                            </div>
                        </div>
                    </div><!-- ./col -->
                    <div class="col-xs-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3>%<?= $clientpacks['budget_final'] > 0 ? round($periods['periods_paid'] / $clientpacks['budget_final'] * 100, 1) : 0 ?></h3>
                                <p>Recuperación Financiado</p>
                                <h3>%<?= $clientpacks['budget_real'] > 0 ? round($periods['periods_paid'] / $clientpacks['budget_real'] * 100, 1) : 0 ?></h3>
                                <p>Recuperación Capital</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-money"></i>
                            </div>
                        </div>
                    </div><!-- ./col -->

                    <div class="col-xs-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3><?= money_formating($periods['periods_paid']) ?></h3>
                                <p>Total Recuperado</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-money"></i>
                            </div>
                        </div>
                    </div><!-- ./col -->

                    <div class="col-xs-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3><?= money_formating($clientpacks['budget_final'] - $clientpacks['budget_real'] - $balances['outcome']) ?></h3>
                                <p title="Valor Financiado - Valor Capital - Gastos">Rentabilidad</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-money"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3><?= $clientpacks['budget_operations'] . ' (' . $clientpacks['operations_expenses'] . ')' ?></h3>
                                <p>Operaciones (con gastos)</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-list"></i>
                            </div>
                        </div>
                    </div><!-- ./col -->

                    <div class="col-xs-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3><?= money_formating($clientpacks['budget_expenses']) ?></h3>
                                <p>Gastos Adm.</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-money"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <h3 class="box-title">Balances Globales</h3>
                <div class="row">
                    <div class="col-xs-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3><?= money_formating($clientpacks_global['budget_final']) ?></h3>
                                <p>Total financiado</p>
                                <h3><?= money_formating($clientpacks_global['budget_real']) ?></h3>
                                <p>Capital</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-folder-open-o"></i>
                            </div>
                        </div>
                    </div><!-- ./col -->
                    <div class="col-xs-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3>%<?= $clientpacks_global['budget_final'] > 0 ? round($periods_global['periods_paid'] / $clientpacks_global['budget_final'] * 100, 1) : 0 ?></h3>
                                <p>Recuperación Financiado</p>
                                <h3>%<?= $clientpacks_global['budget_real'] > 0 ? round($periods_global['periods_paid'] / $clientpacks_global['budget_real'] * 100, 1) : 0 ?></h3>
                                <p>Recuperación Capital</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-money"></i>
                            </div>
                        </div>
                    </div><!-- ./col -->

                    <div class="col-xs-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3><?= money_formating($periods_global['periods_paid']) ?></h3>
                                <p>Total Recuperado</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-money"></i>
                            </div>
                        </div>
                    </div><!-- ./col -->

                    <div class="col-xs-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3><?= money_formating($clientpacks_global['budget_final'] - $clientpacks_global['budget_real'] - $balances_global['outcome']) ?></h3>
                                <p title="Valor Financiado - Valor Capital - Gastos">Rentabilidad</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-money"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3><?= $clientpacks_global['budget_operations'] . ' (' . $clientpacks_global['operations_expenses'] . ')' ?></h3>
                                <p>Operaciones (con gastos)</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-list"></i>
                            </div>
                        </div>
                    </div><!-- ./col -->

                    <div class="col-xs-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3><?= money_formating($clientpacks_global['budget_expenses']) ?></h3>
                                <p>Gastos Adm.</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-money"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- ./col -->
        <div class="row">
            <div class="col-sm-6 text-center">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Detalles en el Rango de Fechas</h3>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <div class="description-block">
                            <!-- <span class="description-percentage text-yellow"><i class="fa fa-caret-left"></i> 0%</span> -->
                            <h5 class="description-header"><?= $clientpacks['budget_operations'] > 0 ? money_formating($clientpacks['budget_real'] / $clientpacks['budget_operations']) : 0 ?></h5>
                            <span class="description-text">PROMEDIO CAPITAL OPERACIÓN</span>
                        </div>
                        <div class="description-block">
                            <!-- <span class="description-percentage text-yellow"><i class="fa fa-caret-left"></i> 0%</span> -->
                            <h5 class="description-header"><?= money_formating($clientpacks['budget_final'] - $clientpacks['budget_real']) ?></h5>
                            <span class="description-text">DIFERENCIA FINANCIADO - CAPITAL</span>
                        </div>
                        <div class="description-block">
                            <!-- <span class="description-percentage text-yellow"><i class="fa fa-caret-left"></i> 0%</span> -->
                            <h5 class="description-header"><?= $clientpacks['budget_operations'] > 0 ? money_formating(($clientpacks['budget_final'] - $clientpacks['budget_real']) / $clientpacks['budget_operations']) : 0 ?></h5>
                            <span class="description-text">PROMEDIO RENTABILIDAD OPERACIÓN</span>
                        </div>
                        <hr />
                        <div class="description-block">
                            <!-- <span class="description-percentage text-yellow"><i class="fa fa-caret-left"></i> 0%</span> -->
                            <h5 class="description-header"><?= money_formating($balances['outcome']) ?></h5>
                            <span class="description-text">TOTAL SALIDAS</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Operaciones Por Codiciones de ventas en el Rango de Fechas</h3>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-nomargin responsive nowrap" width="100%">
                                <thead>
                                    <tr>
                                        <th>Codiciones de ventas</th>
                                        <th>Cant.</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($packs as $pack) : ?>
                                        <tr>
                                            <td><?= $pack['pack_name'] ?></td>
                                            <td><?= $pack['quantity'] ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Operaciones Por plazos en el Rango de Fechas</h3>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-nomargin responsive nowrap" width="100%">
                                <thead>
                                    <tr>
                                        <th>Plazo</th>
                                        <th>Cant.</th>
                                        <th>Valor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Semanal</td>
                                        <td><?= $clientpacks['operations_week'] ?></td>
                                        <td><?= money_formating($clientpacks['budget_week']) ?></td>
                                    </tr>
                                    <tr>
                                        <td>Mensual</td>
                                        <td><?= $clientpacks['operations_month'] ?></td>
                                        <td><?= money_formating($clientpacks['budget_month']) ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-4 hide">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Fuente Clientes Nuevos</h3>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                            <div class="col-xs-8 pull-right">
                                <div class="table-responsive">
                                    <table class="table table-nomargin responsive nowrap" width="100%">
                                        <thead>
                                            <tr>
                                                <th>Origen Clientes</th>
                                                <th>Cant.</th>
                                            </tr>
                                        </thead>
                                        <?
                                        $acum_amount = 0;
                                        foreach ($clients as $i => $cpack) {
                                            $acum_amount += $cpack['qty'];
                                            if ($i < 5) {
                                        ?>
                                                <tr>
                                                    <td><?= !empty($cpack['client_origin']) ? $cpack['client_origin'] : 'No especificado' ?></td>
                                                    <td><?= $cpack['qty'] ?></td>
                                                </tr>
                                        <? }
                                        } ?>
                                    </table>
                                </div>
                            </div><!-- ./col -->
                            <div class="col-xs-4 text-center">
                                <div class="description-block">
                                    <!-- <span class="description-percentage text-yellow"><i class="fa fa-caret-left"></i> 0%</span> -->
                                    <h5 class="description-header"><?= $acum_amount ?></h5>
                                    <span class="description-text">NUEVOS CLIENTES</span>
                                </div>
                            </div>
                        </div><!-- /.row -->
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>