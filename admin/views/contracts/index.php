<div class="tablenav top">
    <form method="get" onchange="this.submit()">
        <input type="hidden" name="page" value="<?= request('page') ?>" />
        <div class="alignleft actions">
            <label class="screen-reader-text" for="filter-by-status">Filter by status</label>
            <select id="filter-by-status" name="status">
                <option <?= request('status') == '' ? 'selected' : '' ?> value="">All statuses</option>
                <option <?= request('status') == 'pending' ? 'selected' : '' ?> value="pending">Pending</option>
                <option <?= request('status') == 'approved' ? 'selected' : '' ?> value="approved">Approved</option>
                <option <?= request('status') == 'inprogress' ? 'selected' : '' ?> value="inprogress">Inprogress</option>
                <option <?= request('status') == 'delivered' ? 'selected' : '' ?> value="delivered">Delivered</option>
                <option <?= request('status') == 'completed' ? 'selected' : '' ?> value="completed">Completed</option>
            </select>
        </div>

        <div class="alignright actions">
            <p class="search-box">
                <label class="screen-reader-text" for="search">Search:</label>
                <input type="search" id="search" name="search" value="<?= request('search') ?>" placeholder="Search">
            </p>
        </div>
    </form>
</div>

<table class="wp-list-table widefat fixed striped table-view-list pages">
    <thead>
        <tr>
            <th>Title</th>
            <th>Provider</th>
            <th>Buyer</th>
            <th>Amount</th>
            <th>Deadline</th>
        </tr>
    </thead>

    <tbody>
        <?php foreach ($contracts['data'] as $contract) : ?>
            <tr>
                <td>
                    <a href="<?= ap_admin_route('contract_view', ['contract' => $contract['id']]) ?>">
                        <?= $contract['title'] ?>
                    </a>
                </td>
                <td>
                    <a target="_blank" href="<?= get_edit_user_link($contract['provider_id']) ?>">
                        <?= $contract['provider'] ?>
                    </a>
                </td>
                <td>
                    <a target="_blank" href="<?= get_edit_user_link($contract['buyer_id']) ?>">
                        <?= $contract['buyer'] ?>
                    </a>
                </td>
                <td>€<?= number_format($contract['budget'], 2) ?></td>
                <td><?= $contract['deadline'] ?? $contract['expected_deadline'] ?></td>
            </tr>
        <?php endforeach ?>
    </tbody>

    <tfoot>
        <tr>
            <td colspan="5">
                <?php ap_admin_paginate_view($contracts) ?>
            </td>
        </tr>
    </tfoot>
</table>