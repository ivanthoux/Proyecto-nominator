<section class="content-header">
    <h1>
        <i class="fa fa-building"></i> Facturación
    </h1>
</section>

<section class="content">
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">Listado </h3>
            <div class="box-tools pull-right">
                <?php if ($user == 5) : ?>
                    <a class="btn btn-primary" href="javascript:;" id="create_voucher"> Crear Facturación</a>
                <?php endif; ?>
                <a class="btn btn-primary" href="javascript:;" id="create_ivabook"><i class="fa fa-download"></i> Libro IVA</a>
            </div><!-- /.box-tools -->
        </div><!-- /.box-header -->
        <div class="box-body">
            <div class="row">
                <div class="col-sm-3">
                    <div class="input-group">
                        <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                        <input type="text" id="datefilter" name="datefilter" autocomplete="off" class="form-control rangepicker" onchange="apply_filter();" />
                    </div>
                </div>
            </div>
            <hr />
            <table id="_list" class="table table-bordered  responsive nowrap" width="100%">
                <thead>
                    <tr class="top">
                        <th>Fecha</th>
                        <th>Número</th>
                        <th>Cliente</th>
                        <th>Monto</th>
                        <th><?= lang('Actions') ?></th>
                    </tr>
                </thead>
                <tfoot>
                    <tr class="top">
                        <th>Fecha</th>
                        <th>Número</th>
                        <th>Cliente</th>
                        <th>Monto</th>
                        <th><?= lang('Actions') ?></th>
                    </tr>
                </tfoot>
            </table>
        </div><!-- /.box-body -->
    </div><!-- /.box -->
</section>