<section class="content-header">
  <h1><i class="fa fa-exclamation"></i> Notificaciones</h1>
  <ol class="breadcrumb">
    <li class=""><a href="<?= site_url('clients/all') ?>"><i class="fa fa-home"></i> Notificaciones</a></li>
  </ol>
</section>

<section class="content client_form">
  <?= (!empty($edit)) ? $this->load->view('manager/clients/client_menu', array('client' => $edit, 'parent' => !empty($parent) ? $parent : false, 'active' => 'client_form'), true) : ''; ?>
  <?php $active = isset($edit) ? $edit['client_active'] : 1; ?>
  <div class="box">
    <div class="box-header with-border">
      <h3 class="box-title">Datos de notificación</h3>
    </div><!-- /.box-header -->
    <div class="box-body">
      <input name="client_id" type="hidden" value="<?= (isset($edit) && !empty($edit['client_id'])) ? $edit['client_id'] : '' ?>" />
      <div class="row">
        <div class="col-sm-12">
          <div class="form-group">
            <label>Título</label>
            <p><?= $notification['notification_title'] ?></p>
          </div>
        </div>
      </div>
      <?php if (!empty($notification['notification_import'])) : ?>
        <div class="row">
          <div class="col-sm-12">
            <div class="form-group">
              <label>Importación vinculada</label>
              <p><?= $notification['notification_import'] ?></p>
            </div>
          </div>
        </div>
      <?php endif; ?>
      <?php if (!empty($notification['notification_export'])) : ?>
        <div class="row">
          <div class="col-sm-12">
            <div class="form-group">
              <label>Exportación vinculada</label>
              <p><?= $notification['notification_export'] ?></p>
            </div>
          </div>
        </div>
      <?php endif; ?>
      <?php if (!empty($notification['notification_observation'])) : ?>
        <div class="row">
          <div class="col-sm-12">
            <div class="form-group">
              <label>Observaciones</label>
              <p><?= $notification['notification_observation'] ?></p>
            </div>
          </div>
        </div>
      <?php endif; ?>
      <hr />
      <table id="detail_list" class="table table-bordered responsive nowrap" width="100%">
      </table>
    </div>
</section>