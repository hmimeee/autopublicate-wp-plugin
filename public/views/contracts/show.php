<div class="row">
    <div class="col-12">
        <?php if ($progressSequences[$contract['status']] == 0) : ?>
            <ul id="progressbar">
                <li class="col-6 active">Created</li>
                <li class="col-6 cancelled">Cancelled</li>
            </ul>
        <?php else : ?>
            <ul id="progressbar">
                <li class="col-3 <?= $progressSequences[$contract['status']] > 2 ? 'active' : 'pending' ?>"><?= $progressSequences[$contract['status']] > 2 ? 'Approved' : 'Pending Approval' ?></li>
                <li class="col-3 <?= $progressSequences[$contract['status']] > 3 ? 'active' : ($contract['status'] == 'approved' ? 'pending' : '') ?>">Working</li>
                <li class="col-3 <?= $progressSequences[$contract['status']] > 4 ? 'active' : ($contract['status'] == 'delivered' ? 'pending' : '') ?>">Delivered</li>
                <li class="col-3 <?= $progressSequences[$contract['status']] >= 5 ? 'active' : '' ?>">Completed</li>
            </ul>
        <?php endif ?>
    </div>
    <div class="col-md-5">
        <div class="page-body">
            <div class="sub-title">
                <h2>Contract Details</h2>
            </div>

            <div class="content-page pt-1">
                <div class="p-3">
                    <div class="pb-2"><i class="fa fa-briefcase"></i> Title: <span class="ps-2"><b><?= $contract['title'] ?></b></span></div>

                    <?php if ($contract['attachments']) : ?>
                        <div class="pb-2"><i class="fa fa-clipboard"></i> Attachments:</div>

                        <div>
                            <?php foreach ($contract['attachments'] as $attachment) : ?>
                                <a class="badge bg-primary text-white" href="<?= $attachment->guid ?>" target="_blank"><i class="fa fa-paperclip"></i> <?= basename($attachment->guid) ?></a>
                            <?php endforeach ?>
                        </div>
                    <?php endif ?>
                    <hr />
                    <div class="pb-2"><i class="fa fa-clock"></i> Budget Type: <span class="ps-2"><?= ucfirst($contract['budget_type'] ?? 'N/A') ?></span></div>
                    <div class="pb-2"><i class="fa fa-dollar-sign p-1"></i> Budget: <span class="ps-2">$<?= number_format($contract['budget'] ?? 0, 2) ?></span></div>
                    <div class="pb-2"><i class="fa fa-clock"></i> Deadline: <span class="ps-2"><?= $contract['deadline'] ?? $contract['expected_deadline'] ?? 'N/A' ?></span></div>
                    <div class="pb-2"><i class="fa fa-info-circle"></i> Status: <span class="badge text-white bg-<?= $statusStyles[$contract['status']] ?>"><?= ucwords($contract['status']) ?></span></div>

                    <?php if (isset($pendingUnder) && $pendingUnder->get('ID') != get_current_user_id()) : ?>
                        <hr />
                        <div class="pb-2"><i class="fa fa-user"></i> Pending Under:</div>
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <img class="rounded-circle" src="<?= "https://ui-avatars.com/api/?name=" . $user->get('display_name') ?>">

                                    <div class="ms-2">
                                        <h5 class="mb-0"><?= $pendingUnder->get('user_nicename') ?> (<small><?= $pendingUnder->get('user_login') ?></small>)</h5>
                                        <i class="fa fa-briefcase me-1"></i> <?= $pendingUnder->get('profession_title') ?: 'N/A' ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif ?>

                    <?php if (($contract['status'] == 'pending' && $contract['provider_id'] == get_current_user_id()) || ($contract['status'] == 'modified' && $contract['modified_by'] == $user->get('ID'))) : ?>
                        <hr />
                        <ul class="my-detail-footer">
                            <li><a class="bg-primary" title="Edit" href="javascript:;" data-bs-toggle="modal" data-bs-target="#edit-contract-modal"><i class="fa fa-pen"></i></a></li>
                            <li><a class="bg-success" title="Approve" href="<?= ap_route('contracts.status-update', ['contract' => $contract['id'], 'status' => 'approved']) ?>"><i class="fa fa-check"></i></a></li>
                            <li><a class="bg-danger" title="Cancel" href="<?= ap_route('contracts.status-update', ['contract' => $contract['id'], 'status' => 'cancelled']) ?>"><i class="fa fa-times"></i></a></li>
                        </ul>
                    <?php endif ?>

                    <?php if ($contract['status'] == 'approved' && $contract['provider_id'] == get_current_user_id()) : ?>
                        <hr />
                        <ul class="my-detail-footer">
                            <li><a class="bg-primary" title="Deliver" href="javascript:;" data-bs-toggle="modal" data-bs-target="#contract-delivery-modal"><i class="fa fa-box"></i></a></li>
                        </ul>
                    <?php endif ?>

                    <?php if ($contract['status'] == 'delivered' && $contract['buyer_id'] == get_current_user_id()) : ?>
                        <hr />
                        <ul class="my-detail-footer">
                            <li><a class="bg-success" title="Completed" href="<?= ap_route('contracts.delivery-action', ['contract' => $contract['id'], 'status' => 'completed']) ?>"><i class="fa fa-check"></i></a></li>
                            <li><a class="bg-warning" title="Return" href="<?= ap_route('contracts.delivery-action', ['contract' => $contract['id'], 'status' => 'approved']) ?>"><i class="fa fa-undo"></i></a></li>
                        </ul>
                    <?php endif ?>
                </div>
            </div>
        </div>
        <br />
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
    </div>

    <div class="col-md-7">
        <?php if ($contract['delivery_attachments'] && $progressSequences[$contract['status']] > 3) : ?>
            <div class="page-body">
                <div class="sub-title">
                    <h2>Delivery</h2>
                </div>

                <div class="content-page p-3">
                    <?= html_entity_decode($contract['delivery_notes']) ?>

                    <hr />
                    <div class="pb-2">Attachments:</div>
                    <div>
                        <?php foreach ($contract['delivery_attachments'] as $attachment) : ?>
                            <a class="badge bg-primary text-white" href="<?= $attachment->guid ?>" target="_blank"><i class="fa fa-paperclip"></i> <?= basename($attachment->guid) ?></a>
                        <?php endforeach ?>
                    </div>
                </div>
            </div>
            <br />
        <?php endif ?>

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

