<section class="content-header">
    <h1>
        Evaluación de personal
        <small><?= isset($edit) ? 'Editar Evaluación de personal' : 'Evaluar personal' ?></small>
    </h1>
    <ol class="breadcrumb">
        <li class=""><a href="<?= site_url('manager/employeeevaluations') ?>"><i class="ion ion-calendar"></i> Evaluación de personal</a></li>
        <li class="active"><a href="<?= site_url('manager/employeeevaluation') ?>"> Nuevo</a></li>
    </ol>
</section>
<section class="content">
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title"><?= isset($edit) ? 'Editar Evaluación de personal' : 'Evaluar personal' ?></h3>
        </div><!-- /.box-header -->
        <div class="box-body">
            <form class="form" action="" method="post" autocomplete="off">

                <div class="row">
                    <div class="col-sm-12">
                        <h4>Evaluación de <b><?= isset($person) ? $person['person_lastname'] . ', ' . $person['person_firstname'] : "Empleado de prueba" ?></b></h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label>Fecha de evaluación</label>
                            <div class="input-group">
                                <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                                <input type="text" name="employeeevaluation_date" id="employeeevaluation_date" autocomplete="off" class="form-control" value="<?= (isset($edit) && !empty($edit['employeeevaluation_date'])) ? date('d-m-Y', strtotime($edit['employeeevaluation_date'])) : '' ?>" />
                            </div>
                        </div>
                    </div>
                </div>
                <hr />
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <p><b>La evaluación de personal no puede omitir ninguna de las categorias</b></p>
                        </div>
                    </div>
                </div>
                <input name="employeeevaluation_id" type="hidden" value="<?= (isset($edit) && !empty($edit['employeeevaluation_id'])) ? $edit['employeeevaluation_id'] : '' ?>" />
                <input name="employeeevaluation_person" type="hidden" value="<?= (isset($person) && !empty($person['person_id'])) ? $person['person_id'] : '' ?>" />
                <?php foreach ([
                    [
                        "fieldName" => "employeeevaluation_productivity",
                        "fieldTitle" => "Productividad",
                        "fieldDescription" => "Volumen y cantidad de trabajos ejecutados normalmente.",
                    ],
                    [
                        "fieldName" => "employeeevaluation_attitude",
                        "fieldTitle" => "Actitud",
                        "fieldDescription" => "Capacidad de relacionarse con la incertidumbre de forma positiva y conducta ante contigencias no planificadas.",
                    ],
                    [
                        "fieldName" => "employeeevaluation_workknowledge",
                        "fieldTitle" => "Conocimiento del trabajo",
                        "fieldDescription" => "Contenido acumulado de conocimiento de trabajo.",
                    ],
                    [
                        "fieldName" => "employeeevaluation_cooperation",
                        "fieldTitle" => "Cooperación",
                        "fieldDescription" => "Actitud hacia la empresa, la jefatura y los compañeros de trabajo.",
                    ],
                    [
                        "fieldName" => "employeeevaluation_situationawareness",
                        "fieldTitle" => "Comprensión de situaciones",
                        "fieldDescription" => "Capta la esencia de un problema. Capacidad de disociar situaciones y captar hechos.",
                    ],
                    [
                        "fieldName" => "employeeevaluation_opentocriticism",
                        "fieldTitle" => "Apertura a críticas constructivas",
                        "fieldDescription" => "Volumen de sermones aceptados sin objeciones.",
                    ],
                    [
                        "fieldName" => "employeeevaluation_creativity",
                        "fieldTitle" => "Creatividad",
                        "fieldDescription" => "Ingenio. Capacidad de crear ideas y proyectos.",
                    ],
                    [
                        "fieldName" => "employeeevaluation_accomplishmentcapacity",
                        "fieldTitle" => "Capacidad de realización",
                        "fieldDescription" => "Capacidad de llevar a cabo ideas y proyectos.",
                    ],
                    [
                        "fieldName" => "employeeevaluation_initiative",
                        "fieldTitle" => "Iniciativa",
                        "fieldDescription" => "Volumen de ideas nuevas y alternativas propuestas para resolver problemas inherentes a la función desempeñada.",
                    ],
                ] as $i => $evaluationField) : ?>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label><?= $evaluationField['fieldTitle'] ?></label>
                                <p><?= $evaluationField['fieldDescription'] ?></p>
                                <div class="input-group">
                                    <?php foreach (["Óptimo", "Bueno", "Regular", "Deficiente", "Insuficiente"] as $j => $evaluationFieldOption) : ?>
                                        <input required name="<?= $evaluationField['fieldName'] ?>" id="<?= $evaluationField['fieldName'] . '_' . (5 - $j) ?>" class="form-check-input" type="radio" value="<?= (5 - $j) ?>" <?= (isset($edit) && isset($edit[$evaluationField['fieldName']]) && $edit[$evaluationField['fieldName']] == (5 - $j)) ? 'checked' : '' ?>>
                                        <label for="<?= $evaluationField['fieldName'] . '_' . (5 - $j) ?>">&nbsp;<?= $evaluationFieldOption ?></label>
                                        <?php if ($j != 4) : ?>
                                            <span>&nbsp;-&nbsp;</span>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                                <?php if ($i != 8) : ?>
                                    <hr />
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
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
                        <a class="btn" href="<?= site_url("persons/form/" . isset($person) &&  isset($person['person_id']) ? $person['person_id'] : '') ?>"><?= lang('Cancel') ?></a>
                    </div>
                </div>
            </div>
        </div>
        </form>
    </div>
    </div>
</section>