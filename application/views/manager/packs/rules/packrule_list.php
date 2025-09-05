<section class="content-header">
    <h1><i class="fa fa-tags"></i> Producto</h1>
    <ol class="breadcrumb">
        <li class=""><a href="<?= site_url('packs/all') ?>"><i class="fa fa-home"></i> Productos</a></li>
    </ol>
</section>
<section class="content">
    <?= (!empty($pack)) ?$this->load->view('manager/packs/pack_menu', array('pack' => $pack, 'parent' => !empty($parent) ? $parent : false, 'active' => 'pack_rule'), true): ''; ?>
    <?php $active = isset($pack) ? $pack['pack_active'] : 1; ?>
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">Listado de Reglas para <b><?= $pack['pack_name'] ?></b> </h3>
            <div class="box-tools pull-right">
                <a class="btn btn-primary <?= $active ? '' : 'hidden'; ?>" href="<?= site_url('packrules/form/' . $pack_id) ?>"> <span class="fa fa-plus"></span> Nueva</a>
            </div><!-- /.box-tools -->

        </div><!-- /.box-header -->
        <div class="box-body">
            <table id="packrules_list" class="table table-bordered responsive nowrap" width="100%">
                <thead>
                    <tr class="top">
                        <th>Nombre</th>
                        <th>Tipo</th>
                        <th>Valor</th>
                        <?php if ($this->session->userdata('user_rol_label') == 'Super'): ?>
                            <th><?= lang('Actions') ?></th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tfoot>
                    <tr class="top">
                        <th>Nombre</th>
                        <th>Tipo</th>
                        <th>Valor</th>
                        <?php if ($this->session->userdata('user_rol_label') == 'Super'): ?>
                            <th><?= lang('Actions') ?></th>
                        <?php endif; ?>
                    </tr>
                </tfoot>
            </table>
        </div><!-- /.box-body -->
    </div><!-- /.box -->
</section>
