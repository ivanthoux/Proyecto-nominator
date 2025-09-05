<section class="content-header">
    <h1>
        <i class="fa fa-file-text"></i> Documentos <?= !empty($client) ? ' al Cliente - ' . $client['client_firstname'] . ' ' . $client['client_lastname'] : ' de TODOS los clientes' ?>
    </h1>
    <ol class="breadcrumb">
        <li class="active"><a href="<?= site_url('clients') ?>"><i class="fa fa-home"></i> Clientes</a></li>
    </ol>
</section>

<section class="content">
    <?= (!empty($client)) ? $this->load->view('manager/clients/client_menu', array('client' => $client, 'parent' => !empty($parent) ? $parent : false, 'active' => 'client_file'), true) : ''; ?>
    <?php $active = isset($client) ? $client['client_active'] : 1; ?>
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">Listado de Documentos </h3>
            <div class="box-tools pull-right">
                <a class="btn btn-primary <?= $active ? '' : 'hidden'; ?>" href="<?= site_url('clientfiles/form/' . $client_id) ?>"> <span class="fa fa-plus"></span> Nuevo</a>
            </div><!-- /.box-tools -->
        </div><!-- /.box-header -->
        <div class="box-body">
            <?php if (!empty($documents)) : ?>
                <div class="row">
                    <div class="col-sm-12">
                        <?= "El cliente posee créditos que requieren de la siguiente documentación: <label>" . join(', ', $documents) . "</label>" ?>
                    </div>
                </div>
            <?php endif; ?>
            <table id="client_file" class="table table-bordered responsive nowrap" width="100%">
                <thead>
                    <tr class="top">
                        <th>Tipo</th>
                        <th>Fecha</th>
                        <th><?= lang('Actions') ?></th>
                    </tr>
                </thead>
                <tfoot>
                    <tr class="top">
                        <th>Tipo</th>
                        <th>Fecha</th>
                        <th><?= lang('Actions') ?></th>
                    </tr>
                </tfoot>
            </table>
        </div><!-- /.box-body -->
    </div><!-- /.box -->
</section>