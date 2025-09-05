<section class="content-header">
    <h1>
        <i class="fa fa-male"></i> Documentos <?= !empty($person) ? ' a la Persona - ' . $person['person_firstname'] . ' ' . $person['person_lastname'] : ' de TODAS lAs personas' ?>
    </h1>
    <ol class="breadcrumb">
        <li class="active"><a href="<?= site_url('personfiles/all/' . $person_id) ?>"><i class="fa fa-home"></i>Documentos de la Persona</a></li>
    </ol>
</section>

<section class="content person_form">
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title"><?= isset($edit) ? 'Editar Documento de la Persona' : 'Crear Documento de la Persona' ?></h3>
        </div><!-- /.box-header -->
        <div class="box-body">
            <form class="form" action="" method="post" onsubmit="app.loading();" enctype="multipart/form-data" autocomplete="off">
                <input name="personfile_id" type="hidden" value="<?= (isset($edit) && !empty($edit['personfile_id'])) ? $edit['personfile_id'] : '' ?>" />
                <input name="personfile_person" type="hidden" value="<?= (!empty($person_id)) ? $person_id : '' ?>" />
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Descripción</label>
                            <textarea class="form-control" name="personfile_type"><?= (isset($edit) && !empty($edit['personfile_type'])) ? $edit['personfile_type'] : '' ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label>Tipo de archivo</label>
                            <select class="form-control filter_field" id="personfile_file_type">
                                <option value="">Seleccione un tipo de archivo</option>
                                <option <?= !empty($edit) && "personal file" == $edit["personfile_file_type"] ? "selected" : "" ?> value="personal file"><?= "Legajo personal" ?></option>
                                <option <?= !empty($edit) && "capacitations" == $edit["personfile_file_type"] ? "selected" : "" ?> value="capacitations"><?= "Certificados de capacitación" ?></option>
                                <option <?= !empty($edit) && "incidents" == $edit["personfile_file_type"] ? "selected" : "" ?> value="incidents"><?= "Incidencias" ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <label>Archivo</label>
                        <div class="form-group">
                            <input name="personfile_file" id="personfile_file" type="file">
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
                                <a class="btn" href="<?= site_url('personfiles/all/' . $person_id) ?>"><?= lang('Cancel') ?></a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>