
<section class="content-header">
    <h1>
        Persona
        <small><?= isset($edit) ? 'Editar Persona' : 'Crear Persona' ?></small>
    </h1>
    <ol class="breadcrumb">
        <li class=""><a href="<?= site_url('manager/properties') ?>"><i class="fa fa-house"></i> Persona</a></li>
    </ol>
</section>
<section class="content">
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title"><?= isset($edit) ? 'Editar Persona' : 'Crear Persona' ?></h3>
        </div><!-- /.box-header -->
        <form class="form" action="" method="post" autocomplete="off">
            <div class="box-body">

                <? if (!empty($errors)) { ?>
                    <div class="clearfix">
                        <div class="alert alert-danger">
                            <?= $errors ?>
                        </div>
                    </div>
                <? } ?>
                <input name="people_id" id="people_id" type="hidden" value="<?= (isset($edit) && !empty($edit['people_id'])) ? $edit['people_id'] : '' ?>"/>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label>Nombre y Apellido</label>
                        <input class="form-control" name="people_name" id="people_name" type="text" value="<?= (isset($edit) && !empty($edit['people_name'])) ? $edit['people_name'] : '' ?>"/>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group ">
                        <label>DNI</label>
                        <input class="form-control" name="people_document" id="people_document" type="text" value="<?= (isset($edit) && !empty($edit['people_document'])) ? $edit['people_document'] : '' ?>"/>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group ">
                        <label>Fecha de Nacimiento</label>
                        <input class="form-control"  placeholder="DD-MM-YYYY" data-inputmask="'mask': '99-99-9999'" data-mask name="people_birth" id="people_birth" type="text" value="<?= (isset($edit) && !empty($edit['people_birth'])) ? $edit['people_birth'] : '' ?>"/>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group ">
                        <label>Dirección</label>
                        <input class="form-control" name="people_address" id="people_address" type="text" value="<?= (isset($edit) && !empty($edit['people_address'])) ? $edit['people_address'] : '' ?>"/>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group ">
                        <label>CUIT</label>
                        <input class="form-control" name="people_cuit" id="people_cuit" type="text" value="<?= (isset($edit) && !empty($edit['people_cuit'])) ? $edit['people_cuit'] : '' ?>"/>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group ">
                        <label>Celular</label>
                        <input class="form-control" name="people_cell" id="people_cell" type="text" value="<?= (isset($edit) && !empty($edit['people_cell'])) ? $edit['people_cell'] : '' ?>"/>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group ">
                        <label>Telefono</label>
                        <input class="form-control" name="people_phone" id="people_phone" type="text" value="<?= (isset($edit) && !empty($edit['people_phone'])) ? $edit['people_phone'] : '' ?>"/>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group ">
                        <label>Email</label>
                        <input class="form-control" name="people_email" id="people_email" type="text" value="<?= (isset($edit) && !empty($edit['people_email'])) ? $edit['people_email'] : '' ?>"/>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group ">
                        <label>Veraz</label>
                        <input class="form-control" name="people_veraz" id="people_veraz" type="text" value="<?= (isset($edit) && !empty($edit['people_veraz'])) ? $edit['people_veraz'] : '' ?>"/>
                    </div>
                </div>

            </div>
            <div class="box-footer">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="col-sm-6">
                            <button class="btn btn-primary"><?= isset($edit) ? lang('Save') : lang('Add') ?></button>
                            <a class="btn" href="<?= site_url('manager/people') ?>"><?= lang('Cancel') ?></a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</content>
