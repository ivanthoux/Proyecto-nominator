<div class="hidden-xs"><br/><br/><br/></div>

<div class="login-box col-sm-4 col-sm-offset-4">
    <div class="login-logo">
        <a href="#" style="font-size: 25px;"><b>nominator</b></a>
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body">
        <div class="hidden-xs"><br/><br/></div>
        <p class="login-box-msg">Registre su cuenta</p>

        <form action="javascript:;" onsubmit="app.signup();" method="post">
            <div class="form-group has-feedback">
                <input name="firstname" type="text" class="form-control" placeholder="Nombre">
                <span class="glyphicon glyphicon-user form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <input name="lastname" type="text" class="form-control" placeholder="Apellido">
                <span class="glyphicon glyphicon-user form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <input name="account" type="text" class="form-control" placeholder="Nombre Local">
                <span class="glyphicon glyphicon-user form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <input name="email" type="email" class="form-control" placeholder="Email" autocomplete="off">
                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <input name="password" type="password" class="form-control" placeholder="Contraseña" autocomplete="off">
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <input name="password_repeat" type="password" class="form-control" placeholder="Repetir Contraseña" autocomplete="off">
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>
            <div class="row">
                <div class="col-xs-4">
                    <button type="submit" class="btn btn-success btn-block btn-flat">Entrar</button>
                </div>
                <!-- /.col -->
            </div>
        </form>
        <!-- /.social-auth-links -->

    </div>
    <!-- /.login-box-body -->
</div>
<!-- /.login-box -->
