<script>
  app.submit = () => {
    $('#form_banks').submit();
  }

  $(document).ready(() => {
    $(".header-live-search").typeahead({
      delay: 500,
      source: function(query, process) {
        return $.get(
          app.baseUrl + "clients/getSearch", {
            search: {
              value: query
            }
          },
          function(data) {
            return process(data);
          },
          "json"
        );
      },
      afterSelect: function(selected) {
        $('#' + $(this.$element).attr('id') + '_h').val(selected.id);
        // $().val(selected.id);
      },
    });
  });
</script>