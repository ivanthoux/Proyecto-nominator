<script>
    $('input[name="user_rol"]').on('ifChecked', function(event) {
        app.userShowRolOptions();
    });
    app.userShowRolOptions = function() {
        if ($("#category-rol").is(':checked')) {
            $("#rol-options").removeClass('hide');
        } else {
            $("#rol-options").addClass('hide');
            //remove category values
            $('input[name="user_category"]').val();
        }
    };
    app.loadLocations = (officelocation_id = null) => {
        if ($('#office_field').val()) {
            $.get(app.baseUrl + 'offices/getLocations/' + $('#office_field').val(), '', function(data) {
                if (data.status == 'success') {
                    $('#user_officelocation').html("<option></option>");
                    
                    data.data.forEach(function(ol) {
                        let option = $("<option>")
                            .val(ol.officelocation_id)
                            .html(ol.officelocation_name);
                        if (officelocation_id !== null && ol.officelocation_id == officelocation_id) {
                            option.prop('selected', true);
                        }
                        $('#user_officelocation').append(option);
                    });
                    if ($('#user_officelocation option').length == 2) {
                        $('#user_officelocation option:eq(1)').prop('selected', true);
                    }
                } else {
                    console.log("ERROR");
                }
            }, 'json').error(function(error) {
                console.log(error)
            });
        } else {
            $('#user_officelocation').html("<option></option>");
        }
    };
    $(document).ready(function() {
        $('#user_password').val('');
        $('#user_password_repeat').val('');
        <?php if (isset($edit)) : ?>
            app.loadLocations(<?= $edit['user_officelocation'] ?>);
        <?php endif; ?>
    });
</script>