<?
$color_primary = !empty($settings['palette_primary']) ? $settings['palette_primary'] : '#014495';
$color_second = !empty($settings['palette_second']) ? $settings['palette_second'] : '#EEA956';
?>
<style>
.login-page{background-color:<?= $color_primary ?>}
.skin-green-light .main-header .navbar{background-color:<?= $color_primary ?>}
.skin-green-light .main-header .logo{background-color:<?= $color_primary ?>}
.skin-green-light .main-header .logo:hover{background-color:<?= $color_second ?>}
.skin-green-light .main-header .navbar .sidebar-toggle:hover{background-color:<?= $color_second ?>}
.skin-green-light .main-header li.user-header{background-color:<?= $color_primary ?>}
.skin-green-light .sidebar-menu>li.active{border-left-color:<?= $color_primary ?>}
.btn-primary{background-color:<?= $color_primary ?>;background-image: none;}
.btn-primary:hover{background-color:<?= $color_primary ?>}
.btn-success{background-color:<?= $color_primary ?>;}
.box.box-success{border-top-color:<?= $color_primary ?>}
.box.box-solid.box-success{border:1px solid <?= $color_primary ?>}
.box.box-solid.box-success>.box-header{color:#fff;background:<?= $color_primary ?>;}
.nav-tabs-custom.tab-success>.nav-tabs>li.active{border-top-color:<?= $color_primary ?>}
.form-group.has-success label{color:<?= $color_primary ?>}
.progress-bar-success{background-color:<?= $color_primary ?>}
.todo-list .success{border-left-color:<?= $color_primary ?>}
.direct-chat-success .right>.direct-chat-text{background:<?= $color_primary ?>;border-color:<?= $color_primary ?>;}
.direct-chat-success .right>.direct-chat-text:after,.direct-chat-success .right>.direct-chat-text:before{border-left-color:<?= $color_primary ?>}
.bg-green,.callout.callout-success,.alert-success,.label-success,.modal-success .modal-body{background-color:<?= $color_primary ?> !important}
.text-green{color:<?= $color_primary ?> !important}
.form-group.has-success .form-control{border-color:<?= $color_primary ?>;box-shadow:none}
.bg-green-gradient{background:<?= $color_primary ?> !important;background:-webkit-gradient(linear, left bottom, left top, color-stop(0, <?= $color_primary ?>), color-stop(1, #00ca6d)) !important;background:-ms-linear-gradient(bottom, <?= $color_primary ?>, #00ca6d) !important;background:-moz-linear-gradient(center bottom, <?= $color_primary ?> 0, #00ca6d 100%) !important;background:-o-linear-gradient(#00ca6d, <?= $color_primary ?>) !important;filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#00ca6d', endColorstr='<?= $color_primary ?>', GradientType=0) !important;color:#fff}
</style>
