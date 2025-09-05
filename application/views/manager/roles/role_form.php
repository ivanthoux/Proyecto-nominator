<section class="content-header">
    <h1>
        Rol
        <small><?= isset($edit) ? 'Editar Rol' : 'Crear Rol' ?></small>
    </h1>
    <ol class="breadcrumb">
        <li class=""><a href="<?= site_url('manager/roles') ?>"><i class="ion ion-person"></i> Rol</a></li>
        <li class="active"><a href="<?= site_url('manager/role') ?>"> Nuevo</a></li>
    </ol>
</section>
<section class="content">
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title"><?= isset($edit) ? 'Editar Rol' : 'Crear Rol' ?></h3>
        </div><!-- /.box-header -->
        <div class="box-body">
            <form class="form" action="" method="post" autocomplete="off">
                <input name="role_id" type="hidden" value="<?= (isset($edit) && !empty($edit['role_id']) ) ? $edit['role_id'] : '' ?>"/>

                <div class="row">
                    <div class="col-sm-4">
                        <div class="col-sm-12">

                            <div class="form-group">
                                <label>Clave</label>
                                <input class="form-control" name="role_key" type="text" value="<?= (isset($edit) && !empty($edit['role_key'])) ? $edit['role_key'] : '' ?>"/>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group ">
                                <label>Nombre</label>
                                <input class="form-control" name="role_name" type="text" value="<?= (isset($edit) && !empty($edit['role_name']) ) ? $edit['role_name'] : '' ?>"/>
                            </div>
                        </div>



                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-4">
                        <div class="col-sm-12">
                            <h4>Permisos</h4>
                            <?php $group_before = "" ?>
                            <? foreach ($permissions as $permission_group => $permission_list): ?>
                            <table class="table table-bordered  responsive nowrap" width="100%">
                                <thead>
                                    <tr>
                                        <th><?= $permission_group ?></th>

                                    </tr>
                                </thead>
                                <tbody>

                                    <? foreach ($permission_list as  $permission_list) : ?>
                                    </tr>

                                <td><div class="checkbox">
                                        <label>
                                            <input <?= isset($edit) && in_array($permission_list["permission_id"], $permissions_role) ? "checked" : "" ?> type="checkbox" name="permissions[]" value="<?= $permission_list['permission_id'] ?>" >
                                            <?= $permission_list['permission_name'] ?>
                                        </label>
                                    </div>
                                </td>

                                </tr>
                                <? endforeach; ?>
                                </tbody>
                            </table>

                            <? endforeach; ?>
                        </div>

                    </div>
                </div>


        </div>
        <?php if (!empty($errors)): ?>
            <div class="clearfix">
                <div class="alert alert-danger">
                    <?php if (gettype($errors) == 'array'): ?>
                        <?php foreach ($errors as $error): ?>
                            <?= $error ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <?= $errors ?>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
        <div class="box-footer">
            <div class="row">
                <div class="col-sm-12">
                    <div class="col-sm-6">
                        <button class="btn btn-primary"><?= isset($edit) ? lang('Save') : lang('Add') ?></button>
                        <a class="btn" href="<?= site_url('manager/roles') ?>"><?= lang('Cancel') ?></a>
                    </div>
                </div>
            </div>
        </div>

        </form>
    </div>
</div>
</section>
