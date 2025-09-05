<section class="content-header">
    <h1><i class="fa fa-bullhorn"></i> Regla</h1>
    <ol class="breadcrumb">
        <li class="active"><a href="<?= site_url('rules/all') ?>"><i class="fa fa-home"></i> Reglas</a></li>
    </ol>
</section>
<section class="content client_form">
    <?php $active = isset($edit) ? $edit['rule_active'] : 1; ?>
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title"><?= isset($edit) ? 'Editar Regla' : 'Crear Regla' ?></h3>
        </div><!-- /.box-header -->
        <div class="box-body">
            <form class="form" action="" method="post" autocomplete="off">
                <input name="rule_id" type="hidden" value="<?= (isset($edit) && !empty($edit['rule_id'])) ? $edit['rule_id'] : '' ?>" />
                <div class="row">
                    <div class="col-sm-4 col-xs-4">
                        <label for="rule_name">Nombre</label>
                        <div class="form-group">
                            <input class="form-control" <?= $active ? '' : 'disabled="disabled"'; ?> required name="rule_name" id="rule_name" type="text" value="<?= (isset($edit) ? $edit['rule_name'] : '') ?>" />
                        </div>
                    </div>
                    <div class="col-sm-4 col-xs-4">
                        <label for="rule_type">Tipo</label>
                        <div class="form-group">
                            <select class="form-control" <?= (isset($edit) && !empty($edit['rule_id'])) ? "disabled" : ""; ?> name="rule_type" id="rule_type">
                                <option data_type=""></option>
                                <?php $rule_type = false ?>
                                <?php foreach ($types as $type) : ?>

                                <?php if (!empty($edit) && $type["id"] == $edit["rule_type"]) : ?>
                                <?php $rule_type = $edit["rule_type"] ?>
                                <?php endif; ?>

                                <option <?= !empty($edit) && $type["id"] == $edit["rule_type"] ? "selected" : "" ?> value="<?= $type["id"] ?>"><?= $type['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($edit) && !empty($edit['rule_id'])) : ?>
                            <input type="hidden" name="rule_type" value="<?= $rule_type ?>">
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label>Requerir Documentación</label>
                            <div class="input-group">
                                <input <?= $active ? '' : 'disabled="disabled"'; ?> name="rule_type_doc_require" id="rule_type_doc_require_si" class="form-check-input" type="radio" value="1" <?= (isset($edit) && $edit['rule_type_doc_require'] == 1) ? 'checked' : '' ?>>
                                <label for="rule_type_doc_require_si">&nbsp;SI</label>
                                <span>&nbsp;-&nbsp;</span>
                                <input <?= $active ? '' : 'disabled="disabled"'; ?> name="rule_type_doc_require" id="rule_type_doc_require_no" class="form-check-input" type="radio" value="0" <?= (isset($edit) && $edit['rule_type_doc_require'] == 0) ? 'checked' : '' ?>>
                                <label for="rule_type_doc_require_no">&nbsp;NO</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-8">
                        <div class="form-group">
                            <label for="rule_description">Descripción</label>
                            <textarea class="form-control" <?= $active ? '' : 'disabled="disabled"'; ?> name="rule_description" id="rule_description"><?= (isset($edit) && !empty($edit['rule_description'])) ? $edit['rule_description'] : '' ?></textarea>
                        </div>
                    </div>
                </div>

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

                <div class="box-footer">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="col-sm-6">
                                <button class="btn btn-primary"><?= isset($edit) ? lang('Save') : lang('Add') ?></button>
                                <a class="btn" href="<?= site_url('rules/all') ?>"><?= lang('Cancel') ?></a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>