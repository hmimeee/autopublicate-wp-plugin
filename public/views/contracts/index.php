<div class="page-body">
    <div class="sub-title">
        <h2>Contracts</h2>
    </div>

    <nav>
        <div class="nav nav-tabs" role="tablist">
            <a class="nav-item nav-link <?= !in_array(request('tab'), ['delivered', 'completed']) ? 'active' : '' ?>" id="nav-ongoing-contract-tab" data-toggle="tab" href="<?= ap_route('contracts.index') ?>">Ongoing</a>
            <a class="nav-item nav-link <?= request('tab') == 'delivered' ? 'active' : '' ?>" id="nav-delivered-contract-tab" data-toggle="tab" href="<?= ap_route('contracts.index', ['tab' => 'delivered']) ?>">Delivered</a>
            <a class="nav-item nav-link <?= request('tab') == 'completed' ? 'active' : '' ?>" id="nav-completed-contract-tab" data-toggle="tab" href="<?= ap_route('contracts.index', ['tab' => 'completed']) ?>">Completed</a>
        </div>
    </nav>
    <div class="tab-content" id="nav-tabContent">
        <div class="tab-pane fade show active" id="nav-ongoing-contract" role="tabpanel" aria-labelledby="nav-ongoing-contract-tab">

            <table class="table mt-3">
                <thead>
                    <tr>
                        <th scope="col" width="5%">#</th>
                        <th scope="col" width="35%">Title</th>
                        <th scope="col" width="15%">Budget</th>
                        <th scope="col" width="15%">Deadline</th>
                        <th scope="col" width="10%">Status</th>
                        <th scope="col" width="10%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($contracts['data'] as $i => $contract) : ?>
                        <tr>
                            <td scope="row"><?= ++$i ?></td>
                            <td><?= strlen($contract['title']) > 50 ? substr($contract['title'], 0, 50) . '...' : $contract['title'] ?></td>
                            <td><?= $contract['budget'] ? '$' . number_format($contract['budget'], 2) : 'N/A' ?></td>
                            <td><?= $contract['deadline'] ?></td>
                            <td><?= ucwords($contract['status']) ?></td>
                            <td>
                                <div class="btn-group">
                                    <a href="<?= ap_route('contracts.show', $contract['id']) ?>" class="btn btn-sm btn-primary"><i class="fa fa-eye"></i></a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>

            <?php paginate_view($contracts) ?>
        </div>
        <div class="tab-pane fade" id="nav-delivered-contract" role="tabpanel" aria-labelledby="nav-delivered-contract-tab">...</div>
        <div class="tab-pane fade" id="nav-contact" role="tabpanel" aria-labelledby="nav-completed-contract-tab">...</div>
    </div>
</div>