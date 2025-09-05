<section class="content-header">
    <h1>
        Adicional
        <small><?= isset($edit) ? 'Editar Adicional' : 'Crear Adicional' ?></small>
    </h1>
    <ol class="breadcrumb">
        <li class=""><a href="<?= site_url('manager/additionals') ?>"><i class="ion ion-calendar"></i> Adicional</a></li>
        <li class="active"><a href="<?= site_url('manager/additional') ?>"> Nuevo</a></li>
    </ol>
</section>
<section class="content">
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title"><?= isset($edit) ? 'Editar Adicional' : 'Crear Adicional' ?></h3>
        </div><!-- /.box-header -->
        <div class="box-body">
            <form class="form" action="" method="post" autocomplete="off">
                <input name="additional_id" type="hidden" value="<?= (isset($edit) && !empty($edit['additional_id'])) ? $edit['additional_id'] : '' ?>" />
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Nombre</label>
                            <input class="form-control" name="additional_name" type="text" value="<?= (isset($edit) && !empty($edit['additional_name'])) ? $edit['additional_name'] : '' ?>" title="Ingresar un mínimo de 8 caractéres" />
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label>Orden</label>
                            <input class="form-control" name="additional_order" type="text" value="<?= (isset($edit) && !empty($edit['additional_order'])) ? $edit['additional_order'] : '' ?>" />
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label>Haber (0: Suma haber. 1: Descuento)</label>
                            <input class="form-control" name="additional_haber" type="text" value="<?= (isset($edit) && !empty($edit['additional_haber'])) ? $edit['additional_haber'] : '' ?>" />
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label>Tipo de adicional</label>
                            <select class="form-control filter_field" id="additional_remunerative" name="additional_remunerative">
                                <option value="">Seleccione un tipo</option>
                                <option <?= !empty($edit) && "1" == $edit["additional_remunerative"] ? "selected" : "" ?> value="1"><?= "Remunerado" ?></option>
                                <option <?= !empty($edit) && "0" == $edit["additional_remunerative"] ? "selected" : "" ?> value="0"><?= "No remunerado" ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label>Coeficiente</label>
                            <input class="form-control" name="additional_coefficient" type="text" value="<?= (isset($edit) && !empty($edit['additional_coefficient'])) ? $edit['additional_coefficient'] : '' ?>" title="Ingresar valor entre Cero y Uno" />
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Descripción</label>
                            <textarea class="form-control" name="additional_description"><?= (isset($edit) && !empty($edit['additional_description'])) ? $edit['additional_description'] : '' ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label>Codigo interno</label>
                            <input class="form-control" name="additional_key" type="text" value="<?= (isset($edit) && !empty($edit['additional_key'])) ? $edit['additional_key'] : '' ?>" title="Ingresar un texto con caracteres a-Z" />
                        </div>
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
                        <a class="btn" href="<?= site_url('manager/additionals') ?>"><?= lang('Cancel') ?></a>
                    </div>
                </div>
            </div>
        </div>
        </form>
    </div>
    </div>
</section>