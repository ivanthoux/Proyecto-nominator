<section class="content-header">
  <!-- title row -->
  <h1>
    <i class="fa fa-money"></i> <?= isset($setting['name']) && !empty($setting['name']) ? $setting['name'] : '' ?> - Cuponera de pago
  </h1>
</section>
<section class="invoice">
  <div class="row padding">
    <div class="col-sm-12 text-justify">
      <?php foreach ($clients as $client) : ?>
        <?php foreach ($client['packs'] as $pack) : ?>
          <?php foreach ($pack['periods'] as $period) : ?>
            <table style="width: 100%; margin-bottom: 5px;">
              <tr>
                <td style="width: 50%; border: 1px dotted; padding: 2px 5px;">
                  <pre style="text-align: center; font-size: 10px; overflow: hidden"><img src="<?= site_url('assets/logo.png') ?>" style="margin-top: -13px; margin-bottom: -13px; float: left; height: 39px;"> PARA CLIENTE</pre>
                  <spam style="font-size: 13px;">
                    <spam style="font-size: 10px;">
                      <b>Comprobante N°: <?= $pack['clientpack_title'] ?></b>
                    </spam> <br>
                    <b>Cliente:</b> <?= $client['client_firstname'] . ' (' . $client['client_doc'] . ')' ?><br>
                    <b>Cuota N°:</b> <?= $period['session'] ?><br>
                    <?php $date = ((strtotime($period['date']) < strtotime(date('Y-m-d 23:59:59'))) ? strtotime(date('Y-m-d 23:59:59')) : strtotime($period['date'])) ?>
                    <b>Vencimiento:</b> <?= date('d-m-Y', $date) ?><br>
                    <b>Importe: <?= money_formating(round($period['amount'] + $period['punitorios'], 2)) ?></b>
                  </spam>
                </td>
                <td style="width: 50%; border: 1px dotted; padding: 2px 5px; vertical-align: top">
                  <pre style="text-align: center; font-size: 10px; overflow: hidden"><img src="<?= site_url('assets/logo.png') ?>" style="margin-top: -13px; margin-bottom: -13px; float: left; height: 39px;"> PARA nominator</pre>
                  <spam style="font-size: 13px;">
                    <spam style="font-size: 10px;">
                      <b>Comprobante N°: <?= $pack['clientpack_title'] ?></b>
                    </spam> <br>
                    <b>Cliente:</b> <?= $client['client_firstname'] . ' (' . $client['client_doc'] . ')' ?><br>
                    <b>Cuota N°:</b> <?= $period['session'] ?><br>
                    <?php $date = ((strtotime($period['date']) < strtotime(date('Y-m-d 23:59:59'))) ? strtotime(date('Y-m-d 23:59:59')) : strtotime($period['date'])) ?>
                    <b>Vencimiento:</b> <?= date('d-m-Y', $date) ?><br>
                    <b>Importe: <?= money_formating(round($period['amount'] + $period['punitorios'], 2)) ?></b>
                  </spam>
                </td>
              </tr>
            </table>
          <?php endforeach; ?>
        <?php endforeach; ?>
      <?php endforeach; ?>
      <!-- <pre><?= print_r($clients, true) ?></pre> -->
    </div><!-- /.row -->
  </div><!-- /.row -->
  <hr />
  <!-- this row will not appear when printing -->
  <div class="row no-print">
    <div class="col-xs-12">
      <a onclick="window.print()" class="btn btn-default"><i class="fa fa-print"></i> Imprimir/PDF</a>
      <a class="btn btn-primary pull-right" href="javascript:;" onclick="window.location = document.referrer;" style="margin-right: 5px;"> Volver</a>
    </div>
  </div>
</section>