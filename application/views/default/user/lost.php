<div class="hidden-xs"><br/><br/><br/></div>
<div class="login-box col-sm-4 col-sm-offset-4">
    <!-- <div class="logo-container text-center">
        <img class="" src="<?= base_url('assets/logo.png') ?>" alt="">
    </div> -->
    <!-- /.login-logo -->
    <div class="login-box-body">
        <p class="login-box-msg">Resetear Contraseña</p>

        <form  action="javascript:;" onsubmit="app.resetPassword();" method="post">
            <div class="form-group has-feedback">
                <label>Email</label>
                <input name="email" type="email" class="form-control" placeholder="Email">
                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
            </div>

            <div class="row">
                <div class="col-xs-4">
                    <button type="submit" class="btn btn-primary btn-block btn-flat">Enviar</button>
                </div>
                <div class="col-xs-4">
                    <a href="<?=  site_url()?>" class="btn btn-info">Volver</a>
                </div>
                <!-- /.col -->
            </div>
        </form>

    </div>
    <!-- /.login-box-body -->
</div>
<!-- /.login-box -->