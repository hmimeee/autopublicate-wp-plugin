<div class="ap-modal-contentbox">
    <div class="ap-modal-contentbox-content">
        <h3 class="ap-modal-contentbox-content-heading"><?= ucfirst($payout['gateway']) ?> Info</h3>
        <?php foreach ($payout['gateway_info'] as $key => $info) : ?>
            <div class="ap-modal-contentbox-info">
                <b><?= ucfirst($key) ?>:</b> <?= $info ?>
            </div>
        <?php endforeach ?>
    </div>
    <div class="ap-modal-contentbox-details">
        <h3 class="ap-modal-contentbox-content-heading">Payout Request Info</h3>
        <ul>
            <li><strong>User:</strong> <?= $payout['user'] ?></li>
            <li><strong>Amount:</strong> <?= number_format($payout['amount'], 2) ?></li>
            <li><strong>Date:</strong> <?= ap_date_format($payout['created_at']) ?></li>
            <li><strong>Updated:</strong> <?= ap_date_format($payout['updated_at']) ?></li>
            <li><strong>Status:</strong> <span class="ap-badge"><?= ucfirst($payout['status']) ?></span></li>
            <?php if (in_array($payout['status'], ['sent', 'cancelled'])) : ?>
                <li><strong>Notes:</strong> <?= $payout['notes'] ?></li>
            <?php endif ?>
        </ul>

        <?php if (!in_array($payout['status'], ['sent', 'cancelled'])) : ?>
            <form method="post" action="<?= ap_admin_route('payout_request_update', ['payout' => $payout['id']]) ?>">
                <textarea rows="4" style="width: 80%;" name="notes" placeholder="Write notes here"><?= $payout['notes'] ?></textarea>
                <select style="width: 61%;" name="status">
                    <option <?= $payout['status'] == 'pending' ? 'selected' : '' ?> value="pending">Pending</option>
                    <option <?= $payout['status'] == 'processing' ? 'selected' : '' ?> value="processing">Processing</option>
                    <option <?= $payout['status'] == 'sent' ? 'selected' : '' ?> value="sent">Sent</option>
                    <option <?= $payout['status'] == 'cancelled' ? 'selected' : '' ?> value="cancelled">Cancel</option>
                </select>
                <button class="button">Update</button>
            </form>
        <?php endif ?>
    </div>
</div>

<style>
    .ap-modal-contentbox {
        height: 100%;
        display: flex;
        justify-content: center;
    }

    .ap-modal-contentbox .ap-modal-contentbox-content {
        display: inline;
        flex-grow: 3;
        padding: 10px;
    }

    .ap-modal-contentbox .ap-modal-contentbox-details {
        display: inline;
        background-color: #f6f7f7;
        flex-grow: 1;
        padding: 10px;
    }

    #TB_ajaxContent {
        width: 100% !important;
        padding: 0 !important;
    }

    .ap-modal-contentbox .ap-modal-contentbox-info {
        display: block;
        border: 1px solid #f6f7f7;
        padding: 10px 5px;
    }
</style>