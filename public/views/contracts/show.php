<div class="row">
    <div class="col-12">
        <ul id="progressbar">
            <li class="col-2 active">Pending Approval</li>
            <li class="col-2 spinner">Modified</li>
            <li class="col-2">Approved</li>
            <li class="col-2">Security Questions</li>
            <li class="col-2">Security Questions</li>
            <li class="col-2">Security Questions</li>
        </ul>
    </div>
    <div class="col-md-5">
        <div class="page-body">
            <div class="sub-title">
                <h2>Provider Details</h2>
            </div>

            <div class="content-page pt-1">
                <div class="ps-4">
                    <div class="d-flex align-items-center">
                        <img width="120px" src="<?= "https://ui-avatars.com/api/?name=" . $user->get('display_name') ?>" alt="<?= $user->get('display_name') ?>">

                        <div class="p-3 pt-1">
                            <a href="<?= ap_route('user_profile', $user->get('user_login')) ?>">
                                <h2><?= ucwords($user->get('user_nicename')) ?></h2>
                            </a>
                            <div><i class="fa fa-map-marker"></i> <?= $user->get('country') ?? 'N/A' ?></div>
                            <div><i class="fa fa-comment"></i> I speak <?= $user->get('languages') ? implode(', ', explode(',', $user->get('languages'))) : 'N/A' ?></div>
                            <div><i class="fa fa-archive"></i> 0 contracts completed</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br />
        <div class="page-body">
            <div class="sub-title">
                <h2>Contract Details</h2>
            </div>

            <div class="content-page pt-1">
                <div class="p-3">
                    <div class="pb-2"><i class="fa fa-briefcase"></i> Title: <span class="ps-2"><b><?= $contract['title'] ?></b></span></div>
                    <div class="pb-2"><i class="fa fa-clock"></i> Budget Type: <span class="ps-2"><?= ucfirst($contract['budget_type'] ?? 'N/A') ?></span></div>
                    <div class="pb-2"><i class="fa fa-dollar-sign p-1"></i> Budget: <span class="ps-2">$<?= number_format($contract['budget'] ?? 0, 2) ?></span></div>
                    <div class="pb-2"><i class="fa fa-clock"></i> Deadline: <span class="ps-2"><?= $contract['expected_deadline'] ?? 'N/A' ?></span></div>
                    <div class="pb-2"><i class="fa fa-info-circle"></i> Status: <span class="ms-2 text-white bg-info p-1 rounded"><?= ucwords($contract['status']) ?></span></div>
                </div>

                <?php if ($contract['status'] == 'pending') : ?>
                    <ul class="my-detail-footer">
                        <li><a class="bg-primary" title="Edit" href="javascript:;" data-bs-toggle="modal" data-bs-target="#edit-contract-modal"><i class="fa fa-pen"></i></a></li>
                        <li><a class="bg-success" title="Approve" href="<?= ap_route('profile.edit') ?>"><i class="fa fa-check"></i></a></li>
                        <li><a class="bg-danger" title="Cancel" href="<?= wp_logout_url() ?>"><i class="fa fa-times"></i></a></li>
                    </ul>
                <?php endif ?>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="page-body">
            <div class="sub-title">
                <h2>Contract Description</h2>
            </div>

            <div class="content-page p-3">
                <?= html_entity_decode($contract['description']) ?>
            </div>
        </div>
    </div>
</div>

<?php if ($contract['status'] == 'pending') : ?>
    <!-- Modal -->
    <div class="modal fade mt-3" id="edit-contract-modal" tabindex="-1" aria-labelledby="edit-contract-modal-label" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="edit-contract-modal-label">Modify Contract</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" action="<?= ap_route('contracts.modify', ['user' => $user->get('user_login'), 'contract' => $contract['id']]) ?>">
                    <?php wp_nonce_field(); ?>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="deadline">Deadline</label>
                            <input type="date" name="deadline" value="<?= $contract['expected_deadline'] ?>" class="form-control" />
                        </div>

                        <div class="form-group">
                            <label for="deadline">Budget</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input <?= $contract['budget_type'] == 'fixed' ? 'disabled' : '' ?> type="number" step="any" name="budget" value="<?= number_format($contract['budget'] ?? 0, 2) ?>" class="form-control" />
                            </div>
                            <?php if ($contract['budget_type'] == 'fixed') : ?>
                                <small class="text-danger fw-light">Client created contract with the fixed budget, can't change it.</small>
                            <?php endif ?>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button class="btn btn-primary">Submit with changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif ?>