
<script>
    var userList;
    var userListUrl = "<?= site_url('manager/users?dt=true') ?>";
    var filterAdded = false;

    dataLoadingStart = function (type) {
        $('#user_list').css('opacity', '0.7');
    };
    dataLoadingEnd = function (type) {
        $('#user_list').css('opacity', '1');
    };
    
    apply_filter = function () {
        var urlSend = userListUrl;
        <?php foreach ($filters as $field => $values): ?>
            urlSend += "&filter[<?= $field ?>]=" + $("#filter_<?= $field ?>").val();
        <?php endforeach; ?>
        userList.ajax.url(urlSend);
        userList.ajax.reload();
    };

    $(document).ready(function () {
        userList = app.datatable({
            url: userListUrl,
            id: "#user_list",
            order: [[3, "asc"]]
        });
    });
</script>