<section class="content-header">
    <h1>
        <i class="fa fa-gears"></i> Configuración
    </h1>
</section>
<section class="content">
    <form class="form" action="" method="post" autocomplete="off">

        <div class="box">
            <div class="box-body">
                <input name="setting_id" type="hidden" value="<?= (isset($edit) && !empty($edit['setting_id'])) ? $edit['setting_id'] : '' ?>" />

                <div class="box-header with-border">
                    <h3 class="box-title">Datos de Local</h3>
                </div><!-- /.box-header -->
                <br>
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label>Razon social</label>
                            <input class="form-control" name="setting_data[name]" type="text" value="<?= (isset($edit) && !empty($edit['setting_data']) && !empty($edit['setting_data']['name'])) ? $edit['setting_data']['name'] : '' ?>" />
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label>Telefono</label>
                            <input class="form-control" name="setting_data[phone]" type="text" value="<?= (isset($edit) && !empty($edit['setting_data']) && !empty($edit['setting_data']['phone'])) ? $edit['setting_data']['phone'] : '' ?>" />
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label>Dirección</label>
                            <input class="form-control" name="setting_data[address]" type="text" value="<?= (isset($edit) && !empty($edit['setting_data']) && !empty($edit['setting_data']['address'])) ? $edit['setting_data']['address'] : '' ?>" />
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group ">
                            <label>Email</label>
                            <input class="form-control" name="setting_data[email]" type="text" value="<?= (isset($edit) && !empty($edit['setting_data']) && !empty($edit['setting_data']['email'])) ? $edit['setting_data']['email'] : '' ?>" />
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group ">
                            <label>Cuit</label>
                            <input class="form-control" name="setting_data[cuit]" type="text" value="<?= (isset($edit) && !empty($edit['setting_data']) && !empty($edit['setting_data']['cuit'])) ? $edit['setting_data']['cuit'] : '' ?>" />
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label>Fecha de inicio de actividades</label>
                            <input class="form-control" name="setting_data[activity_start_date]" type="date" value="<?= (isset($edit) && !empty($edit['setting_data']) && !empty($edit['setting_data']['activity_start_date'])) ? $edit['setting_data']['activity_start_date'] : '' ?>" />
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row <?= $this->session->userdata()['user_id'] != 1 ? 'hide' : '' ?>">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label>Paleta primaria</label>
                            <input class="form-control" name="setting_data[palette_primary]" type="text" value="<?= (isset($edit) && !empty($edit['setting_data']) && !empty($edit['setting_data']['palette_primary'])) ? $edit['setting_data']['palette_primary'] : '' ?>" />
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label>Paleta secundaria</label>
                            <input class="form-control" name="setting_data[palette_second]" type="text" value="<?= (isset($edit) && !empty($edit['setting_data']) && !empty($edit['setting_data']['palette_second'])) ? $edit['setting_data']['palette_second'] : '' ?>" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="box">
            <div class="box-body">
                <div class="box-header with-border">
                    <h3 class="box-title">Logo <?= (isset($edit) && !empty($edit['setting_data']) && !empty($edit['setting_data']['name'])) ? $edit['setting_data']['name'] : '' ?></h3>
                </div><!-- /.box-header -->
                <br />
                <div class="row">
                    <div class="col-sm-6">
                        <label>Logo Empresa</label>
                        <div class="form-group">
                            <input name="settings_logo" id="settings_logo" type="file">
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="box">
            <? if (!empty($errors)) { ?>
                <div class="clearfix">
                    <div class="alert alert-danger">
                        <?= $errors ?>
                    </div>
                </div>
            <? } ?>
            <div class="box-footer">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="col-sm-6">
                            <button class="btn btn-primary"><?= isset($edit) ? lang('Save') : lang('Add') ?></button>
                            <a class="btn" href="<?= site_url('manager') ?>"><?= lang('Cancel') ?></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</section>


<div class="modal modal-primary fade" id="credits-sms">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title">Carga de créditos SMS</h4>
            </div>
            <form action="javascript:;" onsubmit="app.addCreditSMS()" autocomplete="off">
                <div class="modal-body">
                    <div class=" row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label>Crédito</label>
                                <input class="form-control" id="credit" type="text" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-outline">Guardar</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>