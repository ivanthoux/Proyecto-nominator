<section class="content-header">
    <h1>
        <i class="fa fa-hand-o-right"></i> Pagos del Cliente - <?= $client['client_firstname'] . ' ' . $client['client_lastname'] ?>
    </h1>
    <ol class="breadcrumb">
        <li class="active"><a href="<?= site_url('clients') ?>"><i class="fa fa-home"></i> Clientes</a></li>
    </ol>
</section>

<section class="content">
    <?= $this->load->view('manager/clients/client_menu', array('client' => $client, 'parent' => !empty($parent) ? $parent : false, 'active' => 'client_payment'), TRUE) ?>
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">Listado de Pagos </h3>
            <?php if (can("create_pack") && $client['client_active']): ?>
                <div class="box-tools pull-right">
                    <a class="btn btn-primary hidden" href="<?= site_url('clientpayments/form/' . $client_id) ?>"> <span class="fa fa-plus"></span> Nuevo</a>
                </div><!-- /.box-tools -->
            <?php endif; ?>
        </div><!-- /.box-header -->
        <div class="box-body">
            <div class="row">

                <!-- <div class="col-sm-3">
                  <div class="input-group">
                    <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                    <input type="text" id="datefilter" name="datefilter" autocomplete="off" class="form-control rangepicker" onchange="apply_filter();"/>
                  </div>
                </div> -->

                <?php $filters_label = array('pay_created_by' => 'Lo Recibió', 'pay_type' => 'Tipo', 'pay_presented' => 'Presentado'); ?>
                <?php foreach ($filters as $field => $values): ?>
                    <div class="col-sm-3">
                        <select class="form-control filter_field" id="filter_<?= $field ?>" name="<?= $field ?>" onchange="apply_filter();">
                            <option value=""><?= $filters_label[$field] ?></option>
                            <?php foreach ($values as $val): ?>
                                <option value="<?= $val['value'] ?>"><?= $val['title'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endforeach; ?>
            </div>
            <hr/>
            <table id="payment_list" class="table table-bordered responsive nowrap" width="100%">
                <thead>
                    <tr class="top">
                        <th>Nro</th>
                        <th>Fecha</th>
                        <th>Valor</th>
                        <th>Medio</th>
                        <th>Recibio</th>
                        <?php if ($this->session->userdata('user_rol_label') == 'Super'): ?>
                            <th><?= lang('Actions') ?></th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tfoot>
                    <tr class="top">
                        <th>Nro</th>
                        <th>Fecha</th>
                        <th>Valor</th>
                        <th>Medio</th>
                        <th>Recibio</th>
                        <?php if ($this->session->userdata('user_rol_label') == 'Super'): ?>
                            <th><?= lang('Actions') ?></th>
                        <?php endif; ?>
                    </tr>
                </tfoot>
            </table>
        </div><!-- /.box-body -->
    </div><!-- /.box -->
</section>
