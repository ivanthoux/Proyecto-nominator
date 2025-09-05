<section class="content-header">
    <h1>
        Feriado
        <small><?= isset($edit) ? 'Editar Feriado' : 'Crear Feriado' ?></small>
    </h1>
    <ol class="breadcrumb">
        <li class=""><a href="<?= site_url('manager/holidays') ?>"><i class="ion ion-calendar"></i> Feriado</a></li>
        <li class="active"><a href="<?= site_url('manager/holiday') ?>"> Nuevo</a></li>
    </ol>
</section>
<section class="content">
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title"><?= isset($edit) ? 'Editar Feriado' : 'Crear Feriado' ?></h3>
        </div><!-- /.box-header -->
        <div class="box-body">
            <form class="form" action="" method="post" autocomplete="off">
                <input name="holiday_id" type="hidden" value="<?= (isset($edit) && !empty($edit['holiday_id'])) ? $edit['holiday_id'] : '' ?>" />
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label>Fecha</label>
                            <div class="input-group">
                                <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                                <input type="text" name="holiday_date" id="holiday_date" autocomplete="off" class="form-control" value="<?= (isset($edit) && !empty($edit['holiday_date'])) ? date('d-m-Y', strtotime($edit['holiday_date'])) : '' ?>" />
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label>Tipo</label>
                            <select class="form-control filter_field" id="holiday_type" name="holiday_type">
                                <option value="">Seleccione un tipo de feriado</option>
                                <option <?= !empty($edit) && "immovable" == $edit["holiday_type"] ? "selected" : "" ?> value="immovable"><?= "Inamovible" ?></option>
                                <option <?= !empty($edit) && "portable" == $edit["holiday_type"] ? "selected" : "" ?> value="portable"><?= "Transladable" ?></option>
                                <option <?= !empty($edit) && "bridge" == $edit["holiday_type"] ? "selected" : "" ?> value="bridge"><?= "Puente" ?></option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-5">
                        <div class="form-group">
                            <label>Detalle</label>
                            <input class="form-control" name="holiday_detail" type="text" value="<?= (isset($edit) && !empty($edit['holiday_detail'])) ? $edit['holiday_detail'] : '' ?>" title="Ingresar un mínimo de 8 caractéres" />
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
                        <a class="btn" href="<?= site_url('manager/holidays') ?>"><?= lang('Cancel') ?></a>
                    </div>
                </div>
            </div>
        </div>
        </form>
    </div>
    </div>
</section>