<section class="content-header">
  <h1><i class="fa fa-male"></i> Personas</h1>
  <ol class="breadcrumb">
    <li class=""><a href="<?= site_url('persons/all') ?>"><i class="fa fa-home"></i> Personas</a></li>
  </ol>
</section>

<section class="content person_form">
  <?= (!empty($edit)) ? $this->load->view('manager/persons/person_menu', array('person' => $edit, 'parent' => !empty($parent) ? $parent : false, 'active' => 'person_form'), true) : ''; ?>
  <div class="box">
    <div class="box-header with-border">
      <h3 class="box-title"><?= isset($edit) ? (true ? 'Editar Persona' : 'Persona') : 'Crear Persona' ?></h3>
    </div><!-- /.box-header -->
    <div class="box-body">
      <form class="form" action="" onsubmit="return app.validateForm()" method="post" autocomplete="off" id="person-form">
        <input name="person_id" type="hidden" value="<?= (isset($edit) && !empty($edit['person_id'])) ? $edit['person_id'] : '' ?>" />
        <?php if (!empty($edit) && !empty($edit['person_id'])) : ?>
          <div class="row">
            <div class="col-sm-2">
              <div class="form-group">
                <label>ID Persona</label>
                <input class="form-control" readonly name="person_id" type="number" value="<?= (isset($edit) && !empty($edit['person_id'])) ? $edit['person_id'] : '' ?>" />
              </div>
            </div>
          </div>
        <?php endif; ?>
        <div class="row">
          <div class="col-sm-2">
            <div class="form-group">
              <label>Documento</label>
              <input class="form-control" required name="person_doc" type="number" value="<?= (isset($edit) && !empty($edit['person_doc'])) ? $edit['person_doc'] : '' ?>" />
            </div>
          </div>
          <div class="col-sm-3">
            <div class="form-group">
              <label>Nombre completo</label>
              <input class="form-control" required name="person_firstname" type="text" value="<?= (isset($edit) && !empty($edit['person_firstname'])) ? $edit['person_firstname'] : '' ?>" pattern=".{4,}" title="Ingresar un mínimo de 4 caractéres" />
            </div>
          </div>
          <div class="col-sm-3">
            <div class="form-group">
              <label>Apellidos</label>
              <input class="form-control" required name="person_lastname" type="text" value="<?= (isset($edit) && !empty($edit['person_lastname'])) ? $edit['person_lastname'] : '' ?>" pattern=".{4,}" title="Ingresar un mínimo de 4 caractéres" />
            </div>
          </div>
          <div class="col-sm-3">
            <div class="form-group">
              <label>Sexo</label>
              <select class="form-control filter_field" id="person_sex" name="person_sex">
                <option value="">Seleccione un sexo</option>
                <option <?= !empty($edit) && "F" == $edit["person_sex"] ? "selected" : "" ?> value="F"><?= "Femenino" ?></option>
                <option <?= !empty($edit) && "M" == $edit["person_sex"] ? "selected" : "" ?> value="M"><?= "Masculino" ?></option>
                <option <?= !empty($edit) && "X" == $edit["person_sex"] ? "selected" : "" ?> value="X"><?= "No binario" ?></option>
              </select>
            </div>
          </div>
          <div class="col-sm-3">
            <div class="form-group">
              <label>Estado civil</label>
              <select class="form-control filter_field" id="person_civil_status" name="person_civil_status">
                <option value="">Seleccione un estado civil</option>
                <option <?= !empty($edit) && "married" == $edit["person_civil_status"] ? "selected" : "" ?> value="married"><?= "Casado" ?></option>
                <option <?= !empty($edit) && "widow" == $edit["person_civil_status"] ? "selected" : "" ?> value="widow"><?= "Viudo" ?></option>
                <option <?= !empty($edit) && "single" == $edit["person_civil_status"] ? "selected" : "" ?> value="single"><?= "Soltero" ?></option>
                <option <?= !empty($edit) && "divorced" == $edit["person_civil_status"] ? "selected" : "" ?> value="divorced"><?= "Divorciado" ?></option>
              </select>
            </div>
          </div>
          <div class="col-sm-3">
            <div class="form-group">
              <label>Fecha de nacimiento</label>
              <div class="input-group">
                <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                <input type="text" name="person_birth" id="person_birth" autocomplete="off" class="form-control" value="<?= (isset($edit) && !empty($edit['person_birth'])) ? date('d-m-Y', strtotime($edit['person_birth'])) : '' ?>" />
              </div>
            </div>
          </div>
          <div class="col-sm-3">
            <div class="form-group">
              <label>C.U.I.L.</label>
              <input class="form-control" required name="person_cuil" type="text" value="<?= (isset($edit) && !empty($edit['person_cuil'])) ? $edit['person_cuil'] : '' ?>" pattern=".{11}" title="Ingresar 11 digitos sin guiones ni puntos" />
            </div>
          </div>
        </div>
        <h4>Datos bancarios</h4>
        <div class="row">
          <div class="col-sm-4">
            <div class="form-group">
              <label>CVU/ CBU</label>
              <input class="form-control" required name="person_cvu" type="number" value="<?= (isset($edit) && !empty($edit['person_cvu'])) ? $edit['person_cvu'] : '' ?>" />
            </div>
          </div>
        </div>
        <h4>Datos familiares</h4>
        <div class="row">
          <div class="col-sm-4">
            <div class="form-group">
              <label>Cantidad de hijos sin discapacidad</label>
              <input class="form-control" required name="person_children" type="number" value="<?= (isset($edit) && !empty($edit['person_children'])) ? $edit['person_children'] : '' ?>" />
            </div>
          </div>
          <div class="col-sm-4">
            <div class="form-group">
              <label>Cantidad de hijos con discapacidad</label>
              <input class="form-control" required name="person_disabled_children" type="number" value="<?= (isset($edit) && !empty($edit['person_disabled_children'])) ? $edit['person_disabled_children'] : '' ?>" />
            </div>
          </div>
        </div>
        <h4>Datos de contacto</h4>
        <div class="row">
          <div class="col-sm-3">
            <div class="form-group">
              <label>Dirección</label>
              <input class="form-control" name="person_address" type="text" value="<?= (isset($edit) && !empty($edit['person_address'])) ? $edit['person_address'] : '' ?>" title="Ingresar un mínimo de 8 caractéres" />
            </div>
          </div>
          <div class="col-sm-3">
            <div class="form-group">
              <label>Teléfono</label>
              <input class="form-control" name="person_phone" type="text" value="<?= (isset($edit) && !empty($edit['person_phone'])) ? $edit['person_phone'] : '' ?>" title="Ingresar un mínimo de 10 caractéres" />
            </div>
          </div>
          <div class="col-sm-3">
            <div class="form-group">
              <label>Celular</label>
              <input class="form-control" name="person_mobile" type="text" value="<?= (isset($edit) && !empty($edit['person_mobile'])) ? $edit['person_mobile'] : '' ?>" title="Ingresar un mínimo de 10 caractéres" />
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-3">
            <div class="form-group">
              <label>E-mail</label>
              <input class="form-control" name="person_email" type="text" value="<?= (isset($edit) && !empty($edit['person_email'])) ? $edit['person_email'] : '' ?>" title="Ingresar un mínimo de 8 caractéres" />
            </div>
          </div>
        </div>
        <h4>Contactos de emergencia</h4>
        <div class="row">
          <div class="col-sm-3">
            <div class="form-group">
              <label>Información de contacto 1</label>
              <textarea class="form-control" name="person_contact_info"><?= (isset($edit) && !empty($edit['person_contact_info'])) ? $edit['person_contact_info'] : '' ?></textarea>
            </div>
          </div>
        </div>

        <?php if (!empty($errors)) : ?>
          <div class="clearfix">
            <div class="alert alert-danger">
              <?php if (gettype($errors) == 'array') : ?>
                <?php foreach ($errors as $error) : ?>
                  <?= $error ?>
                <?php endforeach; ?>
              <?php else : ?>
                <?= $errors ?>
              <?php endif; ?>
            </div>
          </div>
        <?php endif; ?>

        <div class="box-footer">
          <div class="row">
            <div class="col-sm-12">
              <div class="col-sm-6">
                <button class="btn btn-primary <?= true ? '' : 'hidden'; ?>" id="btn-submit"><?= isset($edit) ? lang('Save') : lang('Add') ?></button>
                <input type="hidden" id="submit" value="" />
                <a class="btn" href="<?= site_url('persons/all') ?>"><?= lang('Cancel') ?></a>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</section>