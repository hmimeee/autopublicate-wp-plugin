<div class="sub-title">
    <h2>Contracts</h2>
</div>

<nav>
    <div class="nav nav-tabs" role="tablist">
        <a class="nav-item nav-link active" id="nav-ongoing-contract-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true">Ongoing Contracts</a>
        <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false">Profile</a>
        <a class="nav-item nav-link" id="nav-contact-tab" data-toggle="tab" href="#nav-contact" role="tab" aria-controls="nav-contact" aria-selected="false">Contact</a>
    </div>
</nav>
<div class="tab-content" id="nav-tabContent">
    <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-ongoing-contract-tab">

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
                        <td>$<?= number_format($contract['budget'], 2) ?></td>
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
    <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">...</div>
    <div class="tab-pane fade" id="nav-contact" role="tabpanel" aria-labelledby="nav-contact-tab">...</div>
</div>