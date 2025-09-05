<script>

    var productDatatable;
    var productUrl = "<?= site_url('products/datatables?dt=true') ?>";

    apply_filter = function () {
        var urlSend = productUrl;
        <?foreach($filters as $field => $values){?>
          urlSend += "&filter[<?=$field?>]=" + $("#filter_<?=$field?>").val();
        <?}?>

        productDatatable.ajax.url(urlSend);
        productDatatable.ajax.reload();
    };

    $(document).ready(function () {
        productDatatable = app.datatable({
            url: productUrl,
            id: "#product_list",
            order: [[1, "asc"]]
        });
    });
</script>
