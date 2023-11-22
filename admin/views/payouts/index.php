<div class="tablenav top">
    <form method="get" onchange="this.submit()">
        <input type="hidden" name="page" value="<?= request('page') ?>" />
        <div class="alignleft actions">
            <label class="screen-reader-text" for="filter-by-status">Filter by status</label>
            <select id="filter-by-status" name="status">
                <option <?= request('status') == '' ? 'selected' : '' ?> value="">All</option>
                <option <?= request('status') == 'pending' ? 'selected' : '' ?> value="pending">Pending</option>
                <option <?= request('status') == 'processing' ? 'selected' : '' ?> value="processing">Processing</option>
                <option <?= request('status') == 'sent' ? 'selected' : '' ?> value="sent">Sent</option>
                <option <?= request('status') == 'cancelled' ? 'selected' : '' ?> value="cancelled">Cancelled</option>
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
            <th>User</th>
            <th>Gateway</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Date</th>
            <th>Actions</th>
        </tr>
    </thead>

    <tbody>
        <?php foreach ($payouts['data'] as $payout) : ?>
            <tr>
                <td>
                    <a href="<?= get_edit_user_link($payout['user_id']) ?>">
                        <?= $payout['user'] ?>
                    </a>
                </td>
                <td><?= ucfirst($payout['gateway']) ?></td>
                <td><b>€<?= number_format($payout['amount'], 2) ?></b></td>
                <td><?= ucfirst($payout['status']) ?></td>
                <td><?= ap_date_format($payout['created_at']) ?></td>
                <td>
                    <a id="payout-<?= $payout['id'] ?>" class="thickbox open-plugin-details-modal" href="<?= ap_admin_api_route('payout_request_view', ['payout' => $payout['id']]) ?>">
                        View
                    </a>
                </td>
            </tr>
        <?php endforeach ?>
    </tbody>

    <tfoot>
        <tr>
            <td colspan="6">
                <?php ap_admin_paginate_view($payouts) ?>
            </td>
        </tr>
    </tfoot>
</table>

<?php if(request('payout')): ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            let payout = '<?= request('payout') ?>';
            setTimeout(() => {
                document.getElementById('payout-'+ payout).click();
            }, 500);
        });
    </script>
<?php endif ?>