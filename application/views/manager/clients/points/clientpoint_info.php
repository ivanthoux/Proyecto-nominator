<section class="content-header">
    <h1>
        <i class="fa fa-bullhorn"></i> Pagos del Cliente - <?= $client['client_firstname'] . ' ' . $client['client_lastname'] ?>
    </h1>
    <ol class="breadcrumb">
        <li class="active"><a href="<?= site_url('clients') ?>"><i class="fa fa-tags"></i> Clientes</a></li>
    </ol>
</section>

<section class="content">
    <?= $this->load->view('manager/clients/client_menu', array('client' => $client, 'parent' => !empty($parent) ? $parent : false, 'active' => 'client_point'), TRUE) ?>
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">Puntuaci&oacute;n del Cliente <span class="text-muted small"><?= !empty($points) ? '(Última Actualizaci&oacute;n ' . $points['last'] . ')' : '' ?></span></h3>
            <div class="box-tools pull-right">
                <a class="btn btn-warning" id="update" href="#"> <span class="fa fa-refresh"></span> <span class="hidden-xs hidden-sm hidden-md">Actualizar</span></a>
                <?php if ($client['client_active']) : ?>
                    <a class="btn btn-primary" href="<?= site_url('clientpacks/form/' . $client_id) ?>"> <span class="fa fa-folder"></span> <span class="hidden-xs hidden-sm hidden-md">Nuevo Producto</span></a>
                <?php endif; ?>
            </div><!-- /.box-tools -->
        </div><!-- /.box-header -->
        <div class="box-body">
            <div class="row">
                <div class="col-lg-6 col-xs-6">
                    <!-- small box -->
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>
                                <?= (!empty($points) ? (strpos($points['clientpoint_verazpoint'], '-') !== false ? 'Error: ' : '') . $points['clientpoint_verazpoint'] : '') ?>
                                <a class="btn btn-default" id="update_veraz" href="#"> <span class="fa fa-refresh"></span> <span class="hidden-xs hidden-sm hidden-md"></span></a>
                            </h3>
                            <p>Informaci&oacute;n de Veraz</p>
                        </div>
                        <div class="icon" id="veraz_info">
                            <i class="fa fa-info"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-xs-6">
                    <!-- small box -->
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>
                                <?= (!empty($points) ? (strpos($points['clientpoint_siisapoint'], '-') !== false ? 'Error: ' : '') . $points['clientpoint_siisapoint'] : '') ?>
                                <a class="btn btn-default" id="update_siisa" href="#"> <span class="fa fa-refresh"></span> <span class="hidden-xs hidden-sm hidden-md"></span></a>
                                <?php if ($this->session->userdata('user_rol') == 'super') : ?>
                                    <a class="btn btn-default" target="_blank" href="<?= site_url('clientpoints/xmlSiisa/' . $client_id . '/true/') ?>"> <span class="fa fa-file"></span> <span class="hidden-xs hidden-sm hidden-md"></span></a>
                                <?php endif; ?>
                            </h3>
                            <p>Informaci&oacute;n de SiiSA</p>
                        </div>
                        <div class="icon" id="siisa_info">
                            <i class="fa fa-info"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6 col-xs-6">
                    <?= (!empty($points) ? $points['clientpoint_verazinfo'] : '') ?>
                </div>
                <div class="col-lg-6 col-xs-6">
                    <?= (!empty($points) ? $points['clientpoint_siisainfo'] : '') ?>
                </div>
            </div>
        </div><!-- /.box-body -->
    </div><!-- /.box -->
</section>