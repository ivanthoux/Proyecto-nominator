<script>

  app.schedulerFormUrl = '<?=site_url('clientpayments/form')?>/';
  apply_filter = function(){
    var form = document.createElement("form");
    form.setAttribute("method", "post");
    form.setAttribute("action", "<?=site_url('scheduler/filtered')?>");
    form.setAttribute("target", "view");

    <?foreach($filters as $field => $values){?>
      var hiddenField = document.createElement("input");
      hiddenField.setAttribute("type", "hidden");
      hiddenField.setAttribute("name", "filter[<?=$field?>]");
      hiddenField.setAttribute("value", $("#filter_<?=$field?>").val());
      form.appendChild(hiddenField);
    <?}?>
    document.body.appendChild(form);
    window.open('', 'view');
    form.submit();
  }

  function appointmentNew(start, end){
    $("#form-appointment").modal('show');
    app.appointmentFormLoad(start, end);
  }

  function appointmentMoved(event){
    window.location.href = app.schedulerFormUrl +event.appoint_id +'/'+event.start;
  }

  getFiltering = function(){
    return {
      filter:{
        appoint_client: $("#filter_appoint_client").val(),
        appoint_resource: $("#filter_appoint_resource").val(),
        appoint_clientpack: $("#filter_appoint_clientpack").val(),
        appoint_agendacolor: $("#filter_appoint_agendacolor").val(),
      }
    };
  }

$(function() { // document ready
  var widthScreen = (window.innerWidth > 0) ? window.innerWidth : screen.width;
  var agendaDays = 7;
  if(widthScreen < 768){
    agendaDays = 3;
    if(widthScreen < 500){
      agendaDays = 1;
    }
  }
  $('#calendar').fullCalendar({
    now: moment(),
    editable: true, // enable draggable events
    defaultView: 'agenda',
    height:"auto",
    dayCount: agendaDays,
    minTime: '08:00',
    maxTime: '21:00',
    nowIndicator: true,
    selectable: true,
    header: {
      left: 'today prev,next',
      center: 'title',
      right: 'agenda,solohoy,month,list, addEventButton'
    },
    buttonText: {
      agenda:   'Agenda',
      today:    'Hoy',
      solohoy:  'Hoy',
      month:    'Mes',
      week:     'Semana',
      day:      'Día',
      list:     'Lista'
    },
    views: {
      solohoy: {
        dayCount: 1
      },
      lista: {
        dayCount: 7
      }
    },
    dayClick: function(start, jsEvent, view) {
      console.log('day click '+start.format());
      appointmentNew(start);
    },
    select: function(start, end, jsEvent, view){
      console.log('selected '+start.format());
      appointmentNew(start, end);
    },
    eventDrop: function(event, delta, revertFunc) {
      console.log(event);
      if (!confirm("Confirmas reagendarlo aqui?")) {
        revertFunc();
      }else{
        appointmentMoved(event);
      }
    },
    drop: function( date, jsEvent, ui, resourceId ){
      console.log(date);
    },
    customButtons: {
      addEventButton: {
        text: 'Nuevo',
        bootstrapGlyphicon: 'glyphicon-plus',
        click: function() {
          appointmentNew();
        }
      }
    },
    eventClick: function(calEvent, jsEvent, view) {
      window.location.href = app.schedulerFormUrl +calEvent.clientperiod_client;
    },
    events: {
      url: '<?=site_url('scheduler/appointments')?>',
      type: 'POST',
      data: getFiltering(),
      error: function() {
        console.log('there was an error while fetching events!');
      }
    }
  });

});

</script>
