<script>

    $(document).ready(function () {
        app.datatable({
            id:'#additional_list',
            onDraw:function () {
                $('tr td:nth-child(2)').each(function (){
                    $(this).addClass('hidden-xs hidden-sm')
                })
                $('tr td:nth-child(3)').each(function (){
                    $(this).addClass('hidden-xs')
                })
                $('tr td:nth-child(5)').each(function (){
                    $(this).addClass('hidden-xs hidden-sm')
                })
            }
        })
    });
</script>