<?php if ($contract['status'] == 'pending' || ($contract['status'] == 'modified' && $contract['modified_by'] == $user->get('ID'))) : ?>
    <!-- Modal -->
    <div class="modal fade mt-3" id="edit-contract-modal" tabindex="-1" aria-labelledby="edit-contract-modal-label" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="edit-contract-modal-label">Modify Contract</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" action="<?= ap_route('contracts.modify', $contract['id']) ?>">
                    <?php wp_nonce_field(); ?>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="deadline">Deadline</label>
                            <input type="date" name="deadline" value="<?= $contract['deadline'] ?? $contract['expected_deadline'] ?>" class="form-control" />
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
                        <button class="btn btn-primary">Submit with changes</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif ?>

<?php if ($contract['status'] == 'approved' && $contract['provider_id'] == get_current_user_id()) : ?>
    <div class="modal fade" id="contract-delivery-modal" tabindex="-1" aria-labelledby="contract-delivery-modal-label" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="contract-delivery-modal-label">Deliver Contract</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" enctype="multipart/form-data" action="<?= ap_route('contracts.deliver', ['user' => $user->get('user_login'), 'contract' => $contract['id']]) ?>">
                    <?php wp_nonce_field(); ?>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name-field">Delivery Notes</label>
                            <textarea id="editor" name="delivery_notes"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="attachments">Attachments</label>
                            <input type="file" name="attachments[]" class="form-control" multiple />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary">Deliver Now</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="<?= str_replace('/views', '', plugin_dir_url(__DIR__)) . 'js/ckeditor.js' ?>"></script>
    <script>
        ClassicEditor
            .create(document.querySelector('#editor'))
            .catch(error => {
                console.error(error);
            });
    </script>
<?php endif ?>