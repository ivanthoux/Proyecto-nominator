<!doctype html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js">
<!--<![endif]-->

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title><?= $this->session->userdata('settings')['name'] ?></title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width">
    <meta name="keywords" content="" />
    <meta name="author" content="" />


    <link rel="stylesheet" href="<?php echo base_url(DEF_CSS . "plugins.css?" . filemtime(DEF_CSS . "plugins.css")); ?>">
    <link rel="stylesheet" href="<?php echo base_url(DEF_CSS . "styles.css?" . filemtime(DEF_CSS . "styles.css")); ?>">
    <!-- extra CSS-->
    <?
    foreach ($css as $c) {
        if (!empty($c['url'])) {
    ?>
            <link rel="stylesheet" href="<?php echo base_url() . DEF_CSS . $c['url'] ?>">
    <?
        } else {
            echo $c;
        }
    }
    ?>
    <? echo $this->load->view('templates/skin_dynamic', NULL, TRUE) ?>
    <!-- Le fav and touch icons -->
    <link rel="shortcut icon" href="<?php echo base_url(IMAGES . 'ico/favicon.ico'); ?>">
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,900&display=swap" rel="stylesheet">
</head>

<body class="hold-transition login-page <?= $pageName ?>" style="<?= !empty($settings['bg']) ? "background-image: url('" . site_url('resources/' . $settings['bg']) . "')" : '' ?>">
    <?php echo $header ?>
    <?php if ($pageName != 'inicio') { ?>
        <div class="container default-container">
        <?php } ?>
        <?php echo $content_body ?>
        <?php if ($pageName != 'inicio') { ?>
        </div>
    <?php } ?>

    <?php echo $footer ?>

    <script>
        var app = {
            "baseUrl": "<?php echo base_url(); ?>",
            "language": "<?php echo $this->config->item('language'); ?>"
        };
    </script>

    <script src="<?php echo base_url(DEF_JS . "plugins.js"); ?>"></script>

    <?php
    foreach ($javascript as $js) {
        if (!empty($js['url'])) {
    ?>
            <script defer src="<?php echo base_url() . DEF_JS . $js['url'] ?>"></script>
    <?php
        } else {
            echo $js;
        }
    }
    ?>
    <script src="<?= base_url(DEF_JS . "script.js?" . filemtime(DEF_JS . "script.js")); ?>"></script>
</body>

</html>