<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">
  <!-- sidebar: style can be found in sidebar.less -->
  <section class="sidebar">
    <!-- Sidebar user panel -->
    <div class="user-panel bg-default">
      <div class="pull-left info">
        <?php if ($this->session->userdata('user_rol') != 'super') : ?>
          <p><?= $this->session->userdata('office_loaded')['office_name'] ?></p>
        <?php else : ?>
          &nbsp;
        <?php endif; ?>
      </div>
    </div>
    <!-- sidebar menu: : style can be found in sidebar.less -->
    <ul class="sidebar-menu">
      <li class="header">Menú <?= $this->session->userdata('user_rol_label') ?></li>
      <?php if (can("view_dashboard")) : ?>
        <li class="hidden treeview <?= !empty($activesidebar) && $activesidebar == "dashboard" ? 'active' : '' ?>">
          <a href="<?= site_url('manager/dashboard') ?>">
            <i class="fa fa-dashboard"></i>
            <span>Tablero</span>
          </a>
        </li>
      <?php endif; ?>
      <li class="treeview">
        <a href="<?= site_url('persons/form') ?>">
          <i class="fa fa-plus"></i>
          <span>Nueva persona</span>
        </a>
      </li>
      <li class="hidden treeview <?= !empty($activesidebar) && ($activesidebar == "balances" || $activesidebar == "officeclosings" || $activesidebar == "vouchers" || $activesidebar == "reports") ? 'active' : '' ?>">
        <a href="<?= site_url('reports/all') ?>">
          <i class="fa fa-file-text-o"></i>
          <span>Informes</span>
        </a>
      </li>
      <li class="treeview <?= !empty($activesidebar) && ($activesidebar == "clients" || $activesidebar == "points" || $activesidebar == "clientpacks") ? 'active' : '' ?>">
        <a href="#">
          <i class="fa fa-users"></i>
          <span>Personas</span>
        </a>
        <ul class="treeview-menu <?= !empty($activesidebar) && ($activesidebar == "scheduler" || $activesidebar == "periods_today") ? 'menu-open' : '' ?>">
          <li class="treeview <?= !empty($activesidebar) && $activesidebar == "clients" ? 'active' : '' ?>">
            <a href="<?= site_url('persons/all') ?>">
              <i class="fa fa-male"></i>
              <span>Lista</span>
            </a>
          </li>
        </ul>
        <ul class="treeview-menu <?= !empty($activesidebar) && ($activesidebar == "contracts") ? 'menu-open' : '' ?>">
          <li class="treeview <?= !empty($activesidebar) && $activesidebar == "contracts" ? 'active' : '' ?>">
            <a href="<?= site_url('contracts/all') ?>">
              <i class="fa fa-file"></i>
              <span>Contratos</span>
            </a>
          </li>
        </ul>
        <ul class="treeview-menu <?= !empty($activesidebar) && ($activesidebar == "scheduler" || $activesidebar == "periods_today") ? 'menu-open' : '' ?>">
          <li class="treeview <?= !empty($activesidebar) && $activesidebar == "employeeevaluations" ? 'active' : '' ?>">
            <a href="<?= site_url('employeeevaluations/all') ?>">
              <i class="fa fa-check-square-o"></i>
              <span>Evaluación de desempeño</span>
            </a>
          </li>
        </ul>
        <ul class="treeview-menu <?= !empty($activesidebar) && ($activesidebar == "scheduler" || $activesidebar == "periods_today") ? 'menu-open' : '' ?>">
          <li class="treeview <?= !empty($activesidebar) && $activesidebar == "paychecks" ? 'active' : '' ?>">
            <a href="<?= site_url('paychecks/all') ?>">
              <i class="fa fa-money"></i>
              <span>Recibos de sueldo</span>
            </a>
          </li>
        </ul>
      </li>
      <hr />
      <li class="treeview <?= !empty($activesidebar) && ($activesidebar == "settings" || $activesidebar == "offices" || $activesidebar == "users" || $activesidebar == "account") ? 'active' : '' ?>">
        <a href="<?= site_url('manager/settings') ?>">
          <i class="fa fa-gears"></i>
          <span>Configuración</span>
        </a>
        <ul class="treeview-menu <?= !empty($activesidebar) && ($activesidebar == "settings" || $activesidebar == "offices" || $activesidebar == "users" || $activesidebar == "account") ? 'menu-open' : '' ?>">
          <?php if (can("view_users")) : ?>
            <li class="treeview <?= !empty($activesidebar) && $activesidebar == "users" ? 'active' : '' ?>">
              <a href="<?= site_url('manager/users') ?>">
                <i class="fa fa-users"></i>
                <span>Usuarios</span>
              </a>
            </li>
            <li class="treeview <?= !empty($activesidebar) && $activesidebar == "holidays" ? 'active' : '' ?>">
              <a href="<?= site_url('manager/holidays') ?>">
                <i class="fa fa-calendar"></i>
                <span>Feriados</span>
              </a>
            </li>
            <li class="treeview <?= !empty($activesidebar) && $activesidebar == "additionals" ? 'active' : '' ?>">
              <a href="<?= site_url('manager/additionals') ?>">
                <i class="fa fa-money"></i>
                <span>Adicionales</span>
              </a>
            </li>
          <?php endif; ?>
          <?php if (can("view_settings")) : ?>
            <li class="treeview <?= !empty($activesidebar) && $activesidebar == "settings" ? 'active' : '' ?>">
              <a href="<?= site_url('manager/settings') ?>">
                <i class="fa fa-gears"></i>
                <span>General</span>
              </a>
            </li>
          <?php endif; ?>
          <li class="treeview <?= !empty($activesidebar) && $activesidebar == "account" ? 'active' : '' ?>">
            <a href="<?= site_url('manager/user/' . $this->session->userdata('user_id')) ?>">
              <i class="fa fa-gears"></i>
              <span>Mi cuenta</span>
            </a>
          </li>
        </ul>
      </li>
      <li class="treeview">
        <a href="<?= site_url('user/logout') ?>">
          <i class="glyphicon glyphicon-share-alt"></i>
          <span>Salir</span>
        </a>
      </li>
    </ul>
  </section>
  <!-- /.sidebar -->
</aside>