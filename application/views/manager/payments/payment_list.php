<section class="content-header">
    <h1>
        <i class="fa fa-hand-o-right"></i> Listado de Caja Entrante <?= (!in_array($this->session->userdata('user_rol_label'), ['Super', 'Administrador', 'Propietario'])) ? '- Sin Cerrar' : '' ?>
    </h1>
</section>

<section class="content">
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">Filtrar Listado </h3>
            <div class="box-tools pull-right">
                <a class="btn btn-primary" href="<?= site_url('payments/form') ?>"> <span class="fa fa-plus"></span> Nuevo</a>
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
            <div class="row">

                <?
                $filters_label = array(
                    'pay_client' => 'Cliente',
                    'pay_office' => 'Oficina',
                    'pay_officelocation' => 'Sucursal',
                    'pay_created_by' => 'Lo Recibió',
                    'pay_presented' => 'Por Cierre Caja',
                    'pay_type' => 'Tipo'
                ); ?>

                <?php foreach ($filters as $field => $values) : ?>
                    <?php if (!empty($filters_label[$field])) : ?>
                        <div class="col-sm-2">
                            <select class="form-control filter_field" id="filter_<?= $field ?>" name="<?= $field ?>" onchange="apply_filter();">
                                <option value=""><?= $filters_label[$field] ?></option>
                                <?
                                foreach ($values as $val) { ?>
                                    <option value="<?= $val['value'] ?>"><?= $val['title'] ?></option>
                                <? } ?>
                            </select>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <hr />
            <table id="payments_list" class="table table-bordered  responsive nowrap" width="100%">

                <thead>
                    <tr class="top">
                        <th>Fecha</th>
                        <th>Registrado</th>
                        <th>Cliente/Detalle</th>
                        <th>Precio</th>
                        <th>Tipo</th>
                        <th>Recibido</th>
                        <? if ($this->session->userdata('user_rol_label') == 'Super') { ?>
                            <th><?= lang('Actions') ?></th>
                        <? } ?>
                    </tr>
                </thead>
                <tfoot>
                    <tr class="top">
                        <th>Fecha</th>
                        <th>Registrado</th>
                        <th>Cliente/Detalle</th>
                        <th>Precio</th>
                        <th>Tipo</th>
                        <th>Recibido</th>
                        <? if ($this->session->userdata('user_rol_label') == 'Super') { ?>
                            <th><?= lang('Actions') ?></th>
                        <? } ?>
                    </tr>
                </tfoot>
            </table>
        </div><!-- /.box-body -->
    </div><!-- /.box -->
</section>