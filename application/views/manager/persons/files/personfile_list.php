<section class="content-header">
    <h1>
        <i class="fa fa-file-text"></i> Documentos <?= !empty($person) ? ' a la Persona - ' . $person['person_firstname'] . ' ' . $person['person_lastname'] : ' de TODOS los personas' ?>
    </h1>
    <ol class="breadcrumb">
        <li class="active"><a href="<?= site_url('persons') ?>"><i class="fa fa-home"></i> Personas</a></li>
    </ol>
</section>

<section class="content">
    <?= (!empty($person)) ? $this->load->view('manager/persons/person_menu', array('person' => $person, false, 'active' => 'person_file'), true) : ''; ?>
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">Listado de Documentos </h3>
            <div class="box-tools pull-right">
                <a class="btn btn-primary" href="<?= site_url('personfiles/form/' . $person_id) ?>"> <span class="fa fa-plus"></span> Nuevo</a>
            </div><!-- /.box-tools -->
        </div><!-- /.box-header -->
        <div class="box-body">
            <table id="person_file" class="table table-bordered responsive nowrap" width="100%">
                <thead>
                    <tr class="top">
                        <th>Nombre</th>
                        <th>Tipo</th>
                        <th>Fecha</th>
                        <th><?= lang('Actions') ?></th>
                    </tr>
                </thead>
                <tfoot>
                    <tr class="top">
                        <th>Nombre</th>
                        <th>Tipo</th>
                        <th>Fecha</th>
                        <th><?= lang('Actions') ?></th>
                    </tr>
                </tfoot>
            </table>
        </div><!-- /.box-body -->
    </div><!-- /.box -->
</section>