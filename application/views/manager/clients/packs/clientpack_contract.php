<section class="invoice">
    <!-- title row -->
    <div class="row">
        <div class="col-xs-12">
            <h2 class="page-header">
                <i class="fa fa-money"></i> nominator - Contrato
            </h2>
        </div><!-- /.col -->
    </div>
    <!-- info row -->
    <div class="row padding">
        <div class="col-sm-12 text-justify">
            <p class="text-right">SOLICITUD DE CREDITO: <b><?= (isset($edit)) ? money_formating($edit['clientpack_id'], true, false) : '' ?></b></p>
            <p>Lugar: <b>Posadas, Misiones</b> fecha: <b><?= (isset($edit) && !empty($edit['clientpack_created_at'])) ? date('d-m-Y', strtotime($edit['clientpack_created_at'])) : '' ?></b></p>
            <p>Por la presente, <b><?= $client['client_firstname'] . ' ' . $client['client_lastname'] ?></b> con D.N.I. <b><?= money_formating($client['client_doc'], TRUE, FALSE) ?></b> reconozco en <b>nominator</b> C.U.I.T. <b><?= isset($setting['cuit']) && !empty($setting['cuit']) ? $setting['cuit'] : '' ?></b> de esta ciudad de Posadas - Provincia de Misiones - el car&aacute;cter de <b>ACREEDOR</b> del PRESTAMO <b><?= $pack['pack_name'] . ' (' . $edit['clientpack_sessions'] . ')' ?></b> en DINERO EN EFECTIVO que estar&aacute; sujeto a las siguientes cl&aacute;usulas y previsiones:</p>
            <p>Por la presente, <b>YO</b>, en mi caracter de <b>DEUDOR</b> solicito al <b>ACREEDOR</b> el otorgamiento del pr&eacute;stamo en dinero en efectivo que ser&aacute; destinado a cancelar obligaciones personales de car&aacute;cter preexistentes.</p>
            <p><b>Reintegro del pr&eacute;stamo:</b> Me obligo y comprometo a reintegrar el monto total de <b><?= money_formating($edit['clientpack_final']) ?></b> en <b><?= $edit['clientpack_sessions'] ?></b> cuotas, <?= $edit['clientpack_type'] ?>es, iguales y consecutivas de <b><?= money_formating($edit['clientpack_sessions_price']) ?></b> cada una, venciendo la primera el dia <b><?= date('d-m-Y', strtotime($period['clientperiod_date'])) ?></b>, y luego, todos los días 10 de cada mes, o el inmediato posterior hábil en caso de no serlo.</p>
            <p><b>Tasa de Inter&eacute;s:</b> Me comprometo a pagar la tasa de inter&eacute;s <?= $edit['clientpack_type'] ?> del, <b><?= money_formating($edit['clientpack_commision'], false, false) ?>%</b>.</p>
            <p>El sistema de amortizaci&oacute;n de capital e intereses es <b>DIRECTA</b></p>
            <p>Forma parte del presente el <b>TABLA DE CUOTAS</b> con el detalle de las cuotas pactadas y fechas de vencimiento de cada una de ellas.</p>
            <p>CONDICIONES</p>
            <p>1. YO, El DEUDOR me obligo a pagar directamente a <b>nominator</b> sin requerimiento previo alguno, las sumas de dinero que correspondan para abonar íntegramente las cuotas o la totalidad del préstamo si correspondiere en los lugares habilitados por <b>nominator</b> y cuya indicación precisa recibiré al momento de la celebración del presente contrato de mutuo; dentro de la misma plaza y en el horario establecido para atención al público, en la moneda pactada en billete o transferencia. Para el supuesto que la fecha estipulada para cualquier pago coincidiera con día inhábil el mismo podrá efectuarse en el día hábil bancario inmediato posterior o por depósito bancario y/o transferencia en la cuenta corriente de titularidad de <b>nominator</b> N° ........................................del Banco .....................................CBU ............................................................</p>
            <p>2. En caso de mora, acepto que a partir de la fecha de mora y hasta la fecha del efectivo pago, se establezca un interés punitorio además del compensatorio fijado equivalente al 50% del mismo.</p>
            <p>3. Otorgo el mandato irrevocable al ACREEDOR a fin de que impute unilateralmente los montos dinerarios recibidos aplicándose en el siguiente orden: Gastos administrativos, impuestos, intereses y eventual saldo de capital.</p>
            <p>4. La falta de pago total y/o parcial de una cualesquiera de las cuotas pactadas, ya sea por capital o intereses en las fechas y los plazos convenidos, producirán la mora automáticamente de pleno derecho, sin necesidad de requerimiento judicial o extrajudicial alguno, caducando en consecuencia todos los plazos otorgados y haciéndose inmediatamente exigible la totalidad de la deuda.</p>
            <p>5. Los pagos parciales o entregas de dinero a cuenta no implicarán en ningún caso novación de las deuda ni concesión de quita o espera de ningún tipo tanto de la obligación resumida por el presente como de las acciones judiciales emergentes, aún en caso que tales pagos o entregas fueran posteriores a la deducción de la demanda la que seguirá su curso siendo computables dichos pagos parciales o entregas de dinero a cuenta en el momento de practicarse la correspondiente liquidación.</p>
            <p>6. En caso de mora el ACREEDOR tendrá derecho a cobrar el capital íntegro con más los intereses moratorios y punitorios determinados en las CONDICIONES GENERALES, las costas del pleito y todos los gastos y honorarios originados por la cobranza del crédito.</p>
            <p>7. El ACREEDOR podrá optar en caso de mora o caducidad de plazos por ejecutar judicialmente el presente en los términos Libro 3o - TITULO II – Juicio Ejecutivo – Ley XII no 27 - o por el procedimiento ordinario establecido en el art. 321 del mismo cuerpo legal.</p>
            <p>8. Todos los impuestos, tasas y gastos que resulten aplicables ahora o en el futuro, al crédito, su cumplimiento o ejecución o garantías serán a mi cargo incluido el impuesto al valor agregado sobre intereses y el cargo con el servicio de verificación de antecedentes y control de crédito.</p>
            <p>9. El/los codeudor/ es que suscriben el presente asume/n su responsabilidad en calidad de liso/ s, llano/s y principal/es pagador/es, en codeudor solidario, liso, llano y principal pagador del DEUDOR por todas las obligaciones principales y accesorias, directas, indirectas y eventuales, emergentes del presente contrato, que declaran conocer y aceptar en todas sus partes y constituyen domicilio especial en ...................................................................................................................; El/ los fiadores, declaran que sus datos personales son: ...............................................................................y que aceptan hacerlo en las condiciones que establecen los arts. 827,828 y 1591 del Código Civil y Comercial de la Nación.</p>
            <p>10. Declaro, estar en pleno ejercicio de los derechos civiles, no tener embargos ni gravámenes sobre sueldos u otros ingresos, conocer las normas de este crédito y no falsear u omitir dato alguno. Autorizo asimismo al ACREEDOR y al Banco Central de la República Argentina o quien esta institución designe a verificar la corrección de la información suministrada. Manifiesto además expresamente que, previo a solicitar éste crédito me he asesorado debidamente y he tenido en cuenta la eventualidad de una futura alteración de las variables económicas del país y sus consecuencias en el repago del mismo al ACREEDOR, por lo que asumo dichas consecuencias y, en el caso de ocurrir tal circunstancia en el futuro se estará a lo convenido en el presente, renunciando a invocar la teoría de la imprevisión, onerosidad sobreviniente o cualquier otra causal eximente.</p>
            <p>11. Acepto que el ACREEDOR en forma expresa que el presente, toda documentación anexa y/o los derechos que el mismo establece en favor del ACREEDOR puedan ser cedidos por éste como posición contractual y/o cesión de derechos a terceros debiendo poner en mi conocimiento tal cesión. </p>
            <p>12. Declaro bajo juramento que nominator me ha notificado y en consecuencia me ha informado previamente sobre el contenido de todos los incisos del Artículo 5° de la Ley de Habeas Data (N° 25.326), motivo por el cual es de mi conocimiento que mis datos personales relacionados con la presente operación crediticia que estoy concertando en este acto en mi carácter de deudor/ codeudor/ fiador / garante solidario, según corresponda, serán inmediatamente registrados en la base de datos de la referida entidad y compartidas con empresas autorizadas de informes y financieras en las nominator es o sea cliente usuaria, socia o asociada ahora y/o en el futuro, las cuales suministran información comercial relativa a la solvencia económica y al crédito de las personas, a sus clientes, usuarios, socios o asociados (destinatarios de la información) con la finalidad que éstos puedan evaluar y decidir sobre eventuales otorgamientos de créditos y otros productos financieros. Por todo lo expuesto presto expreso CONSENTIMIENTO para que mis datos y su tratamiento y mi eventual incumplimiento de la obligación contraída sean registrados en las bases, créditos y compartidas con las empresas autorizadas de informes comerciales antes indicadas. En este último supuesto, autorizo en forma amplia e irrevocable a nominator a efectuar por sí o por terceros todas las gestiones necesarias para obtener el recupero del crédito en caso de mora. Ello incluye entre otros llamados telefónicos visitas y comunicaciones escritas al deudor fiadores garantes y/o codeudores, en los domicilios y/o lugares en que ellos puedan ser efectivamente ubicados, comunicación de la situación de mora y datos personales de los morosos a las empresas y/o entidades oficiales o privadas que controlan y /o registran el cumplimiento de préstamos en el sistema financiero, liberando a nominator de cualquier responsabilidad al respecto y por el uso que dichos terceros hagan de la información suministrada. </p>
            <p>13. El otorgamiento del crédito mediante el sistema operativo financiero proporcionado por nominator constituirá la aceptación y consentimiento a la efectiva y plena celebración del presente contrato, su inicio, validez e integridad, considerándose a nominator (El ACREEDOR) como parte integrante del mismo. </p>
            <p>14. Autorizo a nominator para que, en caso de 5801 incumplimiento en el pago de la operación N° .................. trabe el embargo de mis haberes, renunciando, expresamente por lo tanto a los beneficios que otorga el Decreto 6.754/43 ratificado por la ley 13.894, como así mismo de cualquier Ley Provincial que otorgue iguales o similares beneficios a los concedidos por la citada legislación Nacional. La renuncia que por este acto se efectúa es comprensiva del capital, intereses que pudiere devengar dicha suma, sean tanto moratorios, como así también por los gastos, honorarios y aportes en carácter judicial o extrajudicial emergentes del incumplimiento de la obligación.</p>
            <p>15. nominator está debidamente autorizado a contratar por cuenta y orden del suscripto, un seguro de vida, cuyos costos declara conocer y aceptar y cuyas primas se debitaran con el pago de la operación. El titular reviste la condición de asegurado y nominator de beneficiario, consintiendo el primero de manera irrevocable que la indemnización por las contingencias antes descriptas estará afectada a la cancelación de los saldos adeudados por el TITULAR a nominator a la fecha del siniestro. Los causahabientes del beneficiario deberán comunicar fehacientemente a nominator, dentro del tercer día de tomado conocimiento, la ocurrencia del siniestro a fin de proceder a su liquidación, acompañando el certificado de defunción correspondiente. </p>
            <p>16. A todos los efectos legales del presente las partes presto consentimiento a que la jurisdicción y competencia correspondan a los Tribunales Ordinarios Provinciales de la Ciudad de Posadas, Misiones, con renuncia expresa a toda otra jurisdicción fuero o competencia que les pudieran corresponder, constituyendo las partes domicilio en los arriba indicados. </p>
            <p>En lugar y fecha indicados, se firman ....... ejemplares del mismo tenor del presente y sus anexos.</p>
            <br />
            <br />
            <br />
            <br />
            <br />
            <?php if (!isset($email)) : ?>
                <table style="width: 100%;" class="text-center">
                    <tr>
                        <td>
                            <p>....................................<br />Firma del Deudor</p>
                            <p><span class="border-botton"><b><?= $client['client_firstname'] . ' ' . $client['client_lastname'] ?></b></span><br />Deudor Aclaración</p>
                            <p><span><b><?= money_formating($client['client_doc'], TRUE, FALSE) ?></b></span><br />Deudor Documento</p>
                        </td>
                        <td>
                            <p>....................................<br />Firma del Codeudor</p>
                            <p>....................................<br />Codeudor Aclaración</p>
                            <p>....................................<br />Codeudor Documento</p>
                        </td>
                    </tr>
                </table>
            <?php endif; ?>
        </div><!-- /.row -->
    </div><!-- /.row -->
    <div class="row visible-print" style="page-break-before: always;">
        <!-- <div class="row " style="page-break-before: always;"> -->
        <div class="col-xs-12 text-center">
            <h2 class="page-header">
                ANEXO I
            </h2>
        </div>
    </div>
    <div class="row padding visible-print">
        <!-- <div class="row padding "> -->
        <div class="col-sm-12 text-justify">
            <!-- <pre><?= print_r([$edit, $pack, $period], true) ?></pre> -->
            <p>1. <b>GASTOS E IMPUESTOS:</b> Serán a cargo del TITULAR todo gasto, cargo, arancel, impuesto o tasa relacionada directa o indirectamente con los Servicios que el TITULAR solicita a nominator</p>
            <p>1.1. Sellado de ley: <?= money_formating($pack['pack_expenses']) ?></p>
            <p>1.2. Seguro de vida: <?= $pack['pack_secure'] ?> %</p>
            <p>1.3. IVA sobre interés: <?= number_format($pack['pack_iva'], 2, ',', '.') ?> %</p>
            <p>1.5. Compensatorios (T.E.M): <?= $pack['pack_commision'] ?> %</p>
            <p>1.5. Tasa de interés efectiva anual: <?= number_format(round($pack['pack_commision'] * 12, 5), 5, ',', '.') ?> %</p>
            <p>1.6. Punitorios: 50% de lo correspondiente a los compensatorios, en caso de mora</p>
            <p>2. <b>DATOS DEL PRESTAMO:</b></p>
            <p>2.1. Monto solicitado: <?= money_formating($edit['clientpack_price']) ?></p>
            <p>2.2. Cantidad de cuotas: <?= $edit['clientpack_sessions'] ?>.</p>
            <p>2.3. Importe de las cuotas: <?= money_formating($edit['clientpack_sessions_price']) ?></p>
            <p>2.4. Vencimiento del primer pago: <?= date('d-m-Y', strtotime($period['clientperiod_date'])) ?></p>
            <p>2.5. Total de intereses a pagar: <?= money_formating(round($edit['clientpack_final'] - $edit['clientpack_price'], 2)) ?></p>
            <p>2.6. Monto a devolver: <?= money_formating($edit['clientpack_final']) ?></p>
            <p>2.8. Sistema de amortización del capital y cancelación de los intereses: Directo</p>
            <p>2.7. Vencimiento de los restantes pagos los días (traer día de vencimiento) de cada mes, o el inmediato posterior hábil en caso de no serlo</p>
        </div>
    </div>
    <hr />
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