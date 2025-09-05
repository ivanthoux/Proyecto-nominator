<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head>
        <title></title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="robots" content="noindex,nofollow" />
        <meta property="og:title" content="My First Campaign" />
    </head>
    <body >
        <h1>Confirmación de cuenta</h1>

        <p>Hola, <?= $user_name; ?><br/>
            Necesitamos confirmar este e-mail para proceder a la activaci&oacute;n de la cuenta, por favor haga click en el bot&oacute;n de abajo.</p>
        <a href="<?= $button_link ?>">Activar</a>

        <p>Si nunca se registró en nominator, ignore este correo.</p>
        <p>Su email ha sido usado para crear una cuenta en  nominator</p>
    </body></html>
