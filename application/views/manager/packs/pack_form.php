<section class="content-header">
    <h1><i class="fa fa-tags"></i> Producto <?= (isset($edit) && !empty($edit['pack_name'])) ? $edit['pack_name'] : '' ?></h1>
    <ol class="breadcrumb">
        <li class=""><a href="<?= site_url('packs/all') ?>"><i class="fa fa-home"></i> Productos</a></li>
    </ol>
</section>
<section class="content package_form">
    <?php //= (!empty($edit)) ? $this->load->view('manager/packs/pack_menu', array('pack' => $edit, 'parent' => !empty($parent) ? $parent : false, 'active' => 'pack_form'), true) : ''; ?>
    <?php $active = isset($edit) && !empty($edit['pack_id']) ? $edit['pack_active'] : 1; ?>
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title"><?= isset($edit) ? ($active ? 'Editar Producto' : 'Producto') : 'Crear Producto' ?></h3>
        </div><!-- /.box-header -->
        <div class="box-body">
            <form class="form" action="" method="post" autocomplete="off">
                <input name="pack_id" type="hidden" value="<?= (isset($edit) && !empty($edit['pack_id'])) ? $edit['pack_id'] : '' ?>" />
                <input name="pack_active" type="hidden" value="<?= (isset($edit) && !empty($edit['pack_active'])) ? $edit['pack_active'] : '' ?>" />
                
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label>Titulo</label>
                            <input class="form-control" <?= $active ? '' : 'disabled="disabled"'; ?> required name="pack_name" type="text" value="<?= (isset($edit) && !empty($edit['pack_name'])) ? $edit['pack_name'] : '' ?>" onblur="String($(this).val($(this).val())).toUpperCase()" />
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label>Dias de Cuotas</label>
                            <input class="form-control" <?= $active ? '' : 'disabled="disabled"'; ?> required name="pack_type" type="number" value="<?= (isset($edit) && !empty($edit['pack_type'])) ? $edit['pack_type'] : '' ?>" />
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label>Número de Cuotas</label>
                            <div class="input-group">
                                <span class="input-group-addon">$</span>
                                <input class="form-control" <?= $active ? '' : 'disabled="disabled"'; ?> required name="pack_session_max" type="number" value="<?= (isset($edit) && !empty($edit['pack_session_max'])) ? $edit['pack_session_max'] : '' ?>" />
                            </div>
                        </div>
                    </div>
                </div>

                <input name="pack_price" type="hidden" value="<?= (isset($edit) && !empty($edit['pack_price']) && $edit['pack_price'] > 0) ? $edit['pack_price'] : '9999999.99' ?>" />
                <input name="pack_session_min" type="hidden" value="<?= (isset($edit) && !empty($edit['pack_session_min']) && $edit['pack_session_min']) ? $edit['pack_session_min'] : '1' ?>" />
                
                <div class="row">
                    <div class="col-sm-2">
                        <div class="form-group">
                            <span class="d-inline-block" tabindex="0" data-toggle="tooltip" title="La Taza esta vinculada al periodo del producto">
                                <span class="badge"><i class="fa fa-info"></i></span>
                                <label>Interés 1° Vto</label>
                            </span>
                            <div class="input-group">
                                <span class="input-group-addon">%</span>
                                <input class="form-control" <?= $active ? '' : 'disabled="disabled"'; ?> required name="pack_commision" type="number" step="0.00001" value="<?= (isset($edit) && !empty($edit['pack_commision'])) ? $edit['pack_commision'] : '' ?>" />
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <span class="d-inline-block" tabindex="0" data-toggle="tooltip" title="La Taza esta vinculada al periodo del producto">
                                <span class="badge"><i class="fa fa-info"></i></span>
                                <label>Interés 2° Vto</label>
                            </span>
                            <div class="input-group">
                                <span class="input-group-addon">%</span>
                                <input class="form-control" <?= $active ? '' : 'disabled="disabled"'; ?> required name="pack_commision_2" type="number" step="0.00001" value="<?= (isset($edit) && !empty($edit['pack_commision_2'])) ? $edit['pack_commision_2'] : '' ?>" />
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label>Gastos Administrativos</label>
                            <div class="input-group">
                                <span class="input-group-addon">$</span>
                                <input class="form-control" <?= $active ? '' : 'disabled="disabled"'; ?> required name="pack_expenses" type="number" step="0.01" value="<?= (isset($edit) && !empty($edit['pack_expenses'])) ? $edit['pack_expenses'] : '' ?>" />
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group ">
                            <span class="d-inline-block" tabindex="0" data-toggle="tooltip" title="Tasa punitoria por mora, DIARIA">
                                <span class="badge"><i class="fa fa-info"></i></span>
                                <label>Tasa Punitoria</label>
                            </span>
                            <div class="input-group">
                                <span class="input-group-addon">%</span>
                                <input class="form-control" <?= $active ? '' : 'disabled="disabled"'; ?> name="pack_daytask" type="number" step="0.00001" value="<?= (isset($edit) && !empty($edit['pack_daytask'])) ? $edit['pack_daytask'] : '' ?>" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label>Descripción</label>
                            <textarea class="form-control" rows="1" <?= $active ? '' : 'disabled="disabled"'; ?> name="pack_description"><?= (isset($edit) && !empty($edit['pack_description'])) ? $edit['pack_description'] : '' ?></textarea>
                        </div>
                    </div>
                </div>

                <div id="discount-list" class="hidden">
                    <h4 class="dynamic-table-head">
                        Reglas - Descuento / Pago en término
                        <?php if ($active) : ?>
                            <a href="#" class="btn btn-primary btn-sm" title="Nuevo" onclick="app.addDiscount();"><i class="fa fa-plus"></i></a>
                        <?php endif; ?>
                    </h4>
                    <div class="list-group-item hidden-xs">
                        <div class="row">
                            <div class="col-sm-2">
                                <label>Descuento</label>
                            </div>
                            <div class="col-sm-2">
                                <label>Mín. Cta.</label>
                            </div>
                            <div class="col-sm-2">
                                <label>Máx. Cta.</label>
                            </div>
                            <div class="col-sm-6">
                                <label>Acciones</label>
                            </div>
                        </div>
                    </div>
                    <?php if (isset($edit['packdiscounts'])) : ?>
                        <?php foreach ($edit['packdiscounts'] as $key => $discount) : ?>
                            <div class="list-group-item billable_item position-relative" id="packdiscount_<?= $discount['packdiscount_id'] ?>">
                                <input type="hidden" name="packdiscounts[<?= $key ?>][packdiscount_id]" value="<?= (isset($edit) && !empty($discount['packdiscount_id'])) ? $discount['packdiscount_id'] : '' ?>" />
                                <div class="row">
                                    <div class="col-sm-2">
                                        <em class="hidden-sm hidden-md hidden-lg">Descuento: </em>
                                        <label><?= (isset($edit) && !empty($discount['packdiscount_value'])) ? money_formating($discount['packdiscount_value'], false, false) . ' %' : '0' ?></label>
                                        <input type="hidden" name="packdiscounts[<?= $key ?>][packdiscount_value]" value="<?= (isset($edit) && !empty($discount['packdiscount_value'])) ? $discount['packdiscount_value'] : '' ?>" />
                                    </div>
                                    <div class="col-sm-2">
                                        <em class="hidden-sm hidden-md hidden-lg">Mín. Cta.: </em>
                                        <label><?= (isset($edit) && !empty($discount['packdiscount_min_sessions'])) ? $discount['packdiscount_min_sessions'] : '1' ?></label>
                                        <input type="hidden" name="packdiscounts[<?= $key ?>][packdiscount_min_sessions]" value="<?= (isset($edit) && !empty($discount['packdiscount_min_sessions'])) ? $discount['packdiscount_min_sessions'] : '0' ?>" />
                                    </div>
                                    <div class="col-sm-2">
                                        <em class="hidden-sm hidden-md hidden-lg">Máx. Cta.: </em>
                                        <label><?= (isset($edit) && !empty($discount['packdiscount_max_sessions'])) ? $discount['packdiscount_max_sessions'] : '0' ?></label>
                                        <input type="hidden" name="packdiscounts[<?= $key ?>][packdiscount_max_sessions]" value="<?= (isset($edit) && !empty($discount['packdiscount_max_sessions'])) ? $discount['packdiscount_max_sessions'] : '0' ?>" />
                                    </div>
                                    <div class="col-sm-6">
                                        <?php if ($active) : ?>
                                            <a href="#" class="btn btn-warning btn-sm" title="Editar" onclick="app.editDiscount(<?= (isset($edit) && !empty($discount['packdiscount_id'])) ? $discount['packdiscount_id'] : '' ?>);"><i class="fa fa-edit"></i></a>
                                            <a href="#" class="btn btn-danger btn-sm" title="Eliminar" onclick="app.deleteDiscount(<?= (isset($edit) && !empty($discount['packdiscount_id'])) ? $discount['packdiscount_id'] : '' ?>);"><i class="fa fa-remove"></i></a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
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
                        <div class="col-sm-12">
                            <div class="col-sm-6">
                                <button class="btn btn-primary <?= $active ? '' : 'hidden'; ?>"><?= isset($edit) ? lang('Save') : lang('Add') ?></button>
                                <a class="btn" href="<?= site_url('packs/all') ?>"><?= lang('Cancel') ?></a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<div class="modal modal-primary fade" id="discount">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title">Regla - Descuento / Pago en término</h4>
            </div>
            <form id="discount_form" action="javascript:;" onsubmit="app.saveDiscount()" autocomplete="off">
                <div class="modal-body">
                    <input type="hidden" id="action" />
                    <div class=" row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label>Descuento</label>
                                <input class="form-control" id="packdiscount_value" type="text" />
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label>Mín. Cta.</label>
                                <input class="form-control" id="packdiscount_min_sessions" type="text" />
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label>Máx. Cta.</label>
                                <input class="form-control" id="packdiscount_max_sessions" type="text" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">Cancelar</button>
                    <button class="btn btn-outline" type="submit">Guardar</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>