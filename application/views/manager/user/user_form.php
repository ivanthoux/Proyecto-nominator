<section class="content-header">
  <h1>
    <i class="fa fa-users"></i> <?= !$is_current_user ? 'Usuarios' : "Mi cuenta" ?>
    <?php if (!$is_current_user) : ?>
      <small><?= isset($edit) ? 'Editar Usuario' : 'Crear Usuario' ?></small>
    <?php endif; ?>
  </h1>
  <ol class="breadcrumb">
    <li class=""><a href="<?= site_url('manager/users') ?>"><i class="fa fa-home"></i> Usuarios</a></li>
  </ol>
</section>
<section class="content">

  <form class="form" action="" method="post" autocomplete="off">

    <div class="box">
      <div class="box-header with-border">
        <h3 class="box-title"><?= isset($edit) ? (!$is_current_user ? 'Editar Usuario' : 'Editar mi cuenta') : 'Crear Usuario' ?></h3>
      </div><!-- /.box-header -->
      <div class="box-body">
        <input name="user_id" type="hidden" value="<?= (isset($edit) && !empty($edit['user_id'])) ? $edit['user_id'] : '' ?>" />

        <div class="row">
          <div class="col-sm-3">
            <div class="form-group">
              <label>Código</label>
              <input class="form-control" name="user_code" type="text" value="<?= (isset($edit) && !empty($edit['user_code'])) ? $edit['user_code'] : '' ?>" />
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-sm-6">
            <div class="form-group">
              <label>Nombre</label>
              <input required class="form-control" name="user_firstname" type="text" value="<?= (isset($edit) && !empty($edit['user_firstname'])) ? $edit['user_firstname'] : '' ?>" />
            </div>
          </div>
          <div class="col-sm-6">
            <div class="form-group ">
              <label>Apellido</label>
              <input class="form-control" name="user_lastname" type="text" value="<?= (isset($edit) && !empty($edit['user_lastname'])) ? $edit['user_lastname'] : '' ?>" />
            </div>
          </div>
          <div class="col-sm-6">
            <div class="form-group">
              <label><?= lang('Email') ?></label>
              <input required class="form-control" name="user_email" type="text" value="<?= (isset($edit) && !empty($edit['user_email'])) ? $edit['user_email'] : '' ?>" />
            </div>
          </div>
          <div class="col-sm-6">
            <div class="form-group ">
              <label>Telefono</label>
              <input class="form-control" name="user_phone" type="text" value="<?= (isset($edit) && !empty($edit['user_phone'])) ? $edit['user_phone'] : '' ?>" />
            </div>
          </div>
          <?php if (can("set_user_role")) : ?>
            <?php if (isset($edit) && !empty($edit['user_incomplete']) && $edit['user_incomplete'] == 1) : ?>
              <!-- Se fuerza a que sea vendedor cuando es imcompleto-->
              <input name="user_role_id" type="hidden" value="4" />
            <?php else : ?>
              <div class="col-sm-6">
                <div class="form-group">
                  <label>Rol</label>
                  <select required class="form-control" name="user_role_id" id="user_role">
                    <?php $roles = array_reverse($roles); ?>
                    <?php foreach ($roles as $role) : ?>
                      <option <?= !empty($edit) && $role["role_id"] == $edit["user_role_id"] ? "selected" : "" ?> value="<?= $role["role_id"] ?>"><?= $role["role_name"] ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
            <?php endif; ?>

          <?php endif; ?>
          <?php if (!$is_current_user) : ?>
            <div class="col-sm-12">
              <div class="form-group">
                <label for="user_active">Estado de usuario</label>
                <div class="checkbox">
                  <label>
                    <input id="user_active" name="user_active" value="1" type="checkbox" <?= (isset($edit) && $edit['user_active'] == 1) ? 'checked' : '' ?>>
                    Activo
                  </label>
                </div>
              </div>
            </div>
          <?php else : ?>
            <input id="user_active" name="user_active" value="<?= (isset($edit) && $edit['user_active'] == 1) ? '1' : '0' ?>" type="hidden">
          <?php endif; ?>

          <?php if (isset($edit)) : ?>
            <div id="change-btn-content" class="col-sm-12">
              <div class="form-group">
                <a class="btn btn-warning" onclick="$('#change-pass-box').removeClass('hide');
                                        $('#change-btn-content').remove();">Cambiar contraseña</a>
              </div>
            </div>
          <?php endif; ?>
          <div id="change-pass-box" class="<?= (isset($edit)) ? 'hide' : '' ?> col-sm-12">
            <div class="row">
              <div class="col-sm-6">
                <div class="form-group">
                  <label>Contraseña</label>
                  <input class="form-control" name="user_password" id="user_password" autocomplete="new-password" type="password" value="" />
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label>Repetir Contraseña</label>
                  <input class="form-control" name="user_password_repeat" id="user_password_repeat" autocomplete="new-password" type="password" value="" />
                </div>
              </div>
            </div>
          </div>
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

    <?php if (false) :  //can("set_user_payment")
    ?>
      <div class="col-sm-5">

        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title">Configuración de Pago</h3>
          </div><!-- /.box-header -->
          <div class="box-body">
            <div class="col-sm-6">
              <div class="form-group">
                <label>Forma de pago</label>
                <select name="user_payment_type" class="form-control">
                  <option value="">No Percibe</option>
                  <option value="porcentaje_paquete" <?= (!empty($edit) && $edit['user_payment_type'] == "porcentaje_paquete" ? 'selected' : '') ?>>Porcentaje del paquete</option>
                  <option value="comision_turno" <?= (!empty($edit) && $edit['user_payment_type'] == "comision_turno" ? 'selected' : '') ?>>Comisión de cada turno</option>
                  <option value="sueldo" <?= (!empty($edit) && $edit['user_payment_type'] == "sueldo" ? 'selected' : '') ?>>Sueldo fijo</option>
                </select>
              </div>
            </div>

            <div class="col-sm-6">
              <div class="form-group">
                <label>Valor</label>
                <input class="form-control" name="user_payment_rate" type="text" value="<?= (isset($edit) && !empty($edit['user_payment_rate'])) ? $edit['user_payment_rate'] : '' ?>" />
                <small class="form-text text-muted" id="user_payment_help"></p>
              </div>
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>

    <div class="row">
      <div class="col-sm-6">
        <button class="btn btn-primary"><?= isset($edit) ? lang('Save') : lang('Add') ?></button>
        <?php if (!$is_current_user) : ?>
          <a class="btn" href="<?= site_url('manager/users') ?>"><?= lang('Cancel') ?></a>
        <?php endif; ?>
      </div>
    </div>
  </form>
</section>