<section class="content-header">
    <h1>
        <i class="fa fa-bullhorn"></i> Consultas de Puntajes
    </h1>
</section>

<section class="content">
    <div class="row">

        <div class="col-lg-4 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-info">
                <div class="inner">
                    <h3 id="veraz"><?= money_formating($point_veraz['point'], true, false) ?></h3>
                    <p>Veraz</p>
                </div>
                <div class="icon">
                    <i class="fa fa-hashtag" aria-hidden="true"></i>
                </div>
            </div>
        </div><!-- ./col -->
        <div class="col-lg-4 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-info">
                <div class="inner">
                    <h3 id="siisa"><?= money_formating($point_siisa['point'], true, false) ?></h3>
                    <p>SiiSA</p>
                </div>
                <div class="icon">
                    <i class="fa fa-hashtag" aria-hidden="true"></i>
                </div>
            </div>
        </div><!-- ./col -->

    </div>

    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">Listado </h3>
        </div><!-- /.box-header -->
        <div class="box-body">
            <div class="row">
                <div class="col-sm-5">
                    <div class="input-group">
                        <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                        <input type="text" id="datefilter" name="datefilter" autocomplete="off" class="form-control rangepicker" value="<?= date('d-m-Y', strtotime($start)) . ' / ' . date('d-m-Y', strtotime($end)) ?>"/>
                    </div>
                </div>
                <?php $filters_label = array('office' => 'Oficinas'); ?>

                <?php foreach ($filters as $field => $values): ?>
                    <div class="col-sm-3">
                        <select class="form-control filter_field" id="filter_<?= $field ?>" name="<?= $field ?>" onchange="apply_filter();">
                            <option value=""><?= $filters_label[$field] ?></option>
                            <?php foreach ($values as $val) { ?>
                                <option value="<?= $val['value'] ?>"><?= $val['title'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                <?php endforeach; ?>
            </div>
            <hr/>
            <table id="point_list" class="table table-bordered responsive nowrap" width="100%">
                <thead>
                    <tr class="top">
                        <th>Fecha</th>
                        <th>Tipo</th>
                        <th>Cliente</th>
                        <th>Usuario</th>
                        <th>Oficina</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr class="top">
                        <th>Fecha</th>
                        <th>Tipo</th>
                        <th>Cliente</th>
                        <th>Usuario</th>
                        <th>Oficina</th>
                    </tr>
                </tfoot>
            </table>
        </div><!-- /.box-body -->
    </div><!-- /.box -->
</section>
