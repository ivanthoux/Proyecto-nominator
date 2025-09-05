<section class="content-header">
  <?php if (!function_exists('str_contains')) {
    function str_contains(string $haystack, string $needle)
    {
      return empty($needle) || strpos($haystack, $needle) !== false;
    }
  } ?>
  <?php if (!isset($pdf) || !$pdf) : ?>
    <h1>
      <i class="fa fa-money"></i> <?= isset($setting['name']) && !empty($setting['name']) ? $setting['name'] : '' ?> - <?= (!isset($detail)) ? "Recibo" : "Detalle de Cuota" ?>
    </h1>
  <?php endif; ?>
</section>
<section class="invoice">
  <div class="row padding">
    <div class="col-sm-12 text-justify">
      <?php if (!isset($detail)) : ?>
        <?php foreach ($payments as $key => $payment) : ?>
          <table style="width: 100%" id="table-double">
            <tr>
              <?php for ($x = 0; $x < ((isset($pdf) && $pdf) ? 1 : 2); $x++) : ?>
                <?php $period_total = 0; ?>
                <td style=" border-left: 1px dotted;border-right: 1px dotted; padding: 0 5px;" class="<?= $x == 1 ? 'td-hide' : '' ?>">
                  <p class="text-right"><b><?= (isset($payment) && !empty($payment['pay_date'])) ? date('d-m-Y', strtotime($payment['pay_date'])) : '' ?></b></p>
                  <p>Recibo N&deg;: <b><?= (isset($payment)) ? money_formating($payment['pay_id'], true, false) : '' ?></b></p>
                  <br />Cliente: <b><?= $client[$key]['client_firstname'] . ' ' . $client[$key]['client_lastname'] ?></b>
                  <br />Documento: <b><?= money_formating($client[$key]['client_doc'], TRUE, FALSE) ?></b>
                  <br />Direcci&oacute;n: <b><?= $client[$key]['client_address'] ?></b><br /><br />
                  <table style="width: 100%;">
                    <tr>
                      <td>Concepto</td>
                      <td>Cuota</td>
                      <td>Capital</td>
                      <td>Punitorios</td>
                      <!-- <td>Descuento</td> -->
                      <td>Total</td>
                    </tr>
                    <?php foreach ($payment['paymentClientperiods'] as $clientPeriod) : ?>
                      <?php $paid = floatval($clientPeriod['pay_period_capital']) + floatval($clientPeriod['pay_period_discount']) + floatval($clientPeriod['pay_period_daytask']); ?>
                      <?php $period_total += $paid; ?>
                      <tr>
                        <td><?= $clientPeriod['clientpack_title'] ?></td>
                        <td><?= $clientPeriod['clientperiod_packperiod'] . "/" . $clientPeriod['clientpack_sessions'] ?></td>
                        <td><?= money_formating(floatval($clientPeriod['pay_period_capital'])) ?></td>
                        <td><?= money_formating(floatval($clientPeriod['pay_period_daytask'])) ?></td>
                        <td><?= money_formating($paid) ?></td>
                      </tr>
                    <?php endforeach; ?>
                    <?php if ($payment['pay_amount'] - $period_total > 0) : ?>
                      <tr>
                        <td>ADELANTO</td>
                        <td>-</td>
                        <td><?= money_formating($payment['pay_amount'] - $period_total) ?></td>
                        <td>-</td>
                        <td><?= money_formating($payment['pay_amount'] - $period_total) ?></td>
                      </tr>
                    <?php endif; ?>
                    <tr style="border-bottom: 1px solid #000;">
                      <td></td>
                    </tr>
                    <?php if (!empty($payment['details'])) : ?>
                      <tr>
                        <td colspan="5">
                          <hr>
                        </td>
                      </tr>
                  </table>
                  Detalle de medios de pago:
                  <table style="width:100%">
                    <tr>
                      <th style="width:30%">Medio de pago</th>
                      <th style="width:20%">Monto</th>
                      <th style="padding-left: 10px;width:50%">Datos adicionales</th>
                    </tr>
                    <?php foreach ($payment['details'] as $payment_detail) : ?>
                      <tr>
                        <td>
                          <hr>
                        </td>
                      </tr>
                      <tr>
                        <td><?= $payment_detail['pay_detail_type'] ?></td>
                        <td><?= money_formating(floatval($payment_detail['pay_detail_amount'])) ?></td>
                        <td style="padding-left: 10px">
                          <?php $extra_data = json_decode($payment_detail['pay_detail_extra_data'], true); ?>
                          <?php if (str_contains(strtoupper($payment_detail['pay_detail_type']), 'CHEQUE')) : ?>
                            Banco: <?= (!empty($extra_data['pay_bank_name'])) ? $extra_data['pay_bank_name'] : '' ?> <br>
                            N° Cheque: <?= (!empty($extra_data['pay_number'])) ? $extra_data['pay_number'] : '' ?> <br>
                            Clearing: <?= (!empty($extra_data['pay_clearing'])) ? $extra_data['pay_clearing'] : '' ?> <br>
                            Cuit emisor: <?= (!empty($extra_data['pay_cuit'])) ? $extra_data['pay_cuit'] : '' ?> <br>
                            Exp. de cheque: <?= (!empty($extra_data['pay_expiration_date'])) ? $extra_data['pay_expiration_date'] : '' ?>
                          <?php endif; ?>
                          <?php if (str_contains(strtoupper($payment_detail['pay_detail_type']), 'MP')) : ?>
                            Nº transacción: <?= (!empty($extra_data['pay_transaction_number'])) ? $extra_data['pay_transaction_number'] : '' ?>
                          <?php endif; ?>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                  <?php if (!empty($payment['pay_canvas'])) : ?>
                    <tr>
                      <td colspan="5">&nbsp;</td>
                    </tr>
                    <tr>
                      <td colspan="5">&nbsp;</td>
                    </tr>
                  <?php endif; ?>
                  <?php if (!empty($payment['pay_canvas'])) : ?>
                    <tr>
                      <td colspan="2">Firma</td>
                      <td></td>
                      <td colspan="2">Mapa</td>
                    </tr>
                  <?php endif; ?>
                  <tr>
                    <?php if (!empty($payment['pay_canvas'])) : ?>
                      <td colspan="2">
                        <img alt="firma" class="signature_img img-fluid img-responsive" src="<?php echo base_url() . 'resources/payment_signatures/' . $payment['pay_canvas'] ?>" />
                      </td>
                    <?php endif; ?>
                    <!-- <td>&nbsp;</td> -->
                    <?php if (!empty($payment['pay_lat']) && !empty($payment['pay_lng'])) : ?>
                      <td colspan="3">
                        <div id="gmap_div_<?= $key . '_' . $x ?>" style="height:200px"></div>
                      </td>
                    <?php endif; ?>
                  </tr>
                  <tr>
                    <td colspan="5">&nbsp;</td>
                  </tr>
                  </table><br />
                  <p>Total: <?= money_formating($payment['pay_amount']) ?></p>
                </td>
              <?php endfor; ?>
            </tr>
          </table>
        <?php endforeach; ?>
      <?php else : ?>
        <table style="width: 100%">
          <tr>
            <td style=" border-left: 1px dotted;border-right: 1px dotted; padding: 0 5px;">
              <p>Comprobante N&deg;: <b><?= (isset($edit[0])) ? $edit[0]['clientpack_title'] : '' ?></b></p>
              <br />Cuota: <b><?= $edit[0]['clientperiod_packperiod'] . "/" . $edit[0]['clientpack_sessions'] ?></b>
              <br />Cliente: <b><?= $client[0]['client_firstname'] . ' ' . $client[0]['client_lastname'] ?></b>
              <br />Documento: <b><?= money_formating($client[0]['client_doc'], TRUE, FALSE) ?></b>
              <br />Direcci&oacute;n: <b><?= $client[0]['client_address'] ?></b><br /><br />
              <table style="width: 100%;">
                <tr>
                  <td>Recibo</td>
                  <td>Fecha</td>
                  <td>Capital</td>
                  <td>Punitorios</td>
                  <!-- <td>Descuento</td> -->
                  <td>Total</td>
                </tr>
                <?php $full = 0; ?>
                <?php foreach ($payments as $key => $pay) : ?>
                  <?php $paid = floatval($payments[$key]['pay_amount']) + floatval($payments[$key]['pay_discount']) + floatval($payments[$key]['pay_daytask']); ?>
                  <?php $full += floatval($payments[$key]['pay_amount']) + floatval($payments[$key]['pay_discount']); ?>
                  <tr>
                    <td><?= money_formating($payments[$key]['pay_id'], true, false) ?></td>
                    <td><?= date('d-m-Y', strtotime($payments[$key]['pay_date'])) ?></td>
                    <td><?= money_formating(floatval($payments[$key]['pay_amount'])) ?></td>
                    <td><?= money_formating(floatval($payments[$key]['pay_daytask'])) ?></td>
                    <!-- <td><?= money_formating(floatval($payments[$key]['pay_discount'])) ?></td> -->
                    <td><?= money_formating($paid) ?></td>
                  </tr>
                <?php endforeach; ?>
              </table><br />
              <p>Capital: <?= money_formating($full) ?></p>
              <p>Saldo Cta: <?= money_formating($edit[0]['clientperiod_amountfull'] - $full) ?></p>
            </td>
          </tr>
        </table>
      <?php endif; ?>
    </div><!-- /.row -->
  </div><!-- /.row -->
  <?php if (!isset($pdf) || !$pdf) : ?>
    <hr />
    <!-- this row will not appear when printing -->
    <div class="row no-print">
      <div class="col-xs-12">
        <a onclick="window.print()" id="print_btn" class="btn btn-default"><i class="fa fa-print"></i> <span class="hidden-xs">Imprimir/PDF</span></a>
        <a onclick="app.send()" id="send_btn" class="btn bg-olive"><i class="fa fa-envelope-o"></i> <span class="hidden-xs">Enviar/PDF</span></a>
        <a class="btn btn-primary pull-right" href="javascript:;" onclick="window.location = document.referrer?document.referrer:'<?= site_url('clientperiods/all/' . $edit[0]['clientpack_client']) ?>';" style="margin-right: 5px;"> Volver</a>
        <!-- <a class="btn btn-primary pull-right" href="<?= site_url('clientperiods/all/' . $edit[0]['clientpack_client']) ?>" style="margin-right: 5px;"> Volver</a> -->
      </div>
    </div>
  <?php endif; ?>
</section>