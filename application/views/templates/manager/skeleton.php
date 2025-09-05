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
    <meta name="description" content="" />
    <meta name="viewport" content="width=device-width">
    <meta name="keywords" content="" />
    <meta name="author" content="" />

    <link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css" />
    <link rel="stylesheet" href="<?php echo base_url(MAN_CSS . "plugins.css?" . filemtime(MAN_CSS . "plugins.css")); ?>">
    <link rel="stylesheet" href="<?php echo base_url(MAN_CSS . "styles.css?" . filemtime(MAN_CSS . "styles.css")); ?>">

    <!-- Le fav and touch icons -->
    <link rel="shortcut icon" href="<?php echo base_url('assets/favicon.ico'); ?>">
    <?= $this->load->view('templates/skin_dynamic', NULL, TRUE) ?>
</head>

<body class="<?= $bodyclass ?>">

    <div class="wrapper">
        <?= $header ?>
        <?= $sidebar ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <?= $content_body ?>
        </div><!-- /.content-wrapper -->
    </div><!-- ./wrapper -->
    <?= $footer ?>
    <script>
        var app = {
            "baseUrl": "<?php echo base_url(); ?>",
            "language": "<?php echo $this->config->item('language'); ?>",
        };
    </script>

    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" crossorigin="anonymous">
    <!-- extra CSS-->
    <?php foreach ($css as $c) : ?>
        <?php if (!empty($c['url'])) : ?>
            <link rel="stylesheet" href="<?= $c['url'] ?>">
        <?php else : ?>
            <?= $c; ?>
        <?php endif; ?>
    <?php endforeach; ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
    <script type="text/javascript" defer src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/locale/es.js"></script>

    <script src="<?php echo base_url(MAN_JS . "plugins.js?" . filemtime(MAN_JS . "plugins.js")); ?>"></script>
    <?php if ($this->session->userdata('user_logged')) : ?>
        <script>
            window.setTimeout(app.keepAlive, app.keepAliveWait);
        </script>
    <?php endif; ?>
    <!-- extra js-->
    <?php foreach ($javascript as $js) : ?>
        <?php if (!empty($js['url'])) : ?>
            <script defer src="<?php echo $js['url'] ?>"></script>
        <?php else : ?>
            <?= $js; ?>
        <?php endif; ?>
    <?php endforeach; ?>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <script type="text/javascript" defer src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="<?= base_url(MAN_JS . "script.js?" . filemtime(MAN_JS . "script.js")); ?>"></script>
</body>

</html>