<?php if ($contract['status'] == 'pending' || ($contract['status'] == 'modified' && $contract['modified_by'] == $user->get('ID'))) : ?>
    <!-- Modal -->
    <div class="modal fade mt-3" id="edit-contract-modal" tabindex="-1" aria-labelledby="edit-contract-modal-label" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="edit-contract-modal-label"><?= $contract['modified_by'] ? 'Modify' : 'Accept' ?> Contract</h5>
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
                                <input type="number" step="any" name="budget" value="<?= request('budget') ?? number_format($contract['budget'] ?? 0, 2) ?>" class="form-control" />
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary">Submit changes</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif ?>

<?php if ($contract['status'] == 'inprogress' && $contract['provider_id'] == get_current_user_id()) : ?>
    <div class="modal fade" id="contract-delivery-modal" tabindex="-1" aria-labelledby="contract-delivery-modal-label" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
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
                        <button class="btn btn-primary">Confirm Delivery</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif ?>

<?php if ($contract['status'] == 'approved' && $contract['buyer_id'] == get_current_user_id()) : ?>
    <div class="modal fade" id="contract-payment-modal" tabindex="-1" aria-labelledby="contract-payment-modal-label" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="contract-payment-modal-label">Choose Gateway</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" action="<?= ap_route('contracts.payment', $contract['id']) ?>">
                    <?php wp_nonce_field(); ?>
                    <div class="modal-body">
                        <div class="form-group">
                            <div class="gateway-box">
                                <input class="gateway-input" id="cards" type="radio" name="gateway" value="stripe">
                                <label class="gateway-label" for="cards">Credit or Debit Cards</label>
                            </div>
                            <div class="gateway-box">
                                <input class="gateway-input" id="paypal" type="radio" name="gateway" value="paypal">
                                <label class="gateway-label" for="paypal">PayPal</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary">Pay Now</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif ?>

<div class="modal fade" id="comment-delete-modal" tabindex="-1" aria-labelledby="comment-delete-modal-label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="comment-delete-modal-label">Delete Comment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="">
                <?php wp_nonce_field(); ?>
                <div class="modal-body text-center">
                    <p>Are you sure you want to delete?</p>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button class="btn btn-danger">Yes</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                </div>
            </form>
        </div>
    </div>
</div>