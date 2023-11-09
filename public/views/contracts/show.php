<div class="row">
    <div class="col-12">
        <?php if ($progressSequences[$contract['status']] == 0) : ?>
            <ul id="progressbar">
                <li class="col-6 active">Created</li>
                <li class="col-6 cancelled">Cancelled</li>
            </ul>
        <?php else : ?>
            <ul id="progressbar">
                <li class="col-3 <?= $progressSequences[$contract['status']] > 3 ? 'active' : 'pending' ?>"><?= $progressSequences[$contract['status']] > 2 ? ($contract['status'] == 'approved' ? 'Payment Required' : 'Paid') : 'Pending Approval' ?></li>
                <li class="col-3 <?= $progressSequences[$contract['status']] > 4 ? 'active' : ($contract['status'] == 'inprogress' ? 'pending' : '') ?>">Working</li>
                <li class="col-3 <?= $progressSequences[$contract['status']] > 5 ? 'active' : ($contract['status'] == 'delivered' ? 'pending' : '') ?>">Delivered</li>
                <li class="col-3 <?= $progressSequences[$contract['status']] >= 6 ? 'active' : '' ?>">Completed</li>
            </ul>
        <?php endif ?>
    </div>

    <div class="col-md-5">
        <div class="sticky-top">
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

                        <div class="pb-2"><i class="fa fa-euro-sign p-1"></i> Budget: <span class="ps-2">€<?= number_format($contract['budget'] ?? 0, 2) ?></span></div>
                        <div class="pb-2"><i class="fa fa-clock"></i> Deadline: <span class="ps-2"><?= $contract['deadline'] ?? $contract['expected_deadline'] ?? 'N/A' ?></span></div>
                        <div class="pb-2"><i class="fa fa-info-circle"></i> Status: <span class="badge text-white bg-<?= $statusStyles[$contract['status']] ?>"><?= ucwords($contract['status']) ?></span></div>
                        <?php if ($contract['rating']) : ?>
                            <div class="pb-2">
                                <i class="fa fa-star"></i> Rating:
                                <span class="ratings">
                                    <i class="fa fa-star <?= $contract['rating'] >= 1 ? 'text-warning' : '' ?>"></i>
                                    <i class="fa fa-star <?= $contract['rating'] >= 2 ? 'text-warning' : '' ?>"></i>
                                    <i class="fa fa-star <?= $contract['rating'] >= 3 ? 'text-warning' : '' ?>"></i>
                                    <i class="fa fa-star <?= $contract['rating'] >= 4 ? 'text-warning' : '' ?>"></i>
                                    <i class="fa fa-star <?= $contract['rating'] >= 5 ? 'text-warning' : '' ?>"></i>
                                </span>
                            </div>
                        <?php endif ?>

                        <?php if (isset($pendingUnder) && $pendingUnder->get('ID') != get_current_user_id()) : ?>
                            <hr />
                            <div class="pb-2"><i class="fa fa-user"></i> Pending Under:</div>
                            <div class="row">
                                <div class="col-lg-4 d-flex justify-content-lg-center">
                                    <img width="100" class="rounded" src="<?= $pendingUnder->get('image') ?: "https://ui-avatars.com/api/?name=" . $pendingUnder->get('display_name') ?>">
                                </div>

                                <div class="col-lg-8 d-flex flex-column justify-content-center">
                                    <h5 class="mb-1"><?= $pendingUnder->get('display_name') ?> (<small><?= $pendingUnder->get('user_login') ?></small>)</h5>
                                    <div>
                                        <i class="fa fa-briefcase me-1"></i> <?= $pendingUnder->get('profession_title') ?: 'N/A' ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif ?>

                        <?php if ($contract['status'] == 'approved' && $contract['buyer']->get('ID') == get_current_user_id()) : ?>
                            <hr />
                            <div class="text-center">
                                <button class="btn btn-sm btn-success text-white fw-bold" title="Make Payment" data-bs-toggle="modal" data-bs-target="#contract-payment-modal">
                                    <i class="fa fa-credit-card"></i> Make Payment
                                </button>
                            </div>
                        <?php endif ?>

                        <?php if (($contract['status'] == 'pending' && $contract['provider_id'] == get_current_user_id()) || ($contract['status'] == 'modified' && $contract['modified_by'] == $user->get('ID'))) : ?>
                            <hr />
                            <div class="text-center">
                                <?php if ($contract['modified_by']) : ?>
                                    <a class="btn btn-sm btn-success text-white fw-bold" title="Accept" href="<?= ap_route('contracts.status-update', ['contract' => $contract['id'], 'status' => 'approved']) ?>"><i class="fa fa-check"></i> Accept</a>
                                <?php endif ?>

                                <button class="btn btn-sm btn-<?= $contract['modified_by'] ? 'primary' : 'success' ?> fw-bold" title="Edit" href="javascript:;" data-bs-toggle="modal" data-bs-target="#edit-contract-modal"><i class="fa fa-<?= $contract['modified_by'] ? 'pen' : 'check' ?>"></i> <?= $contract['modified_by'] ? 'Modify' : 'Accept' ?></button>
                                <a class="btn btn-sm btn-danger text-white fw-bold" title="Cancel" href="<?= ap_route('contracts.status-update', ['contract' => $contract['id'], 'status' => 'cancelled']) ?>"><i class="fa fa-times"></i> Cancel</a>
                            </div>
                        <?php endif ?>

                        <?php if ($contract['status'] == 'inprogress' && $contract['provider_id'] == get_current_user_id()) : ?>
                            <hr />
                            <div class="text-center">
                                <a class="btn btn-sm fw-bold <?= $contract['deadline'] == date('Y-m-d') ? 'bg-danger text-white glowing border-0' : 'btn-primary' ?>" title="Deliver" href="javascript:;" data-bs-toggle="modal" data-bs-target="#contract-delivery-modal"><i class="fa fa-box"></i> Deliver Now</a></li>
                            </div>
                        <?php endif ?>

                        <?php if ($contract['status'] == 'delivered' && $contract['buyer_id'] == get_current_user_id()) : ?>
                            <hr />
                            <form method="post" action="<?= ap_route('contracts.delivery-accept', ['contract' => $contract['id'], 'status' => 'completed']) ?>">
                                <span class="field-label-info"></span>
                                <input type="hidden" id="selected_rating" name="rating" value="">
                                </label>

                                <div class="text-center mb-3">
                                    How would you rate the provider?

                                    <h3 class="bold rating-header">
                                        <span class="selected-rating">0</span><small> / 5</small>
                                    </h3>
                                    <span class="btnrating btn btn-secondary text-light btn-sm" data-attr="1" id="rating-star-1">
                                        <i class="fa fa-star" aria-hidden="true"></i>
                                    </span>
                                    <span class="btnrating btn btn-secondary text-light btn-sm" data-attr="2" id="rating-star-2">
                                        <i class="fa fa-star" aria-hidden="true"></i>
                                    </span>
                                    <span class="btnrating btn btn-secondary text-light btn-sm" data-attr="3" id="rating-star-3">
                                        <i class="fa fa-star" aria-hidden="true"></i>
                                    </span>
                                    <span class="btnrating btn btn-secondary text-light btn-sm" data-attr="4" id="rating-star-4">
                                        <i class="fa fa-star" aria-hidden="true"></i>
                                    </span>
                                    <span class="btnrating btn btn-secondary text-light btn-sm" data-attr="5" id="rating-star-5">
                                        <i class="fa fa-star" aria-hidden="true"></i>
                                    </span>
                                </div>

                                <div class="text-center mb-2">
                                    <textarea name="review" placeholder="What's the review regarding the rating?"></textarea>
                                </div>

                                <ul class="my-detail-footer">
                                    <li><button class="bg-success" title="Completed"><i class="fa fa-check"></i></button></li>
                                    <li><a class="bg-warning" title="Return" href="<?= ap_route('contracts.delivery-return', ['contract' => $contract['id'], 'status' => 'approved']) ?>"><i class="fa fa-undo"></i></a></li>
                                </ul>
                            </form>
                        <?php endif ?>
                    </div>
                </div>
            </div>
            <br />
            <div class="page-body">
                <div class="sub-title">
                    <h2><?= $user->get('ID') != $contract['provider_id'] ? 'Buyer' : 'Provider' ?> Details</h2>
                </div>

                <div class="content-page p-3">
                    <div>
                        <img class="rounded" width="150" src="<?= $user->get('image') ?: "https://ui-avatars.com/api/?name=" . $user->get('display_name') ?>" alt="<?= $user->get('display_name') ?>">
                    </div>
                    <div>
                        <a href="<?= ap_route('user_profile', $user->get('user_login')) ?>">
                            <h2><?= ucwords($user->get('display_name')) ?></h2>
                        </a>
                        <div><i class="fa fa-map-marker"></i> <?= $user->get('country') ?? 'N/A' ?></div>
                        <div><i class="fa fa-comment"></i> I speak <?= $user->get('languages') ? implode(', ', explode(',', $user->get('languages'))) : 'N/A' ?></div>
                        <div><i class="fa fa-archive"></i> <?= $user->completed_count ?> contracts completed</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="page-body mt-md-0 mt-sm-4">
            <div class="sub-title">
                <h2>Activity</h2>
            </div>

            <div class="content-page p-3 d-flex flex-column" id="activity">
                <div>
                    <div class="d-flex flex-row <?= get_current_user_id() == $contract['buyer_id'] ? 'flex-row-reverse text-end' : '' ?>">
                        <img class="rounded-circle rounded-50 border border-secondary <?= get_current_user_id() == $contract['buyer_id'] ? 'ms-2' : 'me-2' ?>" src="<?= $contract['buyer']->get('image') ?: "https://ui-avatars.com/api/?name=" . $contract['buyer']->get('display_name') ?>" width="50" alt="<?= $contract['buyer']->get('display_name') ?>">
                        <div class="d-flex flex-column justify-content-start">
                            <span class="d-block fw-bold"><?= $contract['buyer']->get('display_name') ?></span>
                            <span class="date text-black-50"><?= ap_date_format($contract['created_at'], 'd M \a\t h:i a') ?></span>
                        </div>
                    </div>
                    <div class="mt-2 p-3 rounded text-break <?= get_current_user_id() == $contract['buyer_id'] ? 'bg-primary text-white' : 'bg-light' ?>">
                        <?= html_entity_decode($contract['description']) ?>
                    </div>
                </div>

                <?php foreach ($contract['comments'] as $comment) : ?>
                    <hr class="dotted" />
                    <div>
                        <div class="d-flex flex-row <?= get_current_user_id() == $comment['user_id'] ? 'flex-row-reverse text-end' : '' ?>">
                            <img class="rounded-circle rounded-50 border border-secondary <?= get_current_user_id() == $comment['user_id'] ? 'ms-2' : 'me-2' ?>" src="<?= $comment['user']->get('image') ?: "https://ui-avatars.com/api/?name=" . $comment['user']->get('display_name') ?>" width="50" alt="<?= $comment['user']->get('display_name') ?>">
                            <div class="d-flex flex-column justify-content-start">
                                <span class="d-block fw-bold"><?= $comment['user']->get('display_name') ?></span>
                                <span class="date text-black-50"><?= ap_date_format($comment['created_at'], 'd M \a\t h:i a') ?></span>
                            </div>
                        </div>
                        <div class="mt-2 p-3 rounded text-break d-flex justify-content-between <?= get_current_user_id() == $comment['user_id'] ? 'bg-primary text-white' : 'bg-light' ?>">
                            <div><?= html_entity_decode($comment['comment']) ?></div>

                            <?php if (get_current_user_id() == $comment['user_id']) : ?>
                                <div>
                                    <a class="text-danger btn btn-sm btn-light comment-delete" href="javascript:;" data-bs-toggle="modal" data-bs-target="#comment-delete-modal" data-id="<?= $comment['id'] ?>"><i class="fa fa-trash"></i></a>
                                </div>
                            <?php endif ?>
                        </div>
                    </div>
                <?php endforeach ?>

                <?php if ($progressSequences[$contract['status']] > 4) : ?>
                    <hr class="dotted" />
                    <div>
                        <div class="d-flex flex-row <?= get_current_user_id() == $contract['provider_id'] ? 'flex-row-reverse text-end' : '' ?>">
                            <img class="rounded-circle rounded-50 border border-secondary <?= get_current_user_id() == $contract['provider_id'] ? 'ms-2' : 'me-2' ?>" src="<?= $contract['provider']->get('image') ?: "https://ui-avatars.com/api/?name=" . $contract['provider']->get('display_name') ?>" width="50" alt="<?= $contract['provider']->get('display_name') ?>">
                            <div class="d-flex flex-column justify-content-start">
                                <span class="d-block fw-bold"><?= $contract['provider']->get('display_name') ?></span>
                                <div class="date text-black-50 d-flex gap-1  <?= get_current_user_id() == $contract['provider_id'] ? 'flex-row-reverse text-end' : '' ?>">
                                    <div>
                                        <?= ap_date_format($contract['delivered_at'], 'd M \a\t h:i a') ?>
                                    </div>
                                    <span class="badge bg-secondary">Delivery</span>
                                </div>
                            </div>
                        </div>
                        <div class="mt-2 p-3 rounded text-break <?= get_current_user_id() == $contract['provider_id'] ? 'bg-primary text-white' : 'bg-light' ?>">
                            <?= html_entity_decode($contract['delivery_notes']) ?? 'Delivered the contract' ?>

                            <?php if (count($contract['delivery_attachments'])) : ?>
                                <hr class="dotted mb-1" />
                                <div class="pb-2 small">Attachments:</div>
                                <div>
                                    <?php foreach ($contract['delivery_attachments'] as $attachment) : ?>
                                        <a class="badge <?= get_current_user_id() == $contract['provider_id'] ? 'bg-light' : 'bg-primary text-white' ?>" href="<?= $attachment->guid ?>" target="_blank"><i class="fa fa-paperclip"></i> <?= basename($attachment->guid) ?></a>
                                    <?php endforeach ?>
                                </div>
                            <?php endif ?>
                        </div>
                    </div>
                <?php endif ?>

                <?php if ($contract['rating'] && $progressSequences[$contract['status']] > 5) : ?>
                    <hr class="dotted" />
                    <div>
                        <div class="d-flex flex-row <?= get_current_user_id() == $contract['buyer_id'] ? 'flex-row-reverse text-end' : '' ?>">
                            <img class="rounded-circle rounded-50 border border-secondary <?= get_current_user_id() == $contract['buyer_id'] ? 'ms-2' : 'me-2' ?>" src="<?= $contract['buyer']->get('image') ?: "https://ui-avatars.com/api/?name=" . $contract['buyer']->get('display_name') ?>" width="50" alt="<?= $contract['buyer']->get('display_name') ?>">
                            <div class="d-flex flex-column justify-content-start">
                                <span class="d-block fw-bold"><?= $contract['buyer']->get('display_name') ?></span>

                                <div class="date text-black-50 d-flex gap-1  <?= get_current_user_id() != $contract['provider_id'] ? 'flex-row-reverse text-end' : '' ?>">
                                    <span><?= ap_date_format($contract['completed_at'], 'd M \a\t h:i a') ?></span>
                                    <span class="badge bg-secondary">Review</span>
                                </div>
                            </div>
                        </div>
                        <div class="mt-2 p-3 rounded text-break <?= get_current_user_id() == $contract['buyer_id'] ? 'bg-primary text-white' : 'bg-light' ?>">
                            <?php if ($contract['review']) : ?>
                                <?= $contract['review'] ?>
                                <hr class="dotted mb-1" />
                            <?php endif ?>

                            <div class="pb-2 small">Rating:</div>
                            <div class="ratings">
                                <i class="fa fa-star <?= $contract['rating'] >= 1 ? 'text-warning' : '' ?>"></i>
                                <i class="fa fa-star <?= $contract['rating'] >= 2 ? 'text-warning' : '' ?>"></i>
                                <i class="fa fa-star <?= $contract['rating'] >= 3 ? 'text-warning' : '' ?>"></i>
                                <i class="fa fa-star <?= $contract['rating'] >= 4 ? 'text-warning' : '' ?>"></i>
                                <i class="fa fa-star <?= $contract['rating'] >= 5 ? 'text-warning' : '' ?>"></i>
                            </div>
                        </div>
                    </div>
                <?php endif ?>
            </div>

            <?php if (!in_array($contract['status'], ['completed', 'cleared'])) : ?>
                <div class="bg-light p-3">
                    <form method="post" action="<?= ap_route('contracts.comment', $contract['id']) ?>">
                        <?php wp_nonce_field(); ?>
                        <textarea name="comment" class="form-control ms-1 editor"></textarea>
                        <div class="mt-2 d-flex justify-content-end">
                            <button class="btn btn-primary btn-sm">Send</button>
                        </div>
                    </form>
                </div>
            <?php endif ?>
        </div>
    </div>
