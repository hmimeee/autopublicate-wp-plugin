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
                        
                        <?php if ($contract['status'] == 'pending') : ?>
                            <div class="pb-2"><i class="fa fa-clock"></i> Budget Type: <span class="ps-2"><?= ucfirst($contract['budget_type'] ?? 'N/A') ?></span></div>
                        <?php endif ?>

                        <div class="pb-2"><i class="fa fa-dollar-sign p-1"></i> Budget: <span class="ps-2">$<?= number_format($contract['budget'] ?? 0, 2) ?></span></div>
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
                                <div><i class="fa fa-archive"></i> <?= $user->completed_count ?> contracts completed</div>
                            </div>
                        </div>
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
                        <img class="rounded-circle <?= get_current_user_id() == $contract['buyer_id'] ? 'ms-2' : 'me-2' ?>" src="<?= "https://ui-avatars.com/api/?name=" . $contract['buyer']->get('display_name') ?>" width="50" alt="<?= $contract['buyer']->get('user_nicename') ?>">
                        <div class="d-flex flex-column justify-content-start">
                            <span class="d-block fw-bold"><?= $contract['buyer']->get('user_nicename') ?></span>
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
                            <img class="rounded-circle <?= get_current_user_id() == $comment['user_id'] ? 'ms-2' : 'me-2' ?>" src="<?= "https://ui-avatars.com/api/?name=" . $comment['user']->get('display_name') ?>" width="50" alt="<?= $comment['user']->get('user_nicename') ?>">
                            <div class="d-flex flex-column justify-content-start">
                                <span class="d-block fw-bold"><?= $comment['user']->get('user_nicename') ?></span>
                                <span class="date text-black-50"><?= ap_date_format($comment['created_at'], 'd M \a\t h:i a') ?></span>
                            </div>
                        </div>
                        <div class="mt-2 p-3 rounded text-break <?= get_current_user_id() == $comment['user_id'] ? 'bg-primary text-white' : 'bg-light' ?>">
                            <?= html_entity_decode($comment['comment']) ?>
                        </div>
                    </div>
                <?php endforeach ?>

                <?php if ($progressSequences[$contract['status']] > 3) : ?>
                    <hr class="dotted" />
                    <div>
                        <div class="d-flex flex-row <?= get_current_user_id() == $contract['provider_id'] ? 'flex-row-reverse text-end' : '' ?>">
                            <img class="rounded-circle <?= get_current_user_id() == $contract['provider_id'] ? 'ms-2' : 'me-2' ?>" src="<?= "https://ui-avatars.com/api/?name=" . $contract['provider']->get('display_name') ?>" width="50" alt="<?= $contract['provider']->get('user_nicename') ?>">
                            <div class="d-flex flex-column justify-content-start">
                                <span class="d-block fw-bold"><?= $contract['provider']->get('user_nicename') ?></span>
                                <span class="date text-black-50"><?= ap_date_format($contract['delivered_at'], 'd M \a\t h:i a') ?></span>
                            </div>
                        </div>
                        <div class="mt-2 p-3 rounded text-break <?= get_current_user_id() == $contract['provider_id'] ? 'bg-primary text-white' : 'bg-light' ?>">
                            <?= html_entity_decode($contract['delivery_notes']) ?>
                            <hr class="dotted mb-1" />
                            <div class="pb-2 small">Attachments:</div>
                            <div>
                                <?php foreach ($contract['delivery_attachments'] ?? [] as $attachment) : ?>
                                    <a class="badge <?= get_current_user_id() == $contract['provider_id'] ? 'bg-light' : 'bg-primary text-white' ?>" href="<?= $attachment->guid ?>" target="_blank"><i class="fa fa-paperclip"></i> <?= basename($attachment->guid) ?></a>
                                <?php endforeach ?>
                            </div>
                        </div>
                    </div>
                <?php endif ?>

                <?php if ($contract['rating'] && $progressSequences[$contract['status']] > 4) : ?>
                    <hr class="dotted" />
                    <div>
                        <div class="d-flex flex-row <?= get_current_user_id() == $contract['buyer_id'] ? 'flex-row-reverse text-end' : '' ?>">
                            <img class="rounded-circle <?= get_current_user_id() == $contract['buyer_id'] ? 'ms-2' : 'me-2' ?>" src="<?= "https://ui-avatars.com/api/?name=" . $contract['buyer']->get('display_name') ?>" width="50" alt="<?= $contract['buyer']->get('user_nicename') ?>">
                            <div class="d-flex flex-column justify-content-start">
                                <span class="d-block fw-bold"><?= $contract['buyer']->get('user_nicename') ?></span>
                                <span class="date text-black-50"><?= ap_date_format($contract['completed_at'], 'd M \a\t h:i a') ?></span>
                            </div>
                        </div>
                        <div class="mt-2 p-3 rounded text-break <?= get_current_user_id() == $contract['buyer_id'] ? 'bg-primary text-white' : 'bg-light' ?>">
                            <?= $contract['review'] ?? 'N/A' ?>
                            <hr class="dotted mb-1" />
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
    });
</script>