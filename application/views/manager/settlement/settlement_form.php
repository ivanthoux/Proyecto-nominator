<section class="content-header">
  <h1><i class="fa fa-tasks"></i> Liquidación <?= !empty($user) ? ' de '.$user['user_firstname'].' '.$user['user_lastname'] : ''?></h1>
  <ol class="breadcrumb">
    <li class=""><a href="<?= site_url('settlement/all') ?>"> Liquidaciones</a></li>
  </ol>
</section>
<section class="content package_form">
  <div class="box">
    <div class="box-header with-border">
      <h3 class="box-title">Liquidación <?= !empty($user) ? ' de '.$user['user_firstname'].' '.$user['user_lastname'] : ''?></h3>
    </div><!-- /.box-header -->
    <div class="box-body">

      <form class="form" id="settForm" method="post" autocomplete="off">
        <input name="sett_id" id="sett_id" type="hidden" value="<?= (isset($edit) && !empty($edit['sett_id']) ) ? $edit['sett_id'] : '' ?>"/>
        <input name="sett_user" id="sett_user" type="hidden" value="<?= $user['user_id'] ?>"/>
        <input name="sett_amount" id="sett_amount" type="hidden" value=""/>

        <div class="row">
          <div class="col-sm-3">
            <div class="form-group">
              <label>Asesor a liquidar</label>
              <p><?= $user["user_firstname"].' '.$user["user_lastname"] ?></p>
            </div>
          </div>
          <div class="col-sm-3">
            <div class="form-group">
              <label>Pago por <?= str_replace('_',' por ',$user['user_payment_type'])?></label>
              <?if($user['user_payment_type'] == "sueldo"){?>
                <label>Valor Fijo <?= money_formating($user['user_payment_rate'])?></label>
              <?}elseif($user['user_payment_type'] == "comision_turno"){?>
                <label>Valor %<?= $user['user_payment_rate'] ?> por turno</label>
              <?}elseif($user['user_payment_type'] == "porcentaje_paquete"){?>
                <label>Valor %<?= $user['user_payment_rate'] ?> por paquete. <br/>Al inicio %<?= $user['user_payment_rate']/2?> y %<?= $user['user_payment_rate']/2?> al final</label>
              <? } ?>
            </div>
          </div>
          <div class="col-sm-3">
            <div class="form-group">
              <label>Período a liquidar</label>
              <input class="form-control rangepicker" onchange="calculate_settlement()" id="datefilter" autocomplete="off" name="sett_date" id="sett_start" type="text" value="<?= (isset($edit) && !empty($edit['sett_start']) ) ? date('d-m-Y',strtotime($edit['sett_start'])).' / '.date('d-m-Y',strtotime($edit['sett_end'])) : '' ?>"/>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-6" id="sett_value">

          </div>
        </div>
        <div class="row">
          <div class="col-sm-12">
            <div id="sett_error">
            </div>
            <div class="form-group">
              <label>Observaciones</label>
              <textarea class="form-control" name="sett_description"><?= (isset($edit) && !empty($edit['sett_description']) ) ? $edit['sett_description'] : '' ?></textarea>
            </div>
          </div>
        </div>

        <? if (!empty($errors)) { ?>
          <div class="clearfix">
            <div class="alert alert-danger">
              <?= $errors ?>
            </div>
          </div>
        <? } ?>

        <div class="box-footer">
          <div class="row">
            <div class="col-sm-12">
              <div class="col-sm-6">
                <button class="btn btn-primary" id="save_sett">Confirmar Liquidación</button>
                <a class="btn" href="<?= site_url('settlement') ?>"><?= lang('Cancel') ?></a>
              </div>
            </div>
          </div>
        </div>
      </form>

    </div>
  </div>
</section>
