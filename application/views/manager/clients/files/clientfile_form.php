<section class="content-header">
    <h1>
        <i class="fa fa-male"></i> Documentos <?= !empty($client) ? ' al Cliente - ' . $client['client_firstname'] . ' ' . $client['client_lastname'] : ' de TODOS los clientes' ?>
    </h1>
    <ol class="breadcrumb">
        <li class="active"><a href="<?= site_url('clientfiles/all/' . $client_id) ?>"><i class="fa fa-home"></i>Documentos del Cliente</a></li>
    </ol>
</section>

<section class="content client_form">
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title"><?= isset($edit) ? 'Editar Documento del Cliente' : 'Crear Documento del Cliente' ?></h3>
        </div><!-- /.box-header -->
        <div class="box-body">
            <form class="form" action="" method="post" onsubmit="app.loading();" enctype="multipart/form-data" autocomplete="off">
                <input name="clientfile_id" type="hidden" value="<?= (isset($edit) && !empty($edit['clientfile_id'])) ? $edit['clientfile_id'] : '' ?>" />
                <input name="clientfile_client" type="hidden" value="<?= (!empty($client_id)) ? $client_id : '' ?>" />
                <div class="row">
                    <div class="col-sm-4">
                        <label for="packrule_rule">Tipo</label>
                        <div class="form-group">
                            <select class="form-control" name="clientfile_type" id="clientfile_type" required>
                                <option data_type=""></option>
                                <?php foreach ($types as $type) : ?>
                                    <option <?= !empty($edit) && $type["rule_id"] == $edit["clientfile_type"] ? "selected" : "" ?> value="<?= $type["rule_id"] ?>"><?= $type['rule_name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <label>Archivo</label>
                        <div class="form-group">
                            <input name="clientfile_file" id="clientfile_file" type="file">
                        </div>
                    </div>
                    <?php if (!empty($edit) && !empty($edit['clientfile_ocr'])) : ?>
                        <div class="col-sm-6">
                            <label>Verificar Domicilio</label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-check text-danger" aria-hidden="true"></i></span>
                                <input type="text" value="<?= $edit['clientfile_ocr'] ?>" readonly class="form-control">
                            </div>
                        </div>
                    <?php endif; ?>
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
                                <button class="btn btn-primary" ><?= isset($edit) ? lang('Save') : lang('Add') ?></button>
                                <a class="btn" href="<?= site_url('clientfiles/all/' . $client_id) ?>"><?= lang('Cancel') ?></a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>