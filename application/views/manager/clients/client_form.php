<section class="content-header">
  <h1><i class="fa fa-male"></i> Clientes</h1>
  <ol class="breadcrumb">
    <li class=""><a href="<?= site_url('clients/all') ?>"><i class="fa fa-home"></i> Clientes</a></li>
  </ol>
</section>

<section class="content client_form">
  <?= (!empty($edit)) ? $this->load->view('manager/clients/client_menu', array('client' => $edit, 'parent' => !empty($parent) ? $parent : false, 'active' => 'client_form'), true) : ''; ?>
  <?php $active = isset($edit) ? $edit['client_active'] : 1; ?>
  <div class="box">
    <div class="box-header with-border">
      <h3 class="box-title"><?= isset($edit) ? ($active ? 'Editar Cliente' : 'Cliente') : 'Crear Cliente' ?></h3>
    </div><!-- /.box-header -->
    <div class="box-body">
      <form class="form" action="" onsubmit="return app.validateForm()" method="post" autocomplete="off" id="client-form">
        <input name="client_id" type="hidden" value="<?= (isset($edit) && !empty($edit['client_id'])) ? $edit['client_id'] : '' ?>" />
        <div class="row">
          <div class="col-sm-2">
            <div class="form-group">
              <label>ID Cliente</label>
              <input class="form-control" <?= $active ? '' : 'disabled="disabled"'; ?> required name="client_doc" type="number" value="<?= (isset($edit) && !empty($edit['client_doc'])) ? $edit['client_doc'] : '' ?>" />
            </div>
          </div>
          <div class="col-sm-3">
            <div class="form-group">
              <label>Razon Social</label>
              <input class="form-control" <?= $active ? '' : 'disabled="disabled"'; ?> required name="client_firstname" type="text" value="<?= (isset($edit) && !empty($edit['client_firstname'])) ? $edit['client_firstname'] : '' ?>" pattern=".{4,}" title="Ingresar un mínimo de 4 caractéres" onblur="app.capital_letter(this);" />
            </div>
          </div>
          <div class="col-sm-3">
            <div class="form-group">
              <label>C.U.I.T.</label>
              <input class="form-control" <?= $active ? '' : 'disabled="disabled"'; ?> required name="client_cuil" type="text" value="<?= (isset($edit) && !empty($edit['client_cuil'])) ? $edit['client_cuil'] : '' ?>" pattern=".{11}" title="Ingresar 11 digitos sin guiones ni puntos" onblur="app.capital_letter(this);" />
            </div>
          </div>
        </div>

        <h4>Datos de contacto</h4>
        <div class="row <?= !isset($edit) ? "hidden" : "" ?>">
          <div class="col-sm-3">
            <div class="form-group">
              <label>Provincia</label>
              <select <?= $active ? '' : 'disabled="disabled"'; ?> class="form-control filter_field" id="region_id" onchange="app.changeRegion()">
                <option value="">Provincias</option>
                <?php foreach ($regions as $region) : ?>
                  <option <?= !empty($edit) && $region["id"] == $edit["region_id"] ? "selected" : "" ?> value="<?= $region["id"] ?>"><?= $region["name"] ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="col-sm-3">
            <div class="form-group">
              <label>Localidad</label>
              <select <?= $active ? '' : 'disabled="disabled"'; ?> class="form-control filter_field" id="client_city" name="client_city">
                <option value="">Localidades</option>
              </select>
            </div>
          </div>
          <div class="col-sm-3">
            <div class="form-group">
              <label>Dirección</label>
              <input <?= $active ? '' : 'disabled="disabled"'; ?> class="form-control" name="client_address" type="text" value="<?= (isset($edit) && !empty($edit['client_address'])) ? $edit['client_address'] : '' ?>" pattern=".{8,}" title="Ingresar un mínimo de 8 caractéres" onblur="app.capital_letter(this);" />
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-2">
            <div class="form-group">
              <label>Teléfono</label>
              <div class="control-group control-group-inline input-group">
                <?php if (isset($edit) && !empty($edit['client_phone']) && $active) : ?>
                  <input value="<?= $edit['client_phone'] ?>" class="form-control hidden" type="text" name="client_phone" id="client_phone" placeholder="00 0000-0000" data-mask="00 0000-0000" pattern="[0-9]{2} [0-9]{4}-[0-9]{4}">
                  <a href="callto:<?= $edit['client_phone'] ?>" class="editable editable-click inline-input"><?= $edit['client_phone'] ?></a>
                  <a class="edit"> <span class="fa fa-pencil"></span></a>
                <?php else : ?>
                  <input value="<?= !$active ? $edit['client_phone'] : ''; ?>" <?= $active ? '' : 'disabled="disabled"'; ?> placeholder="00 0000-0000" data-mask="00 0000-0000" pattern="[0-9]{2} [0-9]{4}-[0-9]{4}" id="client_phone" class="form-control" type="text" name="client_phone">
                <?php endif; ?>
              </div>
            </div>
          </div>
          <div class="col-sm-2">
            <div class="form-group">
              <span class="d-inline-block" tabindex="0" data-toggle="tooltip" title="Solo se debe ingresar la característica sin el '0' y el número sin el '15', y solo deben ingresar los números (00 0000-0000)">
                <span class="badge"><i class="fa fa-info"></i></span>
                <label>Celular</label>
              </span>
              <div class="control-group control-group-inline">
                <div class="input-group">
                  <?php if (isset($edit) && !empty($edit['client_mobile']) && $active) : ?>
                    <input value="<?= $edit['client_mobile'] ?>" class="form-control hidden" type="text" name="client_mobile" required placeholder="00 0000-0000" data-mask="00 0000-0000" pattern="[0-9]{2} [0-9]{4}-[0-9]{4}" title="Ingresar la característica sin el '0' y el número sin el '15', y solo deben ingresar los números (00 0000-0000)" id="client_mobile" onkeyup="app.changePhone(this)">
                    <div class="input-group-btn hidden">
                      <button class="btn <?= empty($edit['client_mobile_validate']) ? 'btn-danger' : 'bg-olive' ?>" type="button" onclick="app.getCode(this, 'client_mobile')">
                        <i class="fa <?= empty($edit['client_mobile_validate']) ? 'fa-exclamation-triangle' : 'fa-check' ?>"></i>
                      </button>
                    </div>
                    <a href="callto:<?= $edit['client_mobile'] ?>" class="editable editable-click inline-input"><?= $edit['client_mobile'] ?></a>
                    <a class="edit"> <span class="fa fa-pencil"></span></a>
                  <?php else : ?>
                    <input value="<?= !$active ? $edit['client_mobile'] : ''; ?>" <?= $active ? '' : 'disabled="disabled"'; ?> required placeholder="00 0000-0000" data-mask="00 0000-0000" pattern="[0-9]{2} [0-9]{4}-[0-9]{4}" title="Ingresar la característica sin el '0' y el número sin el '15', y solo deben ingresar los números (00 0000-0000)" id="client_mobile" class="form-control" type="text" name="client_mobile" onkeyup="app.changePhone(this)">
                    <div class="input-group-btn hidden">
                      <button class="btn btn-danger" <?= $active ? '' : 'disabled="disabled"'; ?> type="button" onclick="app.getCode(this, 'client_mobile')">
                        <i class="fa fa-exclamation-triangle"></i>
                      </button>
                    </div>
                  <?php endif; ?>
                </div>
              </div>
              <input type="hidden" value="<?= isset($edit) && !empty($edit['client_mobile_validate']) ? $edit['client_mobile_validate'] : '' ?>" name="client_mobile_validate" id="client_mobile_validate">
            </div>
          </div>
          <div class="col-sm-3 <?= !isset($edit) ? "hidden" : "" ?>">
            <div class="form-group">
              <label>Email</label>
              <div class="control-group control-group-inline">
                <?php if (isset($edit) && !empty($edit['client_email']) && $active) : ?>
                  <input value="<?= $edit['client_email'] ?>" class="form-control hidden" required type="text" name="client_email">
                  <a href="mailto:<?= $edit['client_email'] ?>" class="editable editable-click inline-input"><?= $edit['client_email'] ?></a>
                  <a class="edit"> <span class="fa fa-pencil"></span></a>
                <?php else : ?>
                  <input value="<?= !$active ? $edit['client_email'] : ''; ?>" <?= $active ? '' : 'disabled="disabled"'; ?> required class="form-control" type="text" name="client_email">
                <?php endif; ?>
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

        <div class="box-footer">
          <div class="row">
            <div class="col-sm-12">
              <div class="col-sm-6">
                <button class="btn btn-primary <?= $active ? '' : 'hidden'; ?>" id="btn-submit"><?= isset($edit) ? lang('Save') : lang('Add') ?></button>
                <input type="hidden" id="submit" value="" />
                <a class="btn" href="<?= site_url('clients/all') ?>"><?= lang('Cancel') ?></a>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</section>

<div class="modal modal-primary fade" id="code_confirm">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
        <h4 class="modal-title">Confirmación de Celular</h4>
      </div>
      <form action="javascript:;" onsubmit="app.validateCode()" autocomplete="off">
        <div class="modal-body">
          <div class=" row">
            <div class="col-sm-4">
              <div class="form-group">
                <label>Código</label>
                <input class="form-control" id="code" type="text" />
                <input class="form-control" id="code_id" type="hidden" />
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-outline">Validate</button>
        </div>
      </form>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div>