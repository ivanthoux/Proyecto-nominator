<script>

app.daterangepickerLoad = function(){ //datefilter
  var start = moment().startOf('month');
  var end = moment().endOf('month');
  let rangePicker = app.rangePicker;
  rangePicker.singleDatePicker = false;
  rangePicker.startDate= start;
  rangePicker.endDate= end;
  rangePicker.ranges= {
     'Hoy': [moment(), moment()],
     'Ayer': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
     'Esta semana': [moment().startOf('week'), moment()],
     'Este Mes': [moment().startOf('month'), moment().endOf('month')],
     'Último Mes': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
  }
  $('#datefilter').daterangepicker(rangePicker);
}

calculate_settlement = function () {
    let period = $("#datefilter").val();
    $("#sett_value").html('');
    $("#sett_error").html('');
    $("#sett_amount").val('');
    $.ajax({
      url: app.baseUrl+'settlements/calculate',
      method: 'POST',
      data:{'user':$("#sett_user").val(),'period':period},
      dataType: 'json',
      success: function(data) {
        if(data && data.sett_status && data.sett_status !== 'error'){
          $("#sett_value").html('<div class="alert alert-info">'+data.sett_status+'<h4><span class="label label-warning">'+data.sett_amount_formatted+'</span></h4> </div>');
          $("#sett_amount").val(data.sett_amount);

          if(data.sett_list){
            $("#sett_error").append('<table class="table table-striped responsive nowrap" width="100%">');
            $("#sett_error table").append('<tr>'+
              '<td>Titulo</td>'+
              '<td>Cantidad</td>'+
              '<td>Precio pack</td>'+
              '<td>Comision</td>'+
              '</tr>');
            for(let sett in data.sett_list){
              $("#sett_error table").append('<tr>'+
                                       '<td>'+data.sett_list[sett]['title']+'</td>'+
                                      '<td>'+data.sett_list[sett]['qty']+'</td>'+
                                      '<td>'+data.sett_list[sett]['clientpack_price']+'</td>'+
                                      '<td>'+data.sett_list[sett]['sett_comision']+'</td>'+
                                      '</tr>');
            }
          }
        }else{
          $("#sett_error").html('<div class="alert alert-danger">'+data.sett_msg+'</div>');
          $("#save_sett").attr('disabled',true);
        }
      },
      error: function() {
        console.log('there was an error while fetching events!');
      }
    })
};

$(document).ready(function () {
  app.daterangepickerLoad(); //datefilter
});

</script>
