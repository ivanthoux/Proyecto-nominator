<div id="texte_a_afficher" class="texte_inselectionnable" style="height: 270px;">

	<?
	// echo '<pre>';
	// print_r($closing_data);
	?>
	<img style="max-height: 80px;" src="<?=base_url('assets/logo.png')?>" class="img-responsive" alt="nominator SA"/>
		<span class="stabilisation"></span>
    <p style="text-align: center;">
			<strong>RECIBO DE CIERRE DE CAJA<br><br></strong>
		</p>
    <p>
			En la <span id="span_id_provincia_recibo" class="encours">Ciudad de Posadas</span> el <span id="span_id_fecha_recibo"><span class="variable_vide"><?=$closing_date ?></span></span>
    	RECIBIMOS de <span id="span_id_personal"><?=$user_name ?></span></span>, la suma de <?=$closing_amount ?> en concepto del cierre de caja de la fecha.
		</p>
		<p>
			Ingreso en Efectivo: <?= money_formating($input_cash) ?> <br>
			Gastos en Efectivo: <?= money_formating($output_cash) ?> <br>
			Balance: <?= money_formating($total_balance) ?> <br><br>
			Transferencia: <?= money_formating($transfer) ?> <br>
			Cheque: <?= money_formating($cheque) ?> <br>
			Cuenta Corriente: <?= money_formating($ctacte, true, false) ?> <br>
		</p>
		<?php if (!empty($check_payments)):?>
			<p>
				Resumen de cheques recibidos:
			</p>
			<table>
				<tr>
					<th>Banco</th>
					<th>Cantidad</th>
					<th>Monto total</th>
				</tr>
				<?php foreach ($check_payments as $check_payment): ?>
					<tr>
						<td><?=$check_payment['pay_bank_name'] ?></td>
						<td><?=$check_payment['pay_count_bank'] ?></td>
						<td><?=money_formating($check_payment['pay_amount_by_bank']) ?></td>
					</tr>
				<?php endforeach ?>
			</table>
		<?php endif;?>
		<p>
			Detalle de los pagos recibidos:
		</p>
		<table >
			<tr>
				<th>
					Tipo
				</th>
				<th>
					Monto
				</th>
				<th>
					Fecha pago
				</th>
				<th>
					Tipo Pago
				</th>
				<th>
					Cliente
				</th>
			</tr>
			<?
			foreach($closing_data as $key => $payment){
				?>
				<tr>
					<td><?= $payment['mov']=='expenses'?'Gasto':'Pago '.$payment['id'] ?></td>
					<td><?= money_formating($payment['amount']) ?> <br></td>
					<td><?= date('d-m',strtotime($payment['date'])) ?></td>
					<td style="font-size:10px;"><?= $payment['type']?></td>
					<td><?=$payment['info']?></td>
				</tr>
				<?
			}
			?>
		</table>
    <p style="padding-left: 240px; text-align: center;">
      <br>
      <br>
      <br>
      <br>
      <br>_________________________
      <br>
      <br>
	      (DNI
				<span id="span_id_dni_acreedor">
	        <span class="variable_vide">________</span>
	      </span>)
      <span style="text-align: center;"></span>
    </p>
</div>
<style media="print">
table{
	width: 100%;
	border-collapse: collapse;
}
table, th, td {
	border: 1px solid black;
}
th, td {
  padding: 8px;
	text-align: center;
}
</style>
