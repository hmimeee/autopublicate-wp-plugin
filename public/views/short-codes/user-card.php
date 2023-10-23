    <div class="profile-card" onclick="window.open('<?= ap_route('user_profile', $user->get('user_login')) ?>', '_blank')">
        <div class="card-body">
            <div class="row">
                <img class="col-md-4" width="150" height="150" src="<?= $user->get('image') ?: "https://ui-avatars.com/api/?name=" . $user->get('display_name') ?>" />
                <div class="col-md-8 d-flex flex-column align-items-start justify-content-center card-box">
                    <h2><?= $user->get('display_name') ?></h2>
                    <p><?= $user->get('profession_title') ?></p>
                </div>
                <div class="col-12 mt-3">
                    <span class="ratings">
                        <i class="fa fa-star <?= $rating >= 1 ? 'text-warning' : '' ?>"></i>
                        <i class="fa fa-star <?= $rating >= 2 ? 'text-warning' : '' ?>"></i>
                        <i class="fa fa-star <?= $rating >= 3 ? 'text-warning' : '' ?>"></i>
                        <i class="fa fa-star <?= $rating >= 4 ? 'text-warning' : '' ?>"></i>
                        <i class="fa fa-star <?= $rating >= 5 ? 'text-warning' : '' ?>"></i>
                        <i>(<?= $rating_count ?>)</i>
                    </span>
                    <p class="user-info">
                        <?= $user->get('about') ?>
                    </p>
                </div>
            </div>
        </div>
    </div>