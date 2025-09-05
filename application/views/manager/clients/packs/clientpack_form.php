<?php
if (!empty($clientpack_old)) {
    $clientpack_start = date('Y-m-d') . ' ' . date('G:i', strtotime($clientpack_old[(count($clientpack_old) - 1)]['clientpack_start']));
    $clientpack_start_setted = true;
} else {
    $clientpack_start = date('Y-m-d G:00:00');
}
?>
<section class="content-header">
    <h1><i class="fa fa-male"></i> Factura a - <?= $client['client_firstname'] . ' ' . $client['client_lastname'] ?></h1>
    <ol class="breadcrumb">
        <li class="active"><a href="<?= site_url('clientpacks/all/' . $client_id) ?>"><i class="fa fa-tags"></i> Productos del Cliente</a></li>
    </ol>
</section>
<section class="content client_form">
    <?= (!empty($client) && !empty($edit['clientpack_id'])) ? $this->load->view('manager/clients/client_menu', array('client' => $client, 'parent' => !empty($parent) ? $parent : false, 'active' => 'client_pack'), true) : ''; ?>
    <div class="box">
        <div class="box-header with-border">
            <? if (!empty($edit['clientpack_id'])) { ?>
            <h3 class="box-title">Editar Factura al Cliente</h3>
            <? } else { ?>
            <h3 class="box-title">Crear Factura al Cliente</h3>
            <? } ?>
        </div><!-- /.box-header -->
        <div class="box-body">
            <?php if ((count((array) $packages) > 0 || (isset($edit) && !empty($edit['clientpack_id'])))) : ?>
                <form id="clientproduct_form" class="form" action="" method="post" autocomplete="off">
                    <input name="clientpack_id" type="hidden" value="<?= (isset($edit) && !empty($edit['clientpack_id'])) ? $edit['clientpack_id'] : '' ?>" />
                    <input name="clientpack_client" type="hidden" value="<?= (!empty($client_id)) ? $client_id : '' ?>" />
                    <input name="clientpack_verify" id="clientpack_verify" type="hidden" value="" />

                    <?php if (!empty($edit['clientpack_id'])) : ?>
                        <div class="form-group">
                            <label>Vendedor</label>
                            <div>
                                <span class="fa fa-user"></span> <?= $edit['user_firstname'] . ' ' . $edit['user_lastname'] ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label>N° de Comprobante</label>
                                <input type="text" class="form-control" <?= $client['client_active'] ? '' : 'disabled' ?> name="clientpack_title" id="clientpack_title" value="<?= (isset($edit) && !empty($edit['clientpack_title'])) ? $edit['clientpack_title'] : '' ?>" onblur="$(this).val(String($(this).val()).toUpperCase())">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-5">
                            <div class="form-group">
                                <label>Condición de Venta</label>
                                <select required class="form-control" <?= (isset($edit) && !empty($edit['clientpack_id'])) ? "disabled" : ""; ?> id="clientpack_package" name="clientpack_package" onchange="app.packchanged(this)">
                                    <option></option>
                                    <?php foreach ($packages as $pack) :
                                        $maxcredit = $pack["pack_price"];
                                        $maxcredit = $maxcredit > 0 ? $maxcredit : 0;
                                    ?>
                                        <option <?= !empty($edit) && $pack["pack_id"] == $edit["clientpack_package"] ? "selected" : "" ?> value="<?= $pack["pack_id"] ?>" data-sessionsmin="<?= $pack["pack_session_min"] ?>" data-sessionsmax="<?= $pack["pack_session_max"] ?>" data-price="<?= $maxcredit ?>" data-type="<?= $pack["pack_type"] ?>" data-commision="<?= $pack["pack_commision"] ?>" data-commision_2="<?= $pack["pack_commision_2"] ?>" data-expenses="<?= $pack["pack_expenses"] ?>"><?= $pack["pack_name"] . (!empty($pack["pack_sessions"]) ? ' (' . $pack["pack_sessions"] . ')' : '') ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="hidden" name="clientpack_package" id="clientpack_package_hidden" value="<?= !empty($edit) ? $edit["clientpack_package"] : "" ?>">
                            </div>

                            <div class="form-group">
                                <label>Fecha 1era Cuota</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    <input class="form-control" readonly name="clientpack_start" id="clientpack_start" type="text" value="<?= (isset($edit) && !empty($edit['clientpack_start'])) ? date('d-m-Y G:i', strtotime($edit['clientpack_start'])) : date('d-m-Y G:i', strtotime($clientpack_start)) ?>" />
                                    <?php if (isset($edit) && !empty($edit['clientpack_id'])) : ?>
                                        <input name="clientpack_start" id="clientpack_start_hidden" type="hidden" value="<?= isset($edit) ? date('d-m-Y G:i', strtotime($edit['clientpack_start'])) : date('d-m-Y G:i', strtotime($clientpack_start)) ?>" />
                                    <?php endif; ?>
                                </div>
                                <p class="help-block">Hora de cuota tomada para ruta diaria de cobradores</p>
                            </div>
                            <div class="form-group">
                                <label>Termina</label>
                                <div class="input-group">
                                    <span class="input-group-addon">Aprox.</span>
                                    <input class="form-control" name="clientpack_end" readonly id="clientpack_end" type="text" value="<?= (isset($edit) && !empty($edit['clientpack_end'])) ? date('d-m-Y G:i', strtotime($edit['clientpack_end'])) : date('d-m-Y G:00', strtotime('+1 month')) ?>" />
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-sm-offset-1">
                            <div class="row">
                                <div class="col-sm-6 col-xs-6">
                                    <div class="form-group">
                                        <span class="d-inline-block" tabindex="0" data-toggle="tooltip" title="Proporcional al campo salario en perfil del cliente y el balance histórico">
                                            <span class="badge"><i class="fa fa-info"></i></span>
                                            <label>Precio</label>
                                        </span>
                                        <div class="input-group">
                                            <span class="input-group-addon">$</span>
                                            <input class="form-control" <?= (isset($edit) && !empty($edit['clientpack_id'])) ? "disabled" : ""; ?> name="clientpack_price" onchange="app.sessionsUpdate(this)" id="clientpack_price" type="text" value="<?= (isset($edit) && !empty($edit['clientpack_price'])) ? $edit['clientpack_price'] : '' ?>" />
                                            <?php if (isset($edit) && !empty($edit['clientpack_id'])) : ?>
                                                <input name="clientpack_price" id="clientpack_price_hidden" type="hidden" value="<?= $edit['clientpack_price'] ?>" />
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-xs-6">
                                    <div class="form-group">
                                        <label>Cuotas</label>
                                        <input class="form-control" <?= (isset($edit) && !empty($edit['clientpack_id'])) ? "disabled" : ""; ?> name="clientpack_sessions" onchange="app.sessionsUpdate(this)" id="clientpack_sessions" type="text" value="<?= (isset($edit) && !empty($edit['clientpack_sessions'])) ? $edit['clientpack_sessions'] : '' ?>" />
                                        <?php if (isset($edit) && !empty($edit['clientpack_id'])) : ?>
                                            <input name="clientpack_sessions" id="clientpack_sessions_hidden" type="hidden" value="<?= $edit['clientpack_sessions'] ?>" />
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-xs-6">
                                    <div class="form-group">
                                        <label>1° Vencimiento</label>
                                        <div class="input-group">
                                            <span class="input-group-addon">$</span>
                                            <input class="form-control" disabled name="clientpack_sessions_price" id="clientpack_sessions_price" type="text" value="<?= (isset($edit) && !empty($edit['clientpack_sessions_price'])) ? $edit['clientpack_sessions_price'] : '' ?>" />
                                            <input name="clientpack_sessions_price" id="clientpack_sessions_price_hidden" type="hidden" value="<?= isset($edit) ? $edit['clientpack_sessions_price'] : '' ?>" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-xs-6">
                                    <div class="form-group">
                                        <label>2° Vencimiento</label>
                                        <div class="input-group">
                                            <span class="input-group-addon">$</span>
                                            <input class="form-control" disabled name="clientpack_sessions_2_price" id="clientpack_sessions_2_price" type="text" value="<?= (isset($edit) && !empty($edit['clientpack_sessions_2_price'])) ? $edit['clientpack_sessions_2_price'] : '' ?>" />
                                            <input name="clientpack_sessions_2_price" id="clientpack_sessions_2_price_hidden" type="hidden" value="<?= isset($edit) ? $edit['clientpack_sessions_2_price'] : '' ?>" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Observaciones - Comentarios</label>
                        <textarea class="form-control" <?= $client['client_active'] ? '' : 'disabled' ?> name="clientpack_obs"><?= (isset($edit) && !empty($edit['clientpack_obs'])) ? $edit['clientpack_obs'] : '' ?></textarea>
                    </div>

                    <?php if (!empty($errors)) : ?>
                        <div class="clearfix">
                            <div class="alert alert-danger">
                                <?php foreach ($errors as $error) : ?>
                                    <?= $error ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="box-footer">
                        <div class="row">
                            <div class="col-sm-6">
                                <? if (empty($edit['clientpack_id'])) { ?>
                                <a onclick="app.verifyProduct()" class="btn btn-warning <?= $client['client_active'] ? '' : 'hidden' ?>">Verificar</a>
                                <? } ?>
                                <a onclick="app.onSubmit()" class="btn btn-primary <?= $client['client_active'] ? '' : 'hidden' ?>"><?= isset($edit) ? lang('Save') : lang('Crear') ?></a>
                                <a class="btn" href="<?= site_url('clientpacks/all/' . $client['client_id']) ?>"><?= lang('Cancel') ?></a>
                            </div>
                            <div class="col-sm-6 pull-right hidden">
                                <?php if (!empty($edit) && !empty($edit['clientpack_id']) && $this->session->userdata('user_rol') == 'super') : ?>
                                    <div class="form-group">
                                        <select class="form-control" name="clientpack_state">
                                            <option <?= $edit["clientpack_state"] == 1 ? "selected" : "" ?> value="1">Pendiente</option>
                                            <option <?= $edit["clientpack_state"] == 2 ? "selected" : "" ?> value="2">Autorizado</option>
                                            <option <?= $edit["clientpack_state"] == 3 ? "selected" : "" ?> value="3">Rechazado</option>
                                            <option <?= $edit["clientpack_state"] == 4 ? "selected" : "" ?> value="4">Judicializado</option>
                                            <option <?= $edit["clientpack_state"] == 5 ? "selected" : "" ?> value="5">Pendiente A Documentar</option>
                                            <option <?= $edit["clientpack_state"] == 6 ? "selected" : "" ?> value="6">Autorizado A Documentar</option>
                                        </select></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </form>
            <?php else : ?>
                <?php if ($judicialized) : ?>
                    <div class="alert alert-danger">El cliente posee créditos en estudio jurídico. Debe regularizar la situación para poder operar.<br>Contactar al estudio <b>Misiones Legales</b> sito en <b>Hipólito Irigoyen Nro. 2586</b> Posadas-Misiones Tel.- <b>0810-888-0198 / 376-4426354 / 376-4421148</b>.</div>
                <?php else : ?>
                    <div class="alert alert-danger">El prospecto no califica para ninguno de los cr&eacute;ditos disponibles.</div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</section>