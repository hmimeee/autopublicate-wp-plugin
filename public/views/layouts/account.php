<main id="content">
    <div class="mt-3 mb-3">
        <div class="container">
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

                            <div class="white-spacing">
                                <h1><?= $user->get('display_name') ?> <?= $user->get('user_nicename') ? '(' . $user->get('user_nicename') . ')' : '' ?></h1>
                                <span><?= $user->get('profession_title') ?></span>
                            </div>

                            <ul class="list-group text-left">
                                <li class="list-group-item d-flex justify-content-between"><div><i class="fa fa-map-marker mr-5"></i> Location:</div> <b><?= $user->get('country') ?></b></li>
                                <li class="list-group-item d-flex justify-content-between"><div><i class="fa fa-user mr-5"></i> Member since:</div> <b><?= $user->get('user_registered') ?></b></li>
                            </ul>

                            <?php if ($user->get('ID') == get_current_user_id()) : ?>
                                <div id="menu">
                                    <ul class="menu-link">
                                        <?php if ($_current_url != ap_route('profile.edit')) : ?>
                                            <li><a href="<?= ap_route('profile.edit') ?>">Edit</a></li>
                                        <?php endif ?>
                                        <li><a href="<?= wp_logout_url() ?>">Logout</a></li>
                                    </ul>
                                </div>
                            <?php endif ?>

                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="page-body">

                        <?php include_once plugin_dir_path(__DIR__) . $_page . '.php' ?>

                    </div>
                </div>

            </div>
        </div>
    </div>
</main>