<section class="content-header">
    <h1><i class="fa fa-bullhorn"></i> Regla para - <?= $pack['pack_name'] ?></h1>
    <ol class="breadcrumb">
        <li class="active"><a href="<?= site_url('packrules/all/' . $pack['pack_id']) ?>"><i class="fa fa-tags"></i> Reglas del Producto</a></li>
    </ol>
</section>
<section class="content client_form">
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title"><?= isset($edit) ? 'Editar Regla del Producto' : 'Asignar Regla al Producto' ?></h3>
        </div><!-- /.box-header -->
        <div class="box-body">
            <form class="form" action="" method="post" autocomplete="off">
                <input name="packrule_id" type="hidden" value="<?= (isset($edit) && !empty($edit['packrule_id'])) ? $edit['packrule_id'] : '' ?>" />
                <input name="packrule_pack" type="hidden" value="<?= (!empty($pack_id)) ? $pack_id : '' ?>" />
                <input name="rule_type" id="rule_type" type="hidden" value="" />
                <div class="row">
                    <div class="col-sm-4 col-xs-4">
                        <label for="packrule_rule">Regla</label>
                        <div class="form-group">
                            <select class="form-control" <?= (isset($edit) && !empty($edit['packrule_id']) ) ? "disabled" : ""; ?> name="packrule_rule" id="packrule_rule">
                                <option data_type=""></option>
                                <?php foreach ($rules as $rule): ?>
                                    <option <?= !empty($edit) && $rule["rule_id"] == $edit["packrule_rule"] ? "selected" : "" ?> value="<?= $rule["rule_id"] ?>" data-type="<?= $rule["rule_type"] ?>"><?= $rule['rule_name'] . ($rule['rule_type'] == 3 ? '' : " [" . ($rule['rule_type'] == '2' ? 'VMa' : 'VMi') . "]") ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-4 col-xs-4">
                        <label for="packrule_value">Valor</label>
                        <div class="form-group">
                            <input <?= (isset($edit) ? ($edit['rule_type'] == 3 ? 'disabled' : '') : 'disabled') ?> class="form-control" name="packrule_value" id="packrule_value" type="text" value="<?= (isset($edit) ? ($edit['rule_type'] == 3 ? '' : $edit['packrule_value']) : '') ?>" />
                        </div>
                    </div>
                    <div class="col-sm-4 col-xs-4">
                        <label for="packrule_boolean_yes">SI/NO</label>&nbsp;&nbsp;
                        <div class="form-group">
                            <input <?= (isset($edit) ? ($edit['rule_type'] != 3 ? 'disabled' : '') : 'disabled') ?> class="form-check-input" type="radio" name="packrule_value" id="packrule_boolean_yes" value="1" <?= (isset($edit) ? ($edit['rule_type'] != 3 ? '' : ($edit['packrule_value'] == 1 ? 'checked' : '')) : '') ?>>
                            <label for="packrule_boolean_yes">&nbsp;SI</label>
                            <span>&nbsp;-&nbsp;</span>
                            <input <?= (isset($edit) ? ($edit['rule_type'] != 3 ? 'disabled' : '') : 'disabled') ?> class="form-check-input" type="radio" name="packrule_value" id="packrule_boolean_no" value="0" <?= (isset($edit) ? ($edit['rule_type'] != 3 ? '' : ($edit['packrule_value'] == 0 ? 'checked' : '')) : '') ?>>
                            <label for="packrule_boolean_no">&nbsp;NO</label>
                        </div>
                    </div>
                </div>

                <?php if (!empty($errors)): ?>
                    <div class="clearfix">
                        <div class="alert alert-danger">
                            <?php foreach ($errors as $error): ?>
                                <?= $error ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="box-footer">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="col-sm-6">
                                <button class="btn btn-primary"><?= isset($edit) ? lang('Save') : lang('Add') ?></button>
                                <a class="btn" href="<?= site_url('packrules/all/' . $pack['pack_id']) ?>"><?= lang('Cancel') ?></a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
