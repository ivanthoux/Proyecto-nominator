<section class="content-header">
    <h1>
        Recibo de sueldo
        <small><?= isset($edit) ? 'Editar Recibo de sueldo' : 'Liquidar empleado' ?></small>
    </h1>
    <ol class="breadcrumb">
        <li class=""><a href="<?= site_url('manager/contracts') ?>"><i class="ion ion-file"></i> Recibo de sueldo</a></li>
        <li class="active"><a href="<?= site_url('manager/contract') ?>"> Nuevo</a></li>
    </ol>
</section>
<section class="content">
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title"><?= isset($edit) ? 'Editar Recibo de sueldo' : 'Crear contrato' ?></h3>
        </div>
        <div class="box-body">
            <form class="form" action="" method="post" autocomplete="off">
                <div class="row">
                    <div class="col-sm-12">
                        <h4>Recibo de sueldo para <b><?= isset($person) ? $person['person_lastname'] . ', ' . $person['person_firstname'] : "Empleado de prueba" ?></b></h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label>Mes a liquidar</label>
                            <div class="input-group">
                                <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                                <input type="text" name="paycheck_date" id="paycheck_date" autocomplete="off" class="form-control" value="<?= (isset($edit) && !empty($edit['paycheck_date'])) ? date('d-m-Y', strtotime($edit['paycheck_date'])) : '' ?>" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label>Sueldo bruto por jornada <?= money_formating((isset($edit) && !empty($edit['paycheck_basic_diary_amount'])) ? $edit['paycheck_basic_diary_amount'] : $contract['contract_hour_rate']) ?></label>
                            <!-- <input class="form-control" required id="paycheck_basic_diary_amount" name="paycheck_basic_diary_amount" type="hidden" value="<?= (isset($edit) && !empty($edit['paycheck_basic_diary_amount'])) ? $edit['paycheck_basic_diary_amount'] : $contract['contract_hour_rate'] ?>" /> -->
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label>Días hábiles trabajados&nbsp;</label><label id="daysWorked"></label>
                            <!-- <input class="form-control" required id="paycheck_basic_days" name="paycheck_basic_days" type="hidden" value="<?= (isset($edit) && !empty($edit['paycheck_basic_days'])) ? $edit['paycheck_basic_days'] : '' ?>" /> -->
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label>
                                Concepto
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label>
                                Base
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label>
                                Unidad
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label>
                                Haberes
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label>
                                Descuento
                            </label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">
                            Sueldo básico
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label>
                                <input type="text" readonly name="paycheck_basic_diary_amount" id="paycheck_basic_diary_amount" value="<?= (isset($edit) && !empty($edit['paycheck_basic_diary_amount'])) ? $edit['paycheck_basic_diary_amount'] : $contract['contract_hour_rate'] ?>">
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label>
                                <input type="text" readonly name="paycheck_basic_days" id="paycheck_basic_days" value="">
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label>
                                <input type="text" readonly id="paycheck_basic_amount" value="">
                                <input type="hidden" readonly name="paycheck_basic_amount" value="">
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label>
                                <input type="text" readonly value="">
                            </label>
                        </div>
                    </div>
                </div>
                <?php foreach ($additionals as $i => $additional) : ?>
                    <?php if ($i == 0) continue; ?>
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <?= $additional['additional_name'] . ' (' . money_formating($additional['additional_coefficient'] * 100, false, false) . ' %)' ?>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label>
                                    <input type="text" readonly id="paycheck_additional_<?= $additional['additional_key'] ?>_base" value="">
                                </label>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label>
                                    <input type="text" readonly id="paycheck_additional_<?= $additional['additional_key'] ?>_coefficient" value="">
                                </label>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label>
                                    <input type="text" readonly id="paycheck_additional_<?= $additional['additional_key'] ?>_haber" value="">
                                </label>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label>
                                    <input type="text" readonly id="paycheck_additional_<?= $additional['additional_key'] ?>_discount" value="">
                                </label>
                            </div>
                        </div>
                        <input type="hidden" name="paycheck_additional_<?= $additional['additional_key'] ?>_single" value='' />
                    </div>
                <?php endforeach; ?>
                <hr />
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label>Totales</label>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label>
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label>
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label>
                                <input type="text" readonly id="paycheck_additional_haber_total" value="">
                                <input type="hidden" readonly name="paycheck_bruto" value="">
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label>
                                <input type="text" readonly id="paycheck_additional_discount_total" value="">
                            </label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label>Sueldo neto</label>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label>
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label>
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label>
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label>
                                <input type="text" readonly id="paycheck_net" value="">
                                <input type="hidden" readonly name="paycheck_neto" value="">
                            </label>
                        </div>
                    </div>
                </div>
                <input name="paycheck_id" type="hidden" value="<?= (isset($edit) && !empty($edit['paycheck_id'])) ? $edit['paycheck_id'] : '' ?>" />
                <input name="paycheck_person" type="hidden" value="<?= (isset($person) && !empty($person['person_id'])) ? $person['person_id'] : '' ?>" />
                <input name="paycheck_contract" type="hidden" value="<?= (isset($contract) && !empty($contract['contract_id'])) ? $contract['contract_id'] : '' ?>" />
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