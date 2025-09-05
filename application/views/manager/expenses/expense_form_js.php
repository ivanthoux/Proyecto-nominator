<script>

    $(document).ready(function () {
      app.datepickerLoad();
    });

    app.datepickerLoad = function(){
      $('.datepicker').daterangepicker(app.rangePicker);
    }

</script>
