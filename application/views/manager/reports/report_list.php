<section class="content-header">
    <h1>
        <i class="fa fa-bullhorn"></i> Informes
    </h1>
</section>

<section class="content">
    <div class="box">
        <div class="box-body form-horizontal">
            <div class="row">
                <div class="col-sm-6">
                    <label class="control-label col-sm-6 text-left">Seleccione el tipo de reporte</label>
                    <div class="col-sm-6">
                        <select id="report" class="form-control filter_field" onchange="app.setFilters();">
                        </select>
                    </div>
                </div>
            </div>
            <hr />
            <div class="row">
                <div class="col-sm-3" id="periodDiv">
                    <div class="input-group">
                        <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                        <input type="text" id="period" name="datefilter" autocomplete="off" class="form-control rangepicker" />
                    </div>
                </div>
                <div class="col-sm-2" id="dateDiv">
                    <div class="input-group">
                        <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                        <input type="text" id="datePicker" name="datefilter" autocomplete="off" class="form-control" />
                    </div>
                </div>

                <div class="col-sm-3" id="packDiv">
                    <select class="form-control filter_field" id="pack_id" name="pack_id" >
                        <option value="">Productos</option>
                        <?php foreach ($packs as $pack) : ?>
                            <option value="<?= $pack['value'] ?>"><?= $pack['title'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-sm-4" id="otherDiv">
                </div>
            </div>
            <hr />
            <div class="row">
                <div class="col-sm-12">
                    <button class="btn btn-primary" onclick="app.generate();">Generar</button>
                    <button class="btn btn-secundary" onclick="app.reset();">Cancelar</button>
                </div>
            </div>
        </div><!-- /.box-body -->
    </div><!-- /.box -->
</section>