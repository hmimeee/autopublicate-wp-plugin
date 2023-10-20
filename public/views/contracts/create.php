<div class="page-body">
    <div class="sub-title">
        <h2>New Contract</h2>
    </div>

    <div class="content-page">
        <div class="ps-4">
            <form method="post" enctype="multipart/form-data">
                <?php wp_nonce_field(); ?>
                <div class="row pb-4">
                    <div class="col-md-3 border-end">
                        <img width="120px" src="<?= "https://ui-avatars.com/api/?name=" . $user->get('display_name') ?>" alt="<?= $user->get('display_name') ?>">
                        <div class="pt-1">
                            <a href="<?= ap_route('user_profile', $user->get('user_login')) ?>">
                                <h2><?= ucwords($user->get('user_nicename')) ?></h2>
                            </a>
                            <div class="mb-2"><i class="fa fa-map-marker"></i> <?= $user->get('country') ?: 'N/A' ?></div>
                            <div class="mb-2"><i class="fa fa-archive"></i> <?= $user->completed_count ?> contracts completed</div>
                            <div><i class="fa fa-comment"></i> I speak <?= $user->get('languages') ? implode(', ', explode(',', $user->get('languages'))) : 'N/A' ?></div>
                            <div>
                                <span><i class="fa fa-hands-wash"></i> Skills:</span>
                                <?php $skills = array_filter(explode(',', $user->get('skills'))); foreach ($skills as $skill) : ?>
                                    <div class="btn btn-outline-secondary m-1 border"><?= $skill ?></div>
                                <?php endforeach ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-9 border-right">
                        <div class="p-3 py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <h4 class="text-right">Contract Details</h4>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-12 form-group">
                                    <label class="labels">Title</label>
                                    <input name="title" type="text" class="form-control" placeholder="Enter title" value="">
                                </div>
                                <div class="col-md-12 form-group">
                                    <label class="labels">Description</label>
                                    <textarea id="editor" name="description"></textarea>
                                </div>
                            </div>
                            <div class="row mt-3 form-group">
                                <div class="col-md-6"><label class="labels">Expected Deadline</label>
                                    <input type="date" name="expected_deadline" class="form-control" placeholder="Expected date" value="">
                                </div>
                                <div class="col-md-6">
                                    <label class="labels">Budget</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <select name="budget_type" class="form-control">
                                                <option value="">Select Type</option>
                                                <option value="fixed">Fixed</option>
                                                <option value="estimated">Estimated</option>
                                            </select>
                                        </div>
                                        <input type="number" step="any" name="budget" id="budget" class="form-control" placeholder="Enter your budget" value="">
                                        <label for="budget" class="input-group-text">$</label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="labels">Attachments</label>
                                <input name="attachments[]" type="file" class="form-control" multiple />
                            </div>

                            <div class="mt-4">
                                <button class="btn btn-primary">Submit</button>
                                <a href="<?= ap_route('user_profile', $user->get('user_login')) ?>" class="btn btn-secondary">Cancel</a>
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