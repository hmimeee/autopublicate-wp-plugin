<a class="button action <?= $pagination['page'] == 1 ? 'disabled' : '' ?>" href="<?= $pagination['page'] != 1 ? '?' . http_build_query(request()->all()) . '&page_number=' . ($pagination['page'] - 1) : 'javascript:;' ?>" tabindex="-1">Previous</a>
<?php for ($i = 0; $i < $pagination['total_pages']; $i++) : ?>
    <a class="button action <?= $pagination['page'] == $i + 1 ? 'active' : '' ?>" href="<?= $pagination['page'] != ($i + 1) ? '?' . http_build_query(request()->all()) . '&page_number=' . ($i + 1) : 'javascript:;' ?>"><?= ($i + 1) ?></a>
<?php endfor ?>

<a class="button action <?= $pagination['total_pages'] == $pagination['page'] ? 'disabled' : '' ?>" href="<?= $pagination['total_pages'] == $pagination['page'] ? 'javascript:;' : '?' . http_build_query(request()->all()) . '&page_number=' . ++$pagination['page'] ?>">Next</a>