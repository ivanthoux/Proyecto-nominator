<section class="invoice">
    <!-- title row -->
    <div class="row">
        <div class="col-xs-12">
            <h2 class="page-header">
                <i class="fa fa-money"></i> nominator - <?= empty($periods) ? 'Pagar&eacute;' : ' TABLA DE CUOTAS' ?>
            </h2>
        </div><!-- /.col -->
    </div>
    <!-- info row -->
    <div class="row invoice-info <?= empty($periods) ? 'hide' : '' ?>">
        <div class="col-sm-4 invoice-col">
            <address>
                Cliente <strong><?= $client['client_firstname'] . ' ' . $client['client_lastname'] ?></strong><br>
                <?= $client['client_address'] ?><br>
                <?= $client['client_phone'] ?>
            </address>
        </div><!-- /.col -->
        <div class="col-sm-4 invoice-col">
            <strong>Inicio: <?= (isset($edit) && !empty($edit['clientpack_start'])) ? date('d-m-Y', strtotime($edit['clientpack_start'])) : '' ?><br></strong>
            Cuotas: <?= (isset($edit) && !empty($edit['clientpack_sessions'])) ? $edit['clientpack_sessions'] : '' ?>
        </div><!-- /.col -->

        <div class="col-sm-4 invoice-col">
            Final: <?= (isset($edit) && !empty($edit['clientpack_final'])) ? money_formating($edit['clientpack_final']) : '' ?> <br />
            Cuotas Restantes: <?= (isset($edit) && !empty($edit['periods_not_paid'])) ? ($edit['periods_not_paid']) : '' ?> <br />
            Valor Restante: <?= (isset($edit) && !empty($edit['periods_not_paid_amount'])) ? money_formating($edit['periods_not_paid_amount']) : '' ?>
        </div><!-- /.col -->
    </div><!-- /.row -->
    <?php if (isset($pack['packdiscount_value']) && isset($periods)) : ?>
        <?php $discount = round($pack['packdiscount_value'] / 100, 2);  ?>
        <?php $discount = round($periods[0]['clientperiod_amountinterest'] * $discount, 2);  ?>
        <hr />
        <div class="row invoice-info <?= empty($periods) ? 'hide' : '' ?>">
            <div class="col-sm-4 invoice-col">
                Cuota Valor: <?= money_formating($edit['clientpack_sessions_price']) ?> <br />
            </div>
            <div class="col-sm-4 invoice-col">
                Descuento Pago en término: <?= money_formating($discount) ?> <br />
            </div>
            <div class="col-sm-4 invoice-col">
                Cuota Valor con Descuento: <?= money_formating($edit['clientpack_sessions_price'] - $discount) ?> <br />
            </div>
        </div><!-- /.row -->
    <?php endif; ?>
    <?php if (!empty($periods)) : ?>
        <hr />
        <!-- Table row -->
        <div class="row ">
            <div class="col-xs-12 table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Cuota</th>
                            <th>Fecha</th>
                            <th>Valor</th>
                            <th>Pagado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($periods)) : ?>
                            <?php foreach ($periods as $per) : ?>
                                <tr>
                                    <td><?= $per['clientperiod_packperiod'] ?></td>
                                    <td><?= date('d-m-Y', strtotime($per['clientperiod_date'])) ?></td>
                                    <td><?= money_formating($per['clientperiod_amount']) ?></td>
                                    <td><?= $per['clientperiod_paid'] ? 'PAGADO' : '' ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php else : ?>
        <div class="row padding">
            <div class="col-sm-12 text-justify">
                <p class="text-right">SOLICITUD DE CREDITO: <b><?= (isset($edit)) ? money_formating($edit['clientpack_id'], true, false) : '' ?></b></p>
                <p>Lugar: <b>Posadas, Misiones</b> fecha: <b><?= (isset($edit) && !empty($edit['clientpack_created_at'])) ? date('d-m-Y', strtotime($edit['clientpack_created_at'])) : '' ?></b></p>
                <p>A la vista pagaré/mos sin protesto (Artículo 50 Decreto Ley 5965/63) a nominator o a orden la cantidad de Pesos <b><?= lcfirst(trim($Numbertowords->convert($edit['clientpack_price']))) ?> (<?= money_formating($edit['clientpack_price']) ?>)</b> más el <?= money_formating($edit['clientpack_commision'], false, false) ?>% de interés compensatorio.</p>
                <p>La falta en pago en término constituirá en mora a los firmantes sin necesidad de interpelación alguna, pudiendo exigirse desde entonces hasta su efectiva cancelación el pago de dicho importe, más los intereses compensatorios, gastos, impuestos, tasas, honorarios y aportes profesionales correspondientes, con más la suma que se devengue en concepto de intereses punitorios que serán equivalentes al 50% de los intereses compensatorios.</p>
                <p>Por medio del presente, y en ejercicio de las facultades que me confiere el primer párrafo del Artículo 36 del Decreto Ley 5965/63, amplío el plazo de presentación para el pago de este documento a 48 (cuarenta y ocho) meses a partir de la fecha de libramiento, corriendo desde hoy los intereses compensatorios fijados (Artículo 25 del Decreto Ley 5965/63).</p>
                <br />
                <br />
                <br />
                <br />
                <br />
                <div class="col-sm-6 text-center">
                    <p>....................................<br />Firma del Deudor</p>
                    <p><span class="border-botton"><b><?= $client['client_firstname'] . ' ' . $client['client_lastname'] ?></b></span><br />Deudor Aclaración</p>
                    <p><span><b><?= money_formating($client['client_doc'], TRUE, FALSE) ?></b></span><br />Deudor Documento</p>
                </div>
                <div class="col-sm-6 text-center">
                    <p>....................................<br />Firma del Codeudor</p>
                    <p>....................................<br />Codeudor Aclaración</p>
                    <p>....................................<br />Codeudor Documento</p>
                </div>
            </div><!-- /.row -->
        </div><!-- /.row -->
        <hr />
    <?php endif; ?>
    <!-- this row will not appear when printing -->
    <?php if (!isset($email)) : ?>
        <div class="row no-print">
            <div class="col-xs-12">
                <a onclick="window.print()" class="btn btn-default"><i class="fa fa-print"></i> Imprimir/PDF</a>
                <a class="btn btn-primary pull-right" href="<?= site_url('clientpacks/all/' . $client_id) ?>" style="margin-right: 5px;"> Volver</a>
            </div>
        </div>
    <?php endif; ?>
</section>