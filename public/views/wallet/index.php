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
                        <button class="btn btn-primary btn-sm" <?= $_current_user->get('balance') < 10 ? 'disabled' : ''?>>Withdraw Balance</button>
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
                        <td><?= (new DateTime($transaction['date']))->format('Y-m-d') ?></td>
                        <td><?= $transaction['description'] ?></td>
                        <td><?= $transaction['from'] ?></td>
                        <td>€ <?= number_format($transaction['amount'], 2) ?></td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
</div>