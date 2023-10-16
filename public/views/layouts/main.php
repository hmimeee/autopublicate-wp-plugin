<main id="content">
    <div class="mt-3 mb-3">
        <div class="container">
            <?php if (isset($_GET['success_message'])) : ?>
                <div class="alert alert-success"><i class="fa fa-check me-1"></i> <?= $_GET['success_message'] ?></div>
            <?php endif;
            unset($_GET['success_message']); ?>

            <?php if (isset($_GET['error_message'])) : ?>
                <div class="alert alert-danger"><?= $_GET['error_message'] ?></div>
            <?php endif;
            unset($_GET['error_message']); ?>

            <?php file_exists(__DIR__ . '/' . $_layout . '.php') ? include_once($_layout . '.php') : include_once(plugin_dir_path(__DIR__) . $_page . '.php') ?>
        </div>
    </div>
</main>