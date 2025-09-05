<section class="content-header team">
  <h1 class="row">
    <div class="col-sm-6 col-xs-10">
      Agenda <?=!empty($filters_applied) ? 'FILTRADA ' : ''?><i class="fa fa-calendar"></i>
    </div>

    <span class="col-xs-2 visible-xs" onclick="$(this).parent().find('.col-sm-2').toggleClass('hidden-xs')">
      <span class="fa fa-arrow-down"></span>
    </span>
    <div class="clearfix visible-xs"></div>
    <?
    $filters_label = array(
      'clientperiod_client'=>'Filtrar Clientes',
      'clientperiod_pack'=>'Filtrar Producto');

    foreach($filters as $field => $values){
      if(!empty($filters_label[$field])){
      ?>
      <div class="col-sm-2 hidden-xs">
        <select class="form-control filter_field" id="filter_<?=$field?>" name="<?=$field?>" onchange="apply_filter();">
          <option value=""><?= $filters_label[$field]?></option>
          <?
          if($field == "appoint_agendacolor"){
            foreach($values as $val){ ?>
              <option style="width:50px; background-color:<?= $val['value']?>" value="<?=$val['value']?>" <?= !empty($filters_applied[$field]) && $filters_applied[$field] == $val['value'] ? 'selected' :'' ?>></option>
            <? }
          }else{
            foreach($values as $val){ ?>
              <option value="<?=$val['value']?>" <?= !empty($filters_applied[$field]) && $filters_applied[$field] == $val['value'] ? 'selected' :'' ?>><?= $val['title']?></option>
            <? }
          } ?>
        </select>
      </div>
    <? }
    } ?>
  </h1>
</section>

<section class="content">
  <div class="box">

    <div class="box-body">

      <div id='calendar'></div>

    </div>
  </div>
</section>

<div class="modal fade" id="form-appointment">
  <div class="modal-dialog modal-dialog-client">
    <div class="modal-content main">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Datos de la cuota</h4>
      </div>
      <div class="modal-body">
        <?
        // echo $this->load->view('manager//event_form',(array('in_modal'=>true,'clients' => $clients,'users' => $users,'packages' => $packages)), TRUE);
        ?>
      </div>
    </div>
  </div>
</div>
