<div class="row">

    <div class="col-md-4">
        <div>
            <div class="my-pic">
                <img src="<?= "https://ui-avatars.com/api/?name=" . $user->get('display_name') ?>" alt="<?= $user->get('display_name') ?>">
                <div id="menu">
                    <ul class="menu-link">
                        <li><a href="<?= ap_route('user_profile', $user->get('user_login')) ?>">About</a></li>
                        <li><a href="<?= ap_route('user_profile', $user->get('user_login')) ?>">Wallet</a></li>
                        <li><a href="<?= ap_route('user_profile', $user->get('user_login')) ?>">Contracts</a></li>
                    </ul>
                </div>
            </div>

            <div class="my-detail">

                <div class="white-spacing text-center">
                    <h1><?= $user->get('display_name') ?> <?= $user->get('user_nicename') ? '(' . $user->get('user_nicename') . ')' : '' ?></h1>
                    <span><?= $user->get('profession_title') ?></span>
                </div>

                <hr />

                <ul class="list-group text-left">
                    <li class="list-group-item d-flex justify-content-between">
                        <div><i class="fa fa-map-marker me-2"></i> Location:</div> <b><?= $user->get('country') ?? 'N/A' ?></b>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <div><i class="fa fa-user me-2"></i> Member since:</div> <b><?= date('M Y', strtotime($user->get('user_registered'))) ?></b>
                    </li>
                </ul>

                <?php if ($user->get('ID') == get_current_user_id()) : ?>
                    <hr />

                    <ul class="my-detail-footer">
                        <?php if ($_current_url != ap_route('profile.edit')) : ?>
                            <li><a class="bg-primary" title="Edit" href="<?= ap_route('profile.edit') ?>"><i class="fa fa-pen"></i></a></li>
                        <?php endif ?>
                        <li><a class="bg-danger" title="Logout" href="<?= wp_logout_url() ?>"><i class="fa fa-sign-out-alt"></i></a></li>
                    </ul>
                <?php endif ?>

            </div>

            <?php if ($user->get('languages')) : ?>
                <div class="my-detail">
                    <h6>Languages</h6>
                    <ul class="list-group list-group-flush">
                        <?php foreach (explode(',', $user->get('languages')) as $language) : ?>
                            <li class="list-group-item"><i class="fa fa-angle-right"></i> <?= $language ?></li>
                        <?php endforeach ?>
                    </ul>
                </div>
            <?php endif ?>

            <?php if ($user->get('skills')) : ?>
                <div class="my-detail">
                    <h6>Skills</h6>
                    <?php foreach (explode(',', $user->get('skills')) as $skill) : ?>
                        <div class="btn btn-outline-secondary m-1 border"><?= $skill ?></div>
                    <?php endforeach ?>
                </div>
            <?php endif ?>
        </div>
    </div>

    <div class="col-md-8">
        <div class="page-body">

            <?php include_once plugin_dir_path(__DIR__) . $_page . '.php' ?>

        </div>
    </div>

</div>