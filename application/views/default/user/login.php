<div class="hidden-xs"><br/><br/><br/></div>

<div class="login-box col-sm-4 col-sm-offset-4">
    <!-- <div class="logo-container text-center">
        <img class="" src="<?= base_url('assets/logo.png') ?>" alt="">
    </div> -->
    <!-- /.login-logo -->
    <div class="login-box-body">
        <p class="login-box-msg">Coloque sus datos para iniciar sesión</p>

        <form action="javascript:;" onsubmit="app.login();" method="post">
            <div class="form-group has-feedback">
                <input name="email" type="email" class="form-control" placeholder="Email">
                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <input name="password" type="password" class="form-control" placeholder="Contraseña">
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>
            <div class="row">
                <div class="col-xs-8">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="createcookie" value="1"> No cerrar sesión
                        </label>
                    </div>
                </div>
                <!-- /.col -->
                <div class="col-xs-4">
                    <button type="submit" class="btn btn-success btn-block btn-flat">Entrar</button>
                </div>
                <!-- /.col -->
            </div>
        </form>
        <!-- /.social-auth-links -->

        <a href="<?= site_url('user/lost') ?>">¿Olvido su Contraseña?</a><br>

        <? if (false) { ?>
        <hr/>

        <a href="<?= site_url('user/invite') ?>" class="btn btn-success btn-block">Nueva cuenta</a>
        <? } ?>
    </div>
    <!-- /.login-box-body -->
</div>
<!-- /.login-box -->
