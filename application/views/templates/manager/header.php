<header class="main-header">
    <!-- Logo -->
    <a href="<?= site_url() ?>" class="logo">
        <img class="" src="<?= base_url('assets/logo.png') ?>" alt="">
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" title="Mostrar Menú" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                <!-- Messages: style can be found in dropdown.less-->
                <li class="dropdown notifications-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-envelope-o"></i>
                        <?php if (!empty($alerts)) : ?>
                            <span class="label label-danger"><?= count($alerts) ?></span><!-- Numbers of Messages-->
                        <?php endif; ?>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="header">Tiene <?= count($alerts) ?> mensajes</li>
                        <?php foreach ($alerts as $alert) : ?>
                            <li>
                                <!-- inner menu: contains the actual data -->
                                <ul class="menu">
                                    <!-- start message -->
                                    <li>
                                        <a style="white-space: normal;" href="<?= site_url("clientpacks/" . ($alert['clientpack_state'] == 'PENDIENTE' ? 'form' : 'viewed') . "/" . $alert['client_id'] . "/" . $alert['clientpack_id']) ?>">
                                            <i class="fa fa-th-large"></i> <?= $alert['clientpack_state'] ?>
                                            <?php $start_date = new DateTime($alert['clientpack_state'] == 'PENDIENTE' ? $alert['clientpack_created_at'] : $alert['clientpack_audited']); ?>
                                            <?php $diff = $start_date->diff(new DateTime(date('Y-m-d H:i:s'))); ?>
                                            <small class="pull-right"><i class="fa fa-clock-o"></i> <?= ($diff->m > 0 ? $diff->m . " mes/es " : ($diff->d > 0 ? $diff->d . " dia/s " : ($diff->h > 0 ? $diff->h . " hora/s " : ($diff->i > 0 ? $diff->i . " minuto/s" : $diff->s . " segundo/s")))) ?></small><br>
                                            <p><?= $alert['client_lastname'] . ', ' . $alert['client_firstname'] ?>, posee <b><?= $alert['clientpack_state'] ?></b> un cr&eacute;dito <b><?= $alert['pack_name'] ?></b> por un valor de <b><?= money_formating($alert['clientpack_price']) ?></b></p>
                                        </a>
                                    </li>
                                    <!-- end message -->
                                </ul>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </li>
                <!-- User Account: style can be found in dropdown.less -->
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <!-- <img src="_blank.jpg" class="user_image" alt="Usuario" /> -->
                        <span class="hidden-xs"><?= $this->session->userdata('user_firstname') . ' ' . $this->session->userdata('user_lastname') ?></span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- User image -->
                        <li class="user-header">
                            <p>
                                <?= $this->session->userdata('user_firstname') . ' ' . $this->session->userdata('user_lastname') ?>
                            </p>
                        </li>
                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <div class="pull-left">
                                <a href="<?= site_url('manager/user/' . $this->session->userdata('user_id')) ?>" class="btn btn-primary btn-flat">Mi cuenta</a>
                                <?if($this->session->userdata('user_original')){?>
                                    <a href="<?= site_url('manager/sessionchange/' . $this->session->userdata('user_original')) ?>" class="btn btn-primary btn-flat">Volver</a>
                                <?}?>
                            </div>
                            <div class="pull-right">
                                <a href="<?= site_url('user/logout') ?>" class="btn btn-primary btn-flat">
                                    <i class="glyphicon glyphicon-share-alt"></i>
                                    <span>Salir</span>
                                </a>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</header>