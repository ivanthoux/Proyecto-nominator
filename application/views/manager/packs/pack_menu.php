<ul class="nav nav-tabs" role="tablist">
    <li class="<?= !empty($active) && $active == 'pack_form' ? 'active' : '' ?>"><a href="<?= site_url('packs/form/' . $pack['pack_id']) ?>" >Producto</a></li>
    <li class="<?= !empty($active) && $active == 'pack_rule' ? 'active' : '' ?>"><a href="<?= site_url('packrules/all/' . $pack['pack_id']) ?>" >Reglas</a></li>
</ul>
