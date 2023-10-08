<div class="wrap">
    <h1 class="wp-heading-inline"><?= $pageTitle ?? esc_html(get_admin_page_title()); ?></h1>

    <div class="alert-box-property"></div>

    <?php if ($alert = alert()) : ?>
        <div class="<?= $alert['class'] ?> notice">
            <p><?= $alert['message'] ?></p>
        </div>
    <?php endif ?>

    <?php include_once($_page); ?>

</div>