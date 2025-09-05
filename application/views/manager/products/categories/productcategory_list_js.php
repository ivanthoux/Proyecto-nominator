<script>

    var productDatatable;
    var productUrl = "<?= site_url('productcategories/datatables?dt=true') ?>";

    $(document).ready(function () {
        productDatatable = app.datatable({
            url: productUrl,
            id: "#product_list",
            order: [[0, "asc"]]
        });
    });
</script>
