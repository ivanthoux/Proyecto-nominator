<section class="content-header">
    <h1>
        <i class="fa fa-hand-o-right"></i> Cuotas del Cliente - <?= $client['client_firstname'] . ' ' . $client['client_lastname'] ?>
    </h1>
    <ol class="breadcrumb">
        <li class="active"><a href="<?= site_url('clients') ?>"><i class="fa fa-home"></i> Clientes</a></li>
    </ol>
</section>

<section class="content">
    <?= $this->load->view('manager/clients/client_menu', array('client' => $client, 'parent' => !empty($parent) ? $parent : false, 'active' => 'client_period'), TRUE) ?>
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">Listado de Cuotas </h3>
            <?php if (!in_array($this->session->userdata('user_rol'), ["seller", "sell_for"])) : ?>
                <div class="box-tools pull-right">
                    <a class="btn btn-primary" href="<?= site_url('clientpayments/form/' . $client['client_id']) ?>"> A Pagar</a>
                </div><!-- /.box-tools -->
            <?php endif; ?>
        </div><!-- /.box-header -->
        <div class="box-body">
            <div class="row">

                <div class="col-sm-3">
                    <div class="input-group">
                        <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                        <input type="text" id="datefilter" name="datefilter" autocomplete="off" class="form-control rangepicker" onchange="apply_filter();" />
                    </div>
                </div>

                <?php $filters_label = array('clientperiod_pack' => 'Producto', 'clientperiod_paid' => 'Por Estado Pago'); ?>
                <?php foreach ($filters as $field => $values) : ?>
                    <div class="col-sm-3">
                        <select class="form-control filter_field" id="filter_<?= $field ?>" name="<?= $field ?>" onchange="apply_filter();">
                            <option value=""><?= $filters_label[$field] ?></option>
                            <?php foreach ($values as $val) : ?>
                                <option value="<?= $val['value'] ?>"><?= $val['title'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endforeach; ?>
            </div>
            <hr />
            <table id="period_list" class="table table-bordered responsive nowrap" width="100%">
                <thead>
                    <tr class="top">
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th>1° Vto</th>
                        <th>2° Vto</th>
                        <th>Cuota</th>
                        <th>Producto</th>
                        <th>Pagado</th>
                        <th><?= lang('Actions') ?></th>
                    </tr>
                </thead>
                <tfoot>
                    <tr class="top">
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th>1° Vto</th>
                        <th>2° Vto</th>
                        <th>Cuota</th>
                        <th>Producto</th>
                        <th>Pagado</th>
                        <th><?= lang('Actions') ?></th>
                    </tr>
                </tfoot>
            </table>
        </div><!-- /.box-body -->
    </div><!-- /.box -->
</section>