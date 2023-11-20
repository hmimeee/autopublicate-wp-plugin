<div id="dashboard-widgets-wrap">
    <div id="dashboard-widgets" class="metabox-holder">
        <div id="postbox-container-1" class="postbox-container">
            <?php if ($resolution) : ?>
                <div class="postbox">
                    <form method="post" action="<?= ap_admin_route('contract_resolution', ['resolution' => $resolution['id']]) ?>">
                        <div class="postbox-header">
                            <h2 class="hndle">Resolution Request</h2>
                        </div>
                        <div class="inside text-center">
                            <span class="dashicons dashicons-info-outline ap-resolution-icon"></span>
                            <h2>Request for refund!</h2>
                            <p><?= html_entity_decode($resolution['notes']) ?></p>
                            <p>
                                <label>Refund Amount (%)</label>
                                <br/>
                                <input type="number" value="100" name="amount"/>
                            </p>
                            <div class="ap-resolution-actions">
                                <input name="status" type="submit" value="Accept" class="ap-button ap-button-primary" />
                                <input name="status" type="submit" value="Decline" class="ap-button ap-button-danger" />
                            </div>
                        </div>
                    </form>
                </div>
            <?php endif ?>

            <div class="postbox">
                <div class="postbox-header">
                    <h2 class="hndle">Contract Details</h2>
                </div>
                <div class="inside">
                    <div>
                        <p><strong>Title:</strong> <?= $contract['title'] ?></p>
                    </div>
                    <div>
                        <p><strong>Budget:</strong> <span class="ap-focused-text">€<?= number_format($contract['budget'], 2) ?></span></p>
                    </div>
                    <div>
                        <p><strong>Deadline:</strong> <?= $contract['deadline'] ?? $contract['expected_deadline'] ?></p>
                    </div>
                    <div>
                        <p><strong>Status:</strong> <span class="ap-badge"><?= ucwords($contract['status']) ?></span></p>
                    </div>

                    <?php if ($contract['status'] == 'completed' || $contract['status'] == 'cleared') : ?>
                        <div>
                            <p>
                                <strong>Rating:</strong>
                                <?php for ($i = 0; $i < $contract['rating']; $i++) : ?>
                                    <span class="dashicons dashicons-star-filled active"></span>
                                <?php endfor ?>
                                <?php for ($i = 0; $i < (5 - $contract['rating']); $i++) : ?>
                                    <span class="dashicons dashicons-star-filled"></span>
                                <?php endfor ?>
                            </p>
                        </div>

                        <div>
                            <p><strong>Review:</strong> <?= $contract['review'] ?></p>
                        </div>
                    <?php endif ?>

                    <?php if ($contract['attachments']) : ?>
                        <div>
                            <p>
                                <strong>Attachments:</strong>
                                <?php foreach ($contract['attachments'] as $attachment) : ?>
                                    <a target="_blank" href="<?= $attachment->guid ?>" class="ap-attachment"> <span class="dashicons dashicons-paperclip"></span> <?= basename($attachment->guid) ?></a>
                                <?php endforeach ?>
                            </p>
                        </div>
                    <?php endif ?>
                </div>
            </div>

            <div class="postbox">
                <div class="postbox-header">
                    <h2 class="hndle">Provider Details</h2>
                </div>
                <div class="inside">
                    <div class="ap-user-info-box">
                        <img alt="<?= $provider->get('display_name') ?>" src="<?= $provider->get('image') ?: "https://ui-avatars.com/api/?name=" . $provider->get('display_name') ?>" class="avatar avatar-96 photo" height="96" width="96" loading="lazy" decoding="async">
                        <div>
                            <p><strong>Name:</strong> <a href="<?= '/profile/' . $provider->get('user_login') ?>"><?= $provider->get('display_name') ?></a></p>
                            <p><strong>Title:</strong> <?= $provider->get('profession_title') ?: 'N/A' ?></p>
                            <p><strong>Email:</strong> <?= $provider->get('email') ?></p>
                        </div>

                    </div>
                </div>
            </div>

            <div class="postbox">
                <div class="postbox-header">
                    <h2 class="hndle">Buyer Details</h2>
                </div>
                <div class="inside">
                    <div class="ap-user-info-box">
                        <img alt="<?= $buyer->get('display_name') ?>" src="<?= $buyer->get('image') ?: "https://ui-avatars.com/api/?name=" . $buyer->get('display_name') ?>" class="avatar avatar-96 photo" height="96" width="96" loading="lazy" decoding="async">
                        <div>
                            <p><strong>Name:</strong> <a href="<?= '/profile/' . $buyer->get('user_login') ?>"><?= $buyer->get('display_name') ?></a></p>
                            <p><strong>Title:</strong> <?= $buyer->get('profession_title') ?: 'N/A' ?></p>
                            <p><strong>Email:</strong> <?= $buyer->get('email') ?></p>
                        </div>

                    </div>
                </div>
            </div>

        </div>

        <div id="postbox-container-2" class="postbox-container">
            <div class="postbox meta-box-sortables">
                <div class="postbox-header">
                    <h2 class="hndle">Activity</h2>
                </div>
                <div class="inside">
                    <div class="ap-comment-box">
                        <div class="ap-comment-header">
                            <img alt="<?= $buyer->get('display_name') ?>" src="<?= $buyer->get('image') ?: "https://ui-avatars.com/api/?name=" . $buyer->get('display_name') ?>" class="avatar ap-rounded" height="40" width="40" loading="lazy" decoding="async">
                            <div>
                                <span class="d-block"><?= $buyer->get('display_name') ?></span>
                                <span class="ap-badge ap-badge-dark">Buyer</span>
                            </div>

                            <span class="align-right"><?= ap_date_format($contract['created_at'], 'd M \a\t h:i a') ?></span>
                        </div>
                        <div class="ap-comment-body">
                            <?= html_entity_decode($contract['description']) ?>

                            <?php if ($contract['attachments']) : ?>
                                <div>
                                    <p>
                                        <strong>Attachments:</strong>
                                        <?php foreach ($contract['attachments'] as $attachment) : ?>
                                            <a target="_blank" href="<?= $attachment->guid ?>" class="ap-attachment"> <span class="dashicons dashicons-paperclip"></span> <?= basename($attachment->guid) ?></a>
                                        <?php endforeach ?>
                                    </p>
                                </div>
                            <?php endif ?>
                        </div>
                    </div>

                    <?php foreach ($comments as $comment) : ?>
                        <div class="ap-comment-box">
                            <div class="ap-comment-header">
                                <img alt="<?= $comment['user']->get('display_name') ?>" src="<?= $buyer->get('image') ?: "https://ui-avatars.com/api/?name=" . $buyer->get('display_name') ?>" class="avatar ap-rounded" height="40" width="40" loading="lazy" decoding="async">
                                <div>
                                    <span class="d-block"><?= $comment['user']->get('display_name') ?></span>
                                    <span class="ap-badge ap-badge-dark"><?= $comment['user']->get('type') ?></span>
                                </div>
                                <span class="align-right"><?= ap_date_format($comment['created_at'], 'd M \a\t h:i a') ?></span>
                            </div>
                            <div class="ap-comment-body">
                                <?= html_entity_decode($comment['comment']) ?>
                            </div>
                        </div>
                    <?php endforeach ?>

                    <?php if ($contract['status'] == 'delivered' || $contract['status'] == 'completed' || $contract['status'] == 'cleared') : ?>
                        <div class="ap-comment-box">
                            <div class="ap-comment-header">
                                <img alt="<?= $provider->get('display_name') ?>" src="<?= $provider->get('image') ?: "https://ui-avatars.com/api/?name=" . $provider->get('display_name') ?>" class="avatar ap-rounded" height="40" width="40" loading="lazy" decoding="async">
                                <div>
                                    <span class="d-block"><?= $provider->get('display_name') ?></span>
                                    <span class="ap-badge ap-badge-dark">Provider</span>
                                </div>

                                <div class="align-right">
                                    <span class="ap-label">Delivery</span>
                                    <?= ap_date_format($contract['delivered_at'], 'd M \a\t h:i a') ?>
                                </div>
                            </div>
                            <div class="ap-comment-body">
                                <p>
                                    <?= html_entity_decode($contract['delivery_notes']) ?>
                                </p>

                                <?php if ($contract['delivery_attachments']) : ?>
                                    <div>
                                        <p>
                                            <strong>Attachments:</strong>
                                            <?php foreach ($contract['delivery_attachments'] as $attachment) : ?>
                                                <a target="_blank" href="<?= $attachment->guid ?>" class="ap-attachment"> <span class="dashicons dashicons-paperclip"></span> <?= basename($attachment->guid) ?></a>
                                            <?php endforeach ?>
                                        </p>
                                    </div>
                                <?php endif ?>
                            </div>
                        </div>
                    <?php endif ?>

                    <?php if ($contract['status'] == 'completed' || $contract['status'] == 'cleared') : ?>
                        <div class="ap-comment-box">
                            <div class="ap-comment-header">
                                <img alt="<?= $buyer->get('display_name') ?>" src="<?= $buyer->get('image') ?: "https://ui-avatars.com/api/?name=" . $buyer->get('display_name') ?>" class="avatar ap-rounded" height="40" width="40" loading="lazy" decoding="async">
                                <div>
                                    <span class="d-block"><?= $buyer->get('display_name') ?></span>
                                    <span class="ap-badge ap-badge-dark">Buyer</span>
                                </div>

                                <div class="align-right">
                                    <span class="ap-label">Review</span>
                                    <?= ap_date_format($contract['updated_at'], 'd M \a\t h:i a') ?>
                                </div>
                            </div>
                            <div class="ap-comment-body">
                                <p>
                                    <?= html_entity_decode($contract['review']) ?>
                                </p>

                                <div class="ap-padding-box">
                                    <span>Rating:</span>
                                    <?php for ($i = 0; $i < $contract['rating']; $i++) : ?>
                                        <span class="dashicons dashicons-star-filled active"></span>
                                    <?php endfor ?>
                                    <?php for ($i = 0; $i < (5 - $contract['rating']); $i++) : ?>
                                        <span class="dashicons dashicons-star-filled"></span>
                                    <?php endfor ?>
                                </div>
                            </div>
                        </div>
                    <?php endif ?>
                </div>
            </div>
        </div>
    </div>
</div>