</div>

<?php include 'show-modals.php' ?>

<script src="<?= str_replace('/views', '', plugin_dir_url(__DIR__)) . 'js/ckeditor.js' ?>"></script>
<script>
    <?php if (!isset($_GET['error_message'])) : ?>
        window.scrollTo(0, document.getElementById('activity').scrollHeight + 600);
    <?php endif ?>

    ClassicEditor
        .create(document.querySelector('.editor'))
        .catch(error => {
            console.error(error);
        });

    jQuery(document).ready(function($) {
        $(".btnrating").on('click', (function(e) {
            var previous_value = $("#selected_rating").val();

            var selected_value = $(this).attr("data-attr");
            $("#selected_rating").val(selected_value);

            $(".selected-rating").empty();
            $(".selected-rating").html(selected_value);

            for (i = 1; i <= selected_value; ++i) {
                $("#rating-star-" + i).toggleClass('btn-warning');
                $("#rating-star-" + i).toggleClass('btn-secondary');
            }

            for (ix = 1; ix <= previous_value; ++ix) {
                $("#rating-star-" + ix).toggleClass('btn-warning');
                $("#rating-star-" + ix).toggleClass('btn-secondary');
            }
        }));

        $('.comment-delete').on('click', (e) => {
            let commentId = $(e.target).hasClass('comment-delete') ? $(e.target).data('id') : $(e.target).parent().data('id');
            let url = '<?= ap_route('contracts.comment.delete', ['contract' => $contract['id'], 'comment' => ':comment']) ?>';
            url = url.replace(':comment', commentId);

            $('#comment-delete-modal').find('form').attr('action', url);
        });
    });
</script>