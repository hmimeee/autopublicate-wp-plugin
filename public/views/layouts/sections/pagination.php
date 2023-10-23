<ul class="pagination justify-content-center">
    <li class="page-item <?= $pagination['page'] == 1 ? 'disabled' : '' ?>">
        <a class="page-link" href="<?= $pagination['page'] != 1 ? '?page=' . ($pagination['page'] - 1) : 'javascript:;' ?>" tabindex="-1">Previous</a>
    </li>
    <?php for ($i = 0; $i < $pagination['total_pages']; $i++) : ?>
        <li class="page-item <?= $pagination['page'] == $i + 1 ? 'active' : '' ?>"><a class="page-link" href="<?= $pagination['page'] != ($i + 1) ? '?page=' . ($i + 1) : 'javascript:;' ?>"><?= ($i + 1) ?></a></li>
    <?php endfor ?>

    <li class="page-item <?= $pagination['total_pages'] == $pagination['page'] ? 'disabled' : '' ?>">
        <a class="page-link" href="<?= $pagination['total_pages'] == $pagination['page'] ? 'javascript:;' : '?page=' . ++$pagination['page'] ?>">Next</a>
    </li>
</ul>