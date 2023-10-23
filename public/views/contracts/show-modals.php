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
                            <input type="date" name="deadline" value="<?= request('deadline') ?? $contract['deadline'] ?? $contract['expected_deadline'] ?>" class="form-control" />
                        </div>

                        <div class="form-group">
                            <label for="deadline">Budget</label>
                            <div class="input-group">
                                <span class="input-group-text">€</span>
                                <input <?= $contract['budget_type'] == 'fixed' ? 'disabled' : '' ?> type="number" step="any" name="budget" value="<?= request('budget') ?? number_format($contract['budget'] ?? 0, 2) ?>" class="form-control" />
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
                <form method="post" enctype="multipart/form-data" action="<?= ap_route('contracts.deliver', $contract['id']) ?>">
                    <?php wp_nonce_field(); ?>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name-field">Delivery Notes</label>
                            <textarea id="editor" name="delivery_notes"><?= request('delivery_notes') ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="attachments">Attachments</label>
                            <input type="file" name="attachments" class="form-control" />
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
<?php endif ?>