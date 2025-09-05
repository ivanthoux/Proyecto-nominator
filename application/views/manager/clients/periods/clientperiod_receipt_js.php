<script>
  $(document).ready(function() {
    app.mapLoad();
    if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
      $("#send_btn").show();
      $("#print_btn").hide();

      $('.td-hide').remove();
      <?php if (isset($pdf) && $pdf) : ?>
      <?php endif; ?>
    }
  });

  app.send = () => {
    $.get(app.baseUrl + "clientperiods/receipt/<?= "$_id/$_detail" ?>/1", {},
      function(data) {
        app.dialog.success('Mensaje', 'Comprobantes enviados correctamente!');
      },
      "json"
    );
  }

  app.mapLoad = function() {
    setTimeout(function() {

      let lat = '';
      let lng = '';
      <?php foreach ($edit as $key => $value) : ?>
        lat = '<?= $payments[$key]['pay_lat'] ?>';
        lng = '<?= $payments[$key]['pay_lng'] ?>';

        // console.log('gmap_div_<?= $key ?>_0', lat, lng);
        // console.log('gmap_div_<?= $key ?>_1', lat, lng);

        if (lat.trim() != "" && lng.trim() != "") {
          // console.log('gmap_div_<?= $key ?>_0', '<?= $payments[$key]['pay_lat'] ?>', '<?= $payments[$key]['pay_lng'] ?>');
          app.showMap('gmap_div_<?= $key ?>_0', '<?= $payments[$key]['pay_lat'] ?>', '<?= $payments[$key]['pay_lng'] ?>');
          // console.log('gmap_div_<?= $key ?>_1', '<?= $payments[$key]['pay_lat'] ?>', '<?= $payments[$key]['pay_lng'] ?>');
          app.showMap('gmap_div_<?= $key ?>_1', '<?= $payments[$key]['pay_lat'] ?>', '<?= $payments[$key]['pay_lng'] ?>');
        }
      <?php endforeach; ?>
    }, 500);
  }

  app.showMap = function(gmap_div, lat, lng) {
    console.log('showshow', gmap_div, lat, lng);
    let map_options = {
      mapTypeControl: false,
      streetViewControl: false,
      fullscreenControl: false
    };

    var loc = {
      lat: parseFloat(lat),
      lng: parseFloat(lng)
    };

    var map = new google.maps.Map(document.getElementById(gmap_div), map_options);
    let marker = new google.maps.Marker({
      position: loc,
      map: map,
      title: 'Dirección pago'
    });

    map.setZoom(15);
    map.setCenter(loc);
  }
</script>