<script>
  $(document).ready(function() {
    app.datepickerLoad();
  });

  app.datepickerLoad = function() {
    $('#pay_date').datetimepicker({
      locale: 'es',
      stepping: 5,
      showClose: true,
      format: 'D-MM-YYYY',
      useCurrent: false, //Important! See issue #1075
    });
  }
</script>