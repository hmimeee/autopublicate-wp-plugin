<div class="page-body">
    <div class="sub-title">
        <h2>New Contract</h2>
    </div>

    <div class="content-page p-0 pt-3">
        <div class="ps-md-4 ps-2 overflow-hidden">
            <form method="post" enctype="multipart/form-data">
                <?php wp_nonce_field(); ?>
                <div class="row pb-4">
                    <div class="col-md-3 border-end">
                        <img width="120px" src="<?= $user->get('image') ?: "https://ui-avatars.com/api/?name=" . $user->get('display_name') ?>" alt="<?= $user->get('display_name') ?>">
                        <div class="pt-1">
                            <a href="<?= ap_route('user_profile', $user->get('user_login')) ?>">
                                <h2><?= ucwords($user->get('display_name')) ?></h2>
                            </a>
                            <div class="mb-2"><i class="fa fa-map-marker"></i> <?= $user->get('country') ?: 'N/A' ?></div>
                            <div class="mb-2"><i class="fa fa-archive"></i> <?= $user->completed_count ?> contracts completed</div>
                            <div><i class="fa fa-comment"></i> I speak <?= $user->get('languages') ? implode(', ', explode(',', $user->get('languages'))) : 'N/A' ?></div>
                            <div class="mt-3">
                                <span><i class="fa fa-hands-wash"></i> Skills:</span>
                                <?php $skills = array_filter(explode(',', $user->get('skills')));
                                foreach ($skills as $skill) : ?>
                                    <div class="badge bg-secondary"><?= $skill ?></div>
                                <?php endforeach ?>
                            </div>
                        </div>
                    </div>

                    <hr class="d-md-none mt-3"/>

                    <div class="col-md-9 border-right">
                        <div class="p-3 py-3">
                            <h4>Contract Details</h4>

                            <div class="row mt-2">
                                <div class="col-md-12 form-group">
                                    <label class="labels">Title</label>
                                    <input name="title" type="text" class="form-control" placeholder="Enter title" value="<?= request('title') ?>">
                                </div>
                                <div class="col-md-12 form-group">
                                    <label class="labels">Description</label>
                                    <textarea id="editor" name="description"><?= request('description') ?></textarea>
                                </div>
                            </div>
                            <div class="row mt-3 form-group">
                                <div class="col-md-4"><label class="labels">Expected Deadline</label>
                                    <input type="date" name="expected_deadline" class="form-control" placeholder="Expected date" value="<?= request('expected_deadline') ?>">
                                </div>

                                <div class="col-md-8 form-group">
                                    <label class="labels">Attachments</label>
                                    <input name="attachments[]" type="file" class="form-control" multiple />
                                </div>
                            </div>

                            <div class="mt-4">
                                <button class="btn btn-primary">Submit</button>
                                <a href="<?= ap_route('user_profile', $user->get('user_login')) ?>" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </div>
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