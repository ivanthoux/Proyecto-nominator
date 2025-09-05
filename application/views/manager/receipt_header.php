<div class="row receipt-header">
    <div class="col-xs-8">

        <?if(!empty($settings['logo'])){?>
        <img src="<?= site_url('resources/'.$settings['logo']) ?>" alt="nominator">
        <?}?>

    </div>
    <div class="col-xs-4 text-right">
        <address>
            <?= !empty($settings) && !empty($settings['report_head']) ? nl2br($settings['report_head']) : ''?>
        </address>
    </div>
    <hr/>
</div>
