<div class="page-body pb-3">
    <div class="sub-title">
        <h2>Wallet</h2>
    </div>


    <div class="content-page">
        <div class="row gap-5 text-center justify-content-center">
            <div class="card col-md-5">
                <div class="card-body">
                    <i class="fa fa-euro-sign"></i>
                    <h2 class="d-inline"><?= number_format($_current_user->get('balance'), 2) ?></h2>
                    <small class="text-muted">Balance</small>
                    <div class="d-block mt-2">
                        <a href="<?= $_current_user->get('balance') < 10 ? 'javascript:;' : ap_route('wallet.payout') ?>" class="btn btn-primary btn-sm" <?= $_current_user->get('balance') < 10 ? 'disabled' : '' ?>>Withdraw Balance</a>
                    </div>
                </div>
            </div>

            <div class="card col-md-5">
                <div class="card-body">
                    <i class="fa fa-euro-sign"></i>
                    <h2 class="d-inline"><?= number_format($pending['total'] ?? 0, 2) ?></h2>
                    <small class="text-muted">Ongoing</small>
                </div>
            </div>
        </div>
    </div>

    <?php if (count($payoutRequests)) : ?>
        <div class="content-page ps-5 pe-5">
            <h4>Latest Withdraw Requests</h4>
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Date</th>
                        <th scope="col">Gateway</th>
                        <th scope="col">Amount</th>
                        <th scope="col">Status</th>
                        <th scope="col">Notes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($payoutRequests as $payout) : ?>
                        <tr>
                            <td><?= ap_date_format($payout['created_at'], 'Y-m-d') ?></td>
                            <td><?= ucfirst($payout['gateway']) ?></td>
                            <td><?= number_format($payout['amount'], 2) ?></td>
                            <td><?= ucfirst($payout['status']) ?></td>
                            <td><?= $payout['notes'] ?></td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    <?php endif ?>

    <div class="content-page ps-5 pe-5">
        <h4>Latest Transactions</h4>
        <table class="table">
            <thead>
                <tr>
                    <th scope="col" width="15%">Date</th>
                    <th scope="col" width="50%">Description</th>
                    <th scope="col" width="20%">From</th>
                    <th scope="col" width="15%">Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transactions as $transaction) : ?>
                    <tr>
                        <td><?= ap_date_format($transaction['date'], 'Y-m-d') ?></td>
                        <td><a href="<?= $transaction['contract_id'] ? ap_route('contracts.show', $transaction['contract_id']) : 'javascript:;' ?>" target="_blank"><?= $transaction['description'] ?></a></td>
                        <td><a href="<?= ap_route('user_profile', $transaction['user_login']) ?>" target="_blank"><?= $transaction['from'] ?></a></td>
                        <td class="fw-bold <?= $transaction['type'] == 'addition' ? 'text-success' : 'text-danger' ?>"><?= $transaction['type'] == 'deduction' ? '-' : '' ?>€ <?= number_format($transaction['amount'], 2) ?></td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
</div>