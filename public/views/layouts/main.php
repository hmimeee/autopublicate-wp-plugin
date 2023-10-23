<main id="content">
    <div class="mt-3 mb-3">
        <div class="container">
            <nav class="navbar navbar-expand-custom navbar-mainbg bg-primary justify-content-center">
                <button class="navbar-toggler btn-outline-primary border-0" type="button" aria-controls="ap-navbar" aria-expanded="false" aria-label="Toggle navigation">
                    <i class="fas fa-bars text-white"></i>
                </button>
                <div class="collapse navbar-collapse justify-content-center" id="ap-navbar">
                    <ul class="navbar-nav ml-auto">
                        <div class="hori-selector">
                            <div class="left"></div>
                            <div class="right"></div>
                        </div>
                        <li class="nav-item <?= ap_is_route(['profile.edit', 'profile', 'profile.main', 'user_profile' => $_current_user->get('user_login')], [], true) ? 'active' : '' ?>">
                            <a class="nav-link" href="<?= ap_route('user_profile', $_current_user->get('user_login')) ?>"><i class="fa fa-id-card"></i>Profile</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="<?= ap_route('user_profile', $_current_user->get('user_login')) ?>"><i class="fa fa-dollar-sign"></i>Wallet</a>
                        </li>
                        <li class="nav-item <?= ap_is_route('contracts.index') ? 'active' : '' ?>">
                            <a class="nav-link" href="<?= ap_route('contracts.index') ?>">
                                <i class="fa fa-briefcase"></i>Contracts
                                <sup><span class="badge bg-light text-dark rounded-circle"><?= $_contracts_need_action ?></span></sup>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <?php if (isset($_SESSION['success_message'])) : ?>
                <div class="alert alert-success"><i class="fa fa-check me-1"></i> <?= $_SESSION['success_message'] ?></div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error_message'])) : ?>
                <div class="alert alert-danger"><?= $_SESSION['error_message'] ?></div>
            <?php endif; ?>

            <?php file_exists(__DIR__ . '/' . $_layout . '.php') ? include_once($_layout . '.php') : include_once(plugin_dir_path(__DIR__) . $_page . '.php') ?>
        </div>
    </div>
</main>