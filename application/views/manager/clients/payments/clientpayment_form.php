<section class="content-header">
  <h1><i class="fa fa-tags"></i> Pago <?= (!empty($client) ? ' de ' . $client['client_firstname'] . ' ' . $client['client_lastname'] : '') ?></h1>
  <ol class="breadcrumb">
    <li class=""><a href="<?= !empty($client) ? site_url('clientperiods/all') : site_url('clientperiods/getpaid') ?>"><i class="fa fa-home"></i> <?= !empty($client) ? 'Clients' : 'Cobranza' ?></a></li>
  </ol>
</section>
<section class="content package_form">
  <?php if (!empty($client)) : ?>
    <?= $this->load->view('manager/clients/client_menu', array('client' => $client, 'parent' => !empty($parent) ? $parent : false, 'active' => 'client_payment'), TRUE); ?>
  <?php endif; ?>

  <div class="box">
    <?php if (!empty($client)) : ?>
      <div class="box-header with-border">
        <h3 class="box-title"><?= isset($edit) ? 'Editar Pago' : 'Crear Pago' ?> <?= ' de ' . $client['client_firstname'] . ' ' . $client['client_lastname'] ?></h3>
      </div><!-- /.box-header -->
    <?php endif; ?>
    <div class="box-body">
      <form class="form" action="" id="payment" method="post" autocomplete="off">
        <input name="pay_id" id="pay_id" type="hidden" value="<?= (isset($edit) && !empty($edit['pay_id'])) ? $edit['pay_id'] : '' ?>" />
        <input name="pay_lat" id="pay_lat" type="hidden" value="<?= (isset($edit) && !empty($edit['pay_lat'])) ? $edit['pay_lat'] : '' ?>" />
        <input name="pay_lng" id="pay_lng" type="hidden" value="<?= (isset($edit) && !empty($edit['pay_lng'])) ? $edit['pay_lng'] : '' ?>" />
        <input name="sign" id="sign" type="hidden" />

        <?php if (!empty($periods)) : ?>

          <div class="row">
            <div class="col-sm-6">
              <div class="custom-control custom-checbox">
                <!-- <input type="checkbox" onclick="app.sellectAll()" class="custom-control-input payment-all"> -->
                <?php if (count($periods) > 1) : ?>
                  <button class="btn btn-warning" id="selectOrUnselectAllClientPeriods" type="button" onclick="app.selectAll()"><i class="fa fa-check-square-o"></i> <span class="hidden-xs">Seleccionar todas</span></button>
                <?php endif; ?>
                <input type="hidden" id="isSimplePayment" name="isSimplePayment" value="0" />
                <button class="btn btn-warning simplePayment" type="button" onclick="app.setSimplifiedPayment()"><i class="fa fa-square-o"></i> <span class="hidden-xs">Pago simplificado</span></button>
              </div>
            </div>
          </div>
          <hr>
          <h4 class="simplified_payment_alert" style="display:none">Este pago se marco como <b>simplificado</b>; el monto de pago será exactamente el de la deuda documentada seleccionada y por el medio de pago efectivo.</h4>
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
          <div class="row">
            <div class="col-sm-12">
              <h4>Seleccione la fecha sobre la que se registra esta cobranza</h4>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-3">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                <input class="form-control pay_date" name="pay_date" id="pay_date" type="text" value="<?= (isset($edit) && !empty($edit['pay_date'])) ? date('d-m-Y G:i', strtotime($edit['pay_date'])) : date('d-m-Y G:i') ?>" />
              </div>
            </div>
          </div>
          <br>
          <div class="row">
            <div class="col-sm-4">
              <div class="form-group">
                <label>Total de deuda documentada</label>
                <div class="input-group">
                  <input id="total_client_periods_amount" readonly style="border: 0px" class="form-control" type="text" value="<?= money_formating($totalClientPeriodsAmount) ?>" />
                </div>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label>Total de deuda documentada seleccionada</label>
                <div class="input-group">
                  <input readonly style="border: 0px" class="form-control" type="text" id="total_selected_client_period_amount" value="<?= money_formating(0) ?>" />
                </div>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label>Total a pagar</label>
                <div class="input-group">
                  <input readonly style="border: 0px" class="form-control" type="text" id="total_amount_to_pay" value="<?= money_formating(0) ?>" />
                </div>
              </div>
            </div>
          </div>
          <hr>
          <div class="accordion" id="clientAccordion">
            <?php foreach ($clientperiodsByClient as $clientId => $groupedClient) :
              $clientTotalPeriod = 0;
              $clientTotalAmount = 0;
            ?>
              <div class="card">
                <div class="card-header" id="heading_<?= $clientId ?>">
                  <h5 class="mb-0">
                    <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapse_<?= $clientId ?>" aria-expanded="true" aria-controls="collapse_<?= $clientId ?>">
                      <b><?= $groupedClient[0]['client_firstname'] . ' (' . $groupedClient[0]['client_doc'] . ')' . " - Deuda documentada total: " . money_formating($totalClientPeriodsAmountByClient[$clientId]) ?></b>
                    </button>
                  </h5>
                </div>
                <div id="collapse_<?= $clientId ?>" class="collapse <?= count($clientperiodsByClient) == 1 ? 'show' : '' ?>" aria-labelledby="heading_<?= $clientId ?>" data-parent="#clientAccordion">
                  <div class="card-body">
                    <div class="row" style="padding-left:2em">
                      <div class="col-sm-12">
                        <h4>Seleccione la deuda documentada a liquidar de <?= $groupedClient[0]['client_firstname'] . ' (' . $groupedClient[0]['client_doc'] . ')' ?></h4>
                      </div>
                    </div>
                    <div class="row" style="padding-left:3em">
                      <div class="col-sm-2">
                        <b>Deuda documentada</b>
                      </div>
                      <div class="col-sm-10">
                        <div class="row">
                          <div class="col-sm-3 hidden">
                          </div>
                          <div class="text-center col-sm-1">
                            <b>Cuota</b>
                          </div>
                          <div class="text-center col-sm-1">
                            <b>Fecha</b>
                          </div>
                          <div class="text-right col-sm-2">
                            <b>Monto</b>
                          </div>
                          <!-- <div class="text-right col-sm-2">
                            <b>Capital</b>
                          </div>
                          <div class="text-right col-sm-2">
                            <b>Interés 1</b>
                          </div> -->
                          <div class="text-right col-sm-2">
                            <b>Vencimiento</b>
                          </div>
                          <div class="text-right col-sm-2">
                            <b>Punitorios</b>
                          </div>
                          <div class="text-right col-sm-3">
                            <b>Total</b>
                          </div>
                        </div>
                      </div>
                    </div>
                    <?php foreach ($groupedClient as $per) : ?>
                      <?php $clientTotalPeriod += floatval((isset($edit) && !empty($edit['pay_clientperiod'])) ? $per['clientperiod_amount'] : 0); ?>
                      <?php $clientTotalAmount += floatval((isset($edit) && !empty($edit['pay_clientperiod'])) ? $edit['pay_amount'] : 0); ?>
                      <div class="row " style="padding-left:3em">
                        <div class="col-sm-2">
                          <div class="custom-control custom-checkbox">
                            <label class="custom-control-label <?= (isset($edit) && !empty($edit['pay_clientperiod'])) ? 'hidden' : '' ?>" for="_<?= $per['clientperiod_id'] ?>">
                              <input type="checkbox" <?= (isset($edit) && !empty($edit['pay_clientperiod'])) ? 'checked' : '' ?> onclick="app.refreshPayment('_<?= $per['clientperiod_id'] ?>', true)" class="custom-control-input payment-period" data-clientfirstname="<?= $per['client_firstname'] ?>" data-amount="<?= $per['clientperiod_amount'] ?>" data-date="<?= $per['clientperiod_date'] . (strpos($per['clientperiod_date'], ':') === false ? ' 00:00:00' : '') ?>" data-date2="<?= $per['clientperiod_date_2'] . (strpos($per['clientperiod_date_2'], ':') === false ? ' 00:00:00' : '') ?>" data-daytask="<?= $per['pack_daytask'] ?>" data-iva="0" data-discount="<?= isset($per['packdiscount_value']) ? $per['packdiscount_value'] : 0 ?>" data-interest="<?= $per['clientperiod_amountinterest'] ?>" data-interest2="<?= $per['clientperiod_amountinterest_2'] ?>" data-clientid="<?= $clientId ?>" value="<?= $per['clientperiod_amount'] ?>" name="client[<?= $per['client_id'] ?>][clientperiod][<?= $per['clientperiod_id'] ?>][checked]" id="_<?= $per['clientperiod_id'] ?>">
                              <input type="hidden" value="<?= $per['clientpack_title'] ?>" name="client[<?= $per['client_id'] ?>][clientperiod][<?= $per['clientperiod_id'] ?>][packtitle]">
                              <input type="hidden" value="<?= $per['clientperiod_packperiod'] ?>" name="client[<?= $per['client_id'] ?>][clientperiod][<?= $per['clientperiod_id'] ?>][packperiod]">
                              <input type="hidden" value="<?= (isset($edit) && !empty($edit['pay_clientperiod'])) ? $edit['pay_daytask'] : '0' ?>" name="client[<?= $per['client_id'] ?>][clientperiod][<?= $per['clientperiod_id'] ?>][pay_daytask]" id="_<?= $per['clientperiod_id'] ?>_task">
                              <input type="hidden" value="<?= $per['clientperiod_amount'] ?>" name="client[<?= $per['client_id'] ?>][clientperiod][<?= $per['clientperiod_id'] ?>][period_amount]">
                              <?= (empty($client) ? $per['client_firstname'] . ' (' . $per['client_doc'] . ') ' : '') . $per['clientpack_title'] ?>
                            </label>
                          </div>
                        </div>
                        <div class="col-sm-10">
                          <div class="row">
                            <div class="text-center col-sm-1">
                              <b><?= $per['clientperiod_packperiod'] ?></b>
                            </div>
                            <div class="text-center col-sm-1">
                              <b><?= date('d-m-Y', strtotime($per['clientperiod_date_2'])) ?></b>
                            </div>
                            <div class="col-sm-3 hidden">
                              <div class="form-group">
                                <label>Descuento</label>
                                <div class="input-group">
                                  <span class="input-group-addon">$</span>
                                  <input disabled class="form-control" name="client[<?= $per['client_id'] ?>][clientperiod][<?= $per['clientperiod_id'] ?>][pay_discount]" id="_<?= $per['clientperiod_id'] ?>_pay_discount" type="text" value="<?= (isset($edit) && !empty($edit['pay_clientperiod'])) ? $edit['pay_discount'] : '0' ?>" />
                                </div>
                              </div>
                              <input class="form-control" name="client[<?= $per['client_id'] ?>][clientperiod][<?= $per['clientperiod_id'] ?>][pay_discount]" id="_<?= $per['clientperiod_id'] ?>_pay_discount_h" value=" <?= (isset($edit) && !empty($edit['pay_clientperiod'])) ? $edit['pay_discount'] : '0' ?>" type="hidden" />
                            </div>
                            <!-- <div class="text-right col-sm-2">
                              <b><?= "$ " . money_formating($per['clientperiod_amount'], false, false) ?></b>
                            </div> -->
                            <div class="text-right col-sm-2">
                              <b><?= "$ " . money_formating($per['clientperiod_amountcapital']+$per['clientperiod_amountinterest'], false, false) ?></b>
                            </div>
                            <!-- <div class="text-right col-sm-2">
                              <b><?= "$ " . money_formating($per['clientperiod_amountcapital'], false, false) ?></b>
                            </div>
                            <div class="text-right col-sm-2">
                              <b><?= "$ " . money_formating($per['clientperiod_amountinterest'], false, false) ?></b>
                            </div> -->
                            <div class="text-right col-sm-2">
                              <b><?= "$ " . money_formating($per['clientperiod_amountinterest_2'], false, false) ?></b>
                            </div>
                            <div id="clientperiod_<?= $per['clientperiod_id'] ?>_daytask" class="text-right col-sm-2">
                              <b><?= "$ 0,00" ?></b>
                            </div>
                            <div id="clientperiod_<?= $per['clientperiod_id'] ?>_total" class="text-right col-sm-3">
                              <b><?= "$ 0,00" ?></b>
                            </div>
                          </div>
                        </div>
                      </div>
                    <?php endforeach; ?>
                    <div class="row">
                      <div class="col-sm-10">
                      </div>
                      <div class="col-sm-2">
                        <div class="form-group">
                          <label>Total cuotas</label>
                          <div class="input-group">
                            <input readonly style="border: 0px" class="form-control" id="client_<?= $clientId ?>_clientperiod_amount_tot" type="text" value="<?= money_formating(0) ?>" />
                          </div>
                        </div>
                      </div>
                    </div>
                    <hr>
                    <div class="row" style="padding-left:2em">
                      <div class="col-sm-12">
                        <h4 class="payment_instruction">Seleccione los medios de pago a emplear</h4>
                      </div>
                    </div>
                    <div class="payment_details">
                      <div class="row" style="padding-left:2em">
                        <div class="col-sm-3">
                          <div class="form-group">
                            <label></label>
                            <div class="input-group">
                              <button type="button" class="btn btn-primary" onclick="app.showPaymentModal(<?= $clientId ?>); return false;"> <span class="fa fa-plus"></span> Nuevo</button>
                            </div>
                          </div>
                        </div>
                        <div class="col-sm-4">
                        </div>
                        <div class="col-sm-3">
                          <div class="form-group">
                            <label>Monto Total</label>
                            <div class="input-group">
                              <input readonly style="border: 0px" class="" id="client_<?= $clientId ?>_pay_amount_tot" type="text" value="<?= money_formating($clientTotalAmount) ?>" />
                            </div>
                          </div>
                        </div>
                      </div>
                      <div id="client_<?= $clientId ?>_payments" style="padding-left:3em">
                        <div class="list-group-item hidden-xs">
                          <div class="row">
                            <div class="col-sm-6">
                              <label>Descripción</label>
                            </div>
                            <div class="col-sm-3 text-center">
                              <label>Monto</label>
                            </div>
                            <div class="col-sm-1">
                              <label>Acciones</label>
                            </div>
                          </div>
                        </div>
                      </div>
                      <hr>
                      <div class="row">
                        <div class="col-sm-3">
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-sm-8" style="padding-left:3em">
                          <label>Observación</label>
                          <input class="form-control" id="client[<?= $clientId ?>][pay_description]" name="client[<?= $clientId ?>][pay_description]" type="text" value="" placeholder="Ingrese una observación si la hubiere" />
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-sm-3">&nbsp;</div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
          <?php if (!isset($edit)) : ?>
            <div class="row">
              <div class="col-sm-9">
                <div class="col-sm-2">&nbsp;</div>
                <div class="col-sm-8">
                  <div class="signature-pad">
                    <label>Firma del cliente</label>
                    <div class="signature-pad--body _sign">
                      <canvas id="signature" width="200" height="200" style="touch-action: none;"></canvas>
                    </div>
                    <div class="signature-pad--footer">
                      <div class="signature-pad--actions">
                        <button type="button" class="button clear" id="clear">Borrar</button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          <?php endif; ?>

        <?php else : ?>
          <div class="alert alert-success">
            No posee cuotas para pagar
          </div>
        <?php endif; ?>
        <div class="box-footer">
          <div class="row">
            <div class="col-sm-12">
              <div class="col-sm-6">
                <?php if (!empty($periods)) : ?>
                  <button id="submitter" type="button" class="btn btn-primary disabled"><?= isset($edit) ? lang('Save') : lang('Add') ?></button>
                <?php endif; ?>
                <a class="btn" href="<?= empty($client) ? (($activesidebar === 'periods_today') ? site_url('clientperiods/getpaid') : site_url('payments/all')) : site_url('clientperiods/all/') . $client['client_id'] ?>"><?= lang('Cancel') ?></a>
              </div>
            </div>
          </div>
        </div>
        <div class="modal modal-primary fade" id="add-payment">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title">Agregar pago</h4>
              </div>
              <div class="modal-body">
                <div class="container-fluid">
                  <div class="row">
                    <div class="col-sm-8">
                      <div class="form-group">
                        <label>Medio de Pago</label>
                        <select id="pay_type" class="form-control" onchange="app.refreshModal()">
                          <option>Efectivo</option>
                          <option>BANCO CORRIENTES - PAGOS.LINEA.nominator</option>
                          <option>BANCO NACION - PAGOS.LINEA.NACION</option>
                          <option>MERCADO PAGO - PAGOS.LINEA.MP</option>
                          <option>BANCO MACRO - PAGOS.nominator.LINEA</option>
                          <option>Cheque</option>
                          <option>Cuenta Corriente</option>
                          <option>Otro</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-sm-3 hidden">
                      <div class="form-group">
                        <label>Descuento</label>
                        <div class="input-group">
                          <span class="input-group-addon">$</span>
                          <input disabled class="form-control" id="pay_discount" type="text" value="0" />
                        </div>
                      </div>
                      <input class="form-control" id="pay_discount_h" value="0" type="hidden" />
                    </div>
                    <div class="col-sm-4">
                      <div class="form-group">
                        <label>Monto</label>
                        <div class="input-group">
                          <span class="input-group-addon">$</span>
                          <input required class="form-control" id="pay_amount" type="text" value="0" />
                        </div>
                      </div>
                      <input class="form-control" id="pay_daytask" value="0" type="hidden" />
                    </div>
                  </div>
                  <div class="row">
                    <div id="div_payment_bank" hidden="True">
                      <div class="col-sm-8">
                        <div class="form-group">
                          <label>Banco</label>
                          <div class="input-group">
                            <span class="input-group-addon"><span class="fa fa-file-text"></span></span>
                            <input id="payment_bank_cod" type="hidden" value="" title="El campo banco no puede estar vacío">
                            <input class="form-control header-live-search" id="payment_bank" type="text" value="" placeholder="Banco">
                            <span class="input-group-addon" onclick="$('#payment_bank').removeAttr('readonly'); $('#payment_bank').val(''); $('#payment_bank_cod').val('');"><span class="fa fa-remove"></span></span>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div id="div_payment_expiration_date" hidden="True">
                      <div class="col-sm-4">
                        <div class="form-group">
                          <label>Vencimiento de cheque</label>
                          <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            <div style="color: black;">
                              <input placeholder="DD-MM-YYYY" class="form-control pay_date" id="payment_expiration_date" type="text" value="" title="El campo fecha de vencimiento de cheque no puede estar vacío." />
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div id="div_payment_number" hidden="True">
                      <div class="col-sm-4">
                        <div class="form-group">
                          <label>N° de cheque</label>
                          <input placeholder="Ingrese número de cheque" class="form-control" id="payment_number" type="text" pattern="\d*" maxlength="8" value="" title="El campo número de cheque debe ser un número y no puede estar vacío" onfocusout="app.fillCheckNumber('payment_number')" />
                        </div>
                      </div>
                    </div>
                    <div id="div_payment_cuit" hidden="True">
                      <div class="col-sm-4">
                        <div class="form-group">
                          <label>CUIT del emisor</label>
                          <input placeholder="Ingrese número de CUIT" class="form-control" id="payment_cuit" type="text" pattern="\d*" maxlength="11" value="" title="El campo CUIT debe ser un número valido de 11 digitos, no puede estar vacío y debe ser un cuit valido." />
                        </div>
                      </div>
                    </div>
                    <div id="div_payment_clearing" hidden="True">
                      <div class="col-sm-3">
                        <div class="form-group">
                          <label>Clearing</label>
                          <input placeholder="##" class="form-control" id="payment_clearing" type="text" pattern="\d*" maxlength="11" value="" title="El campo Clearing debe ser un número y no puede estar vacío" />
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div id="div_payment_transaction_number" hidden="True">
                      <div class="col-sm-4">
                        <div class="form-group">
                          <label>N° de transacción</label>
                          <div class="input-group">
                            <input class="form-control" placeholder="##" id="payment_transaction_number" type="text" value="" title="El campo número de transaccion debe ser un número y no puede estar vacío" />
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-outline" onClick="app.addPayment()">Agregar</button>
              </div>
            </div><!-- /.modal-content -->
          </div><!-- /.modal-dialog -->
        </div>
      </form>
    </div>
  </div>
</section>