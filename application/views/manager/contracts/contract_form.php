<section class="content-header">
    <h1>
        Contrato
        <small><?= isset($edit) ? 'Editar Contrato' : 'Evaluar personal' ?></small>
    </h1>
    <ol class="breadcrumb">
        <li class=""><a href="<?= site_url('manager/contracts') ?>"><i class="ion ion-file"></i> Contrato</a></li>
        <li class="active"><a href="<?= site_url('manager/contract') ?>"> Nuevo</a></li>
    </ol>
</section>
<section class="content">
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title"><?= isset($edit) ? 'Editar Contrato' : 'Crear contrato' ?></h3>
        </div>
        <div class="box-body">
            <form class="form" action="" method="post" autocomplete="off">
                <div class="row">
                    <div class="col-sm-12">
                        <h4>Contrato para <b><?= isset($person) ? $person['person_lastname'] . ', ' . $person['person_firstname'] : "Empleado de prueba" ?></b></h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label>Rol a desempeñar</label>
                            <select class="form-control filter_field" id="unionagreementcategory_type" onChange="app.categoryChanged()">
                                <option value="">Seleccione un puesto a desempeñar</option>
                                <option value="Administrativo">Administrativo</option>
                                <option value="Auxiliar">Auxiliar</option>
                                <option value="Auxiliar especializado">Auxiliar especializado</option>
                                <option value="Cajero">Cajero</option>
                                <option value="Vendedor">Vendedor</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label>Categoría</label>
                            <select class="form-control filter_field" id="category" name="contract_union_agreement_category">
                                <option value="">Seleccione una categoría</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label>Cantidad de horas de trabajo mensuales</label>
                            <input class="form-control" required name="contract_mensual_hours" type="number" value="<?= (isset($edit) && !empty($edit['contract_mensual_hours'])) ? $edit['contract_mensual_hours'] : '' ?>" />
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label>Sueldo básico</label>
                            <input class="form-control" required name="contract_hour_rate" type="number" value="<?= (isset($edit) && !empty($edit['contract_hour_rate'])) ? $edit['contract_hour_rate'] : '' ?>" />
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label>Fecha de inicio efectiva</label>
                            <div class="input-group">
                                <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                                <input type="text" name="contract_start" id="contract_start" autocomplete="off" class="form-control" value="<?= (isset($edit) && !empty($edit['contract_start'])) ? date('d-m-Y', strtotime($edit['contract_start'])) : '' ?>" />
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label>Fecha de fin efectivo</label>
                            <div class="input-group">
                                <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                                <input type="text" name="contract_end" id="contract_end" autocomplete="off" class="form-control" value="<?= (isset($edit) && !empty($edit['contract_end'])) ? date('d-m-Y', strtotime($edit['contract_end'])) : '' ?>" />
                            </div>
                        </div>
                    </div>
                </div>
                <hr />
                <input name="contract_id" type="hidden" value="<?= (isset($edit) && !empty($edit['contract_id'])) ? $edit['contract_id'] : '' ?>" />
                <input name="contract_person" type="hidden" value="<?= (isset($person) && !empty($person['person_id'])) ? $person['person_id'] : '' ?>" />
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
                                <a class="btn" href="<?= site_url("persons/form/" . isset($person) &&  isset($person['person_id']) ? $person['person_id'] : '') ?>"><?= lang('Cancel') ?></a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>