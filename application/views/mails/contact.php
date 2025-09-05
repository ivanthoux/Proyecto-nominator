<html>
    <body>
        <h1><?=lang('Nuevo Mensaje de contacto')?></h1> 
        <p><?=lang('Enviado')?> : <?= date('m-d-Y') ?></p>
        <p><?=lang('Remitente')?>: {nombre} - ({email}) - ({telefono})</p>
        <p><?=lang('Fechas')?>: {dates_in} - {dates_out}</p>
        <p><?=lang('Cabaña')?>: {roomtype}</p>
        <p><i><?=lang('Mensaje')?></i> <br/>{message}</p>
        
        <p><a href="mailto:{email}"><b><?=lang('Responder')?></b> </a></p>
        
    </body>
</html>