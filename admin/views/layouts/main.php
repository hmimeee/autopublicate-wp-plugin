<div class="wrap">
    <h1 class="wp-heading-inline">
        <?php if(isset($back)): ?>
            <a class="button cancel" href="<?= $back['url'] ?>"><span class="dashicons dashicons-arrow-left-alt"></span> <?= $back['label'] ?></a>
        <?php endif ?>

        <?= $pageTitle ?? esc_html(get_admin_page_title()); ?>
    </h1>

    <div class="alert-box-property"></div>

    <?php if ($alert = ap_alert()) : ?>
        <div class="<?= $alert['class'] ?> notice">
            <p><?= $alert['message'] ?></p>
        </div>
    <?php endif ?>

    <?php include_once(plugin_dir_path(__DIR__) . $_page); ?>

</div>