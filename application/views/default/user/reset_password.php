
<div class="container">
    <div class="row">
        <div class="col-sm-4 col-sm-offset-4">     
            <!-- <div class="logo-container text-center">
                <img class="" src="<?= base_url('assets/logo.png') ?>" alt="">
            </div> -->
            
            <div class="panel panel-default">
                <div class="panel-body">
                    <form id="reset_form" class="form" method="post" action="javascript:;" onsubmit="app.sendNewPassword();">
                        <input type="hidden" name="user_id" value="<?= $user_id; ?>">
                        <input type="hidden" name="reset_hash" value="<?= $reset_hash; ?>">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Nueva Contraseña</label>
                                    <input type="password" class="form-control" required value="" name="password">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Repetir Contraseña</label>
                                    <input type="password" class="form-control" required value="" name="password_repeat">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <button class="btn btn-primary" type="submit">Enviar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>