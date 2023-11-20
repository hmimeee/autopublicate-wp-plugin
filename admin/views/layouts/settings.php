<div class="privacy-settings-header">
    <div>
        <h1><?= $pageTitle ?? esc_html(get_admin_page_title()); ?></h1>
    </div>

    <nav class="privacy-settings-tabs-wrapper">
        <a href="<?= ap_admin_route('settings') ?>" class="privacy-settings-tab <?= request('tab', 'general') == 'general' ? 'active' : '' ?>">General </a>
        <a href="<?= ap_admin_route('settings', ['tab' => 'payment']) ?>" class="privacy-settings-tab <?= request('tab') == 'payment' ? 'active' : '' ?>">Payment</a>
    </nav>
</div>

<?php if ($alert = ap_alert()) : ?>
    <div class="<?= $alert['class'] ?> notice">
        <p><?= $alert['message'] ?></p>
    </div>
<?php endif ?>

<?php include_once(plugin_dir_path(__DIR__) . $_page); ?>