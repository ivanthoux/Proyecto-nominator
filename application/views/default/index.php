<div class="home-section family-roboto">
    <?php if (!isset($client)) : ?>
        <h1 class="color-white family-roboto-black">Bienvenido cliente</h1>
        <div class="hidden-xs"><br /><br /><br /></div>
        <div class="login-box col-sm-4 col-sm-offset-4">
            <!-- /.login-logo -->
            <div class="login-box-body">
                <p class="login-box-msg text-justify">Estimado Cliente, ingrese su N° de cliente o C.U.I.T. para visualizar su estado de cuenta y medios de pago disponibles</p>

                <form method="post">
                    <div class="form-group has-feedback">
                        <input name="dni" type="number" class="form-control" placeholder="N° de cliente o C.U.I.T.">
                        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                    </div>
                    <div class="row">
                        <div class="col-xs-8">
                            <div class="checkbox">
                            </div>
                        </div>
                        <!-- /.col -->
                        <div class="col-xs-4">
                            <button type="submit" class="btn btn-success btn-block btn-flat">Entrar</button>
                        </div>
                        <!-- /.col -->
                    </div>
                    <br>
                    <?php if (!empty($errors)) : ?>
                        <div class="clearfix">
                            <div class="alert alert-danger">
                                <?php if (gettype($errors) == 'array') : ?>
                                    <?php foreach ($errors as $error) : ?>
                                        <?= $error ?>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <?= $errors ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </form>
            </div>
            <!-- /.login-box-body -->
        </div>
    <?php else : ?>
        <div class="login-box col-xl-12">
            <h1 class="color-white family-roboto-black">Bienvenido <?= $client['client_firstname'] ?></h1>
            <!-- /.login-logo -->
            <div class="login-box-body">
                <?php foreach ($packs as $idx => $pack) : ?>
                    <table class="table table-bordered responsive">
                        <thead>
                            <tr class="top">
                                <th colspan="5">
                                    <h3 style="margin: 5px 0; color: #FFF;">
                                        Comprobante N°: <?= $pack['clientpack']['clientpack_title'] ?> - <span style="color: #FFF;" id="a_pagar_<?= $pack['clientpack']['clientpack_id'] ?>"></span>
                                    </h3>
                                </th>
                            </tr>
                            <tr class="top">
                                <th colspan="5">
                                    <table class="responsive" style="width: 100%;">
                                        <tr>
                                            <td class="text-center">
                                                Cliente: <?= $client['client_firstname'] ?><br>
                                            </td>
                                            <td class="text-center">
                                                N° de cliente: <?= money_formating($client['client_doc'], true, false) ?>
                                            </td>
                                            <td class="text-center">
                                                C.U.I.T.: <?= $client['client_cuil'] ?>
                                            </td>
                                            <td class="text-center">
                                                Fecha Solicitud: <?= date('d-m-Y', strtotime($pack['clientpack']['clientpack_created_at'])) ?>
                                            </td>
                                        </tr>
                                    </table>
                                </th>
                            </tr>
                            <tr>
                                <th class="text-center">Cuota N°</th>
                                <th class="text-center">Vencimiento</th>
                                <th class="text-center">Cuota</th>
                                <th class="text-center">Punitorios</th>
                                <th class="text-center">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $amount = 0;
                            $punitorios = 0;
                            $total = 0;
                            ?>
                            <?php foreach ($pack['periods'] as $period) : ?>
                                <tr>
                                    <td class="text-center"><?= $period['session'] ?></td>
                                    <td class="text-center"><?= date('d-m-Y', strtotime($period['date'])) ?></td>
                                    <td class="text-right" style="font-weight: bold; font-size: 16px;"><?= money_formating($period['amount']) ?></td>
                                    <td class="text-right" style="font-weight: bold; font-size: 16px;"><?= money_formating($period['punitorios']) ?></td>
                                    <td class="text-right" style="font-weight: bold; font-size: 16px;"><?= money_formating($period['amount'] + $period['punitorios']) ?></td>
                                </tr>
                                <?php
                                $amount += $period['amount'];
                                $punitorios += $period['punitorios'];
                                $total += $period['amount'] + $period['punitorios'];
                                ?>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th class="text-right" colspan="2">TOTALES</th>
                                <th class="text-right" style="font-size: 16px;"><?= money_formating($amount) ?></th>
                                <th class="text-right" style="font-size: 16px;"><?= money_formating($punitorios) ?></th>
                                <th class="text-right" style="font-size: 16px;"><?= money_formating($total) ?></th>
                            </tr>
                        </tfoot>
                    </table>
                    <h4 class="text-justify" style="margin: 5px 0; color: #FFF;">Se adeuda un total es de <i style='font-weight: bold; font-size: 24px;'><?= money_formating($total) ?></i>.</h4>
                    <input type="hidden" id="values_<?= $pack['clientpack']['clientpack_id'] ?>" data-value="<?= round($total, 2) ?>" value="A pagar <i style='font-weight: bold; font-size: 24px;'><?= money_formating($total) ?></i>">
                    <?php if (($idx + 1) < count($packs)) : ?>
                        <hr>
                    <?php endif; ?>
                <?php endforeach; ?>
                <!-- <pre>
                <?= print_r([$client, $packs], true) ?>
            </pre> -->
            </div>
        </div>
        <div class="home-section family-roboto">
            <hr>
            <h3 class="color-white" style="margin-bottom: 5px;">Ingrese aquí el monto a pagar y le generaremos un link a Mercado Pago</h3>
            <p class="color-white text-center" style="margin-bottom: 25px;">Una vez efectuado pago, sírvase informar el junto con su número de N° de CUIT al mail <a href="mailto:nominator@gmail.com" style="color:#FFF">nominator@gmail.com</a></p>
            <div class="login-box col-sm-4 col-sm-offset-4">
                <div class="login-box-body">
                    <form target="_blank" action="<?= base_url('Scripts/linkMP'); ?>" method="post">
                        <input class="form-control" type="hidden" name="client_id" value="<?= $client['client_id']; ?>">
                        <div class="row">
                            <div class="col-xs-8">
                                <div class="form-group">
                                    <input class="form-control" type="number" step="0.01" name="importe" id="pay_mp">
                                    <span class="fa fa-money form-control-feedback"></span>
                                </div>
                            </div>
                            <div class="col-xs-4">
                                <button type="submit" class="btn btn-success btn-block btn-flat">Pagar</button>
                                <!-- <div class="checkbox">
                                </div> -->
                            </div>
                            <!-- /.col -->
                            <!-- <div class="col-xs-4">
                            </div> -->
                            <!-- /.col -->
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>