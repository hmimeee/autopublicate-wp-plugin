<div class="sub-title">
  <h2>About</h2>
</div>


<div class="content-page">
  <div class="ps-4 pe-4">
    <?= $user->get('about') ? htmlspecialchars_decode($user->get('about')) : 'N/A' ?>
  </div>

  <div class="text-center mt-5 bg-pattern">
    <?php if ($user->get('ID') != get_current_user_id()) : ?>
      <a href="<?= ap_route('contracts.create', $user->get('user_login')) ?>" class="btn btn-outline-primary btn-circle btn-dashed-outline-primary">Hire</a>
    <?php endif ?>
  </div>
</